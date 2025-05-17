<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, computed, watch, nextTick, onMounted, onUnmounted } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Textarea } from '@/components/ui/textarea/index';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs/index';
import { toast } from 'vue-sonner';
import { Globe, FileCode, Upload, Image as ImageIcon, Mail as MailIcon, Settings, Phone, Share2, Plus, Trash2, Instagram, Facebook, Twitter, Youtube, Linkedin, X, Check } from 'lucide-vue-next';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Checkbox } from '@/components/ui/checkbox';
import axios from 'axios';

// Define props
const props = defineProps<{
  settings: {
    id: number;
    site_name: string;
    site_subtitle: string | null;
    site_description: string | null;
    homepage_route: string;
    contact_email: string | null;
    contact_phone: string | null;
    contact_address: string | null;
    copyright_text: string | null;
    copyright_year: number | null;
    logo_path: string | null;
    favicon_path: string | null;
    default_og_image_path: string | null;
    header_scripts: string | null;
    meta_pixel_script: string | null;
    tiktok_pixel_script: string | null;
    google_tag_script: string | null;
    footer_scripts: string | null;
    webhook_api_key: string | null;
    webhook_url: string | null;
    webhook_sender_phone: string | null;
    webhook_is_active: boolean;
  };
  mediaUrls: {
    logo: string | null;
    favicon: string | null;
    og_image: string | null;
  };
  whatsappAdmins?: Array<{
    id: number;
    name: string;
    phone_number: string;
    description: string | null;
    is_active: boolean;
    order: number;
  }>;
  socialMedia?: Array<{
    id: number;
    platform: string;
    username: string;
    url: string;
    icon: string | null;
    is_active: boolean;
    order: number;
  }>;
}>();

// Breadcrumbs untuk navigasi
const breadcrumbs = [
  {
    title: 'Admin',
    href: route('admin.dashboard'),
  },
  {
    title: 'Pengaturan Website',
    href: route('admin.settings.index'),
  },
];

// State
const activeTab = ref('general');
const isProcessing = ref(false);
const showWhatsappDialog = ref(false);
const showSocialDialog = ref(false);
const showWebhookDialog = ref(false);

// Form untuk pengaturan webhook
const webhookForm = useForm({
  api_key: props.settings?.webhook_api_key || '',
  webhook_url: props.settings?.webhook_url || '',
  sender_phone: props.settings?.webhook_sender_phone || '',
  is_active: props.settings?.webhook_is_active === true,
});

// Method untuk menyimpan pengaturan webhook
const saveWebhookSettings = () => {
  webhookForm.post(route('admin.settings.update-webhook'), {
    onSuccess: () => {
      toast.success('Berhasil', {
        description: 'Pengaturan webhook berhasil disimpan',
      });
      showWebhookDialog.value = false;
      
      // Pastikan nilai is_active tersimpan
      props.settings.webhook_is_active = webhookForm.is_active;
    },
    onError: () => {
      toast.error('Gagal', {
        description: 'Terjadi kesalahan saat menyimpan pengaturan webhook',
      });
    },
  });
};

// Form untuk pengaturan umum
const generalForm = useForm({
  site_name: props.settings.site_name || '',
  site_subtitle: props.settings.site_subtitle || '',
  site_description: props.settings.site_description || '',
  homepage_route: props.settings.homepage_route || '/',
  contact_email: props.settings.contact_email || '',
  contact_phone: props.settings.contact_phone || '',
  contact_address: props.settings.contact_address || '',
});

// Form untuk pengaturan footer
const footerForm = useForm({
  copyright_text: props.settings.copyright_text || '',
  copyright_year: props.settings.copyright_year || new Date().getFullYear(),
});

// Form untuk skrip
const scriptsForm = useForm({
  header_scripts: props.settings.header_scripts || '',
  meta_pixel_script: props.settings.meta_pixel_script || '',
  tiktok_pixel_script: props.settings.tiktok_pixel_script || '',
  google_tag_script: props.settings.google_tag_script || '',
  footer_scripts: props.settings.footer_scripts || '',
});

// Forms untuk upload media
const logoForm = useForm({
  logo: null as File | null,
});

const faviconForm = useForm({
  favicon: null as File | null,
});

const ogImageForm = useForm({
  og_image: null as File | null,
});

// State untuk WhatsApp Admin
const whatsappAdmins = ref(props.whatsappAdmins || []);
const whatsappForm = useForm({
  name: '',
  phone_number: '',
  description: '',
  is_active: true,
});

// State untuk Social Media
const socialMedia = ref(props.socialMedia || []);
const socialMediaForm = useForm({
  platform: '',
  username: '',
  url: '',
  is_active: true,
});

// Available social media platforms
const availablePlatforms = [
  { value: 'facebook', label: 'Facebook', icon: Facebook },
  { value: 'instagram', label: 'Instagram', icon: Instagram },
  { value: 'twitter', label: 'Twitter', icon: Twitter },
  { value: 'x', label: 'X (Twitter)', icon: X },
  { value: 'youtube', label: 'YouTube', icon: Youtube },
  { value: 'linkedin', label: 'LinkedIn', icon: Linkedin },
  { value: 'tiktok', label: 'TikTok', icon: null },
  { value: 'pinterest', label: 'Pinterest', icon: null },
];

// Get social media icon component
const getSocialIcon = (platform: string) => {
  const platformInfo = availablePlatforms.find(p => p.value === platform);
  return platformInfo?.icon || Share2;
};

// File references for preview
const logoFile = ref<File | null>(null);
const faviconFile = ref<File | null>(null);
const ogImageFile = ref<File | null>(null);

