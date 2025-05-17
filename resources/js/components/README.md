# Komponen UI Dilogif

Direktori ini berisi semua komponen UI yang digunakan di aplikasi Dilogif. Silakan gunakan komponen-komponen ini untuk memastikan konsistensi tampilan di seluruh aplikasi.

## Komponen Dasar

### Button

Gunakan komponen `Button` untuk semua tombol dalam aplikasi.

```html
<Button>Default</Button>
<Button variant="primary">Primary</Button>
<Button variant="outline">Outline</Button>
<Button variant="destructive">Destructive</Button>
<Button variant="ghost">Ghost</Button>
<Button variant="link">Link</Button>
<Button disabled>Disabled</Button>
```

**Props:**
- `variant`: default, primary, outline, destructive, ghost, link
- `size`: default, sm, lg, icon
- `colorScheme`: primary, secondary, danger, success, warning

### Input

```html
<Input placeholder="Email" />
<Input disabled placeholder="Disabled" />
<Input type="password" placeholder="Password" />
```

### Textarea

```html
<Textarea placeholder="Deskripsi" />
<Textarea disabled placeholder="Disabled" />
```

### Badge

```html
<Badge>Default</Badge>
<Badge variant="outline">Outline</Badge>
<Badge variant="destructive">Destructive</Badge>
```

### Card

```html
<Card>
  <CardHeader>
    <CardTitle>Judul Card</CardTitle>
    <CardDescription>Deskripsi card</CardDescription>
  </CardHeader>
  <CardContent>
    Konten card...
  </CardContent>
  <CardFooter>
    <Button>Tombol</Button>
  </CardFooter>
</Card>
```

## Komponen Tabel

Untuk tampilan data tabular, gunakan komponen `AdminTable`:

```html
<AdminTable 
  :columns="columns" 
  :data="dataArray" 
  :loading="isLoading"
  emptyMessage="Tidak ada data"
>
  <TableRow v-for="item in data" :key="item.id">
    <TableCell>{{ item.name }}</TableCell>
    <TableCell>{{ item.email }}</TableCell>
  </TableRow>
</AdminTable>
```

## Komponen Dialog

### ConfirmationDialog

Untuk konfirmasi tindakan pengguna:

```html
<ConfirmationDialog
  :open="showDeleteDialog"
  @update:open="showDeleteDialog = $event"
  title="Konfirmasi Penghapusan"
  dangerMode
  :icon="TrashIcon"
  confirmLabel="Hapus"
  @confirm="deleteItem()"
>
  <p>Apakah Anda yakin ingin menghapus item ini?</p>
</ConfirmationDialog>
```

## Form & Validasi

### Form Error

Untuk menampilkan pesan error pada form:

```html
<div>
  <Label for="email">Email</Label>
  <Input id="email" v-model="form.email" />
  <InputError :message="form.errors.email" />
</div>
```

## Styling Komponen

Semua komponen mengikuti tema aplikasi yang didefinisikan melalui CSS Variables. Untuk detailnya, lihat dokumentasi [Styling Guide](../docs/STYLING-GUIDE.md).

### Tips Styling:

1. Gunakan variasi yang sudah ada (primary, secondary, dll) daripada membuat styling custom
2. Saat diperlukan custom styling, gunakan `class` prop pada komponen
3. Untuk dark mode, pastikan menggunakan kombinasi light/dark mode: `class="bg-white dark:bg-slate-800"`

## Panduan & Best Practices

1. **Gunakan komponen yang ada**: Hindari membuat komponen baru jika sudah tersedia
2. **Konsistensi prop**: Untuk tombol yang melakukan aksi berbahaya, selalu gunakan `variant="destructive"`
3. **Props vs Class**: Jika ada prop yang tersedia (misal variant), utamakan menggunakan prop daripada kelas custom

---

Untuk panduan lengkap mengenai styling dan design system, lihat [Styling Guide](../docs/STYLING-GUIDE.md) 