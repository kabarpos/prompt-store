# Panduan Integrasi Auto Respons WhatsApp

## Ringkasan

Fitur auto respons WhatsApp memungkinkan sistem secara otomatis mengirimkan notifikasi ke pelanggan melalui WhatsApp pada momen-momen penting dalam proses order, seperti:

1. Saat order dibuat (invoice/tagihan)
2. Saat status order berubah
3. Saat pembayaran dikonfirmasi

## Konfigurasi

Untuk menggunakan fitur ini, pastikan Anda telah melakukan konfigurasi di file `.env`:

```
DRIPSENDER_API_KEY=your_api_key_here
DRIPSENDER_WEBHOOK_URL=your_webhook_url_here
```

Anda telah menerima API key dan webhook URL dari vendor WhatsApp marketing tool (DripSender). Contoh:
- API Key: `f75acb01-32d8-4448-8086-4aedce9f1d6f`
- Webhook URL: `https://adam.dripsender.id:14773/api/integration/3db3e64a-5b92-48a3-b10c-467ad5eaa7be`

## Cara Kerja

1. **Notifikasi Order Dibuat**
   - Saat pelanggan melakukan checkout dan order berhasil dibuat
   - Sistem akan otomatis mengirim detail order ke nomor WhatsApp pelanggan
   - Pesan berisi informasi: nomor pesanan, tanggal, item yang dibeli, total, dan status

2. **Notifikasi Perubahan Status Order**
   - Saat admin mengubah status order (processing, review, completed, cancelled)
   - Pelanggan menerima notifikasi perubahan status dengan deskripsi status terbaru

3. **Notifikasi Pembayaran Dikonfirmasi**
   - Saat admin memverifikasi pembayaran pelanggan
   - Pelanggan menerima konfirmasi bahwa pembayaran telah diterima

## Format Nomor WhatsApp

Sistem dirancang untuk menangani berbagai format nomor telepon:
- Konversi otomatis dari format lokal ke format internasional
- Contoh: `081234567890` dikonversi menjadi `6281234567890`

## Pengujian

Untuk menguji integrasi:

1. Buat pesanan baru dengan nomor WhatsApp yang valid
2. Verifikasi pesan diterima di WhatsApp pelanggan
3. Ubah status order dari panel admin dan verifikasi notifikasi diterima
4. Verifikasi pembayaran dan pastikan pelanggan menerima konfirmasi

## Batas Pengiriman Pesan

Perhatikan batas pengiriman pesan dari vendor WhatsApp marketing tool Anda untuk menghindari pemblokiran nomor.

## Pemecahan Masalah

Jika pesan tidak terkirim:

1. Periksa log aplikasi di `storage/logs/laravel.log`
2. Pastikan nomor telepon pelanggan valid dan dalam format yang benar
3. Periksa koneksi ke API DripSender
4. Verifikasi API key dan webhook URL masih aktif dan benar

## Kustomisasi Template Pesan

Untuk menyesuaikan template pesan, edit file `app/Services/WhatsAppService.php` dan ubah metode-metode berikut sesuai kebutuhan:

- `getOrderCreatedMessage()`
- `getOrderStatusUpdateMessage()`
- `getPaymentConfirmedMessage()`

## Menambahkan Titik Notifikasi Baru

Jika ingin menambahkan momen/titik notifikasi baru:

1. Buat metode baru di `WhatsAppService` untuk template pesan
2. Tambahkan metode notifikasi baru di service
3. Panggil metode ini dari controller yang relevan 