<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { type BreadcrumbItem } from '@/types';
import { AlertCircle, Key, Save } from 'lucide-vue-next';

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Pengaturan',
        href: '/settings/profile',
    },
    {
        title: 'Kata Sandi',
        href: '/settings/password',
    }
];

const passwordInput = ref<HTMLInputElement | null>(null);
const currentPasswordInput = ref<HTMLInputElement | null>(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: (errors: any) => {
            if (errors.password) {
                form.reset('password', 'password_confirmation');
                if (passwordInput.value instanceof HTMLInputElement) {
                    passwordInput.value.focus();
                }
            }

            if (errors.current_password) {
                form.reset('current_password');
                if (currentPasswordInput.value instanceof HTMLInputElement) {
                    currentPasswordInput.value.focus();
                }
            }
        },
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Pengaturan Kata Sandi" />

        <SettingsLayout>
            <div class="space-y-6">
                <div class="flex items-center gap-4 mb-6 pb-6 border-b border-slate-200 dark:border-slate-700">
                    <div class="p-3 rounded-full bg-primary-50 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400">
                        <Key class="h-6 w-6" />
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-slate-900 dark:text-white">Ubah Kata Sandi</h2>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Pastikan akun Anda menggunakan kata sandi yang kuat untuk keamanan</p>
                    </div>
                </div>

                <form @submit.prevent="updatePassword" class="space-y-6">
                    <div class="space-y-4">
                        <div class="grid gap-3">
                            <Label for="current_password">Kata Sandi Saat Ini</Label>
                            <Input
                                id="current_password"
                                ref="currentPasswordInput"
                                v-model="form.current_password"
                                type="password"
                                class="block w-full"
                                autocomplete="current-password"
                                placeholder="Masukkan kata sandi saat ini"
                            />
                            <InputError :message="form.errors.current_password" />
                        </div>

                        <div class="grid gap-3">
                            <Label for="password">Kata Sandi Baru</Label>
                            <Input
                                id="password"
                                ref="passwordInput"
                                v-model="form.password"
                                type="password"
                                class="block w-full"
                                autocomplete="new-password"
                                placeholder="Masukkan kata sandi baru"
                            />
                            <InputError :message="form.errors.password" />
                        </div>

                        <div class="grid gap-3">
                            <Label for="password_confirmation">Konfirmasi Kata Sandi</Label>
                            <Input
                                id="password_confirmation"
                                v-model="form.password_confirmation"
                                type="password"
                                class="block w-full"
                                autocomplete="new-password"
                                placeholder="Konfirmasi kata sandi baru"
                            />
                            <InputError :message="form.errors.password_confirmation" />
                        </div>
                    </div>

                    <div class="flex items-center gap-4 pt-4">
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
                                Kata sandi berhasil diperbarui
                            </p>
                        </Transition>
                    </div>
                </form>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
