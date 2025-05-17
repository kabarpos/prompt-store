<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, onMounted, watch, defineProps, computed, onUnmounted, nextTick } from 'vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import ConfirmationDialog from '@/components/ui/ConfirmationDialog.vue';
import { MoreHorizontal, Plus, Check, X, Trash2, Edit, Shield, Eye, Search, Filter, RefreshCw } from 'lucide-vue-next';
import { toast } from 'vue-sonner';
import { Input } from '@/components/ui/input';
import { Card, CardContent } from '@/components/ui/card';
import AdminFormFix from '@/components/ui/admin/AdminFormFix.vue';
import AdminTable from '@/components/AdminTable.vue';
import StatusBadge from '@/components/StatusBadge.vue';
import { TableRow, TableCell } from '@/components/ui/table';
// @ts-ignore
import debounce from 'lodash/debounce';

// Referensi pengguna yang sedang diproses
const processingUser = ref<number | null>(null);
// State untuk menampilkan loading
const loading = ref(false);
const isFiltering = ref(false);

// State untuk filter
const filters = ref({
  search: '',
  status: '',
  role: ''
});

// State untuk dialog konfirmasi
const selectedUser = ref<{
  id: number;
  name: string;
  email: string;
  status: string;
} | null>(null);
const showActivationDialog = ref(false);
const showBlockDialog = ref(false);
const showDeleteDialog = ref(false);
const showFilterPanel = ref(false);

// State untuk custom select dropdown status
const isStatusSelectOpen = ref(false);
const statusSelectRef = ref(null);

// State untuk custom select dropdown role
const isRoleSelectOpen = ref(false);
const roleSelectRef = ref(null);

// Breadcrumbs untuk navigasi
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Admin',
        href: route('admin.dashboard'),
    },
    {
        title: 'Manajemen Pengguna',
        href: route('admin.users.index'),
    },
];

// Daftar status untuk filter
const statusOptions = [
  { value: '', label: 'Semua Status' },
  { value: 'active', label: 'Aktif' },
  { value: 'inactive', label: 'Tidak Aktif' },
  { value: 'blocked', label: 'Diblokir' },
  { value: 'rejected', label: 'Ditolak' }
];

// Mendapatkan daftar unique roles untuk filter
const roleOptions = ref([
  { value: '', label: 'Semua Peran' }
]);

// Kolom tabel
const columns = [
  { label: 'Nama' },
  { label: 'Email' },
  { label: 'WhatsApp', headerClass: 'hidden md:table-cell' },
  { label: 'Status' },
  { label: 'Peran', headerClass: 'hidden md:table-cell' },
  { label: 'Tanggal Daftar', headerClass: 'hidden md:table-cell' },
  { label: '', headerClass: 'w-[60px]' }
];

// Status map untuk StatusBadge
const statusMap = {
  active: 'Aktif',
  inactive: 'Tidak Aktif',
  blocked: 'Diblokir',
  rejected: 'Ditolak'
};

// Ambil semua peran unik dari data pengguna
onMounted(() => {
  const uniqueRoles = new Set<string>();
  props.users.data.forEach(user => {
    user.roles.forEach(role => {
      uniqueRoles.add(role.name as string);
    });
  });
  
  // Tambahkan roles ke options
  uniqueRoles.forEach(roleName => {
    roleOptions.value.push({
      value: roleName,
      label: roleName
    });
  });
});

const formatDate = (dateString: string) => {
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  }).format(date);
};

// Fungsi untuk melakukan pencarian dan filter
const applyFilters = debounce(() => {
  isFiltering.value = true;
  
  router.get(route('admin.users.index'), {
    search: filters.value.search,
    status: filters.value.status,
    role: filters.value.role
  }, {
    preserveState: true,
    replace: true,
    onSuccess: () => {
      isFiltering.value = false;
    },
    onError: () => {
      isFiltering.value = false;
      toast.error('Gagal', {
        description: 'Gagal menerapkan filter',
      });
    }
  });
}, 500);

// Hapus semua filter
const resetFilters = () => {
  filters.value.search = '';
  filters.value.status = '';
  filters.value.role = '';
  applyFilters();
};

// Terapkan filter saat ada perubahan
watch(() => filters.value.search, applyFilters);
watch(() => filters.value.status, applyFilters);
watch(() => filters.value.role, applyFilters);

