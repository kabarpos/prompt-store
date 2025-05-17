<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
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

defineProps<{
  status?: string;
  verificationLinkSent?: boolean;
}>();

const form = useForm({});

const submit = () => {
  form.post(route('verification.send'));
};

const logout = () => {
  form.post(route('logout'));
};
</script>

<template>
  <Head title="Verifikasi Email" />

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
        <h1 class="text-2xl font-bold text-foreground">Verifikasi Email</h1>
        <p class="mt-2 text-muted-foreground">
          Terima kasih telah mendaftar! Sebelum memulai, bisakah Anda memverifikasi alamat email Anda dengan mengklik link yang baru saja kami kirimkan ke email Anda? Jika Anda tidak menerima email tersebut, kami akan dengan senang hati mengirimkan email yang baru.
        </p>
      </div>

      <div v-if="verificationLinkSent" class="mb-6 p-4 bg-green-50 dark:bg-green-900/50 text-green-700 dark:text-green-300 text-sm rounded-lg">
        Link verifikasi baru telah dikirim ke alamat email yang Anda berikan saat pendaftaran.
      </div>

      <form @submit.prevent="submit" class="space-y-6">
        <div>
          <Button
            type="submit"
            class="w-full h-11 text-base"
            size="lg"
            :disabled="form.processing"
          >
            <LoaderCircle v-if="form.processing" class="mr-2 h-5 w-5 animate-spin" />
            {{ form.processing ? 'Mengirim...' : 'Kirim Ulang Email Verifikasi' }}
          </Button>
        </div>

        <div class="text-center text-sm">
          <form @submit.prevent="logout" class="inline">
            <Button 
              type="submit" 
              variant="link" 
              class="text-primary hover:text-primary/80 transition-colors"
            >
              Logout
            </Button>
          </form>
        </div>
      </form>
    </div>
  </div>
</template>
