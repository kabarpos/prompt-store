<script setup lang="ts">
import { defineComponent as _defineComponent, ref, computed } from "vue";
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle, CardDescription, CardFooter } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { 
    ShoppingBag, Package, Truck, CheckCircle, Clock, 
    ArrowLeft, FileText, User, Phone, Mail,
    CreditCard, ExternalLink, ClipboardIcon, Info
} from 'lucide-vue-next';
import { Alert, AlertTitle, AlertDescription } from '@/components/ui/alert';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';

interface Product {
    id: number;
    name: string;
    featured_image: string;
    category?: {
        id: number;
        name: string;
    };
}

interface OrderItem {
    id: number;
    order_id: number;
    product_id: number;
    quantity: number;
    price: number;
    subtotal: number;
    product: Product;
}

interface PaymentMethod {
    id: number;
    type: string;
    name: string;
    bank_name?: string;
    account_number?: string;
    account_name?: string;
    is_active: boolean;
}

interface Payment {
    id: number;
    order_id: number;
    amount: number;
    status: string;
    payment_method: PaymentMethod;
    payment_date: string;
}

interface Order {
    id: number;
    order_number: string;
    user_id: number;
    customer_name: string;
    customer_phone: string;
    customer_email: string;
    subtotal: number;
    admin_fee: number;
    discount: number;
    total_amount: number;
    status: string;
    notes: string;
    created_at: string;
    updated_at: string;
    items: OrderItem[];
    payment?: Payment;
}

interface Props {
    order: Order;
    allPaymentMethods?: PaymentMethod[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: route('dashboard'),
    },
    {
        title: 'Pesanan',
        href: route('orders.index'),
    },
    {
        title: `Order #${props.order.order_number}`,
        href: route('orders.show', props.order.id),
    },
];

// Format date
const formatDate = (dateString: string): string => {
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }).format(date);
};

// Format currency
const formatPrice = (amount: number): string => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(amount);
};

// Get status label
const getStatusLabel = (status: string): string => {
    const labels: Record<string, string> = {
        'pending': 'Menunggu Konfirmasi',
        'processing': 'Sedang Diproses',
        'shipping': 'Dalam Pengiriman',
        'completed': 'Selesai',
        'cancelled': 'Dibatalkan'
    };

    return labels[status] || status;
};

// Get status color class
const getStatusColor = (status: string): string => {
    const colors: Record<string, string> = {
        'pending': 'border-yellow-400 text-yellow-600 bg-yellow-50 dark:bg-yellow-900/30 dark:text-yellow-300 dark:border-yellow-700',
        'processing': 'border-blue-400 text-blue-600 bg-blue-50 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-700',
        'shipping': 'border-indigo-400 text-indigo-600 bg-indigo-50 dark:bg-indigo-900/30 dark:text-indigo-300 dark:border-indigo-700',
        'completed': 'border-green-400 text-green-600 bg-green-50 dark:bg-green-900/30 dark:text-green-300 dark:border-green-700',
        'cancelled': 'border-red-400 text-red-600 bg-red-50 dark:bg-red-900/30 dark:text-red-300 dark:border-red-700'
    };

    return colors[status] || 'border-gray-400 text-gray-600 bg-gray-50 dark:bg-gray-900/30 dark:text-gray-300 dark:border-gray-700';
};

// Get status icon
const getStatusIcon = (status: string) => {
    const icons: Record<string, any> = {
        'pending': Clock,
        'processing': Package,
        'shipping': Truck,
        'completed': CheckCircle,
        'cancelled': Clock
    };

    return icons[status] || Clock;
};

// Tambahkan fungsi baru untuk status pembayaran
const getPaymentStatusLabel = (status: string): string => {
    const labels: Record<string, string> = {
        'pending': 'Menunggu Pembayaran',
        'completed': 'Pembayaran Diterima',
        'failed': 'Pembayaran Gagal'
    };

    return labels[status] || status;
};

