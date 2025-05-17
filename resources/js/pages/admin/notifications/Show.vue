<template>
    <Head title="Detail Template WhatsApp" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6">
            <!-- Header dengan judul dan tombol aksi -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <h1 class="text-2xl font-bold text-secondary-900 dark:text-white">Detail Template WhatsApp</h1>
                <div class="flex gap-2">
                    <Link :href="route('admin.notifications.index')">
                        <Button variant="outline" class="flex items-center gap-1.5">
                            <ArrowLeftIcon class="h-4 w-4" />
                            Kembali
                        </Button>
                    </Link>
                    <Link :href="route('admin.notifications.edit', template.id)">
                        <Button variant="action" class="flex items-center gap-1.5">
                            <PencilIcon class="h-4 w-4" />
                            Edit Template
                        </Button>
                    </Link>
                </div>
            </div>
            
            <!-- Template Basic Info -->
            <Card class="border border-slate-200 dark:border-slate-700">
                <CardHeader>
                    <CardTitle>Informasi Template</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">Nama Template</h3>
                            <p class="text-base font-medium text-slate-900 dark:text-white">{{ template.name }}</p>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">Tipe</h3>
                            <Badge variant="outline" class="px-2 py-1">
                                {{ template.type === 'customer' ? 'Template Pelanggan' : 'Template Admin' }}
                            </Badge>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">Status Pemicu</h3>
                            <p class="text-base font-medium text-slate-900 dark:text-white">
                                {{ triggerStatusOptions[template.trigger_status] || template.trigger_status }}
                            </p>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">Status</h3>
                            <Badge :variant="template.is_active ? 'success' : 'destructive'" class="px-2 py-1">
                                {{ template.is_active ? 'Aktif' : 'Nonaktif' }}
                            </Badge>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">Urutan</h3>
                            <p class="text-base font-medium text-slate-900 dark:text-white">{{ template.order }}</p>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">Terakhir Diperbarui</h3>
                            <p class="text-base font-medium text-slate-900 dark:text-white">
                                {{ formatDate(template.updated_at) }}
                            </p>
                        </div>
                    </div>
                </CardContent>
            </Card>
            
            <!-- Template Message -->
            <Card class="border border-slate-200 dark:border-slate-700">
                <CardHeader>
                    <CardTitle>Template Pesan</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="p-4 bg-slate-50 dark:bg-slate-800 rounded-md">
                        <pre class="whitespace-pre-wrap break-words text-sm text-slate-800 dark:text-slate-200">{{ template.message_template }}</pre>
                    </div>
                    
                    <!-- Deskripsi -->
                    <div v-if="template.description" class="mt-4">
                        <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">Deskripsi</h3>
                        <p class="text-slate-700 dark:text-slate-300">{{ template.description }}</p>
                    </div>
                    
                    <!-- Variabel yang tersedia -->
                    <div class="mt-6">
                        <h3 class="text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Variabel yang tersedia</h3>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                            <div 
                                v-for="(description, variable) in availableVariables" 
                                :key="variable" 
                                class="text-xs bg-slate-100 dark:bg-slate-700 p-2 rounded flex items-center justify-between"
                            >
                                <code class="text-primary-500 dark:text-primary-400">{{ variable }}</code>
                                <span class="text-slate-500 dark:text-slate-400">{{ description }}</span>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>
            
            <!-- Test Template -->
            <Card class="border border-slate-200 dark:border-slate-700">
                <CardHeader>
                    <CardTitle>Test Pengiriman Template</CardTitle>
                    <CardDescription>
                        Kirim template ke nomor WhatsApp untuk testing
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="sendTestMessage" class="space-y-4">
                        <div>
                            <Label for="test_phone">Nomor WhatsApp</Label>
                            <div class="flex gap-2">
                                <Input 
                                    id="test_phone"
                                    v-model="testPhone"
                                    type="text"
                                    placeholder="628123456789"
                                    class="flex-1"
                                    required
                                />
                                <Button 
                                    type="submit" 
                                    :disabled="processing"
                                    class="whitespace-nowrap"
                                >
                                    <SendIcon v-if="!processing" class="mr-2 h-4 w-4" />
                                    <LoaderIcon v-else class="mr-2 h-4 w-4 animate-spin" />
                                    Kirim Test
                                </Button>
                            </div>
                            <p class="mt-1 text-xs text-slate-500">
                                Format: 62812345678 (tanpa spasi atau karakter khusus)
                            </p>
                        </div>
                    </form>

                    <!-- Preview dengan variabel contoh -->
                    <div class="mt-6">
                        <h3 class="text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Preview Template</h3>
                        <div class="p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                            <div class="flex items-center mb-2">
                                <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center mr-2">
                                    <MessageCircleIcon class="h-4 w-4 text-white" />
                                </div>
                                <h4 class="font-medium text-green-900 dark:text-green-400">Preview WhatsApp</h4>
                            </div>
                            <div class="ml-10 p-3 bg-white dark:bg-slate-800 rounded-lg shadow-sm">
                                <div class="whitespace-pre-wrap break-words text-sm">
                                    {{ previewMessage }}
                                </div>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Log Pengiriman -->
            <Card class="border border-slate-200 dark:border-slate-700">
                <CardHeader>
                    <CardTitle>Riwayat Pengiriman</CardTitle>
                    <CardDescription>
                        Log pengiriman template dalam 24 jam terakhir
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
import { ref, computed, onMounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import axios from 'axios';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardHeader, CardTitle, CardContent, CardDescription } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { 
    ArrowLeftIcon, 
    PencilIcon, 
    MessageCircleIcon, 
    SendIcon, 
    LoaderIcon,
    CheckIcon,
    XIcon
} from 'lucide-vue-next';
import { useToast } from '@/composables/useToast';

