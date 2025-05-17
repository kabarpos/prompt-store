<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Bank Transfer Methods
        $bankMethods = [
            [
                'name' => 'Bank BCA',
                'code' => 'bca',
                'type' => PaymentMethod::TYPE_BANK_TRANSFER,
                'account_number' => '1234567890',
                'account_name' => 'PT Dilogif Indonesia',
                'bank_name' => 'BCA',
                'description' => 'Transfer ke rekening BCA kami',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Bank Mandiri',
                'code' => 'mandiri',
                'type' => PaymentMethod::TYPE_BANK_TRANSFER,
                'account_number' => '9876543210',
                'account_name' => 'PT Dilogif Indonesia',
                'bank_name' => 'Mandiri',
                'description' => 'Transfer ke rekening Mandiri kami',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Bank BNI',
                'code' => 'bni',
                'type' => PaymentMethod::TYPE_BANK_TRANSFER,
                'account_number' => '5678901234',
                'account_name' => 'PT Dilogif Indonesia',
                'bank_name' => 'BNI',
                'description' => 'Transfer ke rekening BNI kami',
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        // Payment Gateway Methods
        $gatewayMethods = [
            [
                'name' => 'Midtrans',
                'code' => 'midtrans',
                'type' => PaymentMethod::TYPE_PAYMENT_GATEWAY,
                'description' => 'Pembayaran online melalui Midtrans',
                'is_active' => false, // Not implemented yet
                'sort_order' => 10,
            ],
        ];

        // Insert all payment methods
        foreach (array_merge($bankMethods, $gatewayMethods) as $method) {
            PaymentMethod::updateOrCreate(
                ['code' => $method['code']],
                $method
            );
        }
    }
}
