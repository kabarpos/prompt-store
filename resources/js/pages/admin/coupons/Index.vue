<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6">
      <!-- Header dengan judul dan tombol tambah -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h1 class="text-2xl font-bold text-secondary-900 dark:text-white">Manajemen Kupon</h1>
        <Link :href="route('admin.coupons.create')" class="cursor-pointer">
          <Button variant="action" class="flex items-center gap-1.5 w-full sm:w-auto cursor-pointer">
            <PlusIcon class="mr-2 h-4 w-4" />
            Tambah Kupon
          </Button>
        </Link>
      </div>

      <div class="bg-white dark:bg-slate-800 text-secondary-900 dark:text-white rounded-xl shadow border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="p-6 border-b border-secondary-200 dark:border-slate-700">
          <div>
            <h2 class="text-lg font-medium text-secondary-900 dark:text-white">Daftar Kupon</h2>
            <p class="text-secondary-600 dark:text-secondary-400 mt-1">Kelola kupon diskon untuk produk Anda</p>
          </div>
        </div>
        
        <AdminTable
          :columns="columns"
          :data="{ data: coupons }"
          emptyMessage="Belum ada data kupon"
        >
          <TableRow v-for="coupon in coupons" :key="coupon.id">
            <TableCell class="font-medium">{{ coupon.code }}</TableCell>
            <TableCell>{{ coupon.description }}</TableCell>
            <TableCell>{{ coupon.discount_type_text }}</TableCell>
            <TableCell>{{ coupon.formatted_value }}</TableCell>
            <TableCell>Rp {{ formatPrice(coupon.min_purchase) }}</TableCell>
            <TableCell>
              {{ coupon.used_count }} / {{ coupon.max_uses > 0 ? coupon.max_uses : '∞' }}
            </TableCell>
            <TableCell>
              <Badge :variant="getCouponStatusColor(coupon.status_text)">
                {{ coupon.status_text }}
              </Badge>
            </TableCell>
            <TableCell>
              <span v-if="coupon.starts_at || coupon.expires_at" class="text-xs">
                {{ formatDatePeriod(coupon.starts_at, coupon.expires_at) }}
              </span>
              <span v-else class="text-xs text-muted-foreground">Tidak dibatasi</span>
            </TableCell>
            <TableCell class="text-center">
              <div class="flex items-center justify-center">
                <DropdownMenu>
                  <DropdownMenuTrigger asChild>
                    <Button variant="ghost" size="icon">
                      <MoreHorizontalIcon class="h-4 w-4" />
                    </Button>
                  </DropdownMenuTrigger>
                  <DropdownMenuContent align="end">
                    <DropdownMenuItem @click="toggleActive(coupon)">
                      <PowerIcon v-if="coupon.is_active" class="h-4 w-4 mr-2 text-green-500" />
                      <PowerOffIcon v-else class="h-4 w-4 mr-2 text-red-500" />
                      {{ coupon.is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                    </DropdownMenuItem>
                    <DropdownMenuItem>
                      <Link :href="route('admin.coupons.edit', coupon.id)" class="flex w-full items-center">
                        <PencilIcon class="h-4 w-4 mr-2" />
                        Edit
                      </Link>
                    </DropdownMenuItem>
                    <DropdownMenuItem @click="confirmDelete(coupon)">
                      <TrashIcon class="h-4 w-4 mr-2 text-red-500" />
                      <span class="text-red-500">Hapus</span>
                    </DropdownMenuItem>
                  </DropdownMenuContent>
                </DropdownMenu>
              </div>
            </TableCell>
          </TableRow>
        </AdminTable>
      </div>
    </div>
    
    <Dialog :open="showDeleteDialog" @update:open="showDeleteDialog = $event">
      <DialogContent class="sm:max-w-md">
        <DialogHeader>
          <DialogTitle>Hapus Kupon</DialogTitle>
          <DialogDescription>Apakah Anda yakin ingin menghapus kupon ini?</DialogDescription>
        </DialogHeader>
        <div class="pt-3 text-center">
          <h3 class="font-medium text-lg">
            {{ couponToDelete?.description || couponToDelete?.code }}
          </h3>
          <p class="text-sm text-muted-foreground">
            Kode: {{ couponToDelete?.code }}
          </p>
        </div>
        <DialogFooter>
          <Button variant="outline" @click="showDeleteDialog = false">
            Batal
          </Button>
          <Button 
            variant="destructive" 
            :disabled="isDeleting" 
            @click="deleteCategory"
          >
            <Loader2Icon v-if="isDeleting" class="mr-2 h-4 w-4 animate-spin" />
            Hapus
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import AdminTable from '@/components/AdminTable.vue';
import { 
  Table, TableBody, TableCell, TableHead, 
  TableHeader, TableRow 
} from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { 
  Dialog, DialogContent, DialogDescription, 
  DialogFooter, DialogHeader, DialogTitle
} from '@/components/ui/dialog';
import {
  DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger
} from '@/components/ui/dropdown-menu';
import { 
  PlusIcon, PencilIcon, TrashIcon, 
  Loader2Icon, PowerIcon, PowerOffIcon, MoreHorizontalIcon
} from 'lucide-vue-next';
import { toast } from '@/lib/toast';

interface Coupon {
  id: number;
  code: string;
  description: string | null;
  type: 'fixed' | 'percentage';
  value: number;
  min_purchase: number;
  max_discount: number | null;
  max_uses: number;
  used_count: number;
  is_active: boolean;
  starts_at: string | null;
  expires_at: string | null;
  created_at: string;
  updated_at: string;
  formatted_value: string;
  discount_type_text: string;
  status_text: string;
}

interface Props {
  coupons: Coupon[];
}

const props = defineProps<Props>();

const breadcrumbs = [
  { title: 'Dashboard', href: route('admin.dashboard') },
  { title: 'Manajemen Kupon', href: route('admin.coupons.index') },
];

// Definisi kolom tabel
const columns = [
  { label: 'Kode', headerClass: 'py-3 px-6 font-medium text-secondary-600 dark:text-secondary-400' },
  { label: 'Deskripsi', headerClass: 'py-3 px-6 font-medium text-secondary-600 dark:text-secondary-400' },
  { label: 'Tipe', headerClass: 'py-3 px-6 font-medium text-secondary-600 dark:text-secondary-400' },
  { label: 'Nilai', headerClass: 'py-3 px-6 font-medium text-secondary-600 dark:text-secondary-400' },
  { label: 'Minimal Pembelian', headerClass: 'py-3 px-6 font-medium text-secondary-600 dark:text-secondary-400' },
  { label: 'Penggunaan', headerClass: 'py-3 px-6 font-medium text-secondary-600 dark:text-secondary-400' },
  { label: 'Status', headerClass: 'py-3 px-6 font-medium text-secondary-600 dark:text-secondary-400' },
  { label: 'Periode', headerClass: 'py-3 px-6 font-medium text-secondary-600 dark:text-secondary-400' },
  { label: 'Aksi', headerClass: 'py-3 px-6 font-medium text-secondary-600 dark:text-secondary-400 text-right' },
];

// State untuk dialog konfirmasi hapus
const showDeleteDialog = ref(false);
const isDeleting = ref(false);
const couponToDelete = ref<Coupon | null>(null);

// Warna badge berdasarkan status kupon
const getCouponStatusColor = (status: string) => {
  switch (status) {
    case 'Aktif':
      return 'success';
    case 'Tidak Aktif':
      return 'destructive';
    case 'Kadaluarsa':
      return 'destructive';
    case 'Belum Berlaku':
      return 'warning';
    case 'Habis':
      return 'destructive';
    default:
      return 'default';
  }
};

// Format periode kupon
const formatDatePeriod = (validFrom: string | null, validTo: string | null) => {
  if (validFrom && validTo) {
    return `${formatDate(validFrom)} - ${formatDate(validTo)}`;
  } else if (validFrom) {
    return `Mulai ${formatDate(validFrom)}`;
  } else if (validTo) {
    return `Sampai ${formatDate(validTo)}`;
  }
  return 'Tidak dibatasi';
};

// Fungsi formatDate secara inline
const formatDate = (date: string | Date): string => {
  return new Intl.DateTimeFormat('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  }).format(new Date(date));
};

// Fungsi formatPrice secara inline
const formatPrice = (amount: number): string => {
  return new Intl.NumberFormat('id-ID', {
    style: 'decimal',
    minimumFractionDigits: 0
  }).format(amount);
};

// Toggle status aktif kupon
const toggleActive = (coupon: Coupon) => {
  router.patch(
    route('admin.coupons.toggle-active', coupon.id),
    {},
    {
      preserveScroll: true,
      onSuccess: () => {
        toast.success(`Kupon ${coupon.is_active ? 'dinonaktifkan' : 'diaktifkan'}`);
      },
      onError: (errors) => {
        toast.error('Gagal mengubah status kupon');
      },
    }
  );
};

// Konfirmasi sebelum hapus kupon
const confirmDelete = (coupon: Coupon) => {
  couponToDelete.value = coupon;
  showDeleteDialog.value = true;
};

// Hapus kupon
const deleteCategory = () => {
  if (!couponToDelete.value) return;
  
  isDeleting.value = true;
  
  router.delete(
    route('admin.coupons.destroy', couponToDelete.value.id),
    {
      preserveScroll: true,
      onSuccess: () => {
        toast.success('Kupon berhasil dihapus');
        showDeleteDialog.value = false;
      },
      onError: (errors) => {
        toast.error(errors.error || 'Gagal menghapus kupon');
      },
      onFinish: () => {
        isDeleting.value = false;
      },
    }
  );
};
</script> 