// Fungsi untuk menampilkan dialog aktivasi
const showAktivasiDialog = (user: any) => {
  selectedUser.value = user;
  showActivationDialog.value = true;
};

// Fungsi untuk mengaktifkan pengguna
const aktivasiUser = () => {
  if (!selectedUser.value) return;
  
  loading.value = true;
  processingUser.value = selectedUser.value.id;
  
  router.patch(route('admin.users.update-status', selectedUser.value.id), {
    status: 'active'
  }, {
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Berhasil', {
        description: selectedUser.value ? `Pengguna ${selectedUser.value.name} berhasil diaktifkan` : 'Pengguna berhasil diaktifkan',
      });
      showActivationDialog.value = false;
      selectedUser.value = null; // Clear selected user
    },
    onError: (errors) => {
      toast.error('Gagal', {
        description: `Terjadi kesalahan saat mengaktifkan pengguna: ${errors.message || 'Unknown error'}`,
      });
      console.error('Error saat aktivasi:', errors);
    },
    onFinish: () => {
      loading.value = false;
      processingUser.value = null;
    }
  });
};

// Fungsi untuk menampilkan dialog blokir
const showBlokirDialog = (user: any) => {
  selectedUser.value = user;
  showBlockDialog.value = true;
};

// Fungsi untuk memblokir pengguna
const blokirUser = () => {
  if (!selectedUser.value) return;
  
  loading.value = true;
  processingUser.value = selectedUser.value.id;
  
  router.patch(route('admin.users.update-status', selectedUser.value.id), {
    status: 'blocked'
  }, {
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Berhasil', {
        description: selectedUser.value ? `Pengguna ${selectedUser.value.name} berhasil diblokir` : 'Pengguna berhasil diblokir',
      });
      showBlockDialog.value = false;
      selectedUser.value = null; // Clear selected user
    },
    onError: (errors) => {
      toast.error('Gagal', {
        description: `Terjadi kesalahan saat memblokir pengguna: ${errors.message || 'Unknown error'}`,
      });
      console.error('Error saat blokir:', errors);
    },
    onFinish: () => {
      loading.value = false;
      processingUser.value = null;
    }
  });
};

// Toggle panel filter
const toggleFilterPanel = () => {
  showFilterPanel.value = !showFilterPanel.value;
};

// Computed property untuk label status terpilih
const selectedStatusLabel = computed(() => {
    if (!filters.value.status) return 'Pilih status';
    const status = statusOptions.find(option => option.value === filters.value.status);
    return status ? status.label : 'Pilih status';
});

// Computed property untuk label peran terpilih
const selectedRoleLabel = computed(() => {
    if (!filters.value.role) return 'Pilih peran';
    const role = roleOptions.value.find(option => option.value === filters.value.role);
    return role ? role.label : 'Pilih peran';
});

// Toggle dropdown status
const toggleStatusSelect = () => {
    isStatusSelectOpen.value = !isStatusSelectOpen.value;
    if (isStatusSelectOpen.value) {
        isRoleSelectOpen.value = false;
    }
};

// Toggle dropdown role
const toggleRoleSelect = () => {
    isRoleSelectOpen.value = !isRoleSelectOpen.value;
    if (isRoleSelectOpen.value) {
        isStatusSelectOpen.value = false;
    }
};

// Pilih status
const selectStatus = (statusValue) => {
    filters.value.status = statusValue;
    isStatusSelectOpen.value = false;
    applyFilters();
};

// Pilih role
const selectRole = (roleValue) => {
    filters.value.role = roleValue;
    isRoleSelectOpen.value = false;
    applyFilters();
};

// Handle click outside untuk status
const handleStatusClickOutside = (evt) => {
    if (statusSelectRef.value && !statusSelectRef.value.contains(evt.target)) {
        isStatusSelectOpen.value = false;
    }
};

// Handle click outside untuk role
const handleRoleClickOutside = (evt) => {
    if (roleSelectRef.value && !roleSelectRef.value.contains(evt.target)) {
        isRoleSelectOpen.value = false;
    }
};

// Lifecycle hooks
onMounted(() => {
    document.addEventListener('click', handleStatusClickOutside);
    document.addEventListener('click', handleRoleClickOutside);
    
    nextTick(() => {
        statusSelectRef.value = document.querySelector('.status-select-container');
        roleSelectRef.value = document.querySelector('.role-select-container');
    });
});

