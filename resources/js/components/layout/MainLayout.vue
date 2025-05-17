<template>
  <div class="min-h-screen">
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
import { usePage } from '@inertiajs/vue3';
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