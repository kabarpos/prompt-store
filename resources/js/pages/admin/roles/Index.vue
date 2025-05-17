<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Table, TableHeader, TableHead, TableBody, TableRow, TableCell } from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import ConfirmationDialog from '@/components/ui/ConfirmationDialog.vue';
import { MoreHorizontal, Plus, Trash2, Edit, Key, Eye } from 'lucide-vue-next';
import { toast } from 'vue-sonner';

// Referensi peran yang sedang diproses
const processingRole = ref(null);
// State untuk menampilkan loading
const loading = ref(false);

// State untuk dialog konfirmasi
const selectedRole = ref(null);
const showDeleteDialog = ref(false);

// Fungsi navigasi ke halaman permissions
const navigateToPermissions = () => {
  router.visit(route('admin.permissions.index'));
};

// Breadcrumbs untuk navigasi
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Admin',
        href: route('admin.dashboard'),
    },
    {
        title: 'Manajemen Peran',
        href: route('admin.roles.index'),
    },
];

// Definisi props dari controller
const props = defineProps<{
    roles: {
        data: Array<{
            id: number;
            name: string;
            permissions: Array<{
                id: number;
                name: string;
            }>;
            users_count?: number;
            created_at: string;
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
}>();

// Filter data peran untuk menghilangkan peran editor dari tampilan
const filteredRoles = computed(() => {
  return props.roles.data.filter(role => role.name !== 'editor');
});

const formatDate = (dateString: string) => {
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  }).format(date);
};

// Fungsi untuk menampilkan dialog hapus
const showHapusDialog = (role) => {
  selectedRole.value = role;
  showDeleteDialog.value = true;
};

// Fungsi untuk menghapus peran
const hapusRole = () => {
  if (!selectedRole.value) return;
  
  loading.value = true;
  processingRole.value = selectedRole.value.id;
  
  router.delete(route('admin.roles.destroy', selectedRole.value.id), {
    onSuccess: () => {
      toast.success('Berhasil', {
        description: `Peran ${selectedRole.value.name} berhasil dihapus`,
      });
      showDeleteDialog.value = false;
    },
    onError: (errors) => {
      toast.error('Gagal', {
        description: `Terjadi kesalahan saat menghapus peran: ${errors.message || 'Unknown error'}`,
      });
      console.error('Error:', errors);
    },
    onFinish: () => {
      loading.value = false;
      processingRole.value = null;
    }
  });
};
</script>

