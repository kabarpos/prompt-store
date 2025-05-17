<script setup lang="ts">
import { defineComponent as _defineComponent } from "vue";
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle, CardDescription, CardFooter } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { 
    ShoppingBag, Package, Truck, CheckCircle, 
    User, Phone, Mail, MapPin, Clock, ChevronRight,
    Calendar, CreditCard, AlertCircle, FileText,
    Eye
} from 'lucide-vue-next';

interface OrderSummary {
    pending: number;
    processing: number;
    review: number;
    completed: number;
    cancelled: number;
}

interface Order {
    id: number;
    order_number: string;
    total_amount: number;
    status: string;
    created_at: string;
}

interface User {
    id: number;
    name: string;
    email: string;
    whatsapp: string;
}

interface DashboardProps {
    recentOrders: Order[];
    orderSummary: OrderSummary;
    user: User;
}

const props = defineProps<DashboardProps>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

// Format date
const formatDate = (dateString: string): string => {
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    }).format(date);
};

// Format currency
const formatCurrency = (amount: number): string => {
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
        'review': 'Review',
        'completed': 'Selesai',
        'cancelled': 'Dibatalkan'
    };

    return labels[status] || status;
};

// Get status color
const getStatusColor = (status: string): string => {
    const colors: Record<string, string> = {
        'pending': 'text-warning-500',
        'processing': 'text-primary-500',
        'review': 'text-purple-500',
        'completed': 'text-success-500',
        'cancelled': 'text-danger-500'
    };

    return colors[status] || 'text-slate-500';
};

// Get status icon
const getStatusIcon = (status: string) => {
    const icons: Record<string, any> = {
        'pending': Clock,
        'processing': Package,
        'review': Eye,
        'completed': CheckCircle,
        'cancelled': AlertCircle
    };

    return icons[status] || Clock;
};

// Get status badge color
const getStatusBadgeColor = (status: string): string => {
    const colors: Record<string, string> = {
        'pending': 'bg-warning-100 text-warning-700 dark:bg-warning-900/30 dark:text-warning-400',
        'processing': 'bg-primary-100 text-primary-700 dark:bg-primary-900/30 dark:text-primary-400',
        'review': 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400',
        'completed': 'bg-success-100 text-success-700 dark:bg-success-900/30 dark:text-success-400',
        'cancelled': 'bg-danger-100 text-danger-700 dark:bg-danger-900/30 dark:text-danger-400'
    };

    return colors[status] || 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300';
};
</script>