// Preview URLs
const logoPreview = ref<string | null>(props.mediaUrls.logo);
const faviconPreview = ref<string | null>(props.mediaUrls.favicon);
const ogImagePreview = ref<string | null>(props.mediaUrls.og_image);

// Fungsi untuk menangani file upload
const handleFileChange = (e: Event, type: 'logo' | 'favicon' | 'og_image') => {
  const target = e.target as HTMLInputElement;
  if (!target.files?.length) return;
  
  const file = target.files[0];
  
  if (type === 'logo') {
    logoFile.value = file;
    logoForm.logo = file;
    logoPreview.value = URL.createObjectURL(file);
  } else if (type === 'favicon') {
    faviconFile.value = file;
    faviconForm.favicon = file;
    faviconPreview.value = URL.createObjectURL(file);
  } else if (type === 'og_image') {
    ogImageFile.value = file;
    ogImageForm.og_image = file;
    ogImagePreview.value = URL.createObjectURL(file);
  }
};

// Submit general settings
const submitGeneral = () => {
  generalForm.put(route('admin.settings.update-general'), {
    onSuccess: () => {
      toast.success('Berhasil', {
        description: 'Pengaturan umum berhasil disimpan',
      });
    },
    onError: () => {
      toast.error('Gagal', {
        description: 'Terjadi kesalahan saat menyimpan pengaturan',
      });
    },
  });
};

// Submit footer settings
const submitFooter = () => {
  footerForm.put(route('admin.settings.update-footer'), {
    onSuccess: () => {
      toast.success('Berhasil', {
        description: 'Pengaturan footer berhasil disimpan',
      });
    },
    onError: () => {
      toast.error('Gagal', {
        description: 'Terjadi kesalahan saat menyimpan pengaturan',
      });
    },
  });
};

// Submit scripts settings
const submitScripts = () => {
  scriptsForm.put(route('admin.settings.update-scripts'), {
    onSuccess: () => {
      toast.success('Berhasil', {
        description: 'Pengaturan skrip berhasil disimpan',
      });
    },
    onError: () => {
      toast.error('Gagal', {
        description: 'Terjadi kesalahan saat menyimpan pengaturan',
      });
    },
  });
};

// Fungsi untuk mengunggah logo
const uploadLogo = () => {
  if (!logoForm.logo) return;
  
  logoForm.post(route('admin.settings.update-logo'), {
    onSuccess: () => {
      toast.success('Berhasil', {
        description: 'Logo berhasil diunggah',
      });
      
      // Reset form
      logoForm.logo = null;
      
      // Hapus cache website settings
      axios.post(route('admin.settings.clear-cache'))
        .then(() => {
          console.log('Cache website settings berhasil dihapus');
          // Reload halaman untuk memperbarui logo
          setTimeout(() => {
            window.location.reload();
          }, 500);
        })
        .catch(error => {
          console.error('Gagal menghapus cache website settings:', error);
        });
    },
    onError: () => {
      toast.error('Gagal', {
        description: 'Terjadi kesalahan saat mengunggah logo',
      });
    },
  });
};

// Upload favicon
const uploadFavicon = () => {
  if (!faviconForm.favicon) return;
  
  faviconForm.post(route('admin.settings.upload-favicon'), {
    forceFormData: true,
    onSuccess: () => {
      toast.success('Berhasil', {
        description: 'Favicon berhasil diupload',
      });
    },
    onError: () => {
      toast.error('Gagal', {
        description: 'Terjadi kesalahan saat mengupload favicon',
      });
    },
  });
};

// Upload OG image
const uploadOgImage = () => {
  if (!ogImageForm.og_image) return;
  
  ogImageForm.post(route('admin.settings.upload-og-image'), {
    forceFormData: true,
    onSuccess: () => {
      toast.success('Berhasil', {
        description: 'Gambar OG berhasil diupload',
      });
    },
    onError: () => {
      toast.error('Gagal', {
        description: 'Terjadi kesalahan saat mengupload gambar OG',
      });
    },
  });
};

// Fungsi navigasi ke halaman email settings
const navigateToEmailSettings = () => {
  router.visit(route('admin.email.index'));
};

// Sinkronisasi data lokal dengan props jika berubah
watch(() => props.whatsappAdmins, (val) => {
  whatsappAdmins.value = val || [];
});
watch(() => props.socialMedia, (val) => {
  socialMedia.value = val || [];
});

// WhatsApp Admin - Create New
const createWhatsappAdmin = () => {
  whatsappForm.post(route('admin.settings.whatsapp.store'), {
    headers: {
      'X-Inertia': 'true'
    },
    onSuccess: (page) => {
      toast.success('Berhasil', {
        description: 'Nomor WhatsApp Admin berhasil ditambahkan',
      });
      // Update array lokal dari response jika ada
      if (page?.props?.whatsappAdmins) {
        whatsappAdmins.value = page.props.whatsappAdmins;
      }
      whatsappForm.reset();
      showWhatsappDialog.value = false;
    },
    onError: () => {
      toast.error('Gagal', {
        description: 'Terjadi kesalahan saat menambahkan nomor WhatsApp',
      });
    },
  });
};

// WhatsApp Admin - Delete
const deleteWhatsappAdmin = (id: number) => {
  if (confirm('Apakah Anda yakin ingin menghapus nomor WhatsApp ini?')) {
    router.delete(route('admin.settings.whatsapp.destroy', id), {
      headers: {
        'X-Inertia': 'true'
      },
      onSuccess: () => {
        toast.success('Berhasil', {
          description: 'Nomor WhatsApp berhasil dihapus',
        });
      },
      onError: () => {
        toast.error('Gagal', {
          description: 'Terjadi kesalahan saat menghapus nomor WhatsApp',
        });
      },
    });
  }
};

