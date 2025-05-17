// Admin role
$adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
$adminRole->syncPermissions([
    // ... existing permissions ...
    'manage payments',
    // ... existing permissions ...
]); 