<template>
  <div class="min-h-screen">
    <Head>
      <title>AI Prompt Marketplace - MyPrompt</title>
      <meta name="description" content="Temukan koleksi prompt AI terbaik untuk ChatGPT, DALL-E, Midjourney, dan berbagai model AI lainnya. Beli, jual, dan bagikan prompt untuk meningkatkan produktivitas Anda." />
      <meta name="keywords" content="prompt AI, marketplace prompt, ChatGPT, DALL-E, prompt generator, Midjourney, AI tools, prompt engineering" />
      <meta property="og:title" content="AI Prompt Marketplace - MyPrompt" />
      <meta property="og:description" content="Marketplace prompt terlengkap dengan berbagai kategori prompt untuk model AI populer." />
      <meta property="og:type" content="website" />
      <meta name="twitter:card" content="summary_large_image" />
    </Head>
    
    <PublicNavigation />
    
    <!-- Page Content -->
    <main :class="[
      'transition-all duration-300',
      route().current('home') ? '' : 'mt-20'
    ]">
      <slot />
    </main>
    
    <!-- Footer -->
    <Footer />
    
    <!-- WhatsApp Button -->
    <WhatsAppButton />
  </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import { usePage, Head } from '@inertiajs/vue3';
import PublicNavigation from '@/components/layout/PublicNavigation.vue';
import Footer from '@/components/layout/Footer.vue';
import WhatsAppButton from '@/components/ui/WhatsAppButton.vue';
import { initializeCartCount } from '@/event-bus';

const page = usePage();

// Watch for changes in cart count
watch(() => page.props.cartCount, (newCount) => {
  if (newCount !== undefined) {
    initializeCartCount(newCount);
  }
});
</script> 