<script setup lang="ts">
import AppLogoIcon from '@/components/ui/AppLogoIcon.vue';
import { usePage } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';

interface WebsiteSettings {
  siteName?: string;
  site_name?: string;
  logoUrl?: string;
  logo_path?: string;
  [key: string]: any;
}

const page = usePage();
const websiteSettings = computed<WebsiteSettings>(() => page.props.websiteSettings as WebsiteSettings || {});
const showDebug = ref(false);

// Debug websiteSettings
onMounted(() => {
  console.log('WebsiteSettings value:', websiteSettings.value);
  console.log('Logo path:', websiteSettings.value.logo_path);
  console.log('Logo URL:', websiteSettings.value.logoUrl);
});

// Periksa logoUrl dan logo_path untuk kompatibilitas dengan format yang berbeda
const logoUrl = computed(() => {
  // Debug nilai
  console.log('Cek websiteSettings: logoUrl =', websiteSettings.value.logoUrl, 'logo_path =', websiteSettings.value.logo_path);
  
  // Prioritaskan logoUrl jika tersedia (yang sudah menjadi URL lengkap)
  if (websiteSettings.value.logoUrl) {
    console.log('Menggunakan logoUrl yang tersedia:', websiteSettings.value.logoUrl);
    return websiteSettings.value.logoUrl;
  }
  
  // Format yang umum digunakan dari database snake_case
  if (websiteSettings.value.logo_path) {
    // Cek apakah logo_path sudah berupa URL lengkap
    if (typeof websiteSettings.value.logo_path === 'string' && websiteSettings.value.logo_path.startsWith('http')) {
      console.log('Menggunakan logo_path sebagai URL lengkap:', websiteSettings.value.logo_path);
      return websiteSettings.value.logo_path;
    }
    // Jika hanya path relatif, tambahkan prefix storage
    const fullPath = `/storage/${websiteSettings.value.logo_path}`;
    console.log('Generated logo URL:', fullPath);
    return fullPath;
  }
  
  console.log('Tidak ada logoUrl atau logo_path yang valid');
  return null;
});

// Periksa format siteName yang berbeda juga
const siteName = computed(() => 
  websiteSettings.value.siteName || 
  websiteSettings.value.site_name || 
  page.props.name || 
  'Admin Panel'
);

// Toggle debug info
const toggleDebug = () => {
  showDebug.value = !showDebug.value;
};
</script>

<template>
    <div @dblclick="toggleDebug" class="relative">
        <!-- Debug info yang akan muncul saat di-double click -->
        <div v-if="showDebug" class="absolute top-full left-0 z-50 p-2 text-xs bg-black text-white rounded w-64 mt-1">
            <p>logoUrl: {{ websiteSettings.logoUrl }}</p>
            <p>logo_path: {{ websiteSettings.logo_path }}</p>
            <p>Computed logoUrl: {{ logoUrl }}</p>
        </div>

        <!-- Wadah yang menyelaraskan logo dan teks dalam satu baris -->
        <div class="flex items-center gap-3">
            <div v-if="logoUrl" class="flex items-center justify-center rounded-md overflow-hidden w-10 h-10 logo-container">
                <img :src="logoUrl" alt="Logo" class="max-h-full max-w-full object-contain" />
            </div>
            <div v-else class="flex items-center justify-center rounded-md overflow-hidden w-10 h-10 logo-container">
                <AppLogoIcon class="w-full h-full" />
            </div>
            
            <div class="text-lg font-bold leading-tight site-name">
                {{ siteName }}
            </div>
        </div>
    </div>
</template>

<style>
.logo-container {
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s;
}

.logo-container:hover {
    transform: scale(1.05);
}

/* Implementasi standar untuk mode default (light) */
.site-name {
    color: #1a1a1a;
}

/* Dark mode menggunakan kelas global dengan warna teks yang lebih terang */
html.dark .site-name {
    color: #ffffff !important;
}
</style>
