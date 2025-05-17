<template>
  <Head title="Edit Pengeluaran" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6">
      <!-- Header dengan judul dan tombol kembali -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h1 class="text-2xl font-bold text-secondary-900 dark:text-white">Edit Pengeluaran</h1>
        <Link :href="route('admin.expenses.index')" class="cursor-pointer">
          <Button variant="outline" class="flex items-center gap-1.5 w-full sm:w-auto cursor-pointer">
            <ArrowLeft class="h-4 w-4" />
            Kembali
          </Button>
        </Link>
      </div>

      <div class="bg-white dark:bg-slate-800 text-secondary-900 dark:text-white rounded-xl shadow border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="p-6 border-b border-slate-200 dark:border-slate-700">
          <div>
            <h2 class="text-lg font-medium text-secondary-900 dark:text-white">Form Edit Pengeluaran</h2>
            <p class="text-secondary-600 dark:text-secondary-400 mt-1">Edit data pengeluaran Anda</p>
          </div>
        </div>
        <div class="p-6">
          <form @submit.prevent="submit" class="space-y-6">
            <!-- Grid 2 columns untuk desktop dan tablet, 1 column untuk mobile -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Kolom Kiri -->
              <div class="space-y-6">
                <!-- Nama Pengeluaran -->
                <div class="space-y-2">
                  <Label for="name">Nama Pengeluaran <span class="text-red-500">*</span></Label>
                  <Input 
                    id="name" 
                    v-model="form.name" 
                    :class="{ 'border-red-500': form.errors.name }"
                    placeholder="Masukkan nama pengeluaran" 
                  />
                  <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</div>
                </div>

                <!-- Kategori -->
                <div class="space-y-2">
                  <Label for="expense_category_id">Kategori <span class="text-red-500">*</span></Label>
                  <div class="relative custom-select-container" :class="{ 'active': isCategorySelectOpen }">
                    <div 
                      @click="toggleCategorySelect" 
                      class="custom-select-trigger flex w-full items-center justify-between gap-2 rounded-md border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 px-3 py-2 text-sm shadow-sm hover:border-slate-300 dark:hover:border-slate-600 focus:outline-none focus:ring-0 disabled:cursor-not-allowed disabled:opacity-50 cursor-pointer h-9"
                    >
                      <span>{{ selectedCategoryLabel }}</span>
                      <ChevronDownIcon class="h-4 w-4 opacity-50 transition-transform" :class="{ 'rotate-180': isCategorySelectOpen }" />
                    </div>
                    
                    <div 
                      v-if="isCategorySelectOpen" 
                      class="custom-select-dropdown bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-md shadow-lg mt-1 overflow-hidden z-50"
                    >
                      <div 
                        class="custom-select-option py-2 px-3 text-slate-900 dark:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-700 cursor-pointer text-sm"
                        @click="selectCategory(null)"
                        :class="{ 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300 font-medium': !form.expense_category_id }"
                      >
                        Pilih Kategori
                      </div>
                      <div 
                        v-for="category in categories" 
                        :key="category.id"
                        @click="selectCategory(category.id)"
                        class="custom-select-option py-2 px-3 text-slate-900 dark:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-700 cursor-pointer text-sm"
                        :class="{ 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300 font-medium': Number(form.expense_category_id) === category.id }"
                      >
                        {{ category.name }}
                      </div>
                    </div>
                  </div>
                  <div v-if="form.errors.expense_category_id" class="text-red-500 text-sm mt-1">{{ form.errors.expense_category_id }}</div>
                </div>

                <!-- Jumlah -->
                <div class="space-y-2">
                  <Label for="amount">Jumlah (Rp) <span class="text-red-500">*</span></Label>
                  <Input 
                    id="amount" 
                    v-model="form.amount" 
                    type="number"
                    step="0.01"
                    :class="{ 'border-red-500': form.errors.amount }"
                    placeholder="Masukkan jumlah pengeluaran" 
                  />
                  <div v-if="form.errors.amount" class="text-red-500 text-sm mt-1">{{ form.errors.amount }}</div>
                </div>

                <!-- Order -->
                <div class="space-y-2">
                  <Label for="order_id">Terkait Pesanan</Label>
                  <div class="relative custom-select-container" :class="{ 'active': isOrderSelectOpen }">
                    <div 
                      @click="toggleOrderSelect" 
                      class="custom-select-trigger flex w-full items-center justify-between gap-2 rounded-md border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 px-3 py-2 text-sm shadow-sm hover:border-slate-300 dark:hover:border-slate-600 focus:outline-none focus:ring-0 disabled:cursor-not-allowed disabled:opacity-50 cursor-pointer h-9"
                    >
                      <span>{{ selectedOrderLabel }}</span>
                      <ChevronDownIcon class="h-4 w-4 opacity-50 transition-transform" :class="{ 'rotate-180': isOrderSelectOpen }" />
                    </div>
                    
                    <div 
                      v-if="isOrderSelectOpen" 
                      class="custom-select-dropdown bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-md shadow-lg mt-1 overflow-hidden z-50"
                    >
                      <div 
                        class="custom-select-option py-2 px-3 text-slate-900 dark:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-700 cursor-pointer text-sm"
                        @click="selectOrder('')"
                        :class="{ 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300 font-medium': form.order_id === '' }"
                      >
                        Tidak Terkait Pesanan
                      </div>
                      <div 
                        v-for="order in orders" 
                        :key="order.id"
                        @click="selectOrder(order.id)"
                        class="custom-select-option py-2 px-3 text-slate-900 dark:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-700 cursor-pointer text-sm"
                        :class="{ 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300 font-medium': Number(form.order_id) === order.id }"
                      >
                        {{ order.order_number }}
                      </div>
                    </div>
                  </div>
                  <div v-if="form.errors.order_id" class="text-red-500 text-sm mt-1">{{ form.errors.order_id }}</div>
                </div>
              </div>

              <!-- Kolom Kanan -->
              <div class="space-y-6">
                <!-- Tanggal -->
                <div class="space-y-2">
                  <Label for="expense_date">Tanggal <span class="text-red-500">*</span></Label>
                  <Input 
                    id="expense_date" 
                    v-model="form.expense_date" 
                    type="date"
                    :class="{ 'border-red-500': form.errors.expense_date }"
                  />
                  <div v-if="form.errors.expense_date" class="text-red-500 text-sm mt-1">{{ form.errors.expense_date }}</div>
                </div>

                <!-- Metode Pembayaran -->
                <div class="space-y-2">
                  <Label for="payment_method">Metode Pembayaran <span class="text-red-500">*</span></Label>
                  <div class="relative custom-select-container" :class="{ 'active': isPaymentMethodSelectOpen }">
                    <div 
                      @click="togglePaymentMethodSelect" 
                      class="custom-select-trigger flex w-full items-center justify-between gap-2 rounded-md border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 px-3 py-2 text-sm shadow-sm hover:border-slate-300 dark:hover:border-slate-600 focus:outline-none focus:ring-0 disabled:cursor-not-allowed disabled:opacity-50 cursor-pointer h-9"
                    >
                      <span>{{ selectedPaymentMethodLabel }}</span>
                      <ChevronDownIcon class="h-4 w-4 opacity-50 transition-transform" :class="{ 'rotate-180': isPaymentMethodSelectOpen }" />
                    </div>
                    
                    <div 
                      v-if="isPaymentMethodSelectOpen" 
                      class="custom-select-dropdown bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-md shadow-lg mt-1 overflow-hidden z-50"
                    >
                      <div 
                        class="custom-select-option py-2 px-3 text-slate-900 dark:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-700 cursor-pointer text-sm"
                        @click="selectPaymentMethod('cash')"
                        :class="{ 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300 font-medium': form.payment_method === 'cash' }"
                      >
                        Tunai
                      </div>
                      <div 
                        class="custom-select-option py-2 px-3 text-slate-900 dark:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-700 cursor-pointer text-sm"
                        @click="selectPaymentMethod('transfer')"
                        :class="{ 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300 font-medium': form.payment_method === 'transfer' }"
                      >
                        Transfer
                      </div>
                      <div 
                        class="custom-select-option py-2 px-3 text-slate-900 dark:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-700 cursor-pointer text-sm"
                        @click="selectPaymentMethod('credit_card')"
                        :class="{ 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300 font-medium': form.payment_method === 'credit_card' }"
                      >
                        Kartu Kredit
                      </div>
                      <div 
                        class="custom-select-option py-2 px-3 text-slate-900 dark:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-700 cursor-pointer text-sm"
                        @click="selectPaymentMethod('other')"
                        :class="{ 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300 font-medium': form.payment_method === 'other' }"
                      >
                        Lainnya
                      </div>
                    </div>
                  </div>
                  <div v-if="form.errors.payment_method" class="text-red-500 text-sm mt-1">{{ form.errors.payment_method }}</div>
                </div>

                <!-- Status -->
                <div class="space-y-2">
                  <Label for="status">Status <span class="text-red-500">*</span></Label>
                  <div class="relative custom-select-container" :class="{ 'active': isStatusSelectOpen }">
                    <div 
                      @click="toggleStatusSelect" 
                      class="custom-select-trigger flex w-full items-center justify-between gap-2 rounded-md border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 px-3 py-2 text-sm shadow-sm hover:border-slate-300 dark:hover:border-slate-600 focus:outline-none focus:ring-0 disabled:cursor-not-allowed disabled:opacity-50 cursor-pointer h-9"
                    >
                      <span>{{ selectedStatusLabel }}</span>
                      <ChevronDownIcon class="h-4 w-4 opacity-50 transition-transform" :class="{ 'rotate-180': isStatusSelectOpen }" />
                    </div>
                    
                    <div 
                      v-if="isStatusSelectOpen" 
                      class="custom-select-dropdown bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-md shadow-lg mt-1 overflow-hidden z-50"
                    >
                      <div 
                        class="custom-select-option py-2 px-3 text-slate-900 dark:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-700 cursor-pointer text-sm"
                        @click="selectStatus('pending')"
                        :class="{ 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300 font-medium': form.status === 'pending' }"
                      >
                        Pending
                      </div>
                      <div 
                        class="custom-select-option py-2 px-3 text-slate-900 dark:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-700 cursor-pointer text-sm"
                        @click="selectStatus('completed')"
                        :class="{ 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300 font-medium': form.status === 'completed' }"
                      >
                        Completed
                      </div>
                      <div 
                        class="custom-select-option py-2 px-3 text-slate-900 dark:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-700 cursor-pointer text-sm"
                        @click="selectStatus('cancelled')"
                        :class="{ 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300 font-medium': form.status === 'cancelled' }"
                      >
                        Cancelled
                      </div>
                    </div>
                  </div>
                  <div v-if="form.errors.status" class="text-red-500 text-sm mt-1">{{ form.errors.status }}</div>
                </div>

                <!-- Bukti Pembayaran -->
                <div class="space-y-2">
                  <Label for="receipt_image">Bukti Pembayaran</Label>
                  <div v-if="expense.receipt_image" class="mb-2">
                    <img :src="'/storage/' + expense.receipt_image" class="w-24 h-24 object-cover rounded" />
                    <p class="text-xs text-slate-500 mt-1">Gambar saat ini</p>
                  </div>
                  <Input 
                    id="receipt_image" 
                    type="file"
                    @input="form.receipt_image = $event.target.files[0]"
                    :class="{ 'border-red-500': form.errors.receipt_image }"
                  />
                  <div v-if="form.errors.receipt_image" class="text-red-500 text-sm mt-1">{{ form.errors.receipt_image }}</div>
                </div>
              </div>
            </div>

            <!-- Deskripsi (full width) -->
            <div class="space-y-2">
              <Label for="description">Deskripsi</Label>
              <Textarea 
                id="description" 
                v-model="form.description" 
                rows="4"
                :class="{ 'border-red-500': form.errors.description }"
                placeholder="Masukkan deskripsi pengeluaran (opsional)" 
              />
              <div v-if="form.errors.description" class="text-red-500 text-sm mt-1">{{ form.errors.description }}</div>
            </div>

            <div class="flex gap-2">
              <Button 
                type="submit" 
                :disabled="form.processing" 
                variant="action"
              >
                <Loader2 v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
                Simpan Perubahan
              </Button>
              <Link :href="route('admin.expenses.index')">
                <Button 
                  type="button" 
                  variant="outline"
                >
                  Batal
                </Button>
              </Link>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { ArrowLeft, Loader2, ChevronDownIcon } from 'lucide-vue-next';
