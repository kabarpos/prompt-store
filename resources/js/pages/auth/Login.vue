<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

interface WebsiteSettings {
  siteName: string;
  siteSubtitle?: string;
  siteDescription?: string;
  contactEmail?: string;
  logoUrl?: string;
  faviconUrl?: string;
  ogImageUrl?: string;
}

const page = usePage();
const websiteSettings = computed<WebsiteSettings>(() => page.props.websiteSettings as WebsiteSettings);

const props = defineProps({
  canResetPassword: {
    type: Boolean,
    default: false
  },
  status: {
    type: String,
    default: ''
  },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    console.log('Form submitted:', form);
    console.log('CSRF Token:', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'));
    
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
        onError: (errors) => {
            console.error('Login error:', errors);
        },
        onSuccess: () => {
            console.log('Login successful');
        }
    });
};
</script>

<template>
  <Head title="Login" />

  <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-b from-white to-gray-50 dark:from-background dark:to-background/80">
    <div class="w-full sm:max-w-md px-6 py-8 bg-white dark:bg-background shadow-xl overflow-hidden sm:rounded-2xl border border-border/50">
      <!-- Logo & Title -->
      <div class="text-center mb-8">
        <img 
          v-if="websiteSettings?.logoUrl" 
          :src="websiteSettings.logoUrl" 
          :alt="websiteSettings.siteName" 
          class="mx-auto h-12 w-auto mb-6"
        />
        <h1 class="text-2xl font-bold text-foreground">Selamat Datang Kembali!</h1>
        <p class="mt-2 text-muted-foreground">Silakan masuk ke akun Anda untuk melanjutkan</p>
      </div>

      <!-- Status Message -->
      <div v-if="props.status" class="mb-6 p-4 bg-green-50 dark:bg-green-900/50 text-green-700 dark:text-green-300 text-sm rounded-lg">
        {{ props.status }}
      </div>

      <!-- Login Form -->
      <form @submit.prevent="submit" class="space-y-6">
        <div class="space-y-2">
          <Label for="email" class="text-sm font-medium">Email</Label>
          <div class="relative">
            <Input
              id="email"
              v-model="form.email"
              type="email"
              required
              autofocus
              class="pl-10"
              placeholder="nama@email.com"
              :disabled="form.processing"
            />
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-muted-foreground" viewBox="0 0 20 20" fill="currentColor">
                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
              </svg>
            </div>
          </div>
          <InputError :message="form.errors.email" />
        </div>

        <div class="space-y-2">
          <Label for="password" class="text-sm font-medium">Password</Label>
          <div class="relative">
            <Input
              id="password"
              v-model="form.password"
              type="password"
              required
              class="pl-10"
              placeholder="••••••••"
              :disabled="form.processing"
            />
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-muted-foreground" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
              </svg>
            </div>
          </div>
          <InputError :message="form.errors.password" />
        </div>

        <div class="flex items-center justify-between">
          <label class="flex items-center gap-2">
            <Checkbox 
              name="remember" 
              v-model:checked="form.remember"
              :disabled="form.processing"
            />
            <span class="text-sm text-muted-foreground">Ingat Saya</span>
          </label>

          <Link
            v-if="props.canResetPassword"
            :href="route('password.request')"
            class="text-sm text-primary hover:text-primary/80 transition-colors"
          >
            Lupa Password?
          </Link>
        </div>

        <div>
          <Button
            type="submit"
            class="w-full h-11 text-base"
            size="lg"
            :disabled="form.processing"
          >
            <LoaderCircle v-if="form.processing" class="mr-2 h-5 w-5 animate-spin" />
            {{ form.processing ? 'Memproses...' : 'Masuk' }}
          </Button>
        </div>

        <div class="relative">
          <div class="absolute inset-0 flex items-center">
            <span class="w-full border-t border-border" />
          </div>
          <div class="relative flex justify-center text-xs uppercase">
            <span class="bg-white dark:bg-background px-2 text-muted-foreground">Atau</span>
          </div>
        </div>

        <div class="text-center text-sm">
          <span class="text-muted-foreground">Belum punya akun? </span>
          <Link 
            :href="route('register')" 
            class="font-semibold text-primary hover:text-primary/80 transition-colors"
          >
            Daftar sekarang
          </Link>
        </div>
      </form>
    </div>
  </div>
</template>
