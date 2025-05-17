<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select } from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import ConfirmationDialog from '@/components/ui/ConfirmationDialog.vue';
import { Mail } from 'lucide-vue-next';
import { toast } from 'vue-sonner';
import axios from 'axios';

const props = defineProps<{
  emailSettings: {
    id: number;
    mail_driver: string;
    mail_host: string | null;
    mail_port: string | null;
    mail_username: string | null;
    mail_password: string | null;
    mail_encryption: string | null;
    mail_from_address: string | null;
    mail_from_name: string | null;
    enable_verification: boolean;
    verification_template: string | null;
    reset_password_template: string | null;
    enable_reset_password_template: boolean;
  }
}>();

// Breadcrumbs untuk navigasi
const breadcrumbs = [
  {
    title: 'Admin',
    href: route('admin.dashboard'),
  },
  {
    title: 'Pengaturan Email',
    href: route('admin.email.index'),
  },
];

const form = useForm({
  mail_driver: props.emailSettings?.mail_driver || 'smtp',
  mail_host: props.emailSettings?.mail_host || '',
  mail_port: props.emailSettings?.mail_port || '',
  mail_username: props.emailSettings?.mail_username || '',
  mail_password: props.emailSettings?.mail_password || '',
  mail_encryption: props.emailSettings?.mail_encryption || 'tls',
  mail_from_address: props.emailSettings?.mail_from_address || '',
  mail_from_name: props.emailSettings?.mail_from_name || '',
  enable_verification: props.emailSettings?.enable_verification ?? true,
  enable_reset_password_template: props.emailSettings?.enable_reset_password_template ?? true,
  verification_template: props.emailSettings?.verification_template || `<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verifikasi Email Anda</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; margin: 0; padding: 20px; color: #333; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .header { background-color: #4f46e5; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; }
        .button { display: inline-block; background-color: #4f46e5; color: white; text-decoration: none; padding: 10px 20px; border-radius: 4px; margin-top: 20px; }
        .footer { background-color: #f9fafb; padding: 15px; text-align: center; font-size: 12px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Verifikasi Email Anda</h1>
        </div>
        <div class="content">
            <p>Hai {name},</p>
            <p>Terima kasih telah mendaftar di aplikasi kami. Silakan klik tombol di bawah ini untuk memverifikasi alamat email Anda.</p>
            <div style="text-align: center;">
                <a href="{verification_url}" class="button">Verifikasi Email</a>
            </div>
            <p>Jika Anda tidak membuat akun, abaikan email ini.</p>
            <p>Salam,<br>Tim Kami</p>
        </div>
        <div class="footer">
            <p>Email ini dikirim secara otomatis, mohon tidak membalas email ini.</p>
        </div>
    </div>
</body>
</html>`,
  reset_password_template: props.emailSettings?.reset_password_template || `<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reset Password</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; margin: 0; padding: 20px; color: #333; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .header { background-color: #4f46e5; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; }
        .button { display: inline-block; background-color: #4f46e5; color: white; text-decoration: none; padding: 10px 20px; border-radius: 4px; margin-top: 20px; }
        .footer { background-color: #f9fafb; padding: 15px; text-align: center; font-size: 12px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Reset Password</h1>
        </div>
        <div class="content">
            <p>Hai {name},</p>
            <p>Kami menerima permintaan untuk mereset password akun Anda. Silakan klik tombol di bawah ini untuk membuat password baru.</p>
            <div style="text-align: center;">
                <a href="{reset_url}" class="button">Reset Password</a>
            </div>
            <p>Link reset password ini akan kadaluarsa dalam 60 menit.</p>
            <p>Jika Anda tidak meminta reset password, abaikan email ini.</p>
            <p>Salam,<br>Tim Kami</p>
        </div>
        <div class="footer">
            <p>Email ini dikirim secara otomatis, mohon tidak membalas email ini.</p>
        </div>
    </div>
</body>
</html>`,
});

const processing = ref(false);
const showTestEmailDialog = ref(false);
const testEmail = ref('');
const isSelectOpen = ref(false);
const isEncryptionSelectOpen = ref(false);
const selectRef = ref<HTMLElement | null>(null);
const encryptionSelectRef = ref<HTMLElement | null>(null);

