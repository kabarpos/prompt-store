<template>
  <div class="bg-white dark:bg-slate-800 p-4 rounded-lg shadow-sm mb-4">
    <h3 class="text-md font-medium mb-2 text-gray-800 dark:text-gray-200">Kode Promo</h3>
    
    <div v-if="appliedCoupon" class="flex items-center justify-between mb-4 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-md">
      <div>
        <p class="text-sm font-medium text-green-700 dark:text-green-400">
          Kupon diterapkan: {{ appliedCoupon.code || appliedCoupon.name }}
        </p>
        <p class="text-xs text-green-600 dark:text-green-500">
          Potongan: {{ appliedCoupon.formattedDiscount || formatDiscountValue(appliedCoupon) }}
        </p>
      </div>
      <Button variant="ghost" size="sm" @click="removeCoupon" class="text-red-500">
        <XIcon class="h-4 w-4 mr-1" /> Hapus
      </Button>
    </div>
    
    <div v-else>
      <form @submit.prevent="applyCoupon" class="flex items-center gap-2">
        <div class="flex-1">
          <Input 
            v-model="couponCode" 
            placeholder="Masukkan kode promo" 
            :disabled="loading"
            class="w-full"
          />
        </div>
        <Button type="submit" variant="default" size="default" :disabled="loading || !couponCode">
          <span v-if="loading" class="flex items-center">
            <Loader2Icon class="h-4 w-4 mr-1 animate-spin" />
            Proses
          </span>
          <span v-else>Terapkan</span>
        </Button>
      </form>
      
      <p v-if="error" class="mt-1 text-xs text-red-500">{{ error }}</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { XIcon, Loader2Icon } from 'lucide-vue-next';
import { toast } from '@/lib/toast';

interface Coupon {
  id: number;
  code: string;
  name: string;
  type: 'fixed' | 'percentage';
  value: number;
  discount: number;
  formattedDiscount: string;
}

interface Props {
  initialCoupon?: Coupon | null;
}

const props = withDefaults(defineProps<Props>(), {
  initialCoupon: null
});

const emit = defineEmits<{
  (e: 'coupon-applied', coupon: Coupon): void;
  (e: 'coupon-removed'): void;
}>();

const couponCode = ref('');
const appliedCoupon = ref<Coupon | null>(props.initialCoupon || null);
const loading = ref(false);
const error = ref('');

onMounted(() => {
  console.log('Initial coupon data:', props.initialCoupon);
});

const formatDiscountValue = (coupon: Coupon | null) => {
  if (!coupon) return '';
  
  console.log('Formatting discount for coupon:', coupon);
  
  if (coupon.formattedDiscount) return coupon.formattedDiscount;
  
  if (coupon.discount) {
    return new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 0
    }).format(coupon.discount);
  }
  
  if (coupon.type === 'fixed') {
    return new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 0
    }).format(coupon.value);
  } else if (coupon.type === 'percentage') {
    return `${coupon.value}%`;
  }
  
  return '';
};

watch(() => props.initialCoupon, (newVal) => {
  console.log('Coupon prop changed:', newVal);
  if (newVal) {
    appliedCoupon.value = newVal;
    console.log('Applied coupon updated:', appliedCoupon.value);
  } else {
    appliedCoupon.value = null;
  }
}, { immediate: true, deep: true });

const applyCoupon = async () => {
  if (!couponCode.value) return;
  
  loading.value = true;
  error.value = '';
  
  try {
    const response = await axios.post(route('coupons.apply'), {
      code: couponCode.value
    });
    
    if (response.data.success) {
      appliedCoupon.value = response.data.coupon;
      couponCode.value = '';
      toast.success('Kupon berhasil diterapkan');
      emit('coupon-applied', response.data.coupon);
    }
  } catch (err: any) {
    if (err.response?.data?.message) {
      error.value = err.response.data.message;
    } else if (err.response?.data?.errors?.code) {
      error.value = err.response.data.errors.code[0];
    } else {
      error.value = 'Terjadi kesalahan saat menerapkan kupon';
    }
    console.error('Error applying coupon:', err);
  } finally {
    loading.value = false;
  }
};

const removeCoupon = async () => {
  loading.value = true;
  
  try {
    const response = await axios.post(route('coupons.remove'));
    
    if (response.data.success) {
      appliedCoupon.value = null;
      toast.success('Kupon berhasil dihapus');
      emit('coupon-removed');
    }
  } catch (err: any) {
    console.error('Error removing coupon:', err);
    toast.error('Terjadi kesalahan saat menghapus kupon');
  } finally {
    loading.value = false;
  }
};
</script> 