// WhatsApp Admin - Toggle Status
const toggleWhatsappStatus = (whatsapp: any) => {
  router.put(route('admin.settings.whatsapp.update', whatsapp.id), {
    name: whatsapp.name,
    phone_number: whatsapp.phone_number,
    description: whatsapp.description,
    is_active: !whatsapp.is_active,
    order: whatsapp.order
  }, {
    headers: {
      'X-Inertia': 'true'
    },
    onSuccess: () => {
      toast.success('Berhasil', {
        description: 'Status WhatsApp berhasil diubah',
      });
    },
    onError: () => {
      toast.error('Gagal', {
        description: 'Terjadi kesalahan saat mengubah status WhatsApp',
      });
    },
  });
};

// Social Media - Create New
const createSocialMedia = () => {
  socialMediaForm.post(route('admin.settings.social-media.store'), {
    headers: {
      'X-Inertia': 'true'
    },
    onSuccess: (page) => {
      toast.success('Berhasil', {
        description: 'Media sosial berhasil ditambahkan',
      });
      if (page?.props?.socialMedia) {
        socialMedia.value = page.props.socialMedia;
      }
      socialMediaForm.reset();
      showSocialDialog.value = false;
    },
    onError: () => {
      toast.error('Gagal', {
        description: 'Terjadi kesalahan saat menambahkan media sosial',
      });
    },
  });
};

// Social Media - Delete
const deleteSocialMedia = (id: number) => {
  if (confirm('Apakah Anda yakin ingin menghapus media sosial ini?')) {
    router.delete(route('admin.settings.social-media.destroy', id), {
      headers: {
        'X-Inertia': 'true'
      },
      onSuccess: () => {
        toast.success('Berhasil', {
          description: 'Media sosial berhasil dihapus',
        });
      },
      onError: () => {
        toast.error('Gagal', {
          description: 'Terjadi kesalahan saat menghapus media sosial',
        });
      },
    });
  }
};

// Social Media - Toggle Status
const toggleSocialMediaStatus = (social: any) => {
  router.put(route('admin.settings.social-media.update', social.id), {
    platform: social.platform,
    username: social.username,
    url: social.url,
    is_active: !social.is_active,
    order: social.order
  }, {
    headers: {
      'X-Inertia': 'true'
    },
    onSuccess: () => {
      toast.success('Berhasil', {
        description: 'Status media sosial berhasil diubah',
      });
    },
    onError: () => {
      toast.error('Gagal', {
        description: 'Terjadi kesalahan saat mengubah status media sosial',
      });
    },
  });
};

// New state for platform selection
const isPlatformSelectOpen = ref(false);
const platformSelectRef = ref<HTMLElement | null>(null);

// Set up click outside handler
const handleOutsideClick = (event: MouseEvent) => {
  if (
    isPlatformSelectOpen.value && 
    platformSelectRef.value && 
    !platformSelectRef.value.contains(event.target as Node)
  ) {
    isPlatformSelectOpen.value = false;
  }
};

// Lifecycle hooks
onMounted(() => {
  document.addEventListener('mousedown', handleOutsideClick);
});

onUnmounted(() => {
  document.removeEventListener('mousedown', handleOutsideClick);
});

// New methods for platform selection
const togglePlatformSelect = () => {
  isPlatformSelectOpen.value = !isPlatformSelectOpen.value;
};

const selectPlatform = (platform: string) => {
  socialMediaForm.platform = platform;
  isPlatformSelectOpen.value = false;
};

const getPlatformIcon = (platform: string) => {
  const platformInfo = availablePlatforms.find(p => p.value === platform);
  return platformInfo?.icon || Share2;
};

const getPlatformLabel = (platform: string) => {
  const platformInfo = availablePlatforms.find(p => p.value === platform);
  return platformInfo?.label || '';
};
</script>

