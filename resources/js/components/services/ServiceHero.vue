<script setup>
import { Button } from '@/components/ui/button'
import { onMounted } from 'vue'

onMounted(() => {
  if (typeof window !== 'undefined' && window.AOS) {
    window.AOS.init()
  }
})
</script>

<template>
  <section class="relative min-h-[80vh] flex items-center overflow-hidden bg-background">
    <!-- Background Patterns/Shapes -->
    <div class="absolute inset-0 z-0 overflow-hidden">
      <!-- Modern Grid Pattern -->
      <div class="absolute top-0 left-0 w-full h-full opacity-5 dark:opacity-10">
        <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
          <defs>
            <linearGradient id="grid-gradient" x1="0%" y1="0%" x2="100%" y2="100%">
              <stop offset="0%" style="stop-color:currentColor;stop-opacity:0.5" />
              <stop offset="100%" style="stop-color:currentColor;stop-opacity:0.2" />
            </linearGradient>
            <pattern id="grid-pattern" width="4" height="4" patternUnits="userSpaceOnUse">
              <path d="M 4 0 L 0 0 0 4" fill="none" stroke="url(#grid-gradient)" stroke-width="0.2" class="text-foreground" />
            </pattern>
          </defs>
          <rect width="100%" height="100%" fill="url(#grid-pattern)" />
        </svg>
      </div>
      
      <!-- Gradient Blobs -->
      <div class="absolute -bottom-40 -right-40 w-96 h-96 rounded-full"
           style="background: radial-gradient(circle, rgba(var(--primary-rgb), 0.2) 0%, rgba(var(--primary-rgb), 0.1) 40%, rgba(var(--primary-rgb), 0) 70%);
                  filter: blur(40px);"></div>
      <div class="absolute -top-20 -left-20 w-72 h-72 rounded-full"
           style="background: radial-gradient(circle, rgba(59, 130, 246, 0.2) 0%, rgba(59, 130, 246, 0.1) 40%, rgba(59, 130, 246, 0) 70%);
                  filter: blur(40px);"></div>
    </div>

    <div class="relative z-10 w-full py-20">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
          <!-- Left Column -->
          <div class="max-w-xl" data-aos="fade-right" data-aos-duration="1000">
            <div class="inline-flex items-center px-3 py-1 rounded-full bg-primary/10 dark:bg-primary/20 backdrop-blur-sm mb-6 text-primary-600 dark:text-primary text-sm font-medium">
              <span class="flex h-2 w-2 rounded-full bg-primary-600 dark:bg-primary mr-2"></span>
              Layanan Digital Marketing
            </div>
            
            <h1 class="text-4xl md:text-5xl font-bold mb-6 text-slate-900 dark:text-foreground leading-tight">
              Layanan <span class="relative inline-block">
                <span class="relative z-10">Profesional</span>
                <span class="absolute bottom-2 left-0 w-full h-3 bg-primary-100 dark:bg-primary/20"></span>
              </span> untuk Bisnis Anda
            </h1>
            
            <p class="text-lg mb-8 text-slate-600 dark:text-muted-foreground leading-relaxed">
              Kami menyediakan berbagai layanan digital yang dapat disesuaikan dengan kebutuhan bisnis Anda. Dari pengembangan website hingga strategi pemasaran digital.
            </p>

            <div class="flex flex-wrap gap-4">
              <button
                class="bg-primary-600 hover:bg-primary-700 dark:bg-primary dark:hover:bg-primary-600 text-white px-8 py-3.5 rounded-xl shadow-lg hover:shadow-primary-600/25 transform hover:-translate-y-0.5 transition-all duration-200 flex items-center gap-2 font-medium"
                @click="$router.push('#service-list')"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/>
                </svg>
                <span>Lihat Semua Layanan</span>
              </button>
            </div>
          </div>

          <!-- Right Column -->
          <div class="relative" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="200">
            <div class="absolute inset-0 bg-gradient-to-tr from-primary-100 to-blue-100 dark:from-primary/20 dark:to-blue-500/20 rounded-2xl transform rotate-3"></div>
            <div class="relative bg-white dark:bg-card backdrop-blur-sm p-2 rounded-2xl shadow-2xl border border-slate-200 dark:border-border">
              <img 
                src="/images/hero-dashboard.svg" 
                alt="Layanan Digital" 
                class="w-full h-auto rounded-xl"
              />
              
              <!-- Floating Elements -->
              <div class="absolute -top-6 -right-6 bg-white dark:bg-card p-3 rounded-lg shadow-lg flex items-center">
                <div class="bg-success-500 h-3 w-3 rounded-full mr-2"></div>
                <span class="text-sm font-medium text-slate-900 dark:text-foreground">UI/UX Design Professional</span>
              </div>
              
              <div class="absolute -bottom-6 -left-6 bg-white dark:bg-card p-3 rounded-lg shadow-lg">
                <div class="flex items-center">
                  <div class="flex -space-x-2 mr-2">
                    <div class="h-6 w-6 rounded-full bg-primary-600 dark:bg-primary"></div>
                    <div class="h-6 w-6 rounded-full bg-blue-600 dark:bg-blue-500"></div>
                    <div class="h-6 w-6 rounded-full bg-success-500"></div>
                  </div>
                  <span class="text-sm font-medium text-slate-900 dark:text-foreground">Branding Identity</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template> 