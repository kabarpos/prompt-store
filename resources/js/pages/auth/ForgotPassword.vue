<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
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
}>();

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <Head title="Lupa Password" />

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
                <h1 class="text-2xl font-bold text-foreground">Lupa Password?</h1>
                <p class="mt-2 text-muted-foreground">Masukkan email Anda untuk mereset password</p>
            </div>

            <div v-if="status" class="mb-6 p-4 bg-green-50 dark:bg-green-900/50 text-green-700 dark:text-green-300 text-sm rounded-lg">
                {{ status }}
            </div>

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

                <div>
                    <Button
                        type="submit"
                        class="w-full h-11 text-base"
                        size="lg"
                        :disabled="form.processing"
                    >
                        <LoaderCircle v-if="form.processing" class="mr-2 h-5 w-5 animate-spin" />
                        {{ form.processing ? 'Memproses...' : 'Kirim Link Reset Password' }}
                    </Button>
                </div>

                <div class="text-center text-sm">
                    <Link 
                        :href="route('login')" 
                        class="font-semibold text-primary hover:text-primary/80 transition-colors"
                    >
                        Kembali ke halaman login
                    </Link>
                </div>
            </form>
        </div>
    </div>
</template>
