<template>
  <Head title="Admin Dashboard" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex flex-col gap-6 p-4 md:p-6 pb-12 bg-slate-50 dark:bg-slate-900 min-h-screen">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-slate-900 dark:text-slate-50">Dashboard Admin</h1>
          <p class="text-sm text-slate-500 dark:text-slate-400">Ringkasan data operasional sistem</p>
        </div>
        <div>
          <div class="text-sm text-slate-500 dark:text-slate-400">{{ today }}</div>
        </div>
      </div>

      <!-- Statistik Utama -->
      <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
        <!-- Total User -->
        <Card class="border-0 shadow-sm">
          <CardContent class="p-6">
            <div class="flex items-start justify-between">
              <div>
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Total User</p>
                <h3 class="text-2xl font-bold mt-2 text-slate-900 dark:text-slate-50">
                  {{ usersCount ? usersCount.toLocaleString('id-ID') : '-' }}
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                  <span v-if="usersTrend && usersTrend > 0" class="text-success-600 dark:text-success-400 font-medium">+{{ usersTrend }}%</span>
                  <span v-else-if="usersTrend && usersTrend < 0" class="text-danger-600 dark:text-danger-400 font-medium">{{ usersTrend }}%</span>
                  <span v-else class="text-slate-500 dark:text-slate-400 font-medium">0%</span>
                  <span class="ml-1">dari bulan lalu</span>
                </p>
              </div>
              <div class="bg-primary-100 dark:bg-primary-900/30 p-3 rounded-full">
                <UsersIcon class="h-5 w-5 text-primary-500 dark:text-primary-400" />
              </div>
            </div>
          </CardContent>
        </Card>

        <!-- Total Order -->
        <Card class="border-0 shadow-sm">
          <CardContent class="p-6">
            <div class="flex items-start justify-between">
              <div>
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Total Order</p>
                <h3 class="text-2xl font-bold mt-2 text-slate-900 dark:text-slate-50">
                  {{ ordersCount ? ordersCount.toLocaleString('id-ID') : '-' }}
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                  <span v-if="ordersTrend && ordersTrend > 0" class="text-success-600 dark:text-success-400 font-medium">+{{ ordersTrend }}%</span>
                  <span v-else-if="ordersTrend && ordersTrend < 0" class="text-danger-600 dark:text-danger-400 font-medium">{{ ordersTrend }}%</span>
                  <span v-else class="text-slate-500 dark:text-slate-400 font-medium">0%</span>
                  <span class="ml-1">dari bulan lalu</span>
                </p>
              </div>
              <div class="bg-success-100 dark:bg-success-900/30 p-3 rounded-full">
                <ShoppingBag class="h-5 w-5 text-success-500 dark:text-success-400" />
              </div>
            </div>
          </CardContent>
        </Card>

        <!-- Total Pendapatan -->
        <Card class="border-0 shadow-sm">
          <CardContent class="p-6">
            <div class="flex items-start justify-between">
              <div>
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Total Pendapatan</p>
                <h3 class="text-2xl font-bold mt-2 text-slate-900 dark:text-slate-50">
                  {{ totalIncome ? `Rp ${formatCurrency(totalIncome)}` : '-' }}
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                  <span v-if="incomeTrend && incomeTrend > 0" class="text-success-600 dark:text-success-400 font-medium">+{{ incomeTrend }}%</span>
                  <span v-else-if="incomeTrend && incomeTrend < 0" class="text-danger-600 dark:text-danger-400 font-medium">{{ incomeTrend }}%</span>
                  <span v-else class="text-slate-500 dark:text-slate-400 font-medium">0%</span>
                  <span class="ml-1">dari bulan lalu</span>
                </p>
              </div>
              <div class="bg-blue-100 dark:bg-blue-900/30 p-3 rounded-full">
                <CreditCard class="h-5 w-5 text-blue-500 dark:text-blue-400" />
              </div>
            </div>
          </CardContent>
        </Card>

        <!-- Total Pengeluaran -->
        <Card class="border-0 shadow-sm">
          <CardContent class="p-6">
            <div class="flex items-start justify-between">
              <div>
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Total Pengeluaran</p>
                <h3 class="text-2xl font-bold mt-2 text-slate-900 dark:text-slate-50">
                  {{ totalExpenses ? `Rp ${formatCurrency(totalExpenses)}` : '-' }}
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                  <span v-if="expensesTrend && expensesTrend > 0" class="text-danger-600 dark:text-danger-400 font-medium">+{{ expensesTrend }}%</span>
                  <span v-else-if="expensesTrend && expensesTrend < 0" class="text-success-600 dark:text-success-400 font-medium">{{ expensesTrend }}%</span>
                  <span v-else class="text-slate-500 dark:text-slate-400 font-medium">0%</span>
                  <span class="ml-1">dari bulan lalu</span>
                </p>
              </div>
              <div class="bg-danger-100 dark:bg-danger-900/30 p-3 rounded-full">
                <Wallet class="h-5 w-5 text-danger-500 dark:text-danger-400" />
              </div>
            </div>
          </CardContent>
        </Card>
      </div>

      <!-- Baris Kedua -->
      <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        <!-- Status Order -->
        <Card class="border-0 shadow-sm lg:col-span-2">
          <CardHeader class="pb-2">
            <CardTitle>Status Order</CardTitle>
            <CardDescription>Distribusi order berdasarkan status</CardDescription>
          </CardHeader>
          <CardContent>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
              <div class="flex flex-col items-center justify-center p-4 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                <div class="bg-warning-100 dark:bg-warning-900/30 p-2 rounded-full mb-2">
                  <ClockIcon class="h-4 w-4 text-warning-500 dark:text-warning-400" />
                </div>
                <span class="text-sm text-slate-500 dark:text-slate-400">Pending</span>
                <span class="text-2xl font-bold text-slate-900 dark:text-slate-50">{{ pendingOrders || 0 }}</span>
              </div>

              <div class="flex flex-col items-center justify-center p-4 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                <div class="bg-primary-100 dark:bg-primary-900/30 p-2 rounded-full mb-2">
                  <TimerIcon class="h-4 w-4 text-primary-500 dark:text-primary-400" />
                </div>
                <span class="text-sm text-slate-500 dark:text-slate-400">Diproses</span>
                <span class="text-2xl font-bold text-slate-900 dark:text-slate-50">{{ processingOrders || 0 }}</span>
              </div>

              <div class="flex flex-col items-center justify-center p-4 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                <div class="bg-blue-100 dark:bg-blue-900/30 p-2 rounded-full mb-2">
                  <EyeIcon class="h-4 w-4 text-blue-500 dark:text-blue-400" />
                </div>
                <span class="text-sm text-slate-500 dark:text-slate-400">Review</span>
                <span class="text-2xl font-bold text-slate-900 dark:text-slate-50">{{ reviewOrders || 0 }}</span>
              </div>

              <div class="flex flex-col items-center justify-center p-4 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                <div class="bg-success-100 dark:bg-success-900/30 p-2 rounded-full mb-2">
                  <CheckCircleIcon class="h-4 w-4 text-success-500 dark:text-success-400" />
                </div>
                <span class="text-sm text-slate-500 dark:text-slate-400">Selesai</span>
                <span class="text-2xl font-bold text-slate-900 dark:text-slate-50">{{ completedOrders || 0 }}</span>
              </div>

              <div class="flex flex-col items-center justify-center p-4 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 md:col-span-4 lg:col-span-2 xl:col-span-1">
                <div class="bg-danger-100 dark:bg-danger-900/30 p-2 rounded-full mb-2">
                  <XCircleIcon class="h-4 w-4 text-danger-500 dark:text-danger-400" />
                </div>
                <span class="text-sm text-slate-500 dark:text-slate-400">Dibatalkan</span>
                <span class="text-2xl font-bold text-slate-900 dark:text-slate-50">{{ cancelledOrders || 0 }}</span>
              </div>
            </div>

            <div v-if="chartData && chartData.length > 0" class="h-64 mt-6">
              <!-- Chart dapat diimplementasikan di sini jika ada library chart -->
              <div class="flex items-end h-full w-full gap-2 border-b border-slate-200 dark:border-slate-700 pt-6">
                <div v-for="(item, i) in chartData" :key="i" class="h-full flex-1 flex flex-col justify-end items-center group relative">
                  <div class="absolute bottom-full mb-2 opacity-0 group-hover:opacity-100 transition-opacity bg-slate-800 dark:bg-slate-700 text-white px-2 py-1 rounded text-xs">
                    {{ item.label }}: {{ item.value }}
                  </div>
                  <div :class="`w-full rounded-t ${getChartBarColor(item.status)}`" :style="{ height: `${getChartPercentage(item.value)}%` }"></div>
                  <div class="text-xs text-slate-500 dark:text-slate-400 mt-2">{{ item.status }}</div>
                </div>
              </div>
            </div>
            <div v-else class="h-64 flex items-center justify-center text-slate-500 dark:text-slate-400">
              Tidak ada data tersedia
            </div>
          </CardContent>
        </Card>

        <!-- Pesanan Terbaru -->
        <Card class="border-0 shadow-sm">
          <CardHeader class="pb-2">
            <CardTitle>Pesanan Terbaru</CardTitle>
            <CardDescription>Order terbaru yang masuk ke sistem</CardDescription>
          </CardHeader>
          <CardContent>
            <div v-if="recentOrders && recentOrders.length > 0" class="space-y-4">
              <div v-for="(order, i) in recentOrders" :key="i" class="flex items-start justify-between p-3 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                <div class="flex gap-3">
                  <div :class="`flex-shrink-0 w-2 h-full rounded-full ${getStatusColor(order.status)}`"></div>
                  <div>
                    <p class="text-sm font-medium text-slate-900 dark:text-slate-50">{{ order.invoice }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ order.customer }}</p>
                    <p class="text-xs font-medium mt-1" :class="getStatusTextColor(order.status)">{{ getStatusLabel(order.status) }}</p>
                  </div>
                </div>
                <div class="text-right">
                  <p class="text-sm font-medium text-slate-900 dark:text-slate-50">Rp {{ formatCurrency(order.amount) }}</p>
                  <p class="text-xs text-slate-500 dark:text-slate-400">{{ formatDate(order.created_at) }}</p>
                </div>
              </div>
            </div>
            <div v-else class="h-48 flex items-center justify-center text-slate-500 dark:text-slate-400">
              Belum ada pesanan terbaru
            </div>
          </CardContent>
          <CardFooter>
            <Link :href="route('admin.orders.index')" class="w-full">
              <Button variant="outline" size="sm" class="w-full">
                Lihat Semua Pesanan
              </Button>
            </Link>
          </CardFooter>
        </Card>
      </div>

      <!-- Baris Ketiga -->
      <div class="grid gap-6 md:grid-cols-3">
        <!-- Produk Terlaris -->
        <Card class="border-0 shadow-sm">
          <CardHeader class="pb-2">
            <CardTitle>Produk Terlaris</CardTitle>
            <CardDescription>Produk dengan penjualan tertinggi</CardDescription>
          </CardHeader>
          <CardContent>
            <div v-if="topProducts && topProducts.length > 0" class="space-y-3">
              <div v-for="(product, i) in topProducts" :key="i" class="flex items-center justify-between p-3 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                <div class="flex items-center gap-3">
                  <div class="flex items-center justify-center h-8 w-8 rounded-full bg-primary-100 dark:bg-primary-900/30 text-primary-500 dark:text-primary-400 font-bold text-xs">
                    {{ i + 1 }}
                  </div>
                  <div>
                    <p class="text-sm font-medium text-slate-900 dark:text-slate-50">{{ product.name }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ product.sold }} terjual</p>
                  </div>
                </div>
                <div class="text-sm font-medium text-slate-900 dark:text-slate-50">
                  Rp {{ formatCurrency(product.price) }}
                </div>
              </div>
            </div>
            <div v-else class="h-48 flex items-center justify-center text-slate-500 dark:text-slate-400">
              Belum ada data produk
            </div>
          </CardContent>
        </Card>

        <!-- Aktivitas Terbaru -->
        <Card class="border-0 shadow-sm md:col-span-2">
          <CardHeader class="pb-2">
            <CardTitle>Aktivitas Terbaru</CardTitle>
            <CardDescription>Aktivitas terbaru dalam sistem</CardDescription>
          </CardHeader>
          <CardContent>
            <div v-if="activities && activities.length > 0" class="space-y-4">
              <div v-for="(activity, i) in activities" :key="i" class="flex items-start gap-4 p-3 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-primary-100 dark:bg-primary-900/30 text-primary-500 dark:text-primary-400">
                  <UserIcon v-if="activity.type === 'user'" class="h-5 w-5" />
                  <ShoppingBag v-else-if="activity.type === 'order'" class="h-5 w-5" />
                  <Package v-else-if="activity.type === 'product'" class="h-5 w-5" />
                  <Settings v-else class="h-5 w-5" />
                </div>
                <div class="flex-1">
                  <p class="text-sm text-slate-900 dark:text-slate-50">
                    <span class="font-medium">{{ activity.user }}</span>
                    {{ activity.action }}
                  </p>
                  <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ formatDate(activity.created_at) }}</p>
                </div>
              </div>
            </div>
            <div v-else class="h-48 flex items-center justify-center text-slate-500 dark:text-slate-400">
              Belum ada aktivitas terbaru
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle, CardFooter, CardDescription } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { 
  BarChart3, Users as UsersIcon, ShoppingBag, Wallet, CreditCard, TrendingUp, TrendingDown,
  Clock as ClockIcon, Timer as TimerIcon, Truck as TruckIcon, CheckCircle as CheckCircleIcon,
  Package, User as UserIcon, Settings, Eye as EyeIcon, XCircle as XCircleIcon
} from 'lucide-vue-next';
import { computed, ref, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';

// Definisikan tipe data
interface ActivityItem {
  id: number;
  user: string;
  type: 'user' | 'order' | 'product' | 'setting';
  action: string;
  created_at: string;
}

interface OrderItem {
  id: number;
  invoice: string;
  customer: string;
  amount: number;
  status: string;
  created_at: string;
}

interface ProductItem {
  id: number;
  name: string;
  sold: number;
  price: number;
}

interface ChartItem {
  status: string;
  label: string;
  value: number;
}

interface DashboardProps {
  title?: string;
  usersCount?: number | null;
  usersTrend?: number | null;
  ordersCount?: number | null;
  ordersTrend?: number | null;
  totalIncome?: number | null;
  incomeTrend?: number | null;
  totalExpenses?: number | null;
  expensesTrend?: number | null;
  pendingOrders?: number | null;
  processingOrders?: number | null;
  reviewOrders?: number | null;
  shippingOrders?: number | null;
  completedOrders?: number | null;
  cancelledOrders?: number | null;
  chartData?: ChartItem[] | null;
  recentOrders?: OrderItem[] | null;
  topProducts?: ProductItem[] | null;
  activities?: ActivityItem[] | null;
}

// Data dashboard
const dashboardData = ref({
  usersCount: 0,
  usersTrend: 0,
  ordersCount: 0,
  ordersTrend: 0,
  totalIncome: 0,
  incomeTrend: 0,
  totalExpenses: 0,
  expensesTrend: 0,
  pendingOrders: 0,
  processingOrders: 0,
  reviewOrders: 0,
  completedOrders: 0,
  cancelledOrders: 0,
  chartData: [],
  recentOrders: [],
  topProducts: [],
  activities: []
});

// Mengambil data dari props jika tersedia
const props = defineProps<DashboardProps>();

// Ekstrak properties untuk access
const usersCount = computed(() => props.usersCount || dashboardData.value.usersCount);
const usersTrend = computed(() => props.usersTrend || dashboardData.value.usersTrend);
const ordersCount = computed(() => props.ordersCount || dashboardData.value.ordersCount);
const ordersTrend = computed(() => props.ordersTrend || dashboardData.value.ordersTrend);
const totalIncome = computed(() => props.totalIncome || dashboardData.value.totalIncome);
const incomeTrend = computed(() => props.incomeTrend || dashboardData.value.incomeTrend);
const totalExpenses = computed(() => props.totalExpenses || dashboardData.value.totalExpenses);
const expensesTrend = computed(() => props.expensesTrend || dashboardData.value.expensesTrend);
const pendingOrders = computed(() => props.pendingOrders || dashboardData.value.pendingOrders);
const processingOrders = computed(() => props.processingOrders || dashboardData.value.processingOrders);
const reviewOrders = computed(() => props.reviewOrders || dashboardData.value.reviewOrders);
const completedOrders = computed(() => props.completedOrders || dashboardData.value.completedOrders);
const cancelledOrders = computed(() => props.cancelledOrders || dashboardData.value.cancelledOrders);
const chartData = computed(() => props.chartData || dashboardData.value.chartData);
const recentOrders = computed(() => props.recentOrders || dashboardData.value.recentOrders);
const topProducts = computed(() => props.topProducts || dashboardData.value.topProducts);
const activities = computed(() => props.activities || dashboardData.value.activities);

// Ambil data dashboard
onMounted(async () => {
  try {
    const response = await axios.get(route('admin.dashboard.data'));
    if (response.data) {
      dashboardData.value = response.data;
    }
  } catch (error) {
    console.error('Gagal mengambil data dashboard:', error);
  }
});

// Breadcrumbs untuk navigasi
const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Admin',
    href: route('admin.dashboard'),
  },
];