import InputError from '@/components/InputError.vue';
import { ref, computed, onMounted } from 'vue';

// Definisikan tipe
interface Category {
  id: number;
  name: string;
}

interface Order {
  id: number;
  order_number: string;
}

interface Expense {
  id: number;
  name: string;
  amount: number;
  expense_date: string;
  expense_category_id: number;
  order_id: number | null;
  user_id: number;
  description: string | null;
  receipt_image: string | null;
  payment_method: 'cash' | 'transfer' | 'credit_card' | 'other';
  status: 'pending' | 'completed' | 'cancelled';
}

interface ExpenseEditProps {
  expense: Expense;
  categories: Category[];
  orders: Order[];
}

// Definisikan props dengan tipe
const props = defineProps<ExpenseEditProps>();

// State untuk custom select dropdowns
const isCategorySelectOpen = ref(false);
const isOrderSelectOpen = ref(false);
const isPaymentMethodSelectOpen = ref(false);
const isStatusSelectOpen = ref(false);

// Format tanggal agar sesuai dengan format input tanggal HTML (YYYY-MM-DD)
const formatDateForInput = (dateString: string): string => {
  if (!dateString) return '';
  
  // Jika sudah dalam format YYYY-MM-DD, kembalikan langsung
  if (/^\d{4}-\d{2}-\d{2}$/.test(dateString)) {
    return dateString;
  }
  
  // Konversi ke format YYYY-MM-DD
  const date = new Date(dateString);
  return date.toISOString().split('T')[0];
};

