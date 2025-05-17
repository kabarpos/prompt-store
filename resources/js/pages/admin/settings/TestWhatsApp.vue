<template>
    <Head title="Test Pengiriman WhatsApp" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <h1 class="text-2xl font-bold text-secondary-900 dark:text-white">Test Pengiriman WhatsApp</h1>
                <div class="flex gap-2">
                    <Link :href="route('admin.settings.index')">
                        <Button variant="outline" class="flex items-center gap-1.5">
                            <ArrowLeftIcon class="h-4 w-4" />
                            Kembali
                        </Button>
                    </Link>
                </div>
            </div>

            <!-- Status Konfigurasi -->
            <Card class="border border-slate-200 dark:border-slate-700">
                <CardHeader>
                    <CardTitle>Status Konfigurasi WhatsApp</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-800 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div :class="[
                                    'w-3 h-3 rounded-full',
                                    settings.webhook_is_active ? 'bg-green-500' : 'bg-red-500'
                                ]"></div>
                                <span class="font-medium">Status Webhook</span>
                            </div>
                            <Badge :variant="settings.webhook_is_active ? 'success' : 'destructive'">
                                {{ settings.webhook_is_active ? 'Aktif' : 'Nonaktif' }}
                            </Badge>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">Nomor Pengirim</h3>
                                <p class="text-base font-medium text-slate-900 dark:text-white">
                                    {{ settings.webhook_sender_phone || 'Belum diatur' }}
                                </p>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">API Key</h3>
                                <p class="text-base font-medium text-slate-900 dark:text-white">
                                    {{ settings.webhook_api_key ? '••••••••' : 'Belum diatur' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Form Test Pengiriman -->
            <Card class="border border-slate-200 dark:border-slate-700">
                <CardHeader>
                    <CardTitle>Test Pengiriman Pesan</CardTitle>
                    <CardDescription>
                        Kirim pesan test ke nomor WhatsApp
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="sendTestMessage" class="space-y-4">
                        <div>
                            <Label for="phone">Nomor WhatsApp Tujuan</Label>
                            <Input 
                                id="phone" 
                                v-model="form.phone" 
                                type="text" 
                                placeholder="628123456789"
                                class="mt-1"
                                required
                            />
                            <p class="mt-1 text-xs text-slate-500">
                                Format: 628xxx (tanpa spasi atau karakter khusus)
                            </p>
                        </div>

                        <div>
                            <Label for="message">Pesan</Label>
                            <Textarea 
                                id="message" 
                                v-model="form.message" 
                                placeholder="Masukkan pesan yang akan dikirim"
                                class="mt-1"
                                rows="4"
                                required
                            />
                        </div>

                        <div class="flex justify-end">
                            <Button 
                                type="submit" 
                                :disabled="form.processing || !settings.webhook_is_active"
                            >
                                <SendIcon v-if="!form.processing" class="mr-2 h-4 w-4" />
                                <LoaderIcon v-else class="mr-2 h-4 w-4 animate-spin" />
                                Kirim Pesan Test
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>

            <!-- Riwayat Pengiriman -->
            <Card class="border border-slate-200 dark:border-slate-700">
                <CardHeader>
                    <CardTitle>Riwayat Pengiriman</CardTitle>
                    <CardDescription>
                        Log pengiriman pesan test
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div v-if="sendingLogs.length === 0" class="text-center py-8 text-slate-500">
                        Belum ada riwayat pengiriman
                    </div>
                    <div v-else class="space-y-4">
                        <div v-for="log in sendingLogs" :key="log.id" class="flex items-start gap-4 p-3 rounded-lg" :class="[
                            log.status === 'success' ? 'bg-green-50 dark:bg-green-900/20' : 'bg-red-50 dark:bg-red-900/20'
                        ]">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center" :class="[
                                log.status === 'success' ? 'bg-green-500' : 'bg-red-500'
                            ]">
                                <CheckIcon v-if="log.status === 'success'" class="h-4 w-4 text-white" />
                                <XIcon v-else class="h-4 w-4 text-white" />
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <p class="font-medium">{{ log.phone }}</p>
                                    <span class="text-xs">{{ formatDate(log.created_at) }}</span>
                                </div>
                                <p class="text-sm mt-1" :class="[
                                    log.status === 'success' ? 'text-green-700 dark:text-green-400' : 'text-red-700 dark:text-red-400'
                                ]">
                                    {{ log.status === 'success' ? 'Berhasil terkirim' : 'Gagal terkirim' }}
                                </p>
                                <p v-if="log.error" class="text-xs text-red-600 dark:text-red-400 mt-1">
                                    Error: {{ log.error }}
                                </p>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardHeader, CardTitle, CardContent, CardDescription } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Label } from '@/components/ui/label';
import { 
    ArrowLeftIcon,
    SendIcon,
    LoaderIcon,
    CheckIcon,
    XIcon
} from 'lucide-vue-next';
import { useToast } from '@/composables/useToast';

const props = defineProps({
    settings: {
        type: Object,
        required: true
    },
    sendingLogs: {
        type: Array,
        default: () => []
    }
});

const { toast } = useToast();

// Form state
const form = useForm({
    phone: '',
    message: ''
});

// Breadcrumbs
const breadcrumbs = [
    {
        title: 'Admin',
        href: route('admin.dashboard'),
    },
    {
        title: 'Pengaturan',
        href: route('admin.settings.index'),
    },
    {
        title: 'Test WhatsApp',
        href: route('admin.settings.test-whatsapp'),
    },
];

// Format date helper
const formatDate = (dateString) => {
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).format(date);
};

// Send test message
const sendTestMessage = () => {
    if (!props.settings.webhook_is_active) {
        toast({
            title: 'Webhook tidak aktif',
            description: 'Aktifkan webhook terlebih dahulu di pengaturan',
            variant: 'destructive'
        });
        return;
    }

    // Validasi format nomor
    if (!/^62[0-9]{9,}$/.test(form.phone)) {
        toast({
            title: 'Format nomor WhatsApp tidak valid',
            description: 'Nomor harus diawali dengan 62',
            variant: 'destructive'
        });
        return;
    }

    form.post(route('admin.settings.send-test-whatsapp'), {
        onSuccess: () => {
            toast({
                title: 'Berhasil',
                description: 'Pesan test berhasil dikirim',
                variant: 'success'
            });
            form.reset();
        },
        onError: (error) => {
            toast({
                title: 'Gagal mengirim pesan',
                description: error.message || 'Terjadi kesalahan saat mengirim pesan',
                variant: 'destructive'
            });
        }
    });
};
</script> 