// Tanggal hari ini
const today = new Date().toLocaleDateString('id-ID', {
  weekday: 'long',
  year: 'numeric',
  month: 'long',
  day: 'numeric'
});

// Fungsi pemformatan
const formatCurrency = (value: number): string => {
  return new Intl.NumberFormat('id-ID').format(value);
};

const formatDate = (dateString: string): string => {
  const date = new Date(dateString);
  const now = new Date();
  const diffTime = Math.abs(now.getTime() - date.getTime());
  const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
  
  if (diffDays === 0) {
    const diffHours = Math.floor(diffTime / (1000 * 60 * 60));
    
    if (diffHours === 0) {
      const diffMinutes = Math.floor(diffTime / (1000 * 60));
      return `${diffMinutes} menit yang lalu`;
    }
    
    return `${diffHours} jam yang lalu`;
  } else if (diffDays === 1) {
    return 'Kemarin';
  } else {
    return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
  }
};

// Helper untuk chart
const getChartPercentage = (value: number): number => {
  if (!chartData.value || chartData.value.length === 0) return 0;
  
  const maxValue = Math.max(...chartData.value.map(item => item.value));
  if (maxValue === 0) return 0;
  
  return (value / maxValue) * 100;
};

const getChartBarColor = (status: string): string => {
  switch (status.toLowerCase()) {
    case 'pending':
      return 'bg-warning-500 dark:bg-warning-600';
    case 'processing':
      return 'bg-primary-500 dark:bg-primary-600';
    case 'review':
      return 'bg-blue-500 dark:bg-blue-600';
    case 'completed':
      return 'bg-success-500 dark:bg-success-600';
    case 'cancelled':
      return 'bg-danger-500 dark:bg-danger-600';
    default:
      return 'bg-slate-500 dark:bg-slate-600';
  }
};