// Form untuk expense
const form = useForm({
  name: props.expense.name,
  amount: props.expense.amount,
  expense_date: formatDateForInput(props.expense.expense_date),
  expense_category_id: props.expense.expense_category_id || '',
  order_id: props.expense.order_id || '',
  description: props.expense.description || '',
  receipt_image: null as File | null,
  payment_method: props.expense.payment_method,
  status: props.expense.status,
  _method: 'PUT'
});

// Toggle functions
const toggleCategorySelect = () => isCategorySelectOpen.value = !isCategorySelectOpen.value;
const toggleOrderSelect = () => isOrderSelectOpen.value = !isOrderSelectOpen.value;
const togglePaymentMethodSelect = () => isPaymentMethodSelectOpen.value = !isPaymentMethodSelectOpen.value;
const toggleStatusSelect = () => isStatusSelectOpen.value = !isStatusSelectOpen.value;

// Selection functions
const selectCategory = (id: number | null) => {
  form.expense_category_id = id === null ? '' : id;
  isCategorySelectOpen.value = false;
};

const selectOrder = (id: string | number) => {
  form.order_id = id;
  isOrderSelectOpen.value = false;
};

const selectPaymentMethod = (method: 'cash' | 'transfer' | 'credit_card' | 'other') => {
  form.payment_method = method;
  isPaymentMethodSelectOpen.value = false;
};

