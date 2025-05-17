# Panduan Standarisasi Tabel Admin

Dokumen ini berisi panduan penggunaan komponen tabel terpusat untuk halaman admin di aplikasi Dilogif.

## Pendahuluan

Sebelumnya, komponen tabel pada setiap halaman admin mengimplementasikan styling secara terpisah dan tidak konsisten. Panduan ini menjelaskan sistem terpusat yang telah diimplementasikan untuk menstandarisasi semua tabel admin.

## Komponen Dasar

### 1. Komponen Shadcn UI

Komponen tabel Shadcn UI telah distandarisasi dengan style yang konsisten:

```
- Table
- TableHeader
- TableHead
- TableBody
- TableRow
- TableCell
```

Komponen-komponen ini berada di `resources/js/components/ui/table/` dan memiliki styling default yang seragam.

### 2. Komponen Higher-Level

#### AdminTable.vue

Komponen `AdminTable.vue` mengabstraksi tabel dasar Shadcn UI dengan fitur tambahan:

- Loading state
- Empty state dengan pesan kustom
- Pagination
- Dukungan untuk kolom yang responsif

```vue
<AdminTable 
  :columns="columns" 
  :data="users" 
  :loading="isFiltering"
  emptyMessage="Tidak ada pengguna ditemukan"
>
  <!-- Definisikan konten baris tabel disini -->
  <TableRow v-for="item in users.data" :key="item.id">
    <!-- Cell contents -->
  </TableRow>
</AdminTable>
```

**Props:**

| Prop | Tipe | Deskripsi |
|------|------|-----------|
| columns | Array | Array objek yang berisi definisi kolom tabel, dengan properti `label` dan `headerClass` |
| data | Object | Data yang diambil dari API, dengan struktur Laravel pagination (data, links, meta) |
| loading | Boolean | Menampilkan indikator loading jika true |
| emptyMessage | String | Pesan yang ditampilkan saat tidak ada data |
| emptyIcon | Function | Komponen ikon yang ditampilkan pada empty state |
| containerClass | String | Class tambahan untuk container tabel |

#### StatusBadge.vue

Komponen `StatusBadge.vue` untuk menampilkan status dengan styling konsisten:

```vue
<StatusBadge 
  status="active" 
  :statusMap="{
    active: 'Aktif',
    inactive: 'Tidak Aktif'
  }"
/>
```

**Props:**

| Prop | Tipe | Deskripsi |
|------|------|-----------|
| status | String | Kode status (active, inactive, blocked, dll.) |
| statusMap | Object | Map untuk mentranslasi kode status menjadi label yang dapat dibaca |
| showDot | Boolean | Tampilkan dot indikator di samping label status |

## Cara Penggunaan

### 1. Definisikan Kolom

```js
const columns = [
  { label: 'Nama' },
  { label: 'Email' },
  { label: 'Status' },
  { label: 'Peran', headerClass: 'hidden md:table-cell' },
  { label: 'Tanggal', headerClass: 'hidden sm:table-cell' },
  { label: '', headerClass: 'w-[60px]' } // Kolom aksi
];
```

### 2. Definisikan Status Map (opsional)

```js
const statusMap = {
  active: 'Aktif',
  inactive: 'Tidak Aktif',
  blocked: 'Diblokir'
};
```

### 3. Gunakan AdminTable dalam Template

```vue
<AdminTable 
  :columns="columns" 
  :data="items" 
  :loading="loading"
  emptyMessage="Tidak ada data ditemukan"
>
  <TableRow v-for="item in items.data" :key="item.id">
    <TableCell>{{ item.name }}</TableCell>
    <TableCell>{{ item.email }}</TableCell>
    <TableCell>
      <StatusBadge :status="item.status" :statusMap="statusMap" />
    </TableCell>
    <TableCell class="hidden md:table-cell">{{ item.role }}</TableCell>
    <TableCell class="hidden sm:table-cell">{{ formatDate(item.created_at) }}</TableCell>
    <TableCell>
      <!-- Action buttons/dropdown -->
    </TableCell>
  </TableRow>
</AdminTable>
```

