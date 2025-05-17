# Dokumentasi Komponen Button

Komponen Button telah diperbarui dengan fitur `variant` dan `colorScheme` yang lebih kaya untuk menyediakan konsistensi desain di seluruh aplikasi.

## Penggunaan Dasar

```vue
<Button>Tombol Default</Button>
```

## Variant yang Tersedia

Button dapat menggunakan salah satu variant berikut:

- `default` - Tombol utama (primary)
- `secondary` - Tombol sekunder
- `outline` - Tombol dengan garis tepi saja
- `destructive` - Tombol untuk tindakan berbahaya
- `ghost` - Tombol transparan tanpa border
- `link` - Tombol yang terlihat seperti tautan
- `success` - Tombol untuk tindakan sukses
- `warning` - Tombol untuk tindakan peringatan

```vue
<Button variant="default">Default</Button>
<Button variant="secondary">Secondary</Button>
<Button variant="outline">Outline</Button>
<Button variant="destructive">Destructive</Button>
<Button variant="ghost">Ghost</Button>
<Button variant="link">Link</Button>
<Button variant="success">Success</Button>
<Button variant="warning">Warning</Button>
```

## Variant Action (Baru)

Variant action baru telah ditambahkan untuk menyediakan gaya yang konsisten untuk tombol aksi di seluruh aplikasi:

```vue
<Button variant="action">Action</Button>
<Button variant="action-secondary">Action Secondary</Button>
<Button variant="action-danger">Action Danger</Button>
<Button variant="action-success">Action Success</Button>
<Button variant="action-warning">Action Warning</Button>
<Button variant="action-outline">Action Outline</Button>
```

## Color Scheme (Tema Warna)

Selain variant, Button juga mendukung `colorScheme` yang memungkinkan pengaturan warna secara langsung:

```vue
<Button colorScheme="primary">Primary</Button>
<Button colorScheme="secondary">Secondary</Button>
<Button colorScheme="success">Success</Button>
<Button colorScheme="warning">Warning</Button>
<Button colorScheme="danger">Danger</Button>
<Button colorScheme="outline">Outline</Button>
<Button colorScheme="ghost">Ghost</Button>
<Button colorScheme="link">Link</Button>
```

## Color Scheme Admin (Baru)

Tema khusus untuk panel admin telah ditambahkan:

```vue
<Button colorScheme="admin">Admin Primary</Button>
<Button colorScheme="admin-secondary">Admin Secondary</Button>
<Button colorScheme="admin-danger">Admin Danger</Button>
<Button colorScheme="admin-outline">Admin Outline</Button>
```

## Ukuran

Button mendukung beberapa ukuran yang dapat diatur melalui prop `size`:

```vue
<Button size="default">Default Size</Button>
<Button size="sm">Small</Button>
<Button size="lg">Large</Button>
<Button size="icon">Icon</Button>
```

## Kustomisasi Tambahan

Button menerima prop `class` untuk menambahkan kelas Tailwind CSS tambahan:

```vue
<Button class="w-full rounded-full">Tombol Kustom</Button>
```

## Menerapkan Best Practice dengan Button

Untuk menjaga konsistensi desain, sebaiknya:

1. Gunakan `variant` dan `colorScheme` yang sudah tersedia, hindari hardcoding warna.
2. Untuk panel admin, gunakan tema admin (`colorScheme="admin"`, dll).
3. Untuk aksi utama, gunakan variant `action` atau `default`.
4. Untuk aksi berbahaya, gunakan variant `destructive` atau `action-danger`.
5. Untuk aksi sekunder, gunakan variant `outline` atau `ghost`.

## Migrasi dari Styling Hardcoded

Jika Anda memiliki tombol dengan styling hardcoded seperti ini:

```vue
<button class="bg-blue-600 hover:bg-blue-700 text-white h-9 px-4 py-2 rounded-md">
  Tombol Lama
</button>
```

Migrasikan ke penggunaan komponen Button:

```vue
<Button variant="action">Tombol Baru</Button>
```

Atau jika membutuhkan ukuran khusus:

```vue
<Button variant="action" class="h-9">Tombol Baru</Button>
``` 