<?php

namespace App\Http\Middleware;

use App\Models\WebsiteSetting;
use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Middleware;
use Illuminate\Support\Facades\Cache;

class HandleInertiaRequests extends Middleware
{
    /**
     * Konstanta untuk pengaturan default
     */
    const DEFAULT_SETTINGS = [
        'site_name' => null, // Akan menggunakan config('app.name')
        'site_subtitle' => '',
        'site_description' => '',
        'contact_email' => null, // Akan menggunakan config('mail.from.address')
        'copyright' => null, // Akan menggunakan date('Y') + app.name
        'logo_url' => null,
        'favicon_url' => null,
        'og_image_url' => null,
    ];

    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        // Get website settings
        try {
            $websiteSettings = Cache::remember('website_settings', now()->addHours(24), function () {
                // Dapatkan model settings
                $settings = WebsiteSetting::getSettings();
                
                // Konversi ke array
                $settingsArray = [
                    'site_name' => $settings->site_name,
                    'site_subtitle' => $settings->site_subtitle,
                    'site_description' => $settings->site_description,
                    'contact_email' => $settings->contact_email,
                    'contact_phone' => $settings->contact_phone,
                    'contact_address' => $settings->contact_address,
                    'logo_path' => $settings->logo_path,
                    'favicon_path' => $settings->favicon_path,
                    'default_og_image_path' => $settings->default_og_image_path,
                ];
                
                // Tambahkan URL lengkap untuk media
                $settingsArray['logoUrl'] = $settings->getLogoUrl();
                $settingsArray['faviconUrl'] = $settings->getFaviconUrl();
                $settingsArray['ogImageUrl'] = $settings->getOgImageUrl();
                
                // Format camelCase untuk frontend
                $settingsArray['siteName'] = $settings->site_name;
                
                return $settingsArray;
            });
        } catch (\Exception $e) {
            Log::error('Error saat mengambil website settings', [
                'message' => $e->getMessage()
            ]);
            $websiteSettings = [];
        }
        
        // Get current component path
        $component = $request->route()?->getAction('controller');
        
        // Get user data if authenticated
        $userData = null;
        if ($request->user()) {
            try {
                $userData = array_merge(
                    $request->user()->only(
                        'id', 'name', 'email', 'email_verified_at', 'profile_photo_path', 'is_active'
                    ),
                    [
                        'avatar' => $request->user()->profile_photo_path,
                        'roles' => $request->user()->roles->map(function ($role) {
                            return [
                                'id' => $role->id,
                                'name' => $role->name
                            ];
                        }),
                        'permissions' => $request->user()->getPermissionsViaRoles()->pluck('name')->toArray()
                    ]
                );
            } catch (\Exception $e) {
                Log::error('Error saat menyiapkan data user', [
                    'message' => $e->getMessage(),
                    'user_id' => $request->user()->id,
                ]);
                $userData = $request->user()->only('id', 'name', 'email');
            }
        }
        
        // Get cart count
        $cartCount = 0;
        try {
            $sessionId = \App\Models\Cart::getSessionId();
            $cartCount = \App\Models\Cart::where('session_id', $sessionId)->sum('quantity');
        } catch (\Exception $e) {
            Log::warning('Error saat mengambil jumlah keranjang', [
                'message' => $e->getMessage()
            ]);
        }

        // Get categories for global access
        $categories = [];
        try {
            $categories = Cache::remember('global_categories', now()->addHours(6), function () {
                return \App\Models\Category::where('is_active', true)
                    ->orderBy('name')
                    ->get();
            });
        } catch (\Exception $e) {
            Log::warning('Error saat mengambil kategori global', [
                'message' => $e->getMessage()
            ]);
        }

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $userData,
            ],
            'cartCount' => $cartCount,
            'categories' => $categories,
            'sidebarOpen' => $this->getSidebarState($request),
            'websiteSettings' => $websiteSettings,
            'componentPath' => $component,
            'flash' => [
                'message' => fn () => $request->session()->get('message'),
                'error' => fn () => $request->session()->get('error'),
            ],
        ]);
    }
    
    /**
     * Get sidebar state from cookie with proper default
     */
    protected function getSidebarState(Request $request): bool
    {
        $cookieValue = $request->cookie('sidebar_state');
        
        // Jika cookie tidak ada, sidebar terbuka
        if ($cookieValue === null) {
            return true;
        }
        
        // Jika cookie ada, konversi nilai ke boolean
        return filter_var($cookieValue, FILTER_VALIDATE_BOOLEAN);
    }
    
    /**
     * Get the component path from the current request (for title extraction).
     */
    protected function getComponentPath(Request $request): ?string
    {
        // Check if we're processing an Inertia request
        if ($request->header('X-Inertia')) {
            $component = $request->input('component');
            if ($component) {
                return $component;
            }
        }
        
        // Try to extract from the URL/route
        try {
            $route = $request->route();
            if (!$route) {
                return null;
            }
            
            $routeName = $route->getName();
            if (!$routeName) {
                return null;
            }
            
            // Gunakan pendekatan dinamis untuk mapping rute ke component
            return $this->mapRouteToComponent($routeName);
            
        } catch (\Exception $e) {
            // Log error dan lanjutkan
            Log::warning('Error saat ekstraksi path komponen', [
                'message' => $e->getMessage(),
                'route' => $request->path()
            ]);
        }
        
        return null;
    }
    
    /**
     * Map a route name to a component path using a dynamic approach
     */
    protected function mapRouteToComponent(string $routeName): ?string
    {
        // Hardcoded mapping untuk backward compatibility
        $routeToComponentMap = [
            'admin.dashboard' => 'admin/dashboard/Index',
            'admin.users.index' => 'admin/users/Index',
            'admin.roles.index' => 'admin/roles/Index',
            'admin.permissions.index' => 'admin/permissions/Index',
            'admin.email.index' => 'admin/email/Index',
            'admin.settings.index' => 'admin/settings/Index',
        ];
        
        // Jika ada di mapping langsung, gunakan itu
        if (isset($routeToComponentMap[$routeName])) {
            return $routeToComponentMap[$routeName];
        }
        
        // Jika tidak, coba generate secara dinamis
        $parts = explode('.', $routeName);
        
        // Jika struktur route tidak sesuai format (minimal namespace.resource[.action])
        if (count($parts) < 2) {
            return null;
        }
        
        // Ambil namespace dan resource
        $namespace = ucfirst($parts[0]);
        $resource = ucfirst($parts[1]);
        
        // Untuk action yang umum (index, create, edit, show)
        if (count($parts) > 2) {
            $action = ucfirst($parts[2]);
            
            // Deteksi pattern yang umum
            if (in_array($action, ['Index', 'Create', 'Edit', 'Show'])) {
                return "{$namespace}/{$resource}/{$action}";
            }
        } else {
            // Default ke Index jika hanya nama.resource (tanpa action)
            return "{$namespace}/{$resource}/Index";
        }
        
        return null;
    }
}