## Best Practices

### Kolom Responsif

Gunakan utility class Tailwind seperti `hidden md:table-cell` untuk kolom yang seharusnya hanya muncul pada layar yang lebih besar:

```vue
<TableCell class="hidden md:table-cell">{{ item.role }}</TableCell>
```

Sesuaikan juga pada definisi kolom dengan `headerClass`:

```js
{ label: 'Peran', headerClass: 'hidden md:table-cell' }
```

### Empty States

Gunakan pesan yang jelas dan deskriptif untuk empty state:

```js
emptyMessage="Tidak ada data kategori ditemukan"
```

### Styling Konsisten

Tambahkan class tambahan ke TableCell hanya ketika benar-benar diperlukan. AdminTable dan komponennya sudah memiliki styling default yang konsisten.

## Migrasi dari Hardcoded Tabel

Untuk migrasi dari hardcoded tabel:

1. Import komponen yang diperlukan:
   ```js
   import AdminTable from '@/components/AdminTable.vue';
   import StatusBadge from '@/components/StatusBadge.vue';
   import { TableRow, TableCell } from '@/components/ui/table';
   ```

2. Definisikan kolom dan statusMap

3. Ganti implementasi tabel dengan AdminTable

4. Ubah status badge custom dengan StatusBadge

## Contoh Implementasi

### Contoh pada Halaman Produk

```vue
<script setup>
import { TableRow, TableCell } from '@/components/ui/table';
import AdminTable from '@/components/AdminTable.vue';
import StatusBadge from '@/components/StatusBadge.vue';

// Definisikan kolom
const columns = [
    { label: 'Gambar' },
    { label: 'Nama' },
    { label: 'Kategori' },
    { label: 'Harga' },
    { label: 'URL' },
    { label: 'Status' },
    { label: 'Tindakan', headerClass: 'text-right' }
];

// Status map
const statusMap = {
    '1': 'Aktif',
    '0': 'Nonaktif'
};
</script>

<template>
  <AdminTable 
    :columns="columns" 
    :data="products" 
    :loading="loading"
    emptyMessage="Tidak ada produk ditemukan"
  >
    <TableRow v-for="product in products.data" :key="product.id">
      <TableCell>
        <img :src="'/storage/' + product.featured_image" :alt="product.name" class="w-16 h-16 object-cover rounded-md" />
      </TableCell>
      <TableCell>{{ product.name }}</TableCell>
      <TableCell>
        <Badge v-if="product.category" variant="outline" class="px-2.5 py-0.5 text-xs font-medium">
          {{ product.category.name }}
        </Badge>
        <Badge v-else variant="outline" class="px-2.5 py-0.5 text-xs font-medium bg-amber-100 text-amber-800">
          Kategori tidak tersedia
        </Badge>
      </TableCell>
      <TableCell>{{ formatPrice(product.price) }}</TableCell>
      <TableCell>
        <!-- URL Cell Content -->
      </TableCell>
      <TableCell>
        <StatusBadge :status="product.is_active ? '1' : '0'" :statusMap="statusMap" />
      </TableCell>
      <TableCell class="text-right">
        <!-- Actions Dropdown -->
      </TableCell>
    </TableRow>
  </AdminTable>
</template>
```

### StatusBadge Standalone

Komponen ini juga dapat digunakan di luar tabel, misalnya pada halaman detail:

```vue
<div class="flex items-start gap-3">
  <div class="flex-1">
    <p class="text-sm text-gray-500">Status</p>
    <StatusBadge :status="product.is_active ? 'active' : 'inactive'" />
  </div>
</div>
```

## Dukungan Browser

Komponen ini didukung oleh semua browser modern yang mendukung Tailwind CSS dan Vue 3. 