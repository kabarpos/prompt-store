<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import { ArrowLeft, Mail, CheckCircle } from 'lucide-vue-next';
import { onMounted, nextTick, ref, watch, computed, onUnmounted } from 'vue';
import axios from 'axios';
import { toast } from 'vue-sonner';
import { Teleport } from 'vue';
import InputError from '@/components/InputError.vue';

// Breadcrumbs untuk navigasi
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Admin',
        href: route('admin.dashboard'),
    },
    {
        title: 'Manajemen Pengguna',
        href: route('admin.users.index'),
    },
    {
        title: 'Edit Pengguna',
        href: '#',
    },
];

// Definisi props dari controller
const props = defineProps<{
    user: {
        id: number;
        name: string;
        email: string;
        whatsapp: string | null;
        status: string;
        email_verified_at: string | null;
    };
    roles: Array<{
        id: number;
        name: string;
    }>;
    userRoles: number[];
}>();

// State untuk menyimpan error status role
const roleError = ref('');
const isEmailVerified = ref(!!props.user.email_verified_at);
const isResendingVerification = ref(false);
const isMarkingVerified = ref(false);

// Buat form untuk mengedit data pengguna
const form = useForm({
    name: props.user.name,
    email: props.user.email,
    whatsapp: props.user.whatsapp || '',
    password: '',
    password_confirmation: '',
    status: props.user.status,
    role_ids: props.userRoles.map(id => Number(id)),
});

// Mengirim ulang email verifikasi
const resendVerificationEmail = () => {
    isResendingVerification.value = true;
    axios.post(route('admin.users.resend-verification', props.user.id))
        .then(() => {
            toast.success('Berhasil', {
                description: 'Email verifikasi berhasil dikirim ulang',
            });
        })
        .catch((error) => {
            toast.error('Gagal', {
                description: `Terjadi kesalahan: ${error.response?.data?.message || error.message}`,
            });
        })
        .finally(() => {
            isResendingVerification.value = false;
        });
};

// Menandai email sebagai terverifikasi secara manual
const markEmailAsVerified = () => {
    isMarkingVerified.value = true;
    axios.post(route('admin.users.mark-verified', props.user.id))
        .then(() => {
            toast.success('Berhasil', {
                description: 'Email pengguna telah ditandai sebagai terverifikasi',
            });
            isEmailVerified.value = true;
        })
        .catch((error) => {
            toast.error('Gagal', {
                description: `Terjadi kesalahan: ${error.response?.data?.message || error.message}`,
            });
        })
        .finally(() => {
            isMarkingVerified.value = false;
        });
};

// Watcher untuk memvalidasi setidaknya ada 1 role yang dipilih
watch(() => form.role_ids, (newValue) => {
    if (newValue.length === 0) {
        roleError.value = 'Pengguna harus memiliki minimal 1 peran';
    } else {
        roleError.value = '';
    }
}, { immediate: true });

// Cek apakah sebuah role dipilih
const isRoleSelected = (roleId: number) => {
    roleId = Number(roleId);
    return form.role_ids.includes(roleId);
};

// Toggle role selection
const toggleRole = (roleId: number) => {
    roleId = Number(roleId);
    const index = form.role_ids.indexOf(roleId);
    
    if (index === -1) {
        form.role_ids.push(roleId);
    } else {
        // Jangan izinkan menghapus role jika hanya tersisa 1
        if (form.role_ids.length > 1) {
            form.role_ids.splice(index, 1);
        } else {
            // Jika mencoba menghapus role terakhir, tampilkan pesan error
            roleError.value = 'Pengguna harus memiliki minimal 1 peran';
        }
    }
};

// Submit form
const submit = () => {
    // Validasi minimal 1 role sebelum submit
    if (form.role_ids.length === 0) {
        roleError.value = 'Pengguna harus memiliki minimal 1 peran';
        return;
    }

    // Pastikan semua role_ids adalah number sebelum dikirim
    form.role_ids = form.role_ids.map(id => Number(id));

    form.put(route('admin.users.update', props.user.id), {
        preserveScroll: true,
        onSuccess: () => {
            // Reset password fields after successful submission
            form.password = '';
            form.password_confirmation = '';
        },
        onError: (errors) => {
            // Biarkan error ditangani oleh mekanisme default inertia
        }
    });
};