// Data untuk email drivers
const emailDrivers = [
  { value: 'smtp', label: 'SMTP' },
  { value: 'mailgun', label: 'Mailgun' },
  { value: 'ses', label: 'Amazon SES' },
  { value: 'log', label: 'Log (untuk pengujian)' }
];

// Data untuk opsi enkripsi
const encryptionOptions = [
  { value: 'tls', label: 'TLS' },
  { value: 'ssl', label: 'SSL' },
  { value: '', label: 'Tidak Ada' }
];

// Computed property untuk label driver terpilih
const selectedDriverLabel = computed(() => {
  const driver = emailDrivers.find(d => d.value === form.mail_driver);
  return driver ? driver.label : 'Pilih driver...';
});

// Computed property untuk label enkripsi terpilih
const selectedEncryptionLabel = computed(() => {
  const encryption = encryptionOptions.find(e => e.value === form.mail_encryption);
  return encryption ? encryption.label : 'Pilih enkripsi...';
});

// Toggle dropdown driver
const toggleSelect = () => {
  isSelectOpen.value = !isSelectOpen.value;
  isEncryptionSelectOpen.value = false;
};

// Toggle dropdown enkripsi
const toggleEncryptionSelect = () => {
  isEncryptionSelectOpen.value = !isEncryptionSelectOpen.value;
  isSelectOpen.value = false;
};

// Pilih driver
const selectDriver = (driverValue: string) => {
  form.mail_driver = driverValue;
  isSelectOpen.value = false;
};

// Pilih enkripsi
const selectEncryption = (encryptionValue: string) => {
  form.mail_encryption = encryptionValue;
  isEncryptionSelectOpen.value = false;
};

// Handle click outside
const handleClickOutside = (event: MouseEvent) => {
  if (selectRef.value && !selectRef.value.contains(event.target as Node)) {
    isSelectOpen.value = false;
  }
  if (encryptionSelectRef.value && !encryptionSelectRef.value.contains(event.target as Node)) {
    isEncryptionSelectOpen.value = false;
  }
};

// Lifecycle hooks untuk select
onMounted(() => {
  document.addEventListener('click', handleClickOutside);
  selectRef.value = document.querySelector('.custom-select-container') as HTMLElement;
  encryptionSelectRef.value = document.querySelector('.custom-select-container:nth-child(2)') as HTMLElement;
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});

const updateEmailSettings = () => {
  processing.value = true;
  form.put(route('admin.email.update'), {
    onSuccess: () => {
      toast.success('Berhasil', {
        description: 'Pengaturan email berhasil disimpan',
      });
      processing.value = false;
    },
    onError: () => {
      toast.error('Gagal', {
        description: 'Terjadi kesalahan saat menyimpan pengaturan',
      });
      processing.value = false;
    },
  });
};

const sendTestEmail = () => {
  processing.value = true;
  axios.post(route('admin.email.test'), {
    test_email: testEmail.value,
  }).then(() => {
    toast.success('Berhasil', {
      description: 'Email tes berhasil dikirim',
    });
    showTestEmailDialog.value = false;
  }).catch((error: any) => {
    toast.error('Gagal', {
      description: `Terjadi kesalahan: ${error.response?.data?.message || error.message}`,
    });
  }).finally(() => {
    processing.value = false;
  });
};
</script>

