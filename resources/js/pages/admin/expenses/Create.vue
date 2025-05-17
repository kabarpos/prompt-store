<template>
  <Head title="Tambah Pengeluaran" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex flex-col gap-4 p-4 md:p-6 pb-12">
      <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-slate-50">Tambah Pengeluaran</h1>
      </div>

      <Card class="border border-slate-200 dark:border-slate-700">
        <CardContent class="p-6">
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
                  <div class="relative custom-select-container category-select-container" :class="{ 'active': isCategorySelectOpen }">
                    <div 
                      @click="toggleCategorySelect" 
                      class="custom-select-trigger flex w-full items-center justify-between gap-2 rounded-md border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 px-3 py-2 text-sm shadow-sm hover:border-slate-300 dark:hover:border-slate-600 focus:outline-none focus:ring-0 disabled:cursor-not-allowed disabled:opacity-50 cursor-pointer h-9"
                      :class="{ 'border-red-500': form.errors.expense_category_id }"
                    >
                      <span>{{ selectedCategoryLabel }}</span>
                      <ChevronDownIcon class="h-4 w-4 opacity-50 transition-transform" :class="{ 'rotate-180': isCategorySelectOpen }" />
                    </div>
                    
                    <div 
                      v-if="isCategorySelectOpen" 
                      class="custom-select-dropdown bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-md shadow-lg mt-1 overflow-hidden z-50"
                    >
                      <div
                        class="custom-select-option py-2 px-3 text-slate-500 dark:text-slate-300 italic hover:bg-slate-50 dark:hover:bg-slate-700 cursor-pointer text-sm"
                        @click="selectCategory('')"
                        :class="{ 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300 font-medium': form.expense_category_id === '' }"
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
                  <div id="combobox-container">
                    <Combobox
                      v-model="form.order_id"
                      :options="ordersFormatted"
                      placeholder="Cari dan pilih order..."
                    />
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
                  <div class="relative custom-select-container payment-select-container" :class="{ 'active': isPaymentSelectOpen }">
                    <div 
                      @click="togglePaymentSelect" 
                      class="custom-select-trigger flex w-full items-center justify-between gap-2 rounded-md border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 px-3 py-2 text-sm shadow-sm hover:border-slate-300 dark:hover:border-slate-600 focus:outline-none focus:ring-0 disabled:cursor-not-allowed disabled:opacity-50 cursor-pointer h-9"
                      :class="{ 'border-red-500': form.errors.payment_method }"
                    >
                      <span>{{ selectedPaymentLabel }}</span>
                      <ChevronDownIcon class="h-4 w-4 opacity-50 transition-transform" :class="{ 'rotate-180': isPaymentSelectOpen }" />
                    </div>
                    
                    <div 
                      v-if="isPaymentSelectOpen" 
                      class="custom-select-dropdown bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-md shadow-lg mt-1 overflow-hidden z-50"
                    >
                      <div
                        class="custom-select-option py-2 px-3 text-slate-900 dark:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-700 cursor-pointer text-sm"
                        @click="selectPayment('cash')"
                        :class="{ 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300 font-medium': form.payment_method === 'cash' }"
                      >
                        Tunai
                      </div>
                      <div
                        class="custom-select-option py-2 px-3 text-slate-900 dark:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-700 cursor-pointer text-sm"
                        @click="selectPayment('transfer')"
                        :class="{ 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300 font-medium': form.payment_method === 'transfer' }"
                      >
                        Transfer
                      </div>
                      <div
                        class="custom-select-option py-2 px-3 text-slate-900 dark:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-700 cursor-pointer text-sm"
                        @click="selectPayment('credit_card')"
                        :class="{ 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300 font-medium': form.payment_method === 'credit_card' }"
                      >
                        Kartu Kredit
                      </div>
                      <div
                        class="custom-select-option py-2 px-3 text-slate-900 dark:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-700 cursor-pointer text-sm"
                        @click="selectPayment('other')"
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
                  <div class="relative custom-select-container status-select-container" :class="{ 'active': isStatusSelectOpen }">
                    <div 
                      @click="toggleStatusSelect" 
                      class="custom-select-trigger flex w-full items-center justify-between gap-2 rounded-md border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 px-3 py-2 text-sm shadow-sm hover:border-slate-300 dark:hover:border-slate-600 focus:outline-none focus:ring-0 disabled:cursor-not-allowed disabled:opacity-50 cursor-pointer h-9"
                      :class="{ 'border-red-500': form.errors.status }"
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
                class="bg-primary-600 text-white hover:bg-primary-500 shadow-sm"
              >
                {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
              </Button>
              <Link :href="route('admin.expenses.index')">
                <Button 
                  type="button" 
                  variant="outline" 
                  class="border-slate-300 hover:bg-slate-100 dark:border-slate-600 dark:hover:bg-slate-800"
                >
                  Batal
                </Button>
              </Link>
            </div>
          </form>
        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Card, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { ChevronDownIcon } from 'lucide-vue-next';
import Combobox from '@/components/ui/combobox/Combobox.vue';
import { ref, computed } from 'vue';

// Definisikan tipe
interface Category {
  id: number;
  name: string;
}

interface Order {
  id: number;
  order_number: string;
}

interface ExpenseCreateProps {
  categories: Category[];
  orders: Order[];
}

// Definisikan props dengan tipe
const props = defineProps<ExpenseCreateProps>();

// Form untuk expense
const form = useForm({
  name: '',
  amount: '',
  expense_date: new Date().toISOString().substr(0, 10), // Format YYYY-MM-DD
  expense_category_id: '',
  order_id: '',
  description: '',
  receipt_image: null as File | null,
  payment_method: 'cash',
  status: 'completed',
});

// Submit form
const submit = () => {
  form.post(route('admin.expenses.store'), {
    preserveScroll: true,
    onSuccess: () => {
      form.reset();
    },
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
    title: 'Tambah',
    href: route('admin.expenses.create'),
  },
];

// Custom select logic
const isCategorySelectOpen = ref(false);
const isPaymentSelectOpen = ref(false);
const isStatusSelectOpen = ref(false);
const selectedCategoryLabel = ref('Pilih Kategori');
const selectedPaymentLabel = ref('Tunai');
const selectedStatusLabel = ref('Completed');

const toggleCategorySelect = () => {
  isCategorySelectOpen.value = !isCategorySelectOpen.value;
};

const togglePaymentSelect = () => {
  isPaymentSelectOpen.value = !isPaymentSelectOpen.value;
};

const toggleStatusSelect = () => {
  isStatusSelectOpen.value = !isStatusSelectOpen.value;
};

const selectCategory = (id: string | number) => {
  form.expense_category_id = id.toString();
  isCategorySelectOpen.value = false;
  
  if (id === '' || id === 0 || id.toString() === '0') {
    selectedCategoryLabel.value = 'Pilih Kategori';
  } else {
    const category = props.categories.find(c => c.id === Number(id));
    selectedCategoryLabel.value = category ? category.name : 'Pilih Kategori';
  }
};

const selectPayment = (method: string) => {
  form.payment_method = method;
  isPaymentSelectOpen.value = false;
  selectedPaymentLabel.value = method === 'cash' ? 'Tunai' : method === 'transfer' ? 'Transfer' : method === 'credit_card' ? 'Kartu Kredit' : 'Lainnya';
};

const selectStatus = (status: string) => {
  form.status = status;
  isStatusSelectOpen.value = false;
  selectedStatusLabel.value = status === 'pending' ? 'Pending' : status === 'completed' ? 'Completed' : 'Cancelled';
};

// Menyiapkan data orders untuk combobox
const ordersFormatted = computed(() => {
  return props.orders.map(order => ({
    label: `${order.order_number}`,
    value: order.id.toString()
  }));
});
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