// Fungsi untuk memastikan password kosong saat pertama kali komponen dimuat
// untuk mengatasi autofill browser
const forceEmptyPassword = () => {
    // Kosongkan password hanya saat pertama kali komponen dimuat
    form.password = '';
    form.password_confirmation = '';
};

// Status options
const statusOptions = [
    { value: 'active', label: 'Aktif' },
    { value: 'inactive', label: 'Tidak Aktif' },
    { value: 'blocked', label: 'Diblokir' },
];

// State untuk custom select dropdown
const isSelectOpen = ref(false);
const selectRef = ref<Element | null>(null);

// Computed property untuk label status terpilih
const selectedStatusLabel = computed(() => {
    if (!form.status) return 'Pilih status pengguna';
    const status = statusOptions.find(option => option.value === form.status);
    return status ? status.label : 'Pilih status pengguna';
});

// Toggle dropdown
const toggleSelect = () => {
    isSelectOpen.value = !isSelectOpen.value;
};

// Pilih status
const selectStatus = (statusValue: string) => {
    form.status = statusValue;
    isSelectOpen.value = false;
};

// Handle click outside
const handleClickOutside = (evt: MouseEvent) => {
    if (selectRef.value && selectRef.value.contains && !selectRef.value.contains(evt.target as Node)) {
        isSelectOpen.value = false;
    }
};