<template>
  <Head title="Pengaturan Email" />
  
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h1 class="text-2xl font-bold">Pengaturan Email</h1>
      </div>

      <div class="bg-white dark:bg-slate-800 text-secondary-900 dark:text-white rounded-xl shadow border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="p-6 border-b border-secondary-200 dark:border-slate-700">
          <h2 class="text-lg font-medium">Konfigurasi SMTP</h2>
          <p class="text-muted-foreground mt-1">Atur konfigurasi email server untuk notifikasi dan verifikasi.</p>
        </div>
        
        <div class="p-6">
          <form @submit.prevent="updateEmailSettings">
            <!-- Pengaturan Driver dan Enkripsi -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
              <div>
                <Label for="mail_driver">Driver Email</Label>
                <div class="relative mt-1">
                  <div 
                    class="custom-select-container" 
                    :class="{ 'active': isSelectOpen }"
                  >
                    <div 
                      @click="toggleSelect" 
                      class="custom-select-trigger flex w-full items-center justify-between gap-2 rounded-md border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 px-3 py-2 text-sm shadow-sm hover:border-slate-300 dark:hover:border-slate-600 focus:outline-none focus:ring-0 disabled:cursor-not-allowed disabled:opacity-50 cursor-pointer h-9"
                    >
                      <span>{{ selectedDriverLabel }}</span>
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-50 transition-transform" :class="{ 'rotate-180': isSelectOpen }">
                        <path d="m6 9 6 6 6-6"></path>
                      </svg>
                    </div>
                    
                    <div 
                      v-if="isSelectOpen" 
                      class="custom-select-dropdown bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-md shadow-lg mt-1 overflow-hidden z-50"
                    >
                      <div 
                        v-for="driver in emailDrivers" 
                        :key="driver.value"
                        @click="selectDriver(driver.value)"
                        class="custom-select-option py-2 px-3 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 cursor-pointer text-sm"
                        :class="{ 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300 font-medium': form.mail_driver === driver.value }"
                      >
                        {{ driver.label }}
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div>
                <Label for="mail_encryption">Enkripsi</Label>
                <div class="relative mt-1">
                  <div 
                    class="custom-select-container" 
                    :class="{ 'active': isEncryptionSelectOpen }"
                  >
                    <div 
                      @click="toggleEncryptionSelect" 
                      class="custom-select-trigger flex w-full items-center justify-between gap-2 rounded-md border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 px-3 py-2 text-sm shadow-sm hover:border-slate-300 dark:hover:border-slate-600 focus:outline-none focus:ring-0 disabled:cursor-not-allowed disabled:opacity-50 cursor-pointer h-9"
                    >
                      <span>{{ selectedEncryptionLabel }}</span>
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-50 transition-transform" :class="{ 'rotate-180': isEncryptionSelectOpen }">
                        <path d="m6 9 6 6 6-6"></path>
                      </svg>
                    </div>
                    
                    <div 
                      v-if="isEncryptionSelectOpen" 
                      class="custom-select-dropdown bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-md shadow-lg mt-1 overflow-hidden z-50"
                    >
                      <div 
                        v-for="encryption in encryptionOptions" 
                        :key="encryption.value"
                        @click="selectEncryption(encryption.value)"
                        class="custom-select-option py-2 px-3 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 cursor-pointer text-sm"
                        :class="{ 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300 font-medium': form.mail_encryption === encryption.value }"
                      >
                        {{ encryption.label }}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- SMTP Settings -->
            <div v-if="form.mail_driver === 'smtp'" class="space-y-4">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <Label for="mail_host">SMTP Host</Label>
                  <Input v-model="form.mail_host" id="mail_host" type="text" class="mt-1.5 border border-slate-200 dark:border-slate-700" />
                </div>
                <div>
                  <Label for="mail_port">SMTP Port</Label>
                  <Input v-model="form.mail_port" id="mail_port" type="text" class="mt-1.5 border border-slate-200 dark:border-slate-700" />
                </div>
              </div>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <Label for="mail_username">SMTP Username</Label>
                  <Input v-model="form.mail_username" id="mail_username" type="text" class="mt-1.5 border border-slate-200 dark:border-slate-700" />
                </div>
                <div>
                  <Label for="mail_password">SMTP Password</Label>
                  <Input v-model="form.mail_password" id="mail_password" type="password" class="mt-1.5 border border-slate-200 dark:border-slate-700" />
                </div>
              </div>
            </div>
            
            <!-- From Address -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
              <div>
                <Label for="mail_from_address">Alamat Email Pengirim</Label>
                <Input v-model="form.mail_from_address" id="mail_from_address" type="email" class="mt-1.5 border border-slate-200 dark:border-slate-700" />
              </div>
              <div>
                <Label for="mail_from_name">Nama Pengirim</Label>
                <Input v-model="form.mail_from_name" id="mail_from_name" type="text" class="mt-1.5 border border-slate-200 dark:border-slate-700" />
              </div>
            </div>
            
            <!-- Verifikasi Email -->
            <div class="mt-8">
              <h3 class="text-md font-medium">Pengaturan Verifikasi Email</h3>
              <div class="mt-4">
                <div class="flex items-center space-x-2">
                  <label 
                    class="inline-flex items-center gap-2 cursor-pointer"
                    for="enable_verification"
                  >
                    <Switch 
                      id="enable_verification"
                      v-model="form.enable_verification"
                      name="enable_verification"
                    />
                    <span>Aktifkan verifikasi email untuk pendaftaran pengguna baru</span>
                  </label>
                </div>
              </div>
            </div>
            
            <!-- Template Email -->
            <div class="mt-4" v-if="form.enable_verification">
              <div class="space-y-6">
                <div>
                  <Label for="verification_template">Template Email Verifikasi (HTML)</Label>
                  <textarea 
                    v-model="form.verification_template" 
                    id="verification_template" 
                    rows="10" 
                    class="flex min-h-[80px] w-full rounded-md border border-slate-200 dark:border-slate-700 bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 resize-none mt-1.5 font-mono text-sm"
                    placeholder="Template HTML dengan variabel: {name}, {verification_url}"
                  ></textarea>
                  <p class="text-xs text-muted-foreground mt-1">
                    Gunakan variabel: {name} untuk nama pengguna, {verification_url} untuk tautan verifikasi
                  </p>
                </div>
              </div>
            </div>
            
            <!-- Reset Password Template -->
            <div class="mt-8">
              <h3 class="text-md font-medium">Pengaturan Reset Password</h3>
              <div class="mt-4">
                <div class="flex items-center space-x-2">
                  <label 
                    class="inline-flex items-center gap-2 cursor-pointer"
                    for="enable_reset_password_template"
                  >
                    <Switch 
                      id="enable_reset_password_template"
                      v-model="form.enable_reset_password_template"
                      name="enable_reset_password_template"
                    />
                    <span>Aktifkan template kustom untuk email reset password</span>
                  </label>
                </div>
              </div>
            </div>
            
            <div class="mt-4" v-if="form.enable_reset_password_template">
              <div class="space-y-6">
                <div>
                  <Label for="reset_password_template">Template Email Reset Password (HTML)</Label>
                  <textarea 
                    v-model="form.reset_password_template" 
                    id="reset_password_template" 
                    rows="10" 
                    class="flex min-h-[80px] w-full rounded-md border border-slate-200 dark:border-slate-700 bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 resize-none mt-1.5 font-mono text-sm"
                    placeholder="Template HTML dengan variabel: {name}, {reset_url}"
                  ></textarea>
                  <p class="text-xs text-muted-foreground mt-1">
                    Gunakan variabel: {name} untuk nama pengguna, {reset_url} untuk tautan reset password
                  </p>
                </div>
              </div>
            </div>
            
            <div class="flex gap-4 mt-8">
              <Button type="submit" :disabled="processing">Simpan Pengaturan</Button>
              <Button type="button" variant="outline" @click="showTestEmailDialog = true">Kirim Email Tes</Button>
            </div>
          </form>
        </div>
      </div>
    </div>
    
    <!-- Dialog Test Email -->
    <ConfirmationDialog
      :open="showTestEmailDialog"
      @update:open="showTestEmailDialog = $event"
      title="Kirim Email Tes"
      :icon="Mail"
      confirmLabel="Kirim Email"
      :loading="processing"
      @confirm="sendTestEmail()"
    >
      <div class="py-2">
        <Label for="test_email">Alamat Email Penerima</Label>
        <Input 
          v-model="testEmail" 
          id="test_email" 
          type="email" 
          placeholder="Masukkan alamat email untuk pengujian"
          class="mt-1.5 border border-slate-200 dark:border-slate-700" 
        />
      </div>
      <p class="text-sm text-muted-foreground mt-2">
        Email tes akan dikirim untuk memverifikasi pengaturan SMTP Anda.
      </p>
    </ConfirmationDialog>
  </AppLayout>
</template>

<style>
/* Custom select styling */
.custom-select-container {
  position: relative;
  width: 100%;
  -webkit-tap-highlight-color: transparent;
  border-radius: 0.375rem;
}

.custom-select-dropdown {
  position: absolute;
  top: 100%;
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

/* Fix tambahan untuk Chrome */
*:focus {
  outline-color: transparent !important;
}
</style> 