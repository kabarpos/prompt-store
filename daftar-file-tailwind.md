# Daftar File untuk Penyesuaian Warna Tailwind CSS

## File Konfigurasi Utama
1. `tailwind.config.js` - File konfigurasi utama Tailwind CSS yang mendefinisikan warna-warna dasar

## File Style dan Theme
2. `resources/css/app.css` - File CSS utama yang mengimpor Tailwind CSS dan berisi kustomisasi dasar
3. `resources/js/components/ui/button/theme.ts` - Konfigurasi warna untuk komponen button

## Komponen UI dengan Penggunaan Warna
4. `resources/js/components/ui/button/Button.vue` - Komponen button yang menggunakan tema warna
5. `resources/js/components/ui/alert/` - Folder komponen alert yang mungkin menggunakan warna tema
6. `resources/js/components/ui/badge/` - Folder komponen badge yang mungkin menggunakan warna tema
7. `resources/js/components/ui/card/` - Folder komponen card yang mungkin menggunakan warna tema
8. `resources/js/components/ui/dialog/` - Folder komponen dialog yang mungkin menggunakan warna tema
9. `resources/js/components/ui/dropdown-menu/` - Folder komponen dropdown yang mungkin menggunakan warna tema
10. `resources/js/components/ui/form/` - Folder komponen form yang mungkin menggunakan warna tema
11. `resources/js/components/ui/toggle/` - Komponen toggle yang mungkin menggunakan warna tema
12. `resources/js/components/ui/switch/` - Folder komponen switch yang mungkin menggunakan warna tema

## File Utilitas dan Helper
13. `resources/js/lib/utils.ts` - Mungkin berisi helper functions untuk manipulasi class Tailwind CSS

## Layout Utama
14. `resources/js/layouts/app/AppSidebarLayout.vue` - Layout utama yang mungkin menggunakan warna tema

## Langkah Penyesuaian
1. Pertama, ubah konfigurasi warna di `tailwind.config.js`
2. Selanjutnya, sesuaikan file tema komponen di `resources/js/components/ui/button/theme.ts`
3. Sesuaikan style dasar di `resources/css/app.css` jika diperlukan
4. Periksa dan sesuaikan komponen-komponen UI yang menggunakan warna tema
5. Pastikan layout utama juga memperhatikan perubahan warna yang telah dilakukan 