const props = defineProps({
    template: {
        type: Object,
        required: true
    },
    availableVariables: {
        type: Object,
        required: true
    },
    triggerStatusOptions: {
        type: Object,
        required: true
    },
    sendingLogs: {
        type: Array,
        required: true
    }
});

const { toast } = useToast();
const testPhone = ref('');
const processing = ref(false);

// Breadcrumbs
const breadcrumbs = [
    {
        title: 'Admin',
        href: route('admin.dashboard'),
    },
    {
        title: 'Template WhatsApp',
        href: route('admin.notifications.index'),
    },
    {
        title: 'Detail Template',
        href: route('admin.notifications.show', props.template.id),
    },
];

// Setup CSRF token when component is mounted
onMounted(() => {
    // Get CSRF token from meta tag
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (token) {
        axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
    } else {
        console.error('CSRF token not found');
    }
});

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

// Hitung preview message dengan contoh variabel
const previewMessage = computed(() => {
    let message = props.template.message_template;
    const exampleVariables = {
        order_number: 'ORD' + new Date().toISOString().slice(0,10).replace(/-/g,'') + '1234',
        order_date: new Date().toLocaleString('id-ID'),
        customer_name: 'User Test',
        total_amount: 'Rp 500.000',
        payment_method: 'Bank Transfer',
        status: 'Sedang Diproses',
        items_list: "- 1x Produk Digital A: Rp 150.000\n- 2x Produk Digital B: Rp 200.000",
        subtotal: 'Rp 350.000',
        admin_fee: 'Rp 0',
        discount: 'Rp 0'
    };

    Object.entries(exampleVariables).forEach(([key, value]) => {
        message = message.replace(`{${key}}`, value);
    });

    return message;
});

const sendTestMessage = async () => {
    if (!testPhone.value) {
        toast({
            title: 'Nomor WhatsApp harus diisi',
            variant: 'destructive'
        });
        return;
    }

    // Validasi format nomor
    if (!/^62[0-9]{9,}$/.test(testPhone.value)) {
        toast({
            title: 'Format nomor WhatsApp tidak valid',
            description: 'Nomor harus diawali dengan 62',
            variant: 'destructive'
        });
        return;
    }

    processing.value = true;

    try {
        // Ambil CSRF token dari meta tag
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        if (!token) {
            console.error('CSRF token not found');
            toast({
                title: 'CSRF token tidak ditemukan',
                variant: 'destructive'
            });
            return;
        }

        const response = await fetch(`/admin/notifications/${props.template.id}/test`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                phone: testPhone.value
            })
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || 'Terjadi kesalahan saat mengirim pesan');
        }

        toast({
            title: 'Pesan test berhasil dikirim',
            variant: 'success'
        });
        
        // Refresh halaman untuk memperbarui log
        window.location.reload();

    } catch (error) {
        console.error('Error sending test message:', error);
        toast({
            title: 'Gagal mengirim pesan test',
            description: error.message,
            variant: 'destructive'
        });
    } finally {
        processing.value = false;
    }
};
</script> 