<template>
  <Head title="Manajemen Peran" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h1 class="text-2xl font-bold">Manajemen Peran</h1>
        <div class="flex gap-2">
          <Button 
            variant="outline" 
            @click="navigateToPermissions"
            class="flex items-center gap-1.5 h-10"
          >
            <Key class="h-4 w-4 mr-1" />
            Kelola Izin
          </Button>
          <Link :href="route('admin.roles.create')" class="cursor-pointer">
            <Button variant="action" class="flex items-center gap-1.5 w-full sm:w-auto cursor-pointer">
              <Plus class="h-4 w-4" />
              Tambah Peran
            </Button>
          </Link>
        </div>
      </div>

      <div class="bg-white dark:bg-slate-800 text-secondary-900 dark:text-white rounded-xl shadow border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="p-6 border-b border-secondary-200 dark:border-slate-700">
          <h2 class="text-lg font-medium">Daftar Peran</h2>
          <p class="text-muted-foreground mt-1">Kelola peran dan izin yang terkait dengannya.</p>
        </div>
        
        <div class="border-t overflow-x-auto border-slate-200 dark:border-slate-700">
          <Table class="w-full">
            <TableHeader>
              <TableRow class="hover:bg-transparent border-b border-secondary-200 dark:border-slate-700">
                <TableHead class="py-3 px-6 font-medium text-secondary-600 dark:text-secondary-400">Nama</TableHead>
                <TableHead class="py-3 px-6 font-medium text-secondary-600 dark:text-secondary-400">Izin</TableHead>
                <TableHead class="py-3 px-6 font-medium text-secondary-600 dark:text-secondary-400 hidden md:table-cell">Jumlah Pengguna</TableHead>
                <TableHead class="py-3 px-6 font-medium text-secondary-600 dark:text-secondary-400 hidden md:table-cell">Tanggal Dibuat</TableHead>
                <TableHead class="py-3 px-6 font-medium text-secondary-600 dark:text-secondary-400 w-[60px]"></TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow v-for="role in filteredRoles" :key="role.id" class="border-b border-secondary-200/60 dark:border-slate-700/60 hover:bg-secondary-100/50 dark:hover:bg-slate-900/90">
                <TableCell class="py-3.5 px-6 align-middle font-medium capitalize">{{ role.name }}</TableCell>
                <TableCell class="py-3.5 px-6 align-middle">
                  <div class="flex gap-1.5 flex-wrap">
                    <Badge variant="outline" class="text-xs px-2 py-0.5">
                      {{ role.permissions.length }} izin
                    </Badge>
                    <Link v-if="role.permissions.length > 0" :href="route('admin.roles.show', role.id)" class="md:hidden">
                      <Badge variant="outline" class="text-xs px-2 py-0.5">
                        <span class="text-xs text-muted-foreground hover:text-foreground cursor-pointer" title="Lihat semua izin">
                          Lihat detail
                        </span>
                      </Badge>
                    </Link>
                    <div class="hidden md:flex md:gap-1.5 md:flex-wrap">
                      <Badge v-if="role.permissions.length > 3" variant="outline" class="text-xs px-2 py-0.5 bg-muted">
                        +{{ role.permissions.length }} izin
                      </Badge>
                      <Badge v-else v-for="permission in role.permissions.slice(0, 3)" :key="permission.id" variant="outline" class="text-xs px-2 py-0.5">
                        {{ permission.name }}
                      </Badge>
                    </div>
                  </div>
                </TableCell>
                <TableCell class="py-3.5 px-6 align-middle hidden md:table-cell text-center text-sm text-muted-foreground">
                  {{ role.users_count || 0 }}
                </TableCell>
                <TableCell class="py-3.5 px-6 align-middle hidden md:table-cell text-sm text-muted-foreground">{{ formatDate(role.created_at) }}</TableCell>
                <TableCell class="py-3.5 px-6 align-middle text-right">
                  <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                      <Button variant="action" size="icon" class="h-8 w-8 rounded-md">
                        <MoreHorizontal class="h-4 w-4" />
                        <span class="sr-only">Menu</span>
                      </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end" class="w-[160px]">
                      <Link :href="route('admin.roles.show', role.id)" class="w-full">
                        <DropdownMenuItem class="flex items-center gap-2 cursor-pointer py-1.5">
                          <Eye class="h-4 w-4" />
                          <span>Lihat Detail</span>
                        </DropdownMenuItem>
                      </Link>
                      <Link :href="route('admin.roles.edit', role.id)" class="w-full">
                        <DropdownMenuItem class="flex items-center gap-2 cursor-pointer py-1.5">
                          <Edit class="h-4 w-4" />
                          <span>Edit</span>
                        </DropdownMenuItem>
                      </Link>
                      <DropdownMenuItem class="flex items-center gap-2 cursor-pointer py-1.5" :disabled="role.name === 'admin'">
                        <Key class="h-4 w-4" />
                        <span>Kelola Izin</span>
                      </DropdownMenuItem>
                      <DropdownMenuItem 
                        class="flex items-center gap-2 cursor-pointer py-1.5" 
                        :disabled="role.name === 'admin'"
                        @click="showHapusDialog(role)"
                        variant="destructive"
                      >
                        <Trash2 class="h-4 w-4" />
                        <span>{{ loading && processingRole === role.id ? 'Memproses...' : 'Hapus' }}</span>
                      </DropdownMenuItem>
                    </DropdownMenuContent>
                  </DropdownMenu>
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </div>
        
        <!-- Pagination -->
        <div v-if="filteredRoles.length > 0" class="py-4 px-6 flex items-center justify-between border-t border-secondary-200 dark:border-slate-700">
          <div class="text-sm text-secondary-500 dark:text-secondary-400">
            <span v-if="props.roles.meta && props.roles.meta.total">
              Menampilkan {{ filteredRoles.length }} dari {{ props.roles.meta.total - 1 }} peran
            </span>
            <span v-else>
              Menampilkan {{ filteredRoles.length }} peran
            </span>
          </div>
          <div v-if="props.roles.links && props.roles.links.length > 2" class="flex gap-2">
            <Link 
              v-for="(link, i) in props.roles.links.slice(1, -1)" 
              :key="i"
              :href="link.url"
              class="px-3 py-1 rounded text-sm border border-slate-200 dark:border-slate-700"
              :class="{ 
                'bg-primary text-white border-primary': link.active,
                'cursor-pointer hover:bg-slate-100/50 dark:hover:bg-slate-800/50': !link.active && link.url
              }"
              v-html="link.label"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- Dialog Konfirmasi Hapus -->
    <ConfirmationDialog
      :open="showDeleteDialog"
      @update:open="showDeleteDialog = $event"
      title="Konfirmasi Penghapusan"
      dangerMode
      :icon="Trash2"
      :loading="loading"
      confirmLabel="Hapus"
      @confirm="hapusRole()"
    >
      <p class="mb-2">PERHATIAN: Tindakan ini tidak dapat dibatalkan!</p>
      <p>Apakah Anda yakin ingin menghapus peran <span class="font-semibold capitalize">{{ selectedRole?.name }}</span>?</p>
    </ConfirmationDialog>
  </AppLayout>
</template> 