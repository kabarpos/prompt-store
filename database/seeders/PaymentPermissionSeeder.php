<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PaymentPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat permission untuk konfirmasi pembayaran
        $permission = Permission::firstOrCreate(['name' => 'manage payments']);
        
        // Tambahkan permission ke role admin jika belum ada
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole && !$adminRole->hasPermissionTo('manage payments')) {
            $adminRole->givePermissionTo($permission);
        }
        
        $this->command->info('Payment permission created and assigned to admin role successfully!');
    }
} 