<template>
  <Head title="Template WhatsApp" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6">
      <!-- Header dengan judul dan tombol tambah -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h1 class="text-2xl font-bold text-secondary-900 dark:text-white">Template WhatsApp</h1>
        <div class="flex gap-2">
          <Link :href="route('admin.notifications.create')">
            <Button variant="action" class="flex items-center gap-1.5">
              <PlusIcon class="h-4 w-4" />
              Tambah Template
            </Button>
          </Link>
        </div>
      </div>
      
      <!-- Template untuk Customer -->
      <div class="space-y-4">
        <h2 class="text-lg font-medium text-secondary-900 dark:text-white">Template Pesan untuk Pelanggan</h2>
        
        <AdminTable
          :columns="columns"
          :data="{ data: customerTemplates }"
          :loading="false"
          emptyMessage="Belum ada template untuk pelanggan"
        >
          <TableRow 
            v-for="record in customerTemplates" 
            :key="record.id" 
            class="hover:bg-slate-50 dark:hover:bg-slate-800/60"
          >
            <TableCell>{{ record.name }}</TableCell>
            <TableCell>{{ triggerStatusOptions[record.trigger_status] }}</TableCell>
            <TableCell>
              <Badge :variant="record.is_active ? 'success' : 'destructive'" class="px-2 py-1">
                {{ record.is_active ? 'Aktif' : 'Nonaktif' }}
              </Badge>
            </TableCell>
            <TableCell>{{ formatDate(record.updated_at) }}</TableCell>
            <TableCell class="text-right">
              <DropdownMenu>
                <DropdownMenuTrigger>
                  <Button variant="outline" size="icon" class="h-8 w-8">
                    <EllipsisVerticalIcon class="h-4 w-4" />
                  </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent>
                  <DropdownMenuItem @click="toggleActive(record)">
                    <div class="flex items-center gap-2">
                      <component :is="record.is_active ? EyeOffIcon : EyeIcon" class="h-4 w-4" />
                      <span>{{ record.is_active ? 'Nonaktifkan' : 'Aktifkan' }}</span>
                    </div>
                  </DropdownMenuItem>
                  <DropdownMenuItem @click="() => visitPage('show', record)">
                    <div class="flex items-center gap-2">
                      <EyeIcon class="h-4 w-4" />
                      <span>Lihat Detail</span>
                    </div>
                  </DropdownMenuItem>
                  <DropdownMenuItem @click="() => visitPage('edit', record)">
                    <div class="flex items-center gap-2">
                      <PencilIcon class="h-4 w-4" />
                      <span>Edit</span>
                    </div>
                  </DropdownMenuItem>
                  <DropdownMenuItem @click="() => confirmDelete(record)" class="text-red-500 focus:text-red-500">
                    <div class="flex items-center gap-2">
                      <TrashIcon class="h-4 w-4" />
                      <span>Hapus</span>
                    </div>
                  </DropdownMenuItem>
                </DropdownMenuContent>
              </DropdownMenu>
            </TableCell>
          </TableRow>
        </AdminTable>
      </div>
      
      <!-- Template untuk Admin -->
      <div class="space-y-4">
        <h2 class="text-lg font-medium text-secondary-900 dark:text-white">Template Pesan untuk Admin</h2>
        
        <AdminTable
          :columns="columns"
          :data="{ data: adminTemplates }"
          :loading="false"
          emptyMessage="Belum ada template untuk admin"
        >
          <TableRow 
            v-for="record in adminTemplates" 
            :key="record.id" 
            class="hover:bg-slate-50 dark:hover:bg-slate-800/60"
          >
            <TableCell>{{ record.name }}</TableCell>
            <TableCell>{{ triggerStatusOptions[record.trigger_status] }}</TableCell>
            <TableCell>
              <Badge :variant="record.is_active ? 'success' : 'destructive'" class="px-2 py-1">
                {{ record.is_active ? 'Aktif' : 'Nonaktif' }}
              </Badge>
            </TableCell>
            <TableCell>{{ formatDate(record.updated_at) }}</TableCell>
            <TableCell class="text-right">
              <DropdownMenu>
                <DropdownMenuTrigger>
                  <Button variant="outline" size="icon" class="h-8 w-8">
                    <EllipsisVerticalIcon class="h-4 w-4" />
                  </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent>
                  <DropdownMenuItem @click="toggleActive(record)">
                    <div class="flex items-center gap-2">
                      <component :is="record.is_active ? EyeOffIcon : EyeIcon" class="h-4 w-4" />
                      <span>{{ record.is_active ? 'Nonaktifkan' : 'Aktifkan' }}</span>
                    </div>
                  </DropdownMenuItem>
                  <DropdownMenuItem @click="() => visitPage('show', record)">
                    <div class="flex items-center gap-2">
                      <EyeIcon class="h-4 w-4" />
                      <span>Lihat Detail</span>
                    </div>
                  </DropdownMenuItem>
                  <DropdownMenuItem @click="() => visitPage('edit', record)">
                    <div class="flex items-center gap-2">
                      <PencilIcon class="h-4 w-4" />
                      <span>Edit</span>
                    </div>
                  </DropdownMenuItem>
                  <DropdownMenuItem @click="() => confirmDelete(record)" class="text-red-500 focus:text-red-500">
                    <div class="flex items-center gap-2">
                      <TrashIcon class="h-4 w-4" />
                      <span>Hapus</span>
                    </div>
                  </DropdownMenuItem>
                </DropdownMenuContent>
              </DropdownMenu>
            </TableCell>
          </TableRow>
        </AdminTable>
      </div>
    </div>
  </AppLayout>
  
  <!-- Dialog Konfirmasi Hapus -->
  <ConfirmationDialog
    :open="showDeleteDialog"
    @update:open="showDeleteDialog = $event"
    title="Konfirmasi Penghapusan"
    dangerMode
    :icon="Trash2"
    :loading="loading"
    confirmLabel="Hapus"
    @confirm="deleteTemplate()"
  >
    <p class="mb-2 text-secondary-900 dark:text-secondary-200">
      Apakah Anda yakin ingin menghapus template {{ templateToDelete?.name }}?
    </p>
  </ConfirmationDialog>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { EyeIcon, EyeOffIcon, PencilIcon, TrashIcon, PlusIcon, EllipsisVerticalIcon, Trash2 } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuTrigger, DropdownMenuContent, DropdownMenuItem } from '@/components/ui/dropdown-menu';
