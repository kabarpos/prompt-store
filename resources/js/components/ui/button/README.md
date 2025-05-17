# Button Component

Button component adalah komponen UI dasar yang digunakan dalam aplikasi untuk menjalankan aksi. Komponen ini mendukung berbagai variant, size, dan tema yang dapat dikustomisasi.

## Penggunaan Dasar

```vue
<Button>Default Button</Button>
<Button variant="action">Action Button</Button>
<Button variant="action-outline">Action Outline Button</Button>
```

## Variant

Button component mendukung beberapa variant berikut:

| Variant           | Deskripsi                                                   |
|-------------------|-------------------------------------------------------------|
| default           | Styling default button                                      |
| action            | Tombol aksi utama (primary) dengan warna biru               |
| action-secondary  | Tombol aksi sekunder dengan warna abu-abu gelap             |
| action-danger     | Tombol aksi berbahaya dengan warna merah                    |
| action-success    | Tombol aksi sukses dengan warna hijau                       |
| action-warning    | Tombol aksi peringatan dengan warna kuning                  |
| action-outline    | Tombol aksi dengan outline tanpa background                 |
| destructive       | Tombol aksi berbahaya dengan warna merah (legacy)           |
| outline           | Tombol dengan outline (legacy)                              |
| secondary         | Tombol sekunder (legacy)                                    |
| ghost             | Tombol transparan tanpa background atau border              |
| link              | Tombol styling seperti link dengan underline                |
| success           | Tombol sukses (legacy)                                      |
| warning           | Tombol peringatan (legacy)                                  |

## Size

Button component mendukung berbagai ukuran:

| Size              | Deskripsi                                                   |
|-------------------|-------------------------------------------------------------|
| default           | Ukuran default (h-9 px-4 py-2)                              |
| sm                | Ukuran kecil (h-8)                                          |
| lg                | Ukuran besar (h-10)                                         |
| icon              | Ukuran untuk tombol ikon (size-9)                           |

## Contoh Penggunaan

### Action Button (Direkomendasikan)

```vue
<!-- Tombol aksi primary -->
<Button variant="action" class="flex items-center gap-1.5">
  <PlusIcon class="h-4 w-4" />
  Tambah Item
</Button>

<!-- Tombol aksi secondary -->
<Button variant="action-secondary">
  Simpan Sebagai Draft
</Button>

<!-- Tombol aksi danger -->
<Button variant="action-danger">
  Hapus
</Button>

<!-- Tombol outline -->
<Button variant="action-outline">
  Batal
</Button>
```

### Tombol dengan Loading State

```vue
<Button variant="action" :loading="isLoading">
  Simpan Data
</Button>
```

### Tombol dengan Ikon

```vue
<Button variant="action" size="icon">
  <PlusIcon class="h-4 w-4" />
</Button>
```

## CSS Classes

Button component menggabungkan classes dari buttonVariants (CVA) dan buttonTheme. Jangan menggunakan kelas-kelas yang mengubah warna secara inline seperti `!bg-red-500` karena ini akan mengacaukan tema button. Sebagai gantinya, gunakan variant yang sesuai. 