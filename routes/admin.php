<?php

use App\Http\Controllers\Admin\EmailController;
use App\Http\Controllers\Admin\EmailSettingController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ExpenseCategoryController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\OrderDocumentController;
use App\Http\Controllers\Admin\PaymentMethodController;
use App\Http\Controllers\Admin\AdminWhatsappController;
use App\Http\Controllers\Admin\SocialMediaController;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Http\Controllers\PaymentConfirmationController;
use App\Http\Controllers\Admin\WhatsAppTemplateController;

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard Admin
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard')->middleware('permission:access_admin_dashboard');
    Route::get('/dashboard/data', [DashboardController::class, 'getData'])->name('dashboard.data')->middleware('permission:access_admin_dashboard');
    
    // Manajemen User
    Route::middleware('permission:manage_users')->group(function () {
        Route::resource('users', UserController::class);
        Route::patch('users/{user}/status', [UserController::class, 'updateStatus'])->name('users.update-status');
        Route::post('users/{user}/resend-verification', [UserController::class, 'resendVerification'])->name('users.resend-verification');
        Route::post('users/{user}/mark-verified', [UserController::class, 'markVerified'])->name('users.mark-verified');
    });
    
    // Manajemen Role
    Route::middleware('permission:manage_roles')->group(function () {
        Route::resource('roles', RoleController::class);
        Route::post('roles/{role}/permissions', [RoleController::class, 'syncPermissions'])->name('roles.permissions.sync');
    });
    
    // Manajemen Permission
    Route::middleware('permission:manage_permissions')->group(function () {
        Route::resource('permissions', PermissionController::class);
    });
    
    // Pengaturan Email dan Website (gabungan)
    Route::middleware('permission:manage_settings')->group(function () {
        // Pengaturan Email
        Route::get('email', [EmailController::class, 'index'])->name('email.index');
        Route::put('email', [EmailController::class, 'update'])->name('email.update');
        Route::post('email/test', [EmailController::class, 'test'])->name('email.test');
        
        // Pengaturan Website
        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('settings', [SettingController::class, 'store'])->name('settings.store');
        Route::put('settings/general', [SettingController::class, 'updateGeneral'])->name('settings.update-general');
        Route::put('settings/footer', [SettingController::class, 'updateFooter'])->name('settings.update-footer');
        Route::put('settings/scripts', [SettingController::class, 'updateScripts'])->name('settings.update-scripts');
        Route::post('settings/logo', [SettingController::class, 'uploadLogo'])->name('settings.upload-logo');
        Route::post('settings/favicon', [SettingController::class, 'uploadFavicon'])->name('settings.upload-favicon');
        Route::post('settings/og-image', [SettingController::class, 'uploadOgImage'])->name('settings.upload-og-image');
        Route::post('settings/update-webhook', [SettingController::class, 'updateWebhook'])->name('settings.update-webhook');
        
        // Coupon Routes
        Route::resource('coupons', \App\Http\Controllers\Admin\CouponController::class);
        Route::patch('coupons/{coupon}/toggle-active', [\App\Http\Controllers\Admin\CouponController::class, 'toggleActive'])->name('coupons.toggle-active');
        
        // WhatsApp Admin routes
        Route::resource('settings/whatsapp', AdminWhatsappController::class)
            ->except(['create', 'edit', 'show'])
            ->names([
                'index' => 'settings.whatsapp.index',
                'store' => 'settings.whatsapp.store',
                'update' => 'settings.whatsapp.update',
                'destroy' => 'settings.whatsapp.destroy',
            ]);
            
        // Social Media routes
        Route::resource('settings/social-media', SocialMediaController::class)
            ->except(['create', 'edit', 'show'])
            ->names([
                'index' => 'settings.social-media.index',
                'store' => 'settings.social-media.store',
                'update' => 'settings.social-media.update',
                'destroy' => 'settings.social-media.destroy',
            ]);
        
        // Deprecated - akan dihapus setelah migrasi selesai
        Route::get('email-settings', [EmailSettingController::class, 'edit'])->name('email-settings.edit');
        Route::put('email-settings', [EmailSettingController::class, 'update'])->name('email-settings.update');
        Route::post('email-settings/test', [EmailSettingController::class, 'sendTestEmail'])->name('email-settings.test');
    });
    
    // Manajemen Pengeluaran
    Route::group([], function () {
        Route::resource('expense-categories', ExpenseCategoryController::class)
            ->middleware([
                'permission:view expense categories',
                'permission:create expense categories',
                'permission:edit expense categories',
                'permission:delete expense categories',
            ]);
            
        Route::resource('expenses', ExpenseController::class)
            ->middleware([
                'permission:view expenses',
                'permission:create expenses',
                'permission:edit expenses',
                'permission:delete expenses',
            ]);
    });

    // Manajemen Metode Pembayaran
    Route::resource('payment-methods', PaymentMethodController::class);

    // Halaman Contoh Komponen UI
    Route::get('components-example', function () {
        return inertia('admin/components-example');
    })->name('components-example');

    // Orders
    Route::middleware('permission:view orders')->group(function () {
        Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::patch('orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status.update');
        Route::get('orders/{order}/pdf', [AdminOrderController::class, 'exportPdf'])->name('orders.pdf');
        Route::get('orders-statistics', [AdminOrderController::class, 'statistics'])->name('orders.statistics');
        
        // Dokumen Order
        Route::get('orders/{order}/documents', [OrderDocumentController::class, 'index'])->name('orders.documents.index');
        Route::get('orders/{order}/documents/create', [OrderDocumentController::class, 'create'])->name('orders.documents.create')->where('order', '[0-9]+|new');
        Route::post('orders/{order}/documents', [OrderDocumentController::class, 'store'])->name('orders.documents.store');
        Route::get('orders/{order}/documents/{document}/edit', [OrderDocumentController::class, 'edit'])->name('orders.documents.edit');
        Route::get('orders/{order}/documents/{document}', [OrderDocumentController::class, 'show'])->name('orders.documents.show');
        Route::put('orders/{order}/documents/{document}', [OrderDocumentController::class, 'update'])->name('orders.documents.update');
        Route::delete('orders/{order}/documents/{document}', [OrderDocumentController::class, 'destroy'])->name('orders.documents.destroy');
        Route::post('orders/{order}/documents/{document}/send', [OrderDocumentController::class, 'send'])->name('orders.documents.send');
        
        // Route untuk melihat semua dokumen order (cocok dengan URL /admin/order-documents)
        Route::get('order-documents', [OrderDocumentController::class, 'allDocuments'])->name('documents.all');
        Route::get('documents/{document}/download', [OrderDocumentController::class, 'download'])->name('documents.download');
    });

    // Payment Confirmations
    Route::middleware('permission:manage payments')->group(function () {
        Route::get('payment-confirmations', [PaymentConfirmationController::class, 'index'])->name('payment-confirmations.index');
        Route::get('payment-confirmations/{confirmation}', [PaymentConfirmationController::class, 'show'])->name('payment-confirmations.show');
        Route::patch('payment-confirmations/{confirmation}/status', [PaymentConfirmationController::class, 'updateStatus'])->name('payment-confirmations.status.update');
    });

    // WhatsApp Template routes
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [WhatsAppTemplateController::class, 'index'])->name('index');
        Route::get('/create', [WhatsAppTemplateController::class, 'create'])->name('create');
        Route::get('/{template}', [WhatsAppTemplateController::class, 'show'])->name('show');
        Route::get('/{template}/edit', [WhatsAppTemplateController::class, 'edit'])->name('edit');
        
        // API endpoints untuk operasi CRUD
        Route::post('/whatsapp-templates', [WhatsAppTemplateController::class, 'store'])->name('whatsapp-templates.store');
        Route::put('/whatsapp-templates/{template}', [WhatsAppTemplateController::class, 'update'])->name('whatsapp-templates.update');
        Route::delete('/whatsapp-templates/{template}', [WhatsAppTemplateController::class, 'destroy'])->name('whatsapp-templates.destroy');
        Route::patch('/whatsapp-templates/{template}/toggle-active', [WhatsAppTemplateController::class, 'toggleActive'])->name('whatsapp-templates.toggle-active');
    });
});