const getPaymentStatusColor = (status: string): string => {
    const colors: Record<string, string> = {
        'pending': 'border-yellow-400 text-yellow-600 bg-yellow-50 dark:bg-yellow-900/30 dark:text-yellow-300 dark:border-yellow-700',
        'completed': 'border-green-400 text-green-600 bg-green-50 dark:bg-green-900/30 dark:text-green-300 dark:border-green-700',
        'failed': 'border-red-400 text-red-600 bg-red-50 dark:bg-red-900/30 dark:text-red-300 dark:border-red-700'
    };

    return colors[status] || 'border-gray-400 text-gray-600 bg-gray-50 dark:bg-gray-900/30 dark:text-gray-300 dark:border-gray-700';
};

// Get available bank accounts
const activeBankAccounts = computed(() => {
    if (props.allPaymentMethods && props.allPaymentMethods.length > 0) {
        return props.allPaymentMethods.filter(
            method => method.type === 'bank_transfer' && method.is_active
        );
    }
    
    // Fallback to default accounts if no data provided
    return [
        {
            id: 1,
            type: 'bank_transfer',
            name: 'Bank BCA',
            bank_name: 'Bank BCA',
            account_number: '1234567890',
            account_name: 'PT Dilogif Indonesia',
            is_active: true
        },
        {
            id: 2,
            type: 'bank_transfer',
            name: 'Bank Mandiri',
            bank_name: 'Bank Mandiri',
            account_number: '9876543210',
            account_name: 'PT Dilogif Indonesia',
            is_active: true
        },
        {
            id: 3,
            type: 'bank_transfer',
            name: 'Bank BNI',
            bank_name: 'Bank BNI',
            account_number: '5678901234',
            account_name: 'PT Dilogif Indonesia',
            is_active: true
        }
    ];
});

// Copy to clipboard functionality
const copiedText = ref('');

const copyToClipboard = (text: string | undefined): void => {
    if (!text) return;
    
    navigator.clipboard.writeText(text).then(() => {
        copiedText.value = text;
        // Toast notification could be added here if available
        
        setTimeout(() => {
            copiedText.value = '';
        }, 2000);
    }).catch(err => {
        console.error('Gagal menyalin teks: ', err);
    });
};
</script>

