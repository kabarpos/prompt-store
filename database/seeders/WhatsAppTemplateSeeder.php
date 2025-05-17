<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WhatsAppTemplate;

class WhatsAppTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Template untuk customer
        WhatsAppTemplate::create([
            "name" => "Notifikasi Order Baru - Customer",
            "type" => "customer",
            "trigger_status" => "new_order",
            "message_template" => "Halo {customer_name},\n\nTerima kasih telah melakukan pemesanan di toko kami.\n\nDetail pesanan:\nNomor Order: {order_number}\nTanggal: {order_date}\nTotal: {total_amount}\n\nDetail produk:\n{items_list}\n\nSilakan lakukan pembayaran sesuai dengan metode yang dipilih.\n\nTerima kasih.",
            "description" => "Template notifikasi ke customer saat order baru dibuat",
            "is_active" => true,
            "order" => 1
        ]);

        // Template untuk admin
        WhatsAppTemplate::create([
            "name" => "Notifikasi Order Baru - Admin",
            "type" => "admin",
            "trigger_status" => "new_order",
            "message_template" => "🔔 Order Baru!\n\nDetail pesanan:\nNomor: {order_number}\nTanggal: {order_date}\nCustomer: {customer_name}\nTotal: {total_amount}\n\nProduk:\n{items_list}\n\nMetode Pembayaran: {payment_method}\n\nSegera proses pesanan ini.",
            "description" => "Template notifikasi ke admin saat ada order baru",
            "is_active" => true,
            "order" => 1
        ]);
    }
}