<template>
  <Head title="Pengaturan Website" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="container mx-auto py-6 px-4">
      <header class="mb-6">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-2xl font-semibold text-gray-900">Pengaturan</h1>
            <p class="mt-1 text-sm text-gray-600">
              Kelola pengaturan aplikasi Anda
            </p>
          </div>
        </div>
      </header>

      <Tabs v-model="activeTab" :default-value="'general'" class="w-full">
        <TabsList class="grid w-full grid-cols-4 lg:w-auto">
          <TabsTrigger value="general" class="flex items-center gap-2">
            <Globe class="h-4 w-4" />
            <span class="hidden md:inline">Umum</span>
          </TabsTrigger>
          <TabsTrigger value="branding" class="flex items-center gap-2">
            <ImageIcon class="h-4 w-4" />
            <span class="hidden md:inline">Branding</span>
          </TabsTrigger>
          <TabsTrigger value="scripts" class="flex items-center gap-2">
            <FileCode class="h-4 w-4" />
            <span class="hidden md:inline">Scripts</span>
          </TabsTrigger>
          <TabsTrigger value="contact" class="flex items-center gap-2">
            <Phone class="h-4 w-4" />
            <span class="hidden md:inline">Kontak</span>
          </TabsTrigger>
        </TabsList>

        <!-- Tab Pengaturan Umum -->
        <TabsContent value="general" class="mt-6">
          <Card>
            <CardHeader>
              <CardTitle>Pengaturan Umum</CardTitle>
              <CardDescription>
                Konfigurasi dasar website Anda
              </CardDescription>
            </CardHeader>
            <CardContent>
              <form @submit.prevent="submitGeneral" class="space-y-6">
                <div class="space-y-4">
                  <div>
                    <Label for="site_name">Nama Website</Label>
                    <Input 
                      id="site_name" 
                      v-model="generalForm.site_name" 
                      type="text" 
                      class="mt-1" 
                      placeholder="Masukkan nama website" 
                    />
                  </div>
                  
                  <div>
                    <Label for="site_subtitle">Subtitle Website</Label>
                    <Input 
                      id="site_subtitle" 
                      v-model="generalForm.site_subtitle" 
                      type="text" 
                      class="mt-1" 
                      placeholder="Masukkan subtitle/slogan website" 
                    />
                  </div>
                  
                  <div>
                    <Label for="site_description">Deskripsi Website</Label>
                    <Textarea 
                      id="site_description" 
                      v-model="generalForm.site_description" 
                      class="mt-1" 
                      placeholder="Masukkan deskripsi singkat tentang website Anda" 
                      rows="3"
                    />
                  </div>
                  
                  <div>
                    <Label for="homepage_route">Rute Halaman Utama</Label>
                    <Input 
                      id="homepage_route" 
                      v-model="generalForm.homepage_route" 
                      type="text" 
                      class="mt-1" 
                      placeholder="Contoh: /" 
                    />
                  </div>

                  <div>
                    <Label for="contact_email">Email Kontak</Label>
                    <Input 
                      id="contact_email" 
                      v-model="generalForm.contact_email" 
                      type="email" 
                      class="mt-1" 
                      placeholder="Masukkan email kontak" 
                    />
                  </div>
                </div>

                <div class="flex justify-end">
                  <Button type="submit" :disabled="generalForm.processing">
                    <span v-if="generalForm.processing" class="mr-2">
                      <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" />
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                      </svg>
                    </span>
                    Simpan Pengaturan
                  </Button>
                </div>
              </form>
            </CardContent>
          </Card>

          <Card class="mt-6">
            <CardHeader>
              <CardTitle>Pengaturan Footer</CardTitle>
              <CardDescription>
                Konfigurasi footer website
              </CardDescription>
            </CardHeader>
            <CardContent>
              <form @submit.prevent="submitFooter" class="space-y-6">
                <div class="space-y-4">
                  <div>
                    <Label for="copyright_text">Teks Copyright</Label>
                    <Input 
                      id="copyright_text" 
                      v-model="footerForm.copyright_text" 
                      type="text" 
                      class="mt-1" 
                      placeholder="Masukkan teks copyright" 
                    />
                  </div>

                  <div>
                    <Label for="copyright_year">Tahun Copyright</Label>
                    <Input 
                      id="copyright_year" 
                      v-model="footerForm.copyright_year" 
                      type="number" 
                      class="mt-1" 
                      placeholder="2023" 
                    />
                  </div>
                </div>

                <div class="flex justify-end">
                  <Button type="submit" :disabled="footerForm.processing">
                    <span v-if="footerForm.processing" class="mr-2">
                      <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" />
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                      </svg>
                    </span>
                    Simpan Pengaturan Footer
                  </Button>
                </div>
              </form>
            </CardContent>
          </Card>
        </TabsContent>

        <!-- Tab Branding -->
        <TabsContent value="branding" class="mt-6">
          <Card>
            <CardHeader>
              <CardTitle>Logo dan Favicon</CardTitle>
              <CardDescription>
                Upload logo dan favicon untuk website Anda
              </CardDescription>
            </CardHeader>
            <CardContent>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Logo -->
                <div>
                  <h3 class="text-sm font-medium text-gray-900">Logo Website</h3>
                  
                  <div class="mt-3 flex items-center">
                    <div class="h-24 w-24 overflow-hidden rounded-md border bg-gray-50 flex items-center justify-center">
                      <img v-if="logoPreview" :src="logoPreview" alt="Logo Preview" class="h-full w-full object-contain" />
                      <ImageIcon v-else class="h-12 w-12 text-gray-300" />
                    </div>
                    
                    <div class="ml-4">
                      <label for="logo" class="cursor-pointer rounded-md bg-white font-medium text-blue-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-blue-500 focus-within:ring-offset-2 hover:text-blue-500">
                        <span>Upload Logo</span>
                        <input 
                          id="logo" 
                          name="logo" 
                          type="file" 
                          class="sr-only" 
                          accept="image/*" 
                          @change="(e) => handleFileChange(e, 'logo')"
                        />
                      </label>
                      <p class="mt-1 text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                      
                      <Button 
                        v-if="logoForm.logo" 
                        type="button"
                        size="sm"
                        @click="uploadLogo"
                        :disabled="logoForm.processing"
                        class="mt-2"
                      >
                        <Upload v-if="!logoForm.processing" class="mr-2 h-4 w-4" />
                        <svg v-else class="mr-2 h-4 w-4 animate-spin" viewBox="0 0 24 24">
                          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" />
                          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                        </svg>
                        Simpan Logo
                      </Button>
                    </div>
                  </div>
                </div>
                
                <!-- Favicon -->
                <div>
                  <h3 class="text-sm font-medium text-gray-900">Favicon</h3>
                  
                  <div class="mt-3 flex items-center">
                    <div class="h-12 w-12 overflow-hidden rounded-md border bg-gray-50 flex items-center justify-center">
                      <img v-if="faviconPreview" :src="faviconPreview" alt="Favicon Preview" class="h-full w-full object-contain" />
                      <ImageIcon v-else class="h-6 w-6 text-gray-300" />
                    </div>
                    
                    <div class="ml-4">
                      <label for="favicon" class="cursor-pointer rounded-md bg-white font-medium text-blue-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-blue-500 focus-within:ring-offset-2 hover:text-blue-500">
                        <span>Upload Favicon</span>
                        <input 
                          id="favicon" 
                          name="favicon" 
                          type="file" 
                          class="sr-only" 
                          accept="image/x-icon,image/png" 
                          @change="(e) => handleFileChange(e, 'favicon')"
                        />
                      </label>
                      <p class="mt-1 text-xs text-gray-500">ICO, PNG up to 1MB</p>
                      
                      <Button 
                        v-if="faviconForm.favicon" 
                        type="button"
                        size="sm"
                        @click="uploadFavicon"
                        :disabled="faviconForm.processing"
                        class="mt-2"
                      >
                        <Upload v-if="!faviconForm.processing" class="mr-2 h-4 w-4" />
                        <svg v-else class="mr-2 h-4 w-4 animate-spin" viewBox="0 0 24 24">
                          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" />
                          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                        </svg>
                        Simpan Favicon
                      </Button>
                    </div>
                  </div>
                </div>
              </div>
            </CardContent>
          </Card>

          <Card class="mt-6">
            <CardHeader>
              <CardTitle>Gambar OG (Open Graph)</CardTitle>
              <CardDescription>
                Gambar default yang ditampilkan saat link website dibagikan
              </CardDescription>
            </CardHeader>
            <CardContent>
              <div>
                <div class="mt-3 flex items-center">
                  <div class="h-32 w-64 overflow-hidden rounded-md border bg-gray-50 flex items-center justify-center">
                    <img v-if="ogImagePreview" :src="ogImagePreview" alt="OG Image Preview" class="h-full w-full object-contain" />
                    <ImageIcon v-else class="h-12 w-12 text-gray-300" />
                  </div>
                  
                  <div class="ml-4">
                    <label for="og_image" class="cursor-pointer rounded-md bg-white font-medium text-blue-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-blue-500 focus-within:ring-offset-2 hover:text-blue-500">
                      <span>Upload Gambar OG</span>
                      <input 
                        id="og_image" 
                        name="og_image" 
                        type="file" 
                        class="sr-only" 
                        accept="image/*" 
                        @change="(e) => handleFileChange(e, 'og_image')"
                      />
                    </label>
                    <p class="mt-1 text-xs text-gray-500">PNG, JPG up to 2MB</p>
                    
                    <Button 
                      v-if="ogImageForm.og_image" 
                      type="button"
                      size="sm"
                      @click="uploadOgImage"
                      :disabled="ogImageForm.processing"
                      class="mt-2"
                    >
                      <Upload v-if="!ogImageForm.processing" class="mr-2 h-4 w-4" />
                      <svg v-else class="mr-2 h-4 w-4 animate-spin" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" />
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                      </svg>
                      Simpan Gambar OG
                    </Button>
                  </div>
                </div>
                <p class="mt-4 text-sm text-gray-500">
                  Gambar ini akan muncul saat URL website Anda dibagikan di media sosial. Ukuran ideal adalah 1200×630 piksel.
                </p>
              </div>
            </CardContent>
          </Card>
        </TabsContent>

        <!-- Tab Scripts -->
        <TabsContent value="scripts" class="mt-6">
          <Card>
            <CardHeader>
              <CardTitle>Skrip Kustom</CardTitle>
              <CardDescription>
                Tambahkan skrip kustom ke website Anda
              </CardDescription>
            </CardHeader>
            <CardContent>
              <form @submit.prevent="submitScripts" class="space-y-6">
                <div class="space-y-4">
                  <div>
                    <Label for="header_scripts">Skrip Header</Label>
                    <Textarea 
                      id="header_scripts" 
                      v-model="scriptsForm.header_scripts" 
                      class="mt-1 font-mono text-sm" 
                      placeholder="<!-- Masukkan skrip untuk header -->" 
                      rows="5"
                    />
                    <p class="mt-1 text-xs text-gray-500">
                      Kode akan ditempatkan di akhir tag &lt;head&gt;
                    </p>
                  </div>
                  
                  <div>
                    <Label for="meta_pixel_script">Facebook Pixel</Label>
                    <Textarea 
                      id="meta_pixel_script" 
                      v-model="scriptsForm.meta_pixel_script" 
                      class="mt-1 font-mono text-sm" 
                      placeholder="<!-- Masukkan kode Meta Pixel -->" 
                      rows="3"
                    />
                  </div>
                  
                  <div>
                    <Label for="tiktok_pixel_script">TikTok Pixel</Label>
                    <Textarea 
                      id="tiktok_pixel_script" 
                      v-model="scriptsForm.tiktok_pixel_script" 
                      class="mt-1 font-mono text-sm" 
                      placeholder="<!-- Masukkan kode TikTok Pixel -->" 
                      rows="3"
                    />
                  </div>
                  
                  <div>
                    <Label for="google_tag_script">Google Tag Manager</Label>
                    <Textarea 
                      id="google_tag_script" 
                      v-model="scriptsForm.google_tag_script" 
                      class="mt-1 font-mono text-sm" 
                      placeholder="<!-- Masukkan kode Google Tag Manager -->" 
                      rows="3"
                    />
                  </div>
                  
                  <div>
                    <Label for="footer_scripts">Skrip Footer</Label>
                    <Textarea 
                      id="footer_scripts" 
                      v-model="scriptsForm.footer_scripts" 
                      class="mt-1 font-mono text-sm" 
                      placeholder="<!-- Masukkan skrip untuk footer -->" 
                      rows="5"
                    />
                    <p class="mt-1 text-xs text-gray-500">
                      Kode akan ditempatkan sebelum tag &lt;/body&gt;
                    </p>
                  </div>
                </div>
                
                <div class="flex justify-end">
                  <Button type="submit" :disabled="scriptsForm.processing">
                    <span v-if="scriptsForm.processing" class="mr-2">
                      <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" />
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                      </svg>
                    </span>
                    Simpan Skrip
                  </Button>
                </div>
              </form>
            </CardContent>
          </Card>
        </TabsContent>

        <!-- Tab Kontak -->
        <TabsContent value="contact" class="mt-6">
          <!-- Baris pertama: Pengaturan Email dan Pengaturan Webhook -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Pengaturan Email -->
            <Card>
              <CardHeader>
                <CardTitle>Pengaturan Email</CardTitle>
                <CardDescription>
                  Konfigurasi email untuk notifikasi dan komunikasi
                </CardDescription>
              </CardHeader>
              <CardContent>
                <p class="text-sm text-gray-600 mb-4">
                  Kelola pengaturan SMTP, template email, dan pengaturan lainnya terkait email.
                </p>
                <Button type="button" @click="navigateToEmailSettings">
                  <MailIcon class="mr-2 h-4 w-4" />
                  Buka Pengaturan Email
                </Button>
              </CardContent>
            </Card>

            <!-- Pengaturan Webhook -->
            <Card>
              <CardHeader>
                <CardTitle>Pengaturan Webhook</CardTitle>
                <CardDescription>
                  Konfigurasi webhook untuk integrasi dengan layanan pihak ketiga
                </CardDescription>
              </CardHeader>
              <CardContent>
                <p class="text-sm text-gray-600 mb-4">
                  Kelola pengaturan webhook untuk WhatsApp Marketing dan integrasi lainnya.
                </p>
                <Button type="button" @click="showWebhookDialog = true">
                  <Settings class="mr-2 h-4 w-4" />
                  Buka Pengaturan Webhook
                </Button>
              </CardContent>
            </Card>
          </div>

          <!-- Baris kedua: WhatsApp Admin dan Social Media -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- WhatsApp Admin -->
            <Card>
              <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                <CardTitle>WhatsApp Admin</CardTitle>
                <Button 
                  variant="outline" 
                  size="icon" 
                  @click="showWhatsappDialog = true"
                  class="h-8 w-8"
                >
                  <Plus class="h-4 w-4" />
                  <span class="sr-only">Tambah WhatsApp</span>
                </Button>
              </CardHeader>
              <CardContent>
                <div class="space-y-4">
                  <div v-if="whatsappAdmins.length === 0" class="text-center py-4">
                    <p class="text-sm text-gray-500">Belum ada kontak WhatsApp Admin</p>
                  </div>
                  <div v-else class="space-y-3">
                    <div v-for="whatsapp in whatsappAdmins" :key="whatsapp.id" class="flex items-center justify-between border-b pb-3 last:border-0 last:pb-0">
                      <div>
                        <div class="font-medium">{{ whatsapp.name }}</div>
                        <div class="text-sm text-gray-500">{{ whatsapp.phone_number }}</div>
                        <div v-if="whatsapp.description" class="text-xs text-gray-400 mt-1">{{ whatsapp.description }}</div>
                      </div>
                      <div class="flex items-center space-x-2">
                        <button 
                          type="button"
                          class="text-sm text-gray-500 hover:text-indigo-600 focus:outline-none transition-colors"
                          @click="toggleWhatsappStatus(whatsapp)"
                        >
                          <Check v-if="whatsapp.is_active" class="h-5 w-5 text-green-500" />
                          <span v-else class="h-5 w-5 flex items-center justify-center text-gray-300 rounded-full border border-gray-300">-</span>
                        </button>
                        
                        <button 
                          type="button" 
                          class="text-sm text-gray-500 hover:text-red-600 focus:outline-none transition-colors"
                          @click="deleteWhatsappAdmin(whatsapp.id)"
                        >
                          <Trash2 class="h-4 w-4" />
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </CardContent>
            </Card>

            <!-- Social Media -->
            <Card>
              <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                <CardTitle>Media Sosial</CardTitle>
                <Button 
                  variant="outline" 
                  size="icon" 
                  @click="showSocialDialog = true"
                  class="h-8 w-8"
                >
                  <Plus class="h-4 w-4" />
                  <span class="sr-only">Tambah Media Sosial</span>
                </Button>
              </CardHeader>
              <CardContent>
                <div class="space-y-4">
                  <div v-if="socialMedia.length === 0" class="text-center py-4">
                    <p class="text-sm text-gray-500">Belum ada media sosial</p>
                  </div>
                  <div v-else class="max-h-[250px] overflow-y-auto pr-2">
                    <div class="space-y-3">
                      <div v-for="social in socialMedia" :key="social.id" class="flex items-center justify-between border rounded-lg p-3">
                        <div class="flex items-center space-x-3">
                          <div class="bg-gray-100 rounded-full p-2">
                            <component :is="getSocialIcon(social.platform)" class="h-5 w-5 text-gray-600" />
                          </div>
                          <div>
                            <div class="font-medium capitalize">{{ social.platform }}</div>
                            <div class="text-sm text-gray-500">@{{ social.username }}</div>
                          </div>
                        </div>
                        <div class="flex items-center space-x-2">
                          <button 
                            type="button"
                            class="text-sm text-gray-500 hover:text-indigo-600 focus:outline-none transition-colors"
                            @click="toggleSocialMediaStatus(social)"
                          >
                            <Check v-if="social.is_active" class="h-5 w-5 text-green-500" />
                            <span v-else class="h-5 w-5 flex items-center justify-center text-gray-300 rounded-full border border-gray-300">-</span>
                          </button>
                          
                          <button 
                            type="button" 
                            class="text-sm text-gray-500 hover:text-red-600 focus:outline-none transition-colors"
                            @click="deleteSocialMedia(social.id)"
                          >
                            <Trash2 class="h-4 w-4" />
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </CardContent>
            </Card>
          </div>
        </TabsContent>
      </Tabs>
    </div>
  </AppLayout>

  <!-- Dialog untuk WhatsApp Admin -->
  <Dialog :open="showWhatsappDialog" @update:open="showWhatsappDialog = $event">
    <DialogContent>
      <DialogHeader>
        <DialogTitle>Tambah WhatsApp Admin</DialogTitle>
        <DialogDescription>
          Tambahkan kontak WhatsApp untuk admin atau customer service.
        </DialogDescription>
      </DialogHeader>
      
      <form @submit.prevent="createWhatsappAdmin" class="space-y-4">
        <div>
          <Label for="whatsapp_name">Nama</Label>
          <Input 
            id="whatsapp_name" 
            v-model="whatsappForm.name" 
            type="text" 
            class="mt-1" 
            placeholder="Admin Support" 
            required
          />
        </div>
        
        <div>
          <Label for="whatsapp_phone">Nomor WhatsApp</Label>
          <Input 
            id="whatsapp_phone" 
            v-model="whatsappForm.phone_number" 
            type="text" 
            class="mt-1" 
            placeholder="6281234567890" 
            required
          />
          <p class="mt-1 text-xs text-gray-500">
            Format: kode negara + nomor telepon (tanpa spasi & tanda)
          </p>
        </div>
        
        <div>
          <Label for="whatsapp_description">Deskripsi (Opsional)</Label>
          <Input 
            id="whatsapp_description" 
            v-model="whatsappForm.description" 
            type="text" 
            class="mt-1" 
            placeholder="Customer Service" 
          />
        </div>
        
        <DialogFooter>
          <Button type="button" variant="outline" @click="showWhatsappDialog = false">
            Batal
          </Button>
          <Button type="submit" :disabled="whatsappForm.processing">
            <span v-if="whatsappForm.processing" class="mr-2">
              <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" />
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
              </svg>
            </span>
            Tambahkan
          </Button>
        </DialogFooter>
      </form>
    </DialogContent>
  </Dialog>

  <!-- Dialog untuk Social Media -->
  <Dialog :open="showSocialDialog" @update:open="showSocialDialog = $event">
    <DialogContent>
      <DialogHeader>
        <DialogTitle>Tambah Media Sosial</DialogTitle>
        <DialogDescription>
          Tambahkan profil media sosial perusahaan Anda.
        </DialogDescription>
      </DialogHeader>
      
      <form @submit.prevent="createSocialMedia" class="space-y-4">
        <div>
          <Label for="social_platform">Platform</Label>
          <div class="relative mt-1">
            <div 
              class="custom-select-container" 
              ref="platformSelectRef"
            >
              <div 
                @click="togglePlatformSelect" 
                class="custom-select-trigger flex w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 cursor-pointer h-10"
              >
                <span>
                  <span v-if="socialMediaForm.platform" class="flex items-center">
                    <component 
                      :is="getPlatformIcon(socialMediaForm.platform)" 
                      class="mr-2 h-4 w-4" 
                    />
                    {{ getPlatformLabel(socialMediaForm.platform) }}
                  </span>
                  <span v-else>Pilih Platform</span>
                </span>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-50 transition-transform" :class="{ 'rotate-180': isPlatformSelectOpen }">
                  <path d="m6 9 6 6 6-6"></path>
                </svg>
              </div>
              
              <div 
                v-if="isPlatformSelectOpen" 
                class="custom-select-dropdown bg-background border border-input rounded-md shadow-lg mt-1 overflow-hidden z-50"
              >
                <div 
                  v-for="platform in availablePlatforms" 
                  :key="platform.value"
                  @click="selectPlatform(platform.value)"
                  class="custom-select-option py-2 px-3 hover:bg-secondary cursor-pointer text-sm flex items-center"
                  :class="{ 'bg-secondary font-medium': socialMediaForm.platform === platform.value }"
                >
                  <component 
                    :is="platform.icon || Share2" 
                    class="mr-2 h-4 w-4" 
                  />
                  {{ platform.label }}
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div>
          <Label for="social_username">Username</Label>
          <Input 
            id="social_username" 
            v-model="socialMediaForm.username" 
            type="text" 
            class="mt-1" 
            placeholder="username" 
            required
          />
        </div>
        
        <div>
          <Label for="social_url">URL</Label>
          <Input 
            id="social_url" 
            v-model="socialMediaForm.url" 
            type="url" 
            class="mt-1" 
            placeholder="https://example.com/username" 
            required
          />
        </div>
        
        <DialogFooter>
          <Button type="button" variant="outline" @click="showSocialDialog = false">
            Batal
          </Button>
          <Button type="submit" :disabled="socialMediaForm.processing || !socialMediaForm.platform">
            <span v-if="socialMediaForm.processing" class="mr-2">
              <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" />
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
              </svg>
            </span>
            Tambahkan
          </Button>
        </DialogFooter>
      </form>
    </DialogContent>
  </Dialog>

  <!-- Dialog untuk Webhook Settings -->
  <Dialog :open="showWebhookDialog" @update:open="showWebhookDialog = $event">
    <DialogContent>
      <DialogHeader>
        <DialogTitle>Pengaturan WhatsApp Webhook</DialogTitle>
        <DialogDescription>
          Konfigurasi API dan webhook untuk integrasi WhatsApp Marketing
        </DialogDescription>
      </DialogHeader>
      
      <form @submit.prevent="saveWebhookSettings" class="space-y-4">
        <div>
          <Label for="sender_phone">Nomor WhatsApp Pengirim</Label>
          <Input 
            id="sender_phone" 
            v-model="webhookForm.sender_phone" 
            type="text" 
            class="mt-1" 
            placeholder="628123456789" 
          />
          <p class="mt-1 text-xs text-gray-500">
            Nomor WhatsApp yang sudah terhubung dengan Dripsender (format: 628xxx)
          </p>
        </div>

        <div>
          <Label for="api_key">API Key</Label>
          <Input 
            id="api_key" 
            v-model="webhookForm.api_key" 
            type="text" 
            class="mt-1" 
            placeholder="f75acb01-32d8-4448-8086-4aedce9f1d6f" 
          />
          <p class="mt-1 text-xs text-gray-500">
            API Key dari layanan WhatsApp Marketing Anda
          </p>
        </div>
        
        <div>
          <Label for="webhook_url">Webhook URL</Label>
          <Input 
            id="webhook_url" 
            v-model="webhookForm.webhook_url" 
            type="text" 
            class="mt-1" 
            placeholder="https://example.dripsender.id:14773/api/integration/uuid" 
          />
          <p class="mt-1 text-xs text-gray-500">
            URL webhook untuk mengirim pesan otomatis
          </p>
        </div>

        <div class="flex items-center space-x-2">
          <Checkbox 
            id="is_active" 
            v-model="webhookForm.is_active"
            :checked="webhookForm.is_active === true"
            @update:modelValue="webhookForm.is_active = $event === true"
          />
          <Label for="is_active" @click="webhookForm.is_active = !webhookForm.is_active">
            Aktifkan notifikasi WhatsApp
          </Label>
        </div>
        
        <DialogFooter>
          <Button type="button" variant="outline" @click="showWebhookDialog = false">
            Batal
          </Button>
          <Button type="submit" :disabled="webhookForm.processing">
            <span v-if="webhookForm.processing" class="mr-2">
              <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" />
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
              </svg>
            </span>
            Simpan Pengaturan
          </Button>
        </DialogFooter>
      </form>
    </DialogContent>
  </Dialog>