const getStatusColor = (status: string): string => {
  switch (status.toLowerCase()) {
    case 'pending':
      return 'bg-warning-500 dark:bg-warning-600';
    case 'processing':
      return 'bg-primary-500 dark:bg-primary-600';
    case 'review':
      return 'bg-blue-500 dark:bg-blue-600';
    case 'completed':
      return 'bg-success-500 dark:bg-success-600';
    case 'cancelled':
      return 'bg-danger-500 dark:bg-danger-600';
    default:
      return 'bg-slate-500 dark:bg-slate-600';
  }
};

const getStatusTextColor = (status: string): string => {
  switch (status.toLowerCase()) {
    case 'pending':
      return 'text-warning-600 dark:text-warning-400';
    case 'processing':
      return 'text-primary-600 dark:text-primary-400';
    case 'review':
      return 'text-blue-600 dark:text-blue-400';
    case 'completed':
      return 'text-success-600 dark:text-success-400';
    case 'cancelled':
      return 'text-danger-600 dark:text-danger-400';
    default:
      return 'text-slate-600 dark:text-slate-400';
  }
};

const getStatusLabel = (status: string): string => {
  switch (status.toLowerCase()) {
    case 'pending':
      return 'Menunggu Pembayaran';
    case 'processing':
      return 'Sedang Diproses';
    case 'review':
      return 'Dalam Review';
    case 'completed':
      return 'Selesai';
    case 'cancelled':
      return 'Dibatalkan';
    default:
      return status;
  }
};
</script> 