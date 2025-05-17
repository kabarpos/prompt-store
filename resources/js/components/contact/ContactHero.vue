<template>
  <section class="py-24 bg-background relative overflow-hidden">
    <!-- Background Decorations -->
    <div class="absolute inset-0 z-0 pointer-events-none">
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

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
      <div class="bg-white dark:bg-card rounded-2xl p-8 lg:p-12 shadow-xl border border-slate-200 dark:border-border backdrop-blur-sm">
        <div class="max-w-3xl mx-auto">
          <!-- Contact Header -->
          <div data-aos="fade-up">
            <div class="inline-flex items-center px-3 py-1 rounded-full bg-primary/10 dark:bg-primary/20 backdrop-blur-sm mb-6 text-primary-600 dark:text-primary text-sm font-medium">
              <span class="flex h-2 w-2 rounded-full bg-primary-600 dark:bg-primary mr-2"></span>
              Hubungi Kami
            </div>
            
            <h1 class="text-4xl md:text-5xl font-bold mb-6 text-slate-900 dark:text-foreground leading-tight">
              Mari Diskusikan <span class="relative inline-block">
                <span class="relative z-10">Project Anda</span>
                <span class="absolute bottom-2 left-0 w-full h-3 bg-primary-100 dark:bg-primary/20"></span>
              </span> Bersama Kami
            </h1>
            
            <p class="text-lg mb-8 text-slate-600 dark:text-muted-foreground">
              Tim kami siap membantu menjawab pertanyaan dan memberikan konsultasi untuk kebutuhan digital bisnis Anda.
            </p>
            
            <!-- Contact Information Cards -->
            <div class="space-y-4 mb-8">
              <div class="group bg-background dark:bg-card p-4 rounded-xl shadow-lg hover:shadow-xl transition-all border border-slate-200 dark:border-border flex items-start gap-4">
                <div class="w-10 h-10 rounded-lg bg-primary-50 dark:bg-primary/20 flex-shrink-0 flex items-center justify-center text-primary-600 dark:text-primary group-hover:bg-primary-100 dark:group-hover:bg-primary/30 transition-colors">
                  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                    <polyline points="22,6 12,13 2,6"></polyline>
                  </svg>
                </div>
                <div>
                  <h3 class="font-medium text-slate-900 dark:text-foreground mb-1">Email</h3>
                  <a href="mailto:info@dilogif.com" class="text-primary-600 dark:text-primary hover:text-primary-700 dark:hover:text-primary/90 transition-colors">info@dilogif.com</a>
                </div>
              </div>
              
              <div class="group bg-background dark:bg-card p-4 rounded-xl shadow-lg hover:shadow-xl transition-all border border-slate-200 dark:border-border flex items-start gap-4">
                <div class="w-10 h-10 rounded-lg bg-primary-50 dark:bg-primary/20 flex-shrink-0 flex items-center justify-center text-primary-600 dark:text-primary group-hover:bg-primary-100 dark:group-hover:bg-primary/30 transition-colors">
                  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M15.05 5A5 5 0 0 1 19 8.95M15.05 1A9 9 0 0 1 23 8.94m-1 7.98v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="font-medium text-slate-900 dark:text-foreground mb-1">Telepon</h3>
                  <a href="tel:+6281234567890" class="text-primary-600 dark:text-primary hover:text-primary-700 dark:hover:text-primary/90 transition-colors">+62 812 3456 7890</a>
                </div>
              </div>
            </div>
            
            <!-- Social Media Links -->
            <div>
              <h3 class="font-medium text-slate-900 dark:text-foreground mb-3">Media Sosial</h3>
              <div class="flex gap-3">
                <a href="#" class="w-10 h-10 rounded-lg bg-primary-600 dark:bg-primary flex items-center justify-center text-white hover:bg-primary-700 dark:hover:bg-primary/90 transition-colors shadow-lg hover:shadow-primary-600/25">
                  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
                  </svg>
                </a>
                <a href="#" class="w-10 h-10 rounded-lg bg-primary-600 dark:bg-primary flex items-center justify-center text-white hover:bg-primary-700 dark:hover:bg-primary/90 transition-colors shadow-lg hover:shadow-primary-600/25">
                  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                    <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                    <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                  </svg>
                </a>
                <a href="#" class="w-10 h-10 rounded-lg bg-primary-600 dark:bg-primary flex items-center justify-center text-white hover:bg-primary-700 dark:hover:bg-primary/90 transition-colors shadow-lg hover:shadow-primary-600/25">
                  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"></path>
                  </svg>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { onMounted } from 'vue'

onMounted(() => {
  if (typeof window !== 'undefined' && window.AOS) {
    window.AOS.init({
      duration: 800,
      once: true
    })
  }
})
</script> 