// Lifecycle hook
onMounted(() => {
    // Force set data dalam onMounted untuk memastikan data terisi
    if (Array.isArray(props.userRoles) && props.userRoles.length > 0) {
        const roleIdsFromProps = props.userRoles.map(id => Number(id));
        form.role_ids = [...roleIdsFromProps];
    }
    
    // Kosongkan password yang mungkin diisi otomatis browser
    nextTick(() => {
        forceEmptyPassword();
    });

    document.addEventListener('click', handleClickOutside);
    const element = document.querySelector('.custom-select-container');
    if (element) {
        selectRef.value = element;
    }
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>

<template>
  <AppLayout :breadcrumbs="[
    { title: 'Dashboard', href: route('admin.dashboard') },
    { title: 'Pengguna', href: route('admin.users.index') },
    { title: 'Edit Pengguna', href: '#' }
  ]">
    <div>
      <Head title="Edit Pengguna" />
      
      <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6">
        <div class="flex items-center gap-4">
          <Link :href="route('admin.users.index')">
            <Button 
              variant="outline" 
              size="icon" 
              class="h-7 w-7 cursor-pointer"
            >
              <ArrowLeft class="h-4 w-4" />
            </Button>
          </Link>
          <h1 class="text-2xl font-bold">Edit Pengguna</h1>
        </div>

        <form @submit.prevent="submit">
          <div class="grid gap-4 md:grid-cols-2">
            <!-- Informasi Pengguna -->
            <Card class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
              <CardHeader>
                <CardTitle>Informasi Pengguna</CardTitle>
                <CardDescription>Ubah informasi dasar pengguna</CardDescription>
              </CardHeader>
              <CardContent class="space-y-4">
                <div class="space-y-2">
                  <Label for="name">Nama</Label>
                  <Input 
                    id="name" 
                    v-model="form.name" 
                    type="text" 
                    placeholder="Masukkan nama pengguna" 
                    :error="form.errors.name"
                  />
                  <p v-if="form.errors.name" class="text-sm text-red-500">{{ form.errors.name }}</p>
                </div>

                <div class="space-y-2">
                  <Label for="email">Email</Label>
                  <Input 
                    id="email" 
                    v-model="form.email" 
                    type="email" 
                    placeholder="Masukkan email pengguna"
                    :error="form.errors.email"
                  />
                  <p v-if="form.errors.email" class="text-sm text-red-500">{{ form.errors.email }}</p>
                </div>

                <div class="space-y-2">
                  <Label for="whatsapp">Nomor WhatsApp</Label>
                  <Input 
                    id="whatsapp" 
                    v-model="form.whatsapp" 
                    type="tel" 
                    placeholder="Masukkan nomor WhatsApp"
                    :error="form.errors.whatsapp"
                  />
                  <p v-if="form.errors.whatsapp" class="text-sm text-red-500">{{ form.errors.whatsapp }}</p>
                </div>

                <!-- Email Verification Status -->
                <div class="mt-4 pt-4 border-t border-slate-200 dark:border-slate-700">
                  <p class="text-sm font-medium mb-3">Status Verifikasi Email</p>
                  <div class="flex items-center gap-2">
                    <div
                      class="peer data-[checked=true]:bg-primary-600 dark:data-[checked=true]:bg-primary-700 data-[checked=false]:bg-secondary-200 dark:data-[checked=false]:bg-secondary-800 inline-flex h-[1.15rem] w-8 shrink-0 items-center rounded-full border border-transparent shadow-xs transition-all outline-none disabled:cursor-not-allowed disabled:opacity-50"
                      :data-checked="isEmailVerified"
                    >
                      <span
                        class="bg-white dark:data-[checked=false]:bg-secondary-300 dark:data-[checked=true]:bg-white pointer-events-none block size-4 rounded-full ring-0 transition-transform"
                        :class="isEmailVerified ? 'translate-x-[calc(100%-2px)]' : 'translate-x-0'"
                        :data-checked="isEmailVerified"
                      ></span>
                    </div>
                    <Label for="verification" class="cursor-pointer">
                      {{ isEmailVerified ? 'Email Terverifikasi' : 'Email Belum Terverifikasi' }}
                    </Label>
                  </div>
                  
                  <div class="flex gap-2 mt-3" v-if="!isEmailVerified">
                    <Button 
                      type="button" 
                      variant="outline" 
                      size="sm"
                      @click="resendVerificationEmail" 
                      :disabled="isResendingVerification"
                    >
                      <Mail class="h-4 w-4 mr-2" />
                      {{ isResendingVerification ? 'Mengirim...' : 'Kirim Ulang Verifikasi' }}
                    </Button>

                    <Button 
                      type="button" 
                      variant="outline" 
                      size="sm"
                      @click="markEmailAsVerified" 
                      :disabled="isMarkingVerified"
                    >
                      <CheckCircle class="h-4 w-4 mr-2" />
                      {{ isMarkingVerified ? 'Memproses...' : 'Tandai Sebagai Terverifikasi' }}
                    </Button>
                  </div>
                </div>

                <div class="pt-4 border-t border-slate-200 dark:border-slate-700">
                  <p class="text-sm font-medium mb-2">Ganti Kata Sandi (opsional)</p>
                  <div class="space-y-2 mb-2">
                    <p class="text-xs text-gray-500 font-medium">
                      Kosongkan kolom password jika tidak ingin mengubahnya
                    </p>
                  </div>
                  <div class="space-y-4">
                    <div class="space-y-2">
                      <Label for="password">Kata Sandi</Label>
                      <Input
                        id="password"
                        v-model="form.password"
                        type="password"
                        class="w-full"
                        placeholder="Biarkan kosong jika tidak ingin mengubah"
                        autocomplete="new-password"
                      />
                      <p class="text-sm text-muted-foreground">
                        Biarkan kosong jika tidak ingin mengubah kata sandi
                      </p>
                      <div v-if="form.errors.password" class="mt-1 text-red-500 text-sm">
                        {{ form.errors.password }}
                      </div>
                    </div>

                    <div class="space-y-2">
                      <Label for="password_confirmation">Konfirmasi Kata Sandi</Label>
                      <Input
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        type="password"
                        class="w-full"
                        placeholder="Konfirmasi kata sandi baru"
                        autocomplete="new-password"
                      />
                      <p class="text-sm text-muted-foreground">
                        Konfirmasi kata sandi baru jika diubah
                      </p>
                      <div v-if="form.errors.password_confirmation" class="mt-1 text-red-500 text-sm">
                        {{ form.errors.password_confirmation }}
                      </div>
                    </div>
                  </div>
                </div>

                <div class="space-y-2">
                  <Label for="status">Status</Label>
                  <div class="relative mt-1">
                    <div 
                      class="custom-select-container" 
                      :class="{ 'active': isSelectOpen }"
                    >
                      <div 
                        @click="toggleSelect" 
                        class="custom-select-trigger flex w-full items-center justify-between gap-2 rounded-md border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 px-3 py-2 text-sm shadow-sm hover:border-slate-300 dark:hover:border-slate-600 focus:outline-none focus:ring-0 disabled:cursor-not-allowed disabled:opacity-50 cursor-pointer h-9"
                        :class="{ 'border-red-500': form.errors.status }"
                      >
                        <span>{{ selectedStatusLabel }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-50 transition-transform" :class="{ 'rotate-180': isSelectOpen }">
                          <path d="m6 9 6 6 6-6"></path>
                        </svg>
                      </div>
                      
                      <div 
                        v-if="isSelectOpen" 
                        class="custom-select-dropdown bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-md shadow-lg mt-1 overflow-hidden z-50"
                      >
                        <div 
                          v-for="option in statusOptions" 
                          :key="option.value"
                          @click="selectStatus(option.value)"
                          class="custom-select-option py-2 px-3 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 cursor-pointer text-sm"
                          :class="{ 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300 font-medium': form.status === option.value }"
                        >
                          {{ option.label }}
                        </div>
                      </div>
                    </div>
                  </div>
                  <p v-if="form.errors.status" class="text-sm text-red-500">{{ form.errors.status }}</p>
                </div>
              </CardContent>
            </Card>

            <!-- Peran Pengguna -->
            <Card class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
              <CardHeader>
                <CardTitle>Peran Pengguna</CardTitle>
                <CardDescription>
                  Ubah peran yang dimiliki oleh pengguna (wajib minimal 1 peran)
                </CardDescription>
              </CardHeader>
              <CardContent>
                <div class="space-y-4">
                  <Alert v-if="props.roles.length === 0" class="bg-amber-50 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300">
                    <AlertDescription>
                      Tidak ada peran yang tersedia. Tambahkan peran terlebih dahulu.
                    </AlertDescription>
                  </Alert>

                  <div v-else class="space-y-2">
                    <div 
                      v-for="role in props.roles" 
                      :key="role.id" 
                      class="flex items-center space-x-2"
                    >
                      <Checkbox 
                        :id="`role-${role.id}`" 
                        :value="Number(role.id)"
                        :model-value="isRoleSelected(role.id)"
                        @update:model-value="(checked) => {
                          if (checked) {
                            if (!form.role_ids.includes(Number(role.id))) {
                              form.role_ids.push(Number(role.id));
                            }
                          } else {
                            if (form.role_ids.length > 1) {
                              const index = form.role_ids.indexOf(Number(role.id));
                              if (index !== -1) form.role_ids.splice(index, 1);
                            } else {
                              roleError = 'Pengguna harus memiliki minimal 1 peran';
                            }
                          }
                        }"
                      />
                      <Label :for="`role-${role.id}`" class="capitalize cursor-pointer">
                        {{ role.name }}
                      </Label>
                    </div>
                  </div>
                  
                  <!-- Error untuk role_ids -->
                  <p v-if="form.errors.role_ids" class="text-sm text-red-500">{{ form.errors.role_ids }}</p>
                  <p v-if="roleError" class="text-sm text-red-500">{{ roleError }}</p>
                </div>
              </CardContent>
            </Card>
          </div>

          <div class="mt-6 flex items-center justify-end gap-4">
            <Link :href="route('admin.users.index')">
              <Button 
                type="button" 
                variant="outline"
                class="cursor-pointer"
              >
                Batal
              </Button>
            </Link>
            <Button 
              type="submit" 
              :disabled="form.processing"
              class="cursor-pointer"
            >
              Simpan Perubahan
            </Button>
          </div>
        </form>
      </div>
    </div>
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
  outline: none;
  -webkit-appearance: none;
  -webkit-tap-highlight-color: transparent;
}

.custom-select-trigger:focus,
.custom-select-trigger:focus-visible,
.custom-select-trigger:active,
.custom-select-trigger:hover,
.custom-select-trigger:-moz-focusring {
  outline: none;
  box-shadow: none;
  border-color: #0ea5e9;
}

/* Fix untuk Firefox */
.custom-select-trigger:-moz-focusring {
  outline: none;
}

/* Fix untuk Safari dan Chrome */
.custom-select-trigger::-webkit-focus-inner {
  border: 0;
}

/* Fix tambahan untuk Chrome */
*:focus {
  outline-color: transparent;
}
</style> 