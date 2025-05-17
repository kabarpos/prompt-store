<template>
  <Head :title="`Pesanan #${order.order_number}`" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6">
      <!-- Header dengan judul dan tombol aksi -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h1 class="text-2xl font-bold text-secondary-900 dark:text-white">Detail Pesanan #{{ order.order_number }}</h1>
        <div class="flex items-center gap-2">
          <Link :href="route('admin.orders.index')" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
            <Button variant="outline" size="sm" class="h-9">
              <ArrowLeftIcon class="h-4 w-4 mr-2" />
              Kembali
            </Button>
          </Link>
          <Link :href="route('admin.orders.pdf', order.id)" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
            <Button variant="outline" size="sm" class="h-9">
              <FileTextIcon class="h-4 w-4 mr-2" />
              Export PDF
            </Button>
          </Link>
        </div>
      </div>

      <!-- Status and Actions Panel -->
      <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm p-6 mb-6 border border-slate-200 dark:border-slate-700">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
          <div>
            <p class="text-sm text-gray-500 dark:text-slate-400 mb-1">Status Pesanan</p>
            <Badge 
              variant="outline" 
              class="text-sm px-3 py-1"
              :class="{
                'border-yellow-400 text-yellow-600 bg-yellow-50 dark:bg-yellow-900/30 dark:text-yellow-300 dark:border-yellow-700': order.status === 'pending',
                'border-blue-400 text-blue-600 bg-blue-50 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-700': order.status === 'processing',
                'border-purple-400 text-purple-600 bg-purple-50 dark:bg-purple-900/30 dark:text-purple-300 dark:border-purple-700': order.status === 'review',
                'border-green-400 text-green-600 bg-green-50 dark:bg-green-900/30 dark:text-green-300 dark:border-green-700': order.status === 'completed',
                'border-red-400 text-red-600 bg-red-50 dark:bg-red-900/30 dark:text-red-300 dark:border-red-700': order.status === 'cancelled'
              }"
            >
              {{ getStatusLabel(order.status) }}
            </Badge>
          </div>
          
          <div class="flex items-center gap-2">
            <div class="flex items-center gap-2">
              <DropdownMenu>
                <DropdownMenuTrigger asChild>
                  <Button variant="outline" class="h-9">
                    {{ getStatusLabel(form.status) }}
                    <ChevronDownIcon class="ml-2 h-4 w-4" />
                  </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end" class="w-[200px]">
                  <DropdownMenuItem v-for="(label, status) in statuses" :key="status" @click="form.status = status" :class="{'bg-primary/10': form.status === status}">
                    {{ label }}
                  </DropdownMenuItem>
                </DropdownMenuContent>
              </DropdownMenu>
              
              <Button 
                @click="updateStatus"
                :disabled="isUpdating || form.status === order.status"
                class="h-9"
              >
                <RefreshCwIcon v-if="isUpdating" class="h-4 w-4 mr-2 animate-spin" />
                <CheckIcon v-else class="h-4 w-4 mr-2" />
                Update Status
              </Button>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Order Details -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Order Information -->
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm p-6 border border-slate-200 dark:border-slate-700">
          <h3 class="text-lg font-medium mb-4 text-slate-900 dark:text-white">Informasi Pesanan</h3>
          <div class="space-y-3">
            <div>
              <p class="text-sm text-slate-500 dark:text-slate-400">Nomor Pesanan</p>
              <p class="font-medium text-slate-900 dark:text-white">{{ order.order_number }}</p>
            </div>
            <div>
              <p class="text-sm text-slate-500 dark:text-slate-400">Tanggal Pesanan</p>
              <p class="font-medium text-slate-900 dark:text-white">{{ formatDate(order.created_at) }}</p>
            </div>
            <div>
              <p class="text-sm text-slate-500 dark:text-slate-400">Status</p>
              <Badge 
                variant="outline" 
                :class="{
                  'border-yellow-400 text-yellow-600 bg-yellow-50 dark:bg-yellow-900/30 dark:text-yellow-300 dark:border-yellow-700': order.status === 'pending',
                  'border-blue-400 text-blue-600 bg-blue-50 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-700': order.status === 'processing',
                  'border-purple-400 text-purple-600 bg-purple-50 dark:bg-purple-900/30 dark:text-purple-300 dark:border-purple-700': order.status === 'review',
                  'border-green-400 text-green-600 bg-green-50 dark:bg-green-900/30 dark:text-green-300 dark:border-green-700': order.status === 'completed',
                  'border-red-400 text-red-600 bg-red-50 dark:bg-red-900/30 dark:text-red-300 dark:border-red-700': order.status === 'cancelled'
                }"
              >
                {{ getStatusLabel(order.status) }}
              </Badge>
            </div>
          </div>
        </div>
        
        <!-- Customer Information -->
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm p-6 border border-slate-200 dark:border-slate-700">
          <h3 class="text-lg font-medium mb-4 text-slate-900 dark:text-white">Informasi Pelanggan</h3>
          <div class="space-y-3">
            <div>
              <p class="text-sm text-gray-500 dark:text-slate-400">Nama</p>
              <p class="font-medium text-slate-900 dark:text-white">{{ order.customer_name }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-500 dark:text-slate-400">Nomor WhatsApp</p>
              <div class="flex items-center gap-2">
                <p class="font-medium text-slate-900 dark:text-white">{{ order.customer_phone }}</p>
                <a 
                  :href="`https://wa.me/${formatWhatsAppNumber(order.customer_phone)}`" 
                  target="_blank" 
                  class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300"
                >
                  <Button variant="ghost" size="sm" class="h-7 px-2">
                    <MessageCircleIcon class="h-4 w-4" />
                  </Button>
                </a>
              </div>
            </div>
            <div v-if="order.customer_email">
              <p class="text-sm text-gray-500 dark:text-slate-400">Email</p>
              <div class="flex items-center gap-2">
                <p class="font-medium text-slate-900 dark:text-white">{{ order.customer_email }}</p>
                <a 
                  :href="`mailto:${order.customer_email}`" 
                  class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                >
                  <Button variant="ghost" size="sm" class="h-7 px-2">
                    <MailIcon class="h-4 w-4" />
                  </Button>
                </a>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Payment Summary -->
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm p-6 border border-slate-200 dark:border-slate-700">
          <h3 class="text-lg font-medium mb-4 text-slate-900 dark:text-white">Ringkasan Pembayaran</h3>
          <div class="space-y-3 mb-4">
            <div class="flex justify-between">
              <p class="text-gray-600 dark:text-slate-400">Subtotal</p>
              <p class="font-medium text-slate-900 dark:text-white">{{ formatPrice(order.subtotal) }}</p>
            </div>
            <div class="flex justify-between">
              <p class="text-gray-600 dark:text-slate-400">Biaya Admin</p>
              <p class="font-medium text-slate-900 dark:text-white">{{ formatPrice(order.admin_fee) }}</p>
            </div>
            <div class="flex justify-between">
              <p class="text-gray-600 dark:text-slate-400">Diskon</p>
              <p class="font-medium text-green-600 dark:text-green-400">-{{ formatPrice(order.discount) }}</p>
            </div>
          </div>
          <div class="pt-3 border-t border-gray-200 dark:border-slate-700 flex justify-between">
            <p class="font-medium text-slate-900 dark:text-white">Total</p>
            <p class="text-lg font-bold text-primary-600 dark:text-primary-400">{{ formatPrice(order.total_amount) }}</p>
          </div>
        </div>
      </div>
      
      <!-- Order Items -->
      <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm overflow-hidden mb-6 border border-slate-200 dark:border-slate-700">
        <div class="p-6 border-b border-gray-200 dark:border-slate-700">
          <h3 class="text-lg font-medium text-slate-900 dark:text-white">Produk yang Dipesan</h3>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
            <thead class="bg-gray-50 dark:bg-slate-800/60">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wider">
                  Produk
                </th>
                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wider">
                  Harga
                </th>
                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wider">
                  Jumlah
                </th>
                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wider">
                  Subtotal
                </th>
              </tr>
            </thead>
            <tbody class="bg-white dark:bg-slate-800 divide-y divide-gray-200 dark:divide-slate-700">
              <tr v-for="item in order.items" :key="item.id">
                <td class="px-6 py-4">
                  <div class="flex items-center">
                    <div class="h-12 w-12 flex-shrink-0 overflow-hidden rounded-md border border-gray-200 dark:border-slate-700">
                      <img 
                        :src="`/storage/${item.product.featured_image}`" 
                        :alt="item.product.name" 
                        class="h-full w-full object-cover object-center"
                      />
                    </div>
                    <div class="ml-4">
                      <div class="text-sm font-medium text-gray-900 dark:text-white">{{ item.product.name }}</div>
                      <div v-if="item.product.category" class="text-xs text-gray-500 dark:text-slate-400">{{ item.product.category.name }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500 dark:text-slate-400">
                  {{ formatPrice(item.price) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500 dark:text-slate-400">
                  {{ item.quantity }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-gray-900 dark:text-white">
                  {{ formatPrice(item.subtotal) }}
                </td>
              </tr>
            </tbody>
            <tfoot class="bg-gray-50 dark:bg-slate-800/60">
              <tr>
                <td colspan="3" class="px-6 py-4 text-right text-sm font-medium text-gray-900 dark:text-white">Total</td>
                <td class="px-6 py-4 text-right text-sm font-bold text-primary-600 dark:text-primary-400">{{ formatPrice(order.total_amount) }}</td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
      
      <!-- Notes -->
      <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm p-6 border border-slate-200 dark:border-slate-700">
        <h3 class="text-lg font-medium mb-4 text-slate-900 dark:text-white">Catatan</h3>
        <div class="mb-4">
          <p v-if="order.notes" class="text-gray-700 dark:text-slate-300 whitespace-pre-line">{{ order.notes }}</p>
          <p v-else class="text-gray-500 dark:text-slate-400 italic">Tidak ada catatan dari pelanggan</p>
        </div>
        
        <!-- Admin Notes -->
        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-slate-700">
          <h4 class="text-md font-medium mb-3 text-slate-900 dark:text-white">Catatan Admin</h4>
          <div v-if="order.admin_notes" class="mb-4 bg-blue-50 dark:bg-blue-900/10 p-4 rounded-md">
            <p class="text-gray-700 dark:text-slate-300 whitespace-pre-line">{{ order.admin_notes }}</p>
          </div>
        </div>
        
        <!-- Admin Notes Form -->
        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-slate-700">
          <h4 class="text-md font-medium mb-3 text-slate-900 dark:text-white">Update Catatan Admin</h4>
          <form @submit.prevent="updateNotes" class="flex flex-col gap-4">
            <textarea 
              v-model="form.admin_notes" 
              rows="3" 
              class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100"
              placeholder="Tambahkan catatan internal untuk pesanan ini"
            ></textarea>
            <div class="flex justify-end">
              <Button 
                type="submit" 
                :disabled="isUpdatingNotes"
                class="h-9"
              >
                <RefreshCwIcon v-if="isUpdatingNotes" class="h-4 w-4 mr-2 animate-spin" />
                <SaveIcon v-else class="h-4 w-4 mr-2" />
                Simpan Catatan
              </Button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { 
  ArrowLeftIcon, 
  FileTextIcon, 
  CheckIcon, 
  RefreshCwIcon, 
  MessageCircleIcon, 
  MailIcon,
  SaveIcon,
  ChevronDownIcon,
  FileText,
  Files
} from 'lucide-vue-next';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { toast } from 'vue-sonner';

const props = defineProps({
  order: Object,
  statuses: Object
});

// Breadcrumbs untuk navigasi
const breadcrumbs = [
  {
    title: 'Admin',
    href: route('admin.dashboard'),
  },
  {
    title: 'Pesanan',
    href: route('admin.orders.index'),
  },
  {
    title: `#${props.order.order_number}`,
    href: route('admin.orders.show', props.order.id),
  }
];

// State
const isUpdating = ref(false);
const isUpdatingNotes = ref(false);
const form = ref({
  status: props.order.status,
  admin_notes: props.order.admin_notes
});

// Methods
const formatPrice = (price) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(price);
};

const formatDate = (dateString) => {
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  }).format(date);
};

const formatWhatsAppNumber = (phone) => {
  // Remove any non-digit character
  let digits = phone.replace(/\D/g, '');
  
  // Make sure it starts with country code
  if (digits.startsWith('0')) {
    digits = '62' + digits.substring(1);
  } else if (!digits.startsWith('62')) {
    digits = '62' + digits;
  }
  
  return digits;
};

const getStatusLabel = (status) => {
  return props.statuses[status] || status;
};

const updateStatus = () => {
  if (isUpdating.value) return;
  if (form.value.status === props.order.status) return;
  
  isUpdating.value = true;
  
  router.patch(route('admin.orders.status.update', props.order.id), {
    status: form.value.status
  }, {
    onSuccess: () => {
      toast.success('Status pesanan berhasil diperbarui');
    },
    onError: (errors) => {
      toast.error('Gagal memperbarui status pesanan');
    },
    onFinish: () => {
      isUpdating.value = false;
    }
  });
};

const updateNotes = () => {
  if (isUpdatingNotes.value) return;
  
  isUpdatingNotes.value = true;
  
  router.patch(route('admin.orders.status.update', props.order.id), {
    status: props.order.status,
    admin_notes: form.value.admin_notes
  }, {
    onSuccess: () => {
      toast.success('Catatan pesanan berhasil diperbarui');
    },
    onError: (errors) => {
      toast.error('Gagal memperbarui catatan pesanan');
    },
    onFinish: () => {
      isUpdatingNotes.value = false;
    }
  });
};
</script> 