const selectStatus = (status: 'pending' | 'completed' | 'cancelled') => {
  form.status = status;
  isStatusSelectOpen.value = false;
};

// Computed properties untuk label
const selectedCategoryLabel = computed(() => {
  if (!form.expense_category_id) return 'Pilih Kategori';
  const category = props.categories.find(c => c.id === Number(form.expense_category_id));
  return category ? category.name : 'Pilih Kategori';
});

const selectedOrderLabel = computed(() => {
  if (!form.order_id) return 'Tidak Terkait Pesanan';
  const order = props.orders.find(o => o.id === Number(form.order_id));
  return order ? order.order_number : 'Tidak Terkait Pesanan';
});

const selectedPaymentMethodLabel = computed(() => {
  const methodLabels = {
    cash: 'Tunai',
    transfer: 'Transfer',
    credit_card: 'Kartu Kredit',
    other: 'Lainnya'
  };
  return methodLabels[form.payment_method] || 'Pilih Metode Pembayaran';
});

const selectedStatusLabel = computed(() => {
  const statusLabels = {
    pending: 'Pending',
    completed: 'Completed',
    cancelled: 'Cancelled'
  };
  return statusLabels[form.status] || 'Pilih Status';
});

// Close dropdowns when clicking outside
const handleClickOutside = (event: MouseEvent) => {
  const target = event.target as HTMLElement;
  
  if (!target.closest('.custom-select-container')) {
    isCategorySelectOpen.value = false;
    isOrderSelectOpen.value = false;
    isPaymentMethodSelectOpen.value = false;
    isStatusSelectOpen.value = false;
  }
};

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
  return () => {
    document.removeEventListener('click', handleClickOutside);
  };
});

