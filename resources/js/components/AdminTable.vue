<script setup>
import { computed } from 'vue';
import { Table, TableHeader, TableHead, TableBody, TableRow, TableCell } from '@/components/ui/table';
import Pagination from '@/components/Pagination.vue';
import { SearchIcon } from 'lucide-vue-next';

const props = defineProps({
  columns: {
    type: Array,
    required: true
  },
  data: {
    type: Object,
    required: true
  },
  loading: {
    type: Boolean,
    default: false
  },
  emptyMessage: {
    type: String,
    default: 'Tidak ada data yang tersedia'
  },
  emptyIcon: {
    type: Object,
    default: () => SearchIcon
  },
  containerClass: {
    type: String,
    default: ''
  }
});

const emit = defineEmits(['row-click']);

const items = computed(() => props.data.data || []);
const hasPagination = computed(() => props.data.links && props.data.links.length > 2);

const handleRowClick = (item) => {
  emit('row-click', item);
};
</script>

<template>
  <div 
    class="bg-white dark:bg-slate-800 text-secondary-900 dark:text-white rounded-xl shadow-lg border border-slate-200 dark:border-slate-700 overflow-hidden"
    :class="containerClass"
  >
    <div class="overflow-x-auto">
      <Table class="w-full">
        <TableHeader>
          <TableRow class="hover:bg-transparent border-b border-slate-200 dark:border-slate-700">
            <TableHead v-for="(column, index) in columns" :key="index" :class="column.headerClass">
              {{ column.label }}
            </TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableRow v-if="loading">
            <TableCell :colspan="columns.length" class="py-16 text-center">
              <div class="flex flex-col items-center justify-center gap-4">
                <div class="animate-spin rounded-full h-12 w-12 border-4 border-t-4 border-primary"></div>
                <span class="text-base font-medium text-secondary-500 dark:text-secondary-400">Memuat data...</span>
              </div>
            </TableCell>
          </TableRow>
          
          <TableRow v-else-if="items.length === 0">
            <TableCell :colspan="columns.length" class="py-12 text-center">
              <div class="flex flex-col items-center justify-center gap-2">
                <div class="bg-secondary-100 dark:bg-slate-800 rounded-full p-3">
                  <SearchIcon class="h-6 w-6 text-secondary-500 dark:text-secondary-400" />
                </div>
                <span class="text-lg font-medium text-secondary-900 dark:text-white">{{ emptyMessage }}</span>
                <span class="text-sm text-secondary-500 dark:text-secondary-400">Coba ubah filter atau tambahkan data baru</span>
              </div>
            </TableCell>
          </TableRow>
          
          <slot v-else></slot>
        </TableBody>
      </Table>
    </div>
    
    <!-- Pagination -->
    <div v-if="hasPagination && items.length > 0" class="py-4 px-6 flex items-center justify-between border-t border-secondary-200 dark:border-slate-700">
      <div class="text-sm text-secondary-500 dark:text-secondary-400" v-if="data.meta && data.meta.total">
        Menampilkan {{ items.length }} dari {{ data.meta.total }} item
      </div>
      <Pagination :links="data.links" />
    </div>
  </div>
</template> 