<template>
    <Head title="Detail Pesanan" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-4 md:p-6">
            <!-- Header -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-xl md:text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                        Order #{{ order.order_number }}
                    </h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400">{{ formatDate(order.created_at) }}</p>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-3">
                    <Badge 
                        variant="outline" 
                        class="text-sm md:text-base px-3 py-1.5 self-start md:self-auto"
                        :class="getStatusColor(order.status)"
                    >
                        <component :is="getStatusIcon(order.status)" class="h-4 w-4 mr-1.5" />
                        {{ getStatusLabel(order.status) }}
                    </Badge>
                    
                    <!-- Tombol Pembayaran -->
                    <div v-if="order.payment?.status === 'pending'">
                        <Link :href="route('orders.payment.confirm', order.id)">
                            <Button variant="default" class="self-start md:self-auto">
                                <CreditCard class="h-4 w-4 mr-1.5" />
                                Konfirmasi Pembayaran
                            </Button>
                        </Link>
                    </div>
                </div>
            </div>
            
            <!-- Back Button -->
            <div>
                <Link :href="route('orders.index')" class="inline-flex items-center text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300">
                    <ArrowLeft class="h-4 w-4 mr-1" />
                    Kembali ke Daftar Pesanan
                </Link>
            </div>
            
            <!-- Order Details -->
            <div class="grid md:grid-cols-3 gap-6">
                <!-- Customer Info -->
                <div class="md:col-span-1">
                    <Card class="h-full">
                        <CardHeader>
                            <CardTitle>Informasi Pemesan</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-4">
                                <div class="flex items-start gap-3">
                                    <User class="h-5 w-5 text-slate-400 mt-0.5" />
                                    <div>
                                        <p class="text-sm text-slate-500 dark:text-slate-400">Nama</p>
                                        <p class="font-medium">{{ order.customer_name }}</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <Phone class="h-5 w-5 text-slate-400 mt-0.5" />
                                    <div>
                                        <p class="text-sm text-slate-500 dark:text-slate-400">WhatsApp</p>
                                        <p class="font-medium">{{ order.customer_phone }}</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <Mail class="h-5 w-5 text-slate-400 mt-0.5" />
                                    <div>
                                        <p class="text-sm text-slate-500 dark:text-slate-400">Email</p>
                                        <p class="font-medium">{{ order.customer_email }}</p>
                                    </div>
                                </div>
                                <div v-if="order.notes" class="flex items-start gap-3">
                                    <FileText class="h-5 w-5 text-slate-400 mt-0.5" />
                                    <div>
                                        <p class="text-sm text-slate-500 dark:text-slate-400">Catatan</p>
                                        <p class="font-medium">{{ order.notes }}</p>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
                
                <!-- Order Items -->
                <div class="md:col-span-2">
                    <Card>
                        <CardHeader>
                            <CardTitle>Detail Produk</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <!-- Products Table -->
                            <div class="rounded-lg border">
                                <Table>
                                    <TableHeader>
                                        <TableRow>
                                            <TableHead class="w-[50%]">Produk</TableHead>
                                            <TableHead class="text-center">Harga</TableHead>
                                            <TableHead class="text-center">Jumlah</TableHead>
                                            <TableHead class="text-right">Subtotal</TableHead>
                                        </TableRow>
                                    </TableHeader>
                                    <TableBody>
                                        <TableRow v-for="item in order.items" :key="item.id" class="hover:bg-slate-50 dark:hover:bg-slate-700/50">
                                            <TableCell>
                                                <div class="flex items-center">
                                                    <div class="h-12 w-12 flex-shrink-0 overflow-hidden rounded-md border border-slate-200 dark:border-slate-700">
                                                        <img 
                                                            :src="`/storage/${item.product.featured_image}`" 
                                                            :alt="item.product.name" 
                                                            class="h-full w-full object-cover object-center"
                                                        />
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-slate-900 dark:text-white">{{ item.product.name }}</div>
                                                        <div v-if="item.product.category" class="text-xs text-slate-500 dark:text-slate-400">{{ item.product.category.name }}</div>
                                                    </div>
                                                </div>
                                            </TableCell>
                                            <TableCell class="text-center text-sm text-slate-500 dark:text-slate-400">
                                                {{ formatPrice(item.price) }}
                                            </TableCell>
                                            <TableCell class="text-center text-sm text-slate-500 dark:text-slate-400">
                                                {{ item.quantity }}
                                            </TableCell>
                                            <TableCell class="text-right text-sm font-medium text-slate-900 dark:text-white">
                                                {{ formatPrice(item.subtotal) }}
                                            </TableCell>
                                        </TableRow>
                                    </TableBody>
                                </Table>
                            </div>
                            
                            <!-- Order Summary -->
                            <div class="mt-4 rounded-lg border p-4 divide-y divide-slate-200 dark:divide-slate-700">
                                <div class="pb-3 space-y-1">
                                    <div class="flex justify-between">
                                        <p class="text-sm text-slate-500 dark:text-slate-400">Subtotal</p>
                                        <p class="text-sm font-medium">{{ formatPrice(order.subtotal) }}</p>
                                    </div>
                                    <div class="flex justify-between">
                                        <p class="text-sm text-slate-500 dark:text-slate-400">Biaya Admin</p>
                                        <p class="text-sm font-medium">{{ formatPrice(order.admin_fee) }}</p>
                                    </div>
                                    <div class="flex justify-between">
                                        <p class="text-sm text-slate-500 dark:text-slate-400">Diskon</p>
                                        <p class="text-sm font-medium text-green-600 dark:text-green-400">-{{ formatPrice(order.discount) }}</p>
                                    </div>
                                </div>
                                <div class="pt-3 flex justify-between">
                                    <p class="text-sm font-medium text-slate-700 dark:text-slate-300">Total</p>
                                    <p class="text-sm font-bold text-primary-600 dark:text-primary-400">{{ formatPrice(order.total_amount) }}</p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
            

        </div>
    </AppLayout>
</template> 