// Submit form
const submit = () => {
  form.post(route('admin.expenses.update', props.expense.id), {
    preserveScroll: true,
  });
};

// Breadcrumbs untuk navigasi
const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Admin',
    href: route('admin.dashboard'),
  },
  {
    title: 'Expenses',
    href: route('admin.expenses.index'),
  },
  {
    title: 'Edit',
    href: route('admin.expenses.edit', props.expense.id),
  },
];
</script>

<style>
/* Custom select styling */
.custom-select-container {
  position: relative;
  width: 100%;
  -webkit-tap-highlight-color: transparent;
  border-radius: 0.375rem;
}

.custom-select-dropdown {
  position: absolute;
  top: 100%;
  left: 0;
  width: 100%;
  max-height: 200px;
  overflow-y: auto;
  animation: slideDown 0.15s ease-out;
  z-index: 50;
}

.custom-select-option:first-child {
  border-top-left-radius: 0.375rem;
  border-top-right-radius: 0.375rem;
}

.custom-select-option:last-child {
  border-bottom-left-radius: 0.375rem;
  border-bottom-right-radius: 0.375rem;
}

@keyframes slideDown {
  from { opacity: 0; transform: translateY(-5px); }
  to { opacity: 1; transform: translateY(0); }
}

/* Perbaikan outline saat fokus */
.custom-select-trigger {
  outline: none !important;
  -webkit-appearance: none;
  -webkit-tap-highlight-color: transparent !important;
}

.custom-select-trigger:focus,
.custom-select-trigger:focus-visible,
.custom-select-trigger:active,
.custom-select-trigger:hover,
.custom-select-trigger:-moz-focusring {
  outline: none !important;
  box-shadow: none !important;
  border-color: #0ea5e9 !important;
}

/* Fix untuk Firefox */
.custom-select-trigger:-moz-focusring {
  outline: none !important;
}

/* Fix untuk Safari dan Chrome */
.custom-select-trigger::-webkit-focus-inner {
  border: 0;
}

/* Fix tambahan untuk Chrome */
*:focus {
  outline-color: transparent !important;
}
</style> 