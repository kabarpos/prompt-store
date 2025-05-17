<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed, onBeforeUnmount, watch } from 'vue';

import DeleteUser from '@/components/DeleteUser.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Card, CardContent, CardHeader, CardTitle, CardDescription, CardFooter } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { type BreadcrumbItem, type SharedData, type User } from '@/types';
import { Upload, User as UserIcon, Trash2, AlertCircle, Mail, Save, Phone } from 'lucide-vue-next';
import { useInitials } from '@/composables/useInitials';
import { getAvatarUrl, validateAvatarFile, createAvatarPreview } from '@/utils/avatarUtils';

interface Props {
    mustVerifyEmail: boolean;
    status?: string;
    userData: {
        id: number;
        name: string;
        email: string;
        whatsapp: string;
        profile_photo_path: string | null;
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Pengaturan',
        href: '/settings/profile',
    },
    {
        title: 'Profil',
        href: '/settings/profile',
    }
];

const page = usePage<SharedData>();
const user = page.props.auth.user as User;
const { getInitials } = useInitials();
const errorMessage = ref<string | null>(null);

// Debug output user info
console.log('User data from server:', user);
console.log('User data from props:', props.userData);

// Avatar preview URL dan cleanup function
const avatarPreview = ref<string | null>(getAvatarUrl(user.profile_photo_path || null));
const avatarFile = ref<File | null>(null);
const revokePreviewFn = ref<(() => void) | null>(null);

// Cleanup function untuk URL objek saat komponen unmounted
onBeforeUnmount(() => {
    if (revokePreviewFn.value) {
        revokePreviewFn.value();
    }
});

// Get initials for avatar fallback
const userInitials = computed(() => getInitials(user.name));

// Form untuk update profile
const form = useForm({
    name: props.userData.name,
    email: props.userData.email,
    whatsapp: props.userData.whatsapp || '',
    avatar: null as File | null,
    _method: 'PATCH',
});

// Tambahkan watch untuk memastikan form.whatsapp diupdate jika user.whatsapp berubah
watch(() => user.whatsapp, (newValue) => {
    if (newValue && !form.whatsapp) {
        form.whatsapp = newValue;
    }
}, { immediate: true });

// Fungsi untuk menangani file upload avatar
const handleAvatarChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (!target.files?.length) return;
    
    const file = target.files[0];
    
    // Reset error message
    errorMessage.value = null;
    
    // Validasi file
    const validation = validateAvatarFile(file);
    if (!validation.valid) {
        errorMessage.value = validation.message || 'File tidak valid';
        target.value = ''; // Reset input file
        return;
    }
    
    // Cleanup preview URL sebelumnya
    if (revokePreviewFn.value) {
        revokePreviewFn.value();
    }
    
    // Buat preview baru
    const { previewUrl, revokePreview } = createAvatarPreview(file);
    avatarPreview.value = previewUrl;
    revokePreviewFn.value = revokePreview;
    
    avatarFile.value = file;
    form.avatar = file;
};

// Fungsi untuk menghapus avatar
const removeAvatar = () => {
    // Cleanup preview URL
    if (revokePreviewFn.value) {
        revokePreviewFn.value();
        revokePreviewFn.value = null;
    }
    
    avatarPreview.value = null;
    avatarFile.value = null;
    form.avatar = null;
    
    // Set ini ke true untuk memberitahu server bahwa kita ingin menghapus avatar
    form._method = 'DELETE';
    form.post(route('profile.avatar.destroy'), {
        preserveScroll: true,
        onSuccess: () => {
            form._method = 'PATCH'; // Kembalikan ke PATCH untuk operasi normal
        },
    });
};