onUnmounted(() => {
    document.removeEventListener('click', handleStatusClickOutside);
    document.removeEventListener('click', handleRoleClickOutside);
});

// Definisi props dari controller
const props = defineProps<{
    users: {
        data: Array<{
            id: number;
            name: string;
            email: string;
            status: string;
            created_at: string;
            roles: Array<{
                id: number;
                name: string;
            }>;
            whatsapp?: string;
        }>;
        meta?: {
            total?: number;
            current_page?: number;
            last_page?: number;
        };
        links?: Array<{
            url: string | null;
            label: string;
            active: boolean;
        }>;
    };
    filters?: {
        search: string;
        status: string;
        role: string;
    };
}>();

// Inisialisasi filter dari props jika tersedia
if (props.filters) {
    filters.value.search = props.filters.search || '';
    filters.value.status = props.filters.status || '';
    filters.value.role = props.filters.role || '';
}

// Fungsi untuk menampilkan dialog hapus
const showHapusDialog = (user: any) => {
  selectedUser.value = user;
  showBlockDialog.value = false; // Tutup dialog lain jika masih terbuka
  showDeleteDialog.value = true;
};

// Fungsi untuk menghapus pengguna
const hapusUser = () => {
  if (!selectedUser.value) return;
  
  loading.value = true;
  processingUser.value = selectedUser.value.id;
  
  router.delete(route('admin.users.destroy', selectedUser.value.id), {
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Berhasil', {
        description: selectedUser.value ? `Pengguna ${selectedUser.value.name} berhasil dihapus` : 'Pengguna berhasil dihapus',
      });
      showDeleteDialog.value = false;
      selectedUser.value = null; // Clear selected user
    },
    onError: (errors) => {
      toast.error('Gagal', {
        description: `Terjadi kesalahan saat menghapus pengguna: ${errors.message || 'Unknown error'}`,
      });
      console.error('Error saat hapus:', errors);
    },
    onFinish: () => {
      loading.value = false;
      processingUser.value = null;
    }
  });
};
</script>