<template>
    <Head title="Dashboard Member" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-4 md:p-6">
            <!-- Welcome & User Info -->
            <div class="grid md:grid-cols-3 gap-6">
                <!-- Welcome Card -->
                <div class="md:col-span-2">
                    <Card class="h-full">
                        <CardHeader>
                            <CardTitle class="text-2xl font-bold">Selamat Datang, {{ user.name }}!</CardTitle>
                            <CardDescription class="text-base">
                                Ini adalah dashboard pribadi Anda. Di sini Anda dapat memantau pesanan, melihat status pembayaran, dan mengelola akun.
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                                <div class="flex flex-col items-center justify-center p-4 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-md transition-shadow duration-300">
                                    <div class="bg-warning-100 dark:bg-warning-900/30 p-3 rounded-full mb-3">
                                        <Clock class="h-5 w-5 text-warning-500 dark:text-warning-400" />
                                    </div>
                                    <span class="text-sm text-slate-500 dark:text-slate-400 mb-1">Pending</span>
                                    <span class="text-2xl font-bold text-slate-900 dark:text-slate-50">{{ orderSummary.pending }}</span>
                                </div>

                                <div class="flex flex-col items-center justify-center p-4 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-md transition-shadow duration-300">
                                    <div class="bg-primary-100 dark:bg-primary-900/30 p-3 rounded-full mb-3">
                                        <Package class="h-5 w-5 text-primary-500 dark:text-primary-400" />
                                    </div>
                                    <span class="text-sm text-slate-500 dark:text-slate-400 mb-1">Diproses</span>
                                    <span class="text-2xl font-bold text-slate-900 dark:text-slate-50">{{ orderSummary.processing }}</span>
                                </div>

                                <div class="flex flex-col items-center justify-center p-4 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-md transition-shadow duration-300">
                                    <div class="bg-purple-100 dark:bg-purple-900/30 p-3 rounded-full mb-3">
                                        <Eye class="h-5 w-5 text-purple-500 dark:text-purple-400" />
                                    </div>
                                    <span class="text-sm text-slate-500 dark:text-slate-400 mb-1">Review</span>
                                    <span class="text-2xl font-bold text-slate-900 dark:text-slate-50">{{ orderSummary.review }}</span>
                                </div>

                                <div class="flex flex-col items-center justify-center p-4 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-md transition-shadow duration-300">
                                    <div class="bg-success-100 dark:bg-success-900/30 p-3 rounded-full mb-3">
                                        <CheckCircle class="h-5 w-5 text-success-500 dark:text-success-400" />
                                    </div>
                                    <span class="text-sm text-slate-500 dark:text-slate-400 mb-1">Selesai</span>
                                    <span class="text-2xl font-bold text-slate-900 dark:text-slate-50">{{ orderSummary.completed }}</span>
                                </div>
                                
                                <div class="flex flex-col items-center justify-center p-4 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-md transition-shadow duration-300">
                                    <div class="bg-danger-100 dark:bg-danger-900/30 p-3 rounded-full mb-3">
                                        <AlertCircle class="h-5 w-5 text-danger-500 dark:text-danger-400" />
                                    </div>
                                    <span class="text-sm text-slate-500 dark:text-slate-400 mb-1">Dibatalkan</span>
                                    <span class="text-2xl font-bold text-slate-900 dark:text-slate-50">{{ orderSummary.cancelled }}</span>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                                <Link :href="route('orders.index')" class="block w-full">
                                    <Button variant="default" class="w-full group">
                                        <FileText class="h-4 w-4 mr-2" />
                                        Lihat Semua Pesanan
                                        <ChevronRight class="h-4 w-4 ml-2 opacity-70 group-hover:translate-x-0.5 transition-transform" />
                                    </Button>
                                </Link>
                                <Link :href="route('products.index')" class="block w-full">
                                    <Button variant="outline" class="w-full group">
                                        <ShoppingBag class="h-4 w-4 mr-2" />
                                        Belanja Produk Baru
                                        <ChevronRight class="h-4 w-4 ml-2 opacity-70 group-hover:translate-x-0.5 transition-transform" />
                                    </Button>
                                </Link>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- User Info Card -->
                <div>
                    <Card class="h-full">
                        <CardHeader class="pb-3">
                            <CardTitle class="flex items-center">
                                <User class="h-5 w-5 mr-2 text-primary-500" />
                                Informasi Akun
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-4">
                                <div class="flex items-start gap-3 p-3 rounded-lg bg-slate-50 dark:bg-slate-800/50">
                                    <User class="h-5 w-5 text-slate-400 mt-0.5" />
                                    <div>
                                        <p class="text-sm text-slate-500 dark:text-slate-400">Nama</p>
                                        <p class="font-medium">{{ user.name }}</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3 p-3 rounded-lg bg-slate-50 dark:bg-slate-800/50">
                                    <Phone class="h-5 w-5 text-slate-400 mt-0.5" />
                                    <div>
                                        <p class="text-sm text-slate-500 dark:text-slate-400">WhatsApp</p>
                                        <p class="font-medium">{{ user.whatsapp || '-' }}</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3 p-3 rounded-lg bg-slate-50 dark:bg-slate-800/50">
                                    <Mail class="h-5 w-5 text-slate-400 mt-0.5" />
                                    <div>
                                        <p class="text-sm text-slate-500 dark:text-slate-400">Email</p>
                                        <p class="font-medium">{{ user.email }}</p>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                        <CardFooter>
                            <Link :href="route('profile.edit')" class="block w-full">
                                <Button variant="outline" class="w-full text-sm group">
                                    Edit Profil
                                    <ChevronRight class="h-4 w-4 ml-2 opacity-70 group-hover:translate-x-0.5 transition-transform" />
                                </Button>
                            </Link>
                        </CardFooter>
                    </Card>
                </div>
            </div>

            <!-- Recent Orders -->
            <Card>
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <CardTitle class="flex items-center">
                            <ShoppingBag class="h-5 w-5 mr-2 text-primary-500" />
                            Pesanan Terbaru
                        </CardTitle>
                        <Link v-if="recentOrders && recentOrders.length > 0" :href="route('orders.index')" class="text-sm text-primary-600 hover:text-primary-700 flex items-center">
                            Lihat Semua
                            <ChevronRight class="h-4 w-4 ml-1" />
                        </Link>
                    </div>
                </CardHeader>
                <CardContent>
                    <div v-if="recentOrders && recentOrders.length > 0">
                        <div class="rounded-lg border border-slate-200 dark:border-slate-700 overflow-hidden">
                            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                                <thead class="bg-slate-50 dark:bg-slate-800">
                                    <tr>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">No. Pesanan</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tanggal</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-700">
                                    <tr v-for="order in recentOrders" :key="order.id" class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-slate-900 dark:text-white">{{ order.order_number }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
                                            <div class="flex items-center">
                                                <Calendar class="h-4 w-4 mr-1.5 text-slate-400" />
                                                {{ formatDate(order.created_at) }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-slate-900 dark:text-white">
                                            <div class="flex items-center">
                                                <CreditCard class="h-4 w-4 mr-1.5 text-slate-400" />
                                                {{ formatCurrency(order.total_amount) }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="getStatusBadgeColor(order.status)">
                                                <component :is="getStatusIcon(order.status)" class="h-3.5 w-3.5 mr-1" />
                                                {{ getStatusLabel(order.status) }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                            <Link :href="`/orders/${order.id}`" class="text-primary-600 hover:text-primary-900 dark:hover:text-primary-400 inline-flex items-center">
                                                Detail
                                                <ChevronRight class="h-4 w-4 ml-1 opacity-70" />
                                            </Link>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div v-else class="text-center py-10 px-4">
                        <div class="bg-slate-50 dark:bg-slate-800/50 rounded-full p-3 w-16 h-16 flex items-center justify-center mx-auto mb-4">
                            <ShoppingBag class="h-8 w-8 text-slate-400" />
                        </div>
                        <h3 class="text-lg font-medium text-slate-900 dark:text-slate-100 mb-2">Belum Ada Pesanan</h3>
                        <p class="text-slate-500 dark:text-slate-400 mb-6 max-w-md mx-auto">Anda belum memiliki pesanan. Mulailah berbelanja dan temukan produk yang Anda butuhkan.</p>
                        <Button variant="default" as-child>
                            <Link :href="route('products.index')" class="inline-flex items-center">
                                <ShoppingBag class="h-4 w-4 mr-2" />
                                Mulai Belanja
                            </Link>
                        </Button>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