// Submit form
const submit = () => {
    form.post(route('profile.update'), {
        preserveScroll: true,
        forceFormData: true,
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Pengaturan Profil" />

        <SettingsLayout>
            <div class="space-y-8">
                <!-- Header -->
                <div class="flex items-center gap-4 mb-8 pb-6 border-b border-slate-200 dark:border-slate-700">
                    <div class="p-3 rounded-full bg-primary-50 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400">
                        <UserIcon class="h-6 w-6" />
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-slate-900 dark:text-white">Profil Anda</h2>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Kelola informasi profil dan akun Anda</p>
                    </div>
                </div>

                <!-- Avatar Upload Section -->
                <div class="space-y-1">
                    <h3 class="text-lg font-medium text-slate-900 dark:text-white">Foto Profil</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Ini adalah foto yang akan ditampilkan di profil Anda</p>
                    
                    <div class="flex flex-col md:flex-row md:items-center gap-6 mt-4">
                        <div class="flex items-center justify-center">
                            <Avatar class="h-24 w-24">
                                <AvatarImage v-if="avatarPreview" :src="avatarPreview" alt="Avatar" />
                                <AvatarFallback class="text-lg bg-primary-100 text-primary-700 dark:bg-primary-900/50 dark:text-primary-300">{{ userInitials }}</AvatarFallback>
                            </Avatar>
                        </div>
                        
                        <div class="flex flex-col space-y-4 flex-1">
                            <div>
                                <Label for="avatar" class="block mb-2">Unggah foto baru</Label>
                                <Input 
                                    id="avatar" 
                                    type="file" 
                                    accept="image/*" 
                                    @change="handleAvatarChange" 
                                    :error="!!form.errors.avatar"
                                    class="w-full"
                                />
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Format: JPG, PNG, GIF, WEBP. Ukuran maksimal: 2MB.</p>
                                <InputError class="mt-1" :message="form.errors.avatar" />
                                <p v-if="errorMessage" class="mt-1 text-sm text-destructive">{{ errorMessage }}</p>
                            </div>
                            
                            <div class="flex gap-2">
                                <Button 
                                    type="button" 
                                    variant="outline" 
                                    size="sm" 
                                    @click="removeAvatar"
                                    :disabled="!avatarPreview || form.processing"
                                    class="gap-1"
                                >
                                    <Trash2 class="h-4 w-4" />
                                    Hapus Foto
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>

                <Separator />

                <!-- Profile Information -->
                <div class="space-y-1">
                    <h3 class="text-lg font-medium text-slate-900 dark:text-white">Informasi Profil</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Perbarui nama dan alamat email akun Anda</p>
                    
                    <form @submit.prevent="submit" class="space-y-6 mt-4">
                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="name">Nama Lengkap</Label>
                                <Input id="name" v-model="form.name" required autocomplete="name" placeholder="Nama lengkap Anda" />
                                <InputError :message="form.errors.name" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="email">Alamat Email</Label>
                                <div class="relative">
                                    <Mail class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                                    <Input
                                        id="email"
                                        type="email"
                                        class="pl-10"
                                        v-model="form.email"
                                        required
                                        autocomplete="username"
                                        placeholder="email@example.com"
                                    />
                                </div>
                                <InputError :message="form.errors.email" />
                            </div>
                            
                            <div class="grid gap-2 md:col-span-2">
                                <Label for="whatsapp">Nomor WhatsApp</Label>
                                <div class="relative">
                                    <Phone class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                                    <Input
                                        id="whatsapp"
                                        type="text"
                                        class="pl-10"
                                        v-model="form.whatsapp"
                                        required
                                        placeholder="08xxxxxxxxxx"
                                    />
                                </div>
                                <InputError :message="form.errors.whatsapp" />
                            </div>
                        </div>

                        <Alert v-if="mustVerifyEmail && !user.email_verified_at" variant="warning" class="mt-4">
                            <AlertCircle class="h-4 w-4" />
                            <AlertTitle>Email belum diverifikasi</AlertTitle>
                            <AlertDescription>
                                <p>
                                    Alamat email Anda belum diverifikasi.
                                    <Link
                                        :href="route('verification.send')"
                                        method="post"
                                        as="button"
                                        class="font-medium text-warning-700 dark:text-warning-400 underline hover:text-warning-800 dark:hover:text-warning-300"
                                    >
                                        Klik di sini untuk mengirim ulang email verifikasi.
                                    </Link>
                                </p>
                                <p v-if="status === 'verification-link-sent'" class="mt-2 font-medium text-success-600">
                                    Link verifikasi baru telah dikirim ke alamat email Anda.
                                </p>
                            </AlertDescription>
                        </Alert>

                        <div class="flex items-center gap-4 pt-2">
                            <Button type="submit" :disabled="form.processing" class="gap-2">
                                <Save v-if="!form.processing" class="h-4 w-4" />
                                <span v-if="form.processing">Menyimpan...</span>
                                <span v-else>Simpan Perubahan</span>
                            </Button>

                            <Transition
                                enter-active-class="transition ease-in-out"
                                enter-from-class="opacity-0"
                                leave-active-class="transition ease-in-out"
                                leave-to-class="opacity-0"
                            >
                                <p v-show="form.recentlySuccessful" class="text-sm text-success-600 dark:text-success-400 flex items-center gap-1">
                                    <AlertCircle class="h-4 w-4" />
                                    Profil berhasil diperbarui
                                </p>
                            </Transition>
                        </div>
                    </form>
                </div>

                <Separator />

                <!-- Delete Account Section -->
                <DeleteUser />
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
