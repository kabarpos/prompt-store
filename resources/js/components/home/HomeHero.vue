<template>
  <section class="relative min-h-screen flex items-center overflow-hidden bg-background">
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
      
      <!-- Additional Decorative Elements -->
      <div class="absolute top-1/4 right-1/4 w-48 h-48 rounded-full"
           style="background: radial-gradient(circle, rgba(var(--primary-rgb), 0.1) 0%, rgba(var(--primary-rgb), 0) 70%);
                  filter: blur(30px);"></div>
      <div class="absolute bottom-1/4 left-1/3 w-64 h-64 rounded-full"
           style="background: radial-gradient(circle, rgba(59, 130, 246, 0.1) 0%, rgba(59, 130, 246, 0) 70%);
                  filter: blur(30px);"></div>
    </div>

    <!-- Content -->
    <div class="relative z-10 w-full py-20 md:py-32">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
          <!-- Left Column - Text Content -->
          <div class="max-w-xl" data-aos="fade-right" data-aos-duration="1000">
            <div class="inline-flex items-center px-3 py-1 rounded-full bg-primary/10 dark:bg-primary/20 backdrop-blur-sm mb-6 text-primary-600 dark:text-primary text-sm font-medium">
              <span class="flex h-2 w-2 rounded-full bg-primary-600 dark:bg-primary mr-2"></span>
              Agency Digital Marketing Terpercaya
            </div>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 text-slate-900 dark:text-foreground leading-tight">
              Buat Bisnis Anda <span class="relative inline-block">
                <span class="relative z-10">Makin Dikenal</span>
                <span class="absolute bottom-2 left-0 w-full h-3 bg-primary-100 dark:bg-green-500"></span>
              </span> Luas
            </h1>
            <p class="text-lg mb-8 text-slate-600 dark:text-muted-foreground leading-relaxed">
              Kami hadir untuk membantu bisnis Anda berkembang, 
              melalui jasa digital marketing yang profesional dan tepat sasaran.
            </p>
            <div class="flex flex-wrap gap-4">
              <button
                class="bg-primary-600 hover:bg-primary-700 dark:bg-primary dark:hover:bg-primary-600 text-white px-8 py-3.5 rounded-xl shadow-lg hover:shadow-primary-600/25 transform hover:-translate-y-0.5 transition-all duration-200 flex items-center gap-2 font-medium"
                @click="$router.push({ name: 'services' })"
              >
                <span>Cek Layanan Kami</span>
                <ArrowRightIcon class="w-5 h-5 transition-transform group-hover:translate-x-1" />
              </button>
              <button
                class="border-2 border-primary-600 dark:border-primary text-primary-600 dark:text-primary hover:bg-primary-50 dark:hover:bg-primary/10 px-8 py-3.5 rounded-xl hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200 flex items-center gap-2 font-medium group"
                @click="$router.push({ name: 'contact' })"
              >
                <PhoneIcon class="w-5 h-5" />
                <span>Konsultasi Gratis</span>
              </button>
            </div>
          </div>
          
          <!-- Right Column - Image -->
          <div class="relative" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="200">
            <div class="absolute inset-0 bg-gradient-to-tr from-primary-100 to-blue-100 dark:from-primary/20 dark:to-blue-500/20 rounded-2xl transform rotate-3"></div>
            <div class="relative bg-white dark:bg-card backdrop-blur-sm p-2 rounded-2xl shadow-2xl border border-slate-200 dark:border-border">
              <img 
                src="/images/hero-dashboard.svg" 
                alt="Dashboard Preview" 
                class="w-full h-auto rounded-xl"
              />
              <!-- Floating Elements -->
              <div class="absolute -top-6 -right-6 bg-white dark:bg-card p-3 rounded-lg shadow-lg flex items-center">
                <div class="bg-success-500 h-3 w-3 rounded-full mr-2"></div>
                <span class="text-sm font-medium text-slate-900 dark:text-foreground">Bisnis Meningkat 120%</span>
              </div>
              <div class="absolute -bottom-6 -left-6 bg-white dark:bg-card p-3 rounded-lg shadow-lg">
                <div class="flex items-center">
                  <div class="flex -space-x-2 mr-2">
                    <div class="h-6 w-6 rounded-full bg-primary-600 dark:bg-primary"></div>
                    <div class="h-6 w-6 rounded-full bg-blue-600 dark:bg-blue-500"></div>
                    <div class="h-6 w-6 rounded-full bg-success-500"></div>
                  </div>
                  <span class="text-sm font-medium text-slate-900 dark:text-foreground">Tim Ahli</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ArrowRightIcon, PhoneIcon } from '@heroicons/vue/24/outline'
import { onMounted } from 'vue'

onMounted(() => {
  if (typeof window !== 'undefined' && window.AOS) {
    window.AOS.init()
  }
})
</script> 