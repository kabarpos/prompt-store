import './bootstrap.ts';
import '../css/app.css';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
// Import ZiggyVue dari file lokal
import { ZiggyVue } from './ziggy-vue';
import { initializeTheme } from './composables/useAppearance';
import { Toaster } from 'vue-sonner';

// Import AOS (Animate On Scroll) library
// @ts-ignore
import AOS from 'aos';
import 'aos/dist/aos.css';

// Initialize AOS
AOS.init({
    duration: 800,
    once: true,
    offset: 50
});

// Deklarasi typing untuk objek __page global yang disisipkan oleh Inertia
declare global {
    interface Window {
        __page?: {
            props?: {
                websiteSettings?: {
                    site_name?: string;
                    siteName?: string;
                    logoUrl?: string;
                    faviconUrl?: string;
                };
                name?: string;
                title?: string;
                componentPath?: string;
            };
            component?: string;
        };
    }
}

// Ambil nama aplikasi dari environment variable
const appName = import.meta.env.VITE_APP_NAME || 'MyPrompt';

// Konfigurasi CSRF token untuk semua request
const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
if (token) {
    console.log('CSRF token found and configured');
} else {
    console.error('CSRF token not found in meta tags');
}

createInertiaApp({
    // Format judul halaman: [title] - [siteName]
    title: (title) => {
        // Log untuk debugging
        console.log('Title dari Head component:', title);
        console.log('Page props:', window.__page?.props);
        
        // Ambil nama situs dari props Inertia
        const siteName = window.__page?.props?.websiteSettings?.siteName 
            || window.__page?.props?.websiteSettings?.site_name
            || window.__page?.props?.name
            || import.meta.env.VITE_APP_NAME 
            || 'Laravel';
        
        // Coba dapatkan judul dari berbagai sumber
        let pageTitle = title;
        
        // Jika tidak ada title dari Head component, coba ambil dari props
        if (!pageTitle && window.__page?.props?.title) {
            pageTitle = window.__page.props.title;
            console.log('Menggunakan title dari props:', pageTitle);
        }
        
        // Jika masih tidak ada, periksa componentPath dari props
        if (!pageTitle && window.__page?.props?.componentPath) {
            const path = window.__page.props.componentPath;
            console.log('Using componentPath from props:', path);
            const parts = path.split('/');
            if (parts.length > 1) {
                // Pengaturan Email/Index -> Pengaturan Email
                const lastPart = parts[parts.length - 1];
                if (lastPart === 'Index' && parts.length > 2) {
                    pageTitle = parts[parts.length - 2];
                    // Format: Email -> Pengaturan Email
                    if (pageTitle === 'Email') pageTitle = 'Pengaturan Email';
                    else if (pageTitle === 'Roles') pageTitle = 'Manajemen Peran';
                    else if (pageTitle === 'Permissions') pageTitle = 'Manajemen Izin';
                    else if (pageTitle === 'Users') pageTitle = 'Manajemen Pengguna';
                    else if (pageTitle === 'Settings') pageTitle = 'Pengaturan Website';
                } else {
                    pageTitle = lastPart;
                }
                console.log('Extracted title from componentPath:', pageTitle);
            }
        }
        
        // Jika masih tidak ada, coba ekstrak dari komponen path
        if (!pageTitle && window.__page?.component) {
            const parts = window.__page.component.split('/');
            if (parts.length > 1) {
                // Pengaturan Email/Index -> Pengaturan Email
                const lastPart = parts[parts.length - 1];
                if (lastPart === 'Index' && parts.length > 2) {
                    pageTitle = parts[parts.length - 2];
                    // Format: Email -> Pengaturan Email
                    if (pageTitle === 'Email') pageTitle = 'Pengaturan Email';
                    else if (pageTitle === 'Roles') pageTitle = 'Manajemen Peran';
                    else if (pageTitle === 'Permissions') pageTitle = 'Manajemen Izin';
                    else if (pageTitle === 'Users') pageTitle = 'Manajemen Pengguna';
                    else if (pageTitle === 'Settings') pageTitle = 'Pengaturan Website';
                } else {
                    pageTitle = lastPart;
                }
                console.log('Extracted title from component path:', pageTitle);
            }
        }
        
        // Jika masih tidak ada judul, gunakan nama situs
        if (!pageTitle) {
            console.log('Tidak ada title, menggunakan siteName:', siteName);
            return siteName;
        }
        
        // Jika judul sudah mengandung nama situs, gunakan apa adanya
        if (pageTitle.includes(siteName)) {
            console.log('Title sudah mengandung siteName:', pageTitle);
            return pageTitle;
        }
        
        // Format: Title - SiteName
        const finalTitle = `${pageTitle} - ${siteName}`;
        console.log('Final title:', finalTitle);
        return finalTitle;
    },
    resolve: (name) => {
        const segments = name.split('/');
        
        // Jangan konversi ke lowercase karena beberapa file menggunakan kapitalisasi (Index.vue)
        const path = segments.join('/');
        
        console.log('Resolving path for:', name);
        
        // Files directly in the pages directory
        if (!path.includes('/')) {
            const directPath = `./pages/${path}.vue`;
            console.log('Resolving direct file path:', directPath);
            return resolvePageComponent(directPath, 
                import.meta.glob<DefineComponent>('./pages/**/*.vue'));
        }
        
        // Check if path ends with 'index' explicitly
        if (path.toLowerCase().endsWith('/index')) {
            const pagePath = `./pages/${path}.vue`;
            console.log('Resolving explicit index path:', pagePath);
            return resolvePageComponent(pagePath, 
                import.meta.glob<DefineComponent>('./pages/**/*.vue'));
        }
        
        // Coba cari file langsung di level path yang diberikan (./pages/path.vue)
        try {
            const directPathAtLevel = `./pages/${path}.vue`;
            console.log('Trying to resolve direct file at path level:', directPathAtLevel);
            return resolvePageComponent(directPathAtLevel,
                import.meta.glob<DefineComponent>('./pages/**/*.vue'));
        } catch (error) {
            console.log('File not found at direct path level, trying with Index.vue');
            
            // For all other cases, try with /Index appended by default
            // This handles routes like "public/home" to map to "public/home/Index.vue"
            const indexPath = `./pages/${path}/Index.vue`;
            console.log('Resolving with index appended:', indexPath);
            
            return resolvePageComponent(indexPath, 
                import.meta.glob<DefineComponent>('./pages/**/*.vue'));
        }
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });
        app.use(plugin)
           .use(ZiggyVue)
           .component('Toaster', Toaster)
           .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

// This will set light / dark mode on page load...
console.log('Calling initializeTheme from app.ts');
initializeTheme();

// Tambahkan listener saat DOM dimuat untuk memastikan tema diterapkan
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM loaded, reinitializing theme');
    initializeTheme();
    
    // Fungsi untuk memastikan kelas dark sudah benar
    const checkDarkMode = () => {
        const savedAppearance = localStorage.getItem('appearance') as 'light' | 'dark' | 'system' | null;
        console.log('Checking dark mode, saved appearance:', savedAppearance);
        
        if (savedAppearance === 'dark' || 
            (savedAppearance === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            if (!document.documentElement.classList.contains('dark')) {
                console.log('Adding missing dark class to HTML');
                document.documentElement.classList.add('dark');
            }
        }
    };
    
    // Jalankan untuk memastikan kelas dark sudah benar
    checkDarkMode();
});
