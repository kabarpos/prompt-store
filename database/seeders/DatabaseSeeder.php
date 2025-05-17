<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed roles dan permissions terlebih dahulu
        $this->call([
            RoleAndPermissionSeeder::class,
            UserSeeder::class,
            ProductPermissionsSeeder::class,
            ExpensePermissionSeeder::class,
            // other seeders if any
        ]);
        
        // Seed user admin
        $this->call(AdminUserSeeder::class);
        
        // Seed pengaturan email
        $this->call(EmailSettingsSeeder::class);

        $this->call([
            PermissionRoleSeeder::class,
            PaymentMethodSeeder::class,
            PaymentPermissionSeeder::class,
            WhatsAppTemplateSeeder::class,
        ]);

        // Buat role customer jika belum ada
        DB::table('roles')->insertOrIgnore([
            'name' => 'customer',
            'guard_name' => 'web',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