<template>
  <AdminFormFix>
    <Head title="Manajemen Pengguna" />

    <AppLayout :breadcrumbs="breadcrumbs">
      <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
          <h1 class="text-2xl font-bold text-secondary-900 dark:text-white">Manajemen Pengguna</h1>
          <Link :href="route('admin.users.create')" class="cursor-pointer">
            <Button variant="action" class="flex items-center gap-1.5 w-full sm:w-auto cursor-pointer">
              <Plus class="h-4 w-4" />
              Tambah Pengguna
            </Button>
          </Link>
        </div>

        <div class="bg-white dark:bg-slate-800 text-secondary-900 dark:text-white rounded-xl shadow border border-slate-200 dark:border-slate-700 overflow-hidden">
          <div class="p-6 border-b border-secondary-200 dark:border-slate-700">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
              <div>
                <h2 class="text-lg font-medium text-secondary-900 dark:text-white">Daftar Pengguna</h2>
                <p class="text-secondary-500 dark:text-secondary-400 mt-1">Kelola pengguna dan akses mereka di sistem.</p>
              </div>
              
              <div class="flex items-center gap-3">
                <!-- Filter dan Pencarian -->
                <div class="relative w-full sm:w-64">
                  <Search class="absolute left-2.5 top-2.5 h-4 w-4 text-secondary-500 dark:text-secondary-400" />
                  <Input 
                    type="search" 
                    placeholder="Cari nama atau email..."
                    class="pl-9 w-full"
                    v-model="filters.search"
                  />
                </div>
                
                <Button 
                  variant="outline" 
                  size="icon" 
                  @click="toggleFilterPanel"
                  :class="{'bg-primary/10': showFilterPanel}"
                >
                  <Filter class="h-4 w-4" />
                </Button>
                
                <Button 
                  variant="outline"
                  size="icon"
                  @click="resetFilters"
                  :disabled="!filters.search && !filters.status && !filters.role"
                >
                  <RefreshCw class="h-4 w-4" />
                </Button>
              </div>
            </div>
            
            <!-- Panel Filter yang bisa ditoggle -->
            <Card v-if="showFilterPanel" class="mt-4">
              <CardContent class="p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div class="space-y-2">
                    <label class="text-sm font-medium text-secondary-900 dark:text-white">Status</label>
                    <div class="relative mt-1">
                      <div 
                        class="custom-select-container status-select-container" 
                        :class="{ 'active': isStatusSelectOpen }"
                      >
                        <div 
                          @click="toggleStatusSelect" 
                          class="custom-select-trigger flex w-full items-center justify-between gap-2 rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 px-3 py-2 text-sm shadow-sm hover:border-gray-300 dark:hover:border-gray-600 focus:outline-none focus:ring-0 disabled:cursor-not-allowed disabled:opacity-50 cursor-pointer h-9"
                        >
                          <span>{{ selectedStatusLabel }}</span>
                          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-50 transition-transform" :class="{ 'rotate-180': isStatusSelectOpen }">
                            <path d="m6 9 6 6 6-6"></path>
                          </svg>
                        </div>
                        
                        <div 
                          v-if="isStatusSelectOpen" 
                          class="custom-select-dropdown bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg mt-1 overflow-hidden z-50"
                        >
                          <div 
                            v-for="option in statusOptions" 
                            :key="option.value"
                            @click="selectStatus(option.value)"
                            class="custom-select-option py-2 px-3 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer text-sm"
                            :class="{ 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300 font-medium': filters.status === option.value }"
                          >
                            {{ option.label }}
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <div class="space-y-2">
                    <label class="text-sm font-medium text-secondary-900 dark:text-white">Peran</label>
                    <div class="relative mt-1">
                      <div 
                        class="custom-select-container role-select-container" 
                        :class="{ 'active': isRoleSelectOpen }"
                      >
                        <div 
                          @click="toggleRoleSelect" 
                          class="custom-select-trigger flex w-full items-center justify-between gap-2 rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 px-3 py-2 text-sm shadow-sm hover:border-gray-300 dark:hover:border-gray-600 focus:outline-none focus:ring-0 disabled:cursor-not-allowed disabled:opacity-50 cursor-pointer h-9"
                        >
                          <span>{{ selectedRoleLabel }}</span>
                          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-50 transition-transform" :class="{ 'rotate-180': isRoleSelectOpen }">
                            <path d="m6 9 6 6 6-6"></path>
                          </svg>
                        </div>
                        
                        <div 
                          v-if="isRoleSelectOpen" 
                          class="custom-select-dropdown bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg mt-1 overflow-hidden z-50"
                        >
                          <div 
                            v-for="option in roleOptions" 
                            :key="option.value"
                            @click="selectRole(option.value)"
                            class="custom-select-option py-2 px-3 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer text-sm"
                            :class="{ 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300 font-medium': filters.role === option.value }"
                          >
                            {{ option.label }}
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </CardContent>
            </Card>
          </div>
          
          <!-- Tabel Users dengan AdminTable -->
          <AdminTable 
            :columns="columns" 
            :data="users" 
            :loading="isFiltering"
            emptyMessage="Tidak ada pengguna ditemukan"
          >
            <TableRow v-for="user in users.data" :key="user.id" class="hover:bg-secondary-100/50 dark:hover:bg-slate-900/90">
              <TableCell class="py-3.5 px-6 align-middle font-medium text-secondary-900 dark:text-white">{{ user.name }}</TableCell>
              <TableCell class="py-3.5 px-6 align-middle text-sm text-secondary-900 dark:text-white">{{ user.email }}</TableCell>
              <TableCell class="py-3.5 px-6 align-middle text-sm text-secondary-900 dark:text-white hidden md:table-cell">{{ user.whatsapp || '-' }}</TableCell>
              <TableCell class="py-3.5 px-6 align-middle">
                <StatusBadge :status="user.status" :statusMap="statusMap" />
              </TableCell>
              <TableCell class="py-3.5 px-6 align-middle hidden md:table-cell">
                <div class="flex gap-1.5 flex-wrap">
                  <Badge 
                    v-for="role in user.roles" 
                    :key="role.id" 
                    variant="outline" 
                    class="capitalize text-xs px-2 py-0.5 bg-slate-50 text-slate-700 dark:bg-slate-800 dark:text-slate-300"
                  >
                    {{ role.name }}
                  </Badge>
                </div>
              </TableCell>
              <TableCell class="py-3.5 px-6 align-middle hidden md:table-cell text-sm text-secondary-500 dark:text-secondary-400">{{ formatDate(user.created_at) }}</TableCell>
              <TableCell class="py-3.5 px-6 align-middle text-right">
                <DropdownMenu>
                  <DropdownMenuTrigger asChild>
                    <Button variant="action" size="icon" class="h-8 w-8 rounded-md">
                      <MoreHorizontal class="h-4 w-4" />
                      <span class="sr-only">Menu</span>
                    </Button>
                  </DropdownMenuTrigger>
                  <DropdownMenuContent align="end" class="w-[160px]">
                    <Link :href="route('admin.users.show', user.id)" class="w-full">
                      <DropdownMenuItem class="flex items-center gap-2 cursor-pointer py-1.5">
                        <Eye class="h-4 w-4" />
                        <span>Lihat Detail</span>
                      </DropdownMenuItem>
                    </Link>
                    <Link :href="route('admin.users.edit', user.id)" class="w-full">
                      <DropdownMenuItem class="flex items-center gap-2 cursor-pointer py-1.5">
                        <Edit class="h-4 w-4" />
                        <span>Edit</span>
                      </DropdownMenuItem>
                    </Link>
                    <DropdownMenuItem 
                      v-if="user.status !== 'active'" 
                      @click="showAktivasiDialog(user)"
                      class="flex items-center gap-2 cursor-pointer py-1.5"
                      :disabled="loading && processingUser === user.id"
                    >
                      <Check class="h-4 w-4" />
                      <span>{{ loading && processingUser === user.id ? 'Memproses...' : 'Aktifkan' }}</span>
                    </DropdownMenuItem>
                    <DropdownMenuItem 
                      v-if="user.status !== 'blocked'" 
                      @click="showBlokirDialog(user)"
                      variant="destructive"
                      class="flex items-center gap-2 cursor-pointer py-1.5"
                      :disabled="loading && processingUser === user.id"
                    >
                      <X class="h-4 w-4" />
                      <span>{{ loading && processingUser === user.id ? 'Memproses...' : 'Blokir' }}</span>
                    </DropdownMenuItem>
                    <DropdownMenuItem 
                      @click="showHapusDialog(user)"
                      variant="destructive"
                      class="flex items-center gap-2 cursor-pointer py-1.5"
                      :disabled="loading && processingUser === user.id"
                    >
                      <Trash2 class="h-4 w-4" />
                      <span>{{ loading && processingUser === user.id ? 'Memproses...' : 'Hapus' }}</span>
                    </DropdownMenuItem>
                  </DropdownMenuContent>
                </DropdownMenu>
              </TableCell>
            </TableRow>
          </AdminTable>
        </div>
      </div>

      <!-- Dialog Konfirmasi Aktivasi -->
      <ConfirmationDialog
        :open="showActivationDialog"
        @update:open="showActivationDialog = $event"
        title="Konfirmasi Aktivasi"
        :description="selectedUser ? `Apakah Anda yakin ingin mengaktifkan pengguna ${selectedUser.name}?` : 'Apakah Anda yakin ingin mengaktifkan pengguna ini?'"
        confirmLabel="Aktifkan"
        :loading="loading"
        :icon="Check"
        @confirm="aktivasiUser()"
      />

      <!-- Dialog Konfirmasi Blokir -->
      <ConfirmationDialog
        :open="showBlockDialog"
        @update:open="showBlockDialog = $event"
        title="Konfirmasi Pemblokiran"
        dangerMode
        :icon="X"
        :loading="loading"
        confirmLabel="Blokir"
        @confirm="blokirUser()"
      >
        Apakah Anda yakin ingin memblokir pengguna <span class="font-semibold">{{ selectedUser ? selectedUser.name : '' }}</span>? Pengguna tidak akan bisa login.
      </ConfirmationDialog>

      <!-- Dialog Konfirmasi Hapus -->
      <ConfirmationDialog
        :open="showDeleteDialog"
        @update:open="(value) => { 
          showDeleteDialog = value;
          if (!value) selectedUser = null;  // Reset selectedUser saat dialog ditutup
        }"
        title="Konfirmasi Penghapusan"
        dangerMode
        :icon="Trash2"
        :loading="loading"
        confirmLabel="Hapus"
        @confirm="hapusUser()"
      >
        <p class="mb-2">PERHATIAN: Tindakan ini tidak dapat dibatalkan!</p>
        <p>Apakah Anda yakin ingin menghapus pengguna <span class="font-semibold">{{ selectedUser ? selectedUser.name : '' }}</span>?</p>
      </ConfirmationDialog>
    </AppLayout>
  </AdminFormFix>
</template>

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