</template>

<style>
/* Custom select styling */
.custom-select-container {
  position: relative;
  width: 100%;
  border-radius: 0.375rem;
}

.custom-select-dropdown {
  position: absolute;
  top: calc(100%);
  left: 0;
  width: 100%;
  max-height: 200px;
  overflow-y: auto;
  animation: slideDown 0.15s ease-out;
  z-index: 50;
}

.custom-select-option:first-child {
  border-top-left-radius: 0.375rem;
  border-top-right-radius: 0.375rem;
}

.custom-select-option:last-child {
  border-bottom-left-radius: 0.375rem;
  border-bottom-right-radius: 0.375rem;
}

@keyframes slideDown {
  from { opacity: 0; transform: translateY(-5px); }
  to { opacity: 1; transform: translateY(0); }
}

/* Perbaikan outline saat fokus */
.custom-select-trigger {
  outline: none !important;
  -webkit-appearance: none;
  -webkit-tap-highlight-color: transparent !important;
}

.custom-select-trigger:focus,
.custom-select-trigger:focus-visible,
.custom-select-trigger:active,
.custom-select-trigger:hover,
.custom-select-trigger:-moz-focusring {
  outline: none !important;
  box-shadow: none !important;
  border-color: #0ea5e9 !important;
}

/* Fix untuk Firefox */
.custom-select-trigger:-moz-focusring {
  outline: none !important;
}