import { Badge } from '@/components/ui/badge';
import AppLayout from '@/layouts/AppLayout.vue';
import AdminTable from '@/components/AdminTable.vue';
import { Table, TableHeader, TableRow, TableHead, TableBody, TableCell } from '@/components/ui/table';
import { toast } from 'vue-sonner';
import ConfirmationDialog from '@/components/ui/ConfirmationDialog.vue';

const props = defineProps({
  customerTemplates: Array,
  adminTemplates: Array,
  availableVariables: Object,
  triggerStatusOptions: Object,
});

// Breadcrumbs
const breadcrumbs = [
  {
    title: 'Admin',
    href: route('admin.dashboard')
  },
  {
    title: 'Template WhatsApp',
    href: route('admin.notifications.index')
  }
];

// Format date helper
const formatDate = (dateString) => {
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  }).format(date);
};

// Admin table columns
const columns = [
  { label: 'Nama Template' },
  { label: 'Status Pemicu' },
  { label: 'Status' },
  { label: 'Terakhir Diperbarui' },
  { label: 'Aksi', headerClass: 'text-right' }
];

// Fungsi untuk toggle status aktif
const toggleActive = (template) => {
  router.patch(route('admin.notifications.whatsapp-templates.toggle-active', template.id));
};

// Fungsi untuk navigasi ke halaman detail atau edit
const visitPage = (type, template) => {
  if (type === 'show') {
    router.visit(route('admin.notifications.show', template.id));
  } else if (type === 'edit') {
    router.visit(route('admin.notifications.edit', template.id));
  }
};

// Dialog konfirmasi untuk hapus
const showDeleteDialog = ref(false);
const templateToDelete = ref(null);
const loading = ref(false);

// Fungsi untuk hapus template
const confirmDelete = (template) => {
  templateToDelete.value = template;
  showDeleteDialog.value = true;
};

const deleteTemplate = () => {
  if (!templateToDelete.value) return;
  
  loading.value = true;
  
  useForm({}).delete(route('admin.notifications.whatsapp-templates.destroy', templateToDelete.value.id), {
    onSuccess: () => {
      toast.success('Template berhasil dihapus');
      showDeleteDialog.value = false;
      templateToDelete.value = null;
    },
    onError: () => {
      toast.error('Gagal menghapus template');
    },
    onFinish: () => {
      loading.value = false;
    }
  });
};
</script> 