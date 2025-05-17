# Panduan Styling Dilogif App

Dokumen ini merupakan panduan mengenai sistem styling aplikasi Dilogif, yang dibuat untuk memastikan konsistensi dan kemudahan pengelolaan kode CSS.

## Pendekatan Styling

Aplikasi ini menggunakan pendekatan **Theme-First Tailwind CSS** yang mengutamakan variabel tema terpusat dengan implementasi melalui Tailwind CSS.

### Prinsip Utama Styling

1. **Satu Sumber Kebenaran**: Semua nilai styling dasar (warna, font, dll) didefinisikan di `app.css` sebagai CSS Variables
2. **Tailwind Sebagai Interface**: Variabel CSS diekspos sebagai tema Tailwind melalui `tailwind.config.js`
3. **Layer System**: Style dipisahkan melalui sistem layer (`@layer`) untuk menghindari konflik spesifisitas

## Struktur File Styling

### 1. Konfigurasi Tema: CSS Variables (`app.css`)

File `app.css` berisi deklarasi semua variabel CSS yang menjadi fondasi sistem tema:

```css
:root {
  /* Base Variables */
  --font-family-base: 'Manrope', ui-sans-serif, system-ui, sans-serif;
  --border-color-base: #e2e8f0;
  --radius: 0.5rem;
  --transition-base: 150ms;
  
  /* Sidebar Component */
  --sidebar-accent: var(--primary-600);
  --sidebar-hover: rgba(203, 213, 225, 0.1);
  --sidebar-bg-hover: var(--slate-100);
  --sidebar-dark-bg-hover: var(--slate-700);
  /* ... etc */
}

.dark {
  /* Dark Theme Overrides */
  --border-color-base: #334155;
  --sidebar-bg-hover: var(--slate-700);
  --sidebar-dark-bg-hover: rgba(203, 213, 225, 0.1);
  /* ... etc */
}
```

### 2. Integrasi dengan Tailwind (`tailwind.config.js`)

Variabel CSS diekspos sebagai tema Tailwind untuk akses melalui kelas-kelas utility:

```js
theme: {
  extend: {
    colors: {
      sidebar: {
        accent: {
          DEFAULT: 'var(--sidebar-accent)',
          foreground: 'var(--sidebar-accent-foreground)',
        },
        bgHover: 'var(--sidebar-bg-hover)',
        darkBgHover: 'var(--sidebar-dark-bg-hover)',
        /* ... etc */
      },
    },
    transitionDuration: {
      DEFAULT: 'var(--transition-base)',
    },
  },
},
```

### 3. Layer-based Components (`app.css`)

Komponen khusus yang tidak bisa dibuat hanya dengan utility diletakkan dalam `@layer components`:

```css
@layer components {
  .sidebar-menu-button {
    @apply flex w-full items-center gap-2 rounded-md p-2;
    @apply text-sm text-slate-700 dark:text-slate-300;
    @apply hover:bg-sidebar-bg-hover dark:hover:bg-sidebar-dark-bg-hover;
    /* ... etc */
  }
}
```

## Cara Menggunakan Sistem Styling

### 1. Menggunakan Warna Tema

Gunakan kelas warna dari tema Tailwind, bukan hardcoded values:

**❌ TIDAK DISARANKAN**:
```html
<div class="bg-[#0ea5e9] text-white">...</div>
```

**✅ DISARANKAN**:
```html
<div class="bg-primary-500 text-white">...</div>
```

### 2. Menggunakan Variabel Komponen

Untuk styling komponen yang lebih kompleks, gunakan tema komponen yang sudah didefinisikan:

**❌ TIDAK DISARANKAN**:
```html
<div class="bg-indigo-600 hover:bg-indigo-700 text-white">...</div>
```

**✅ DISARANKAN**:
```html
<div class="bg-sidebar-accent text-sidebar-accent-foreground">...</div>
```

### 3. Dark Mode Support

Semua komponen mendukung dark mode melalui kelas Tailwind dan variabel tema:

```html
<div class="bg-white dark:bg-slate-800 text-gray-900 dark:text-gray-100">...</div>
```

## Pola Styling Umum

### Tabel

```html
<table class="w-full border-collapse">
  <thead class="bg-slate-100 dark:bg-slate-700">
    <tr>
      <th class="px-4 py-2 text-left text-sm font-semibold">...</th>
    </tr>
  </thead>
  <tbody class="bg-white dark:bg-slate-800">
    <tr class="border-b border-slate-200 dark:border-slate-700">...</tr>
  </tbody>
</table>
```

### Card / Container

```html
<div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
  ...
</div>
```

### Button

```html
<!-- Gunakan komponen Button.vue yang sudah ada -->
<Button variant="primary">Button Text</Button>
<Button variant="destructive">Hapus</Button>
<Button variant="outline">Kembali</Button>
```

## Cara Mengubah Tema

Untuk mengubah tema secara global:

1. Ubah variabel CSS di `app.css`
2. Tailwind akan menggunakan nilai-nilai tersebut secara otomatis

Contoh mengubah warna primary:

```css
:root {
  --sidebar-accent: var(--blue-500); /* Mengubah dari nilai default */
}
```

## Praktik Terbaik

1. **Hindari Hardcoded Values**: Jangan gunakan nilai hex, rgb, atau hsl secara langsung
2. **Gunakan Variabel Tema**: Selalu gunakan variabel tema untuk warna, spasi, dll
3. **Konsistensi Penamaan**: Ikuti konvensi penamaan yang sudah ada
4. **Gunakan Komponen**: Untuk UI yang kompleks, buat dan gunakan komponen Vue
5. **Minimize Custom CSS**: Gunakan utility Tailwind sebisa mungkin
6. **Hindari !important**: Gunakan cascade CSS yang tepat, bukan !important
7. **Perhatikan Mobile First**: Desain untuk mobile terlebih dahulu, baru desktop

## Dos and Don'ts

### Do:
- ✅ Gunakan variabel tema (`var(--sidebar-accent)`, `bg-sidebar-accent`)
- ✅ Terapkan dark mode dengan prefiks `dark:`
- ✅ Kelompokkan styling terkait dalam satu komponen
- ✅ Gunakan komponen yang sudah ada (Button, Card, dll)

### Don't:
- ❌ Hardcode nilai warna (`#0ea5e9`, `rgb(255,0,0)`)
- ❌ Duplikasi styling yang sudah ada
- ❌ Gunakan `!important` kecuali benar-benar diperlukan
- ❌ Buat variasi styling untuk kasus spesifik yang dapat digeneralisasi
- ❌ Membuat class kustom yang memerlukan modifier (seperti hover:) tanpa mendefinisikannya dengan benar

---

Dokumen ini akan terus diperbarui seiring perkembangan aplikasi. Jika ada pertanyaan atau saran, silakan ajukan di Issue GitHub. 