/* Fix untuk Safari dan Chrome */
.custom-select-trigger::-webkit-focus-inner {
  border: 0;
}

/* Perbaikan dark mode */
:root {
  --card-bg-dark: #1a2234;
  --card-border-dark: #2c3850;
  --text-primary-dark: #ffffff;
  --text-secondary-dark: #a8b3cf;
  --text-muted-dark: #6b7a9a;
  --border-subtle-dark: #2c3850;
}

.dark .text-gray-900 {
  color: var(--text-primary-dark);
}

.dark .text-gray-600, 
.dark .text-gray-500, 
.dark .text-gray-400 {
  color: var(--text-secondary-dark);
}

.dark .border-b {
  border-color: var(--border-subtle-dark);
}

.dark .bg-gray-50, 
.dark .bg-gray-100 {
  background-color: #283042;
}

/* Perbaikan tampilan card di dark mode */
.dark .border {
  border-color: var(--card-border-dark);
}

.dark .rounded-lg {
  background-color: #232b3d;
  border-color: var(--card-border-dark);
}

/* Tingkatkan kontras pada tombol-tombol */
.dark button.text-gray-500:hover {
  color: #c1d1f7;
}

/* Perbaikan warna hover pada form element */
.dark .focus\:border-blue-500:focus,
.dark .hover\:border-blue-500:hover {
  border-color: #60a5fa;
}

/* Perbaikan kontras pada label dan text pada dark mode */
.dark label, 
.dark h3.text-sm {
  color: var(--text-primary-dark);
}

/* Perbaikan tampilan dialog/modal di dark mode */
.dark .bg-background {
  background-color: var(--card-bg-dark);
}
</style> 