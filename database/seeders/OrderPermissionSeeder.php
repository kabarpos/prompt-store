<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class OrderPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat permission jika belum ada
        Permission::firstOrCreate(['name' => 'manage_orders']);
        Permission::firstOrCreate(['name' => 'view orders']);
        
        // Berikan permission ke role admin
        $adminRole = Role::where('name', 'admin')->first();
        
        if ($adminRole) {
            $adminRole->givePermissionTo(['manage_orders', 'view orders']);
            $this->command->info('Permission manage_orders dan view orders berhasil ditambahkan ke role admin');
        } else {
            $this->command->error('Role admin tidak ditemukan');
        }
    }
} 