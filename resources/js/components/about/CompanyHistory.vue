<template>
  <!-- Company Profile Banner -->
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
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-center">
        <!-- Text Content -->
        <div data-aos="fade-right" data-aos-duration="1000">
          <div class="inline-flex items-center px-3 py-1 rounded-full bg-primary/10 dark:bg-primary/20 backdrop-blur-sm mb-6 text-primary-600 dark:text-primary text-sm font-medium">
            <span class="flex h-2 w-2 rounded-full bg-primary-600 dark:bg-primary mr-2"></span>
            Tentang Kami
          </div>
          
          <h1 class="text-4xl md:text-5xl font-bold mb-6 text-slate-900 dark:text-foreground leading-tight">
            Menghadirkan <span class="relative inline-block">
              <span class="relative z-10">Solusi Digital</span>
              <span class="absolute bottom-2 left-0 w-full h-3 bg-primary-100 dark:bg-primary/20"></span>
            </span> untuk Bisnis Anda
          </h1>
          
          <div class="text-lg mb-8 text-slate-600 dark:text-muted-foreground space-y-4 leading-relaxed">
            <p>Dilogif didirikan dengan visi untuk membantu bisnis berkembang di era digital. Kami percaya bahwa teknologi yang tepat dapat mendorong pertumbuhan dan kesuksesan bisnis Anda.</p>
            <p>Tim kami terdiri dari para ahli yang berpengalaman dalam berbagai bidang digital. Dengan pendekatan yang kolaboratif, kami bekerja sama dengan klien untuk menciptakan solusi yang sesuai dengan kebutuhan mereka.</p>
          </div>
        </div>
        
        <!-- Visual Content -->
        <div class="relative" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="200">
          <div class="absolute inset-0 bg-gradient-to-tr from-primary-100 to-blue-100 dark:from-primary/20 dark:to-blue-500/20 rounded-2xl transform rotate-3"></div>
          <div class="relative bg-white dark:bg-card backdrop-blur-sm p-2 rounded-2xl shadow-2xl border border-slate-200 dark:border-border">
            <img 
              src="/images/hero-dashboard.svg" 
              alt="Tim Dilogif" 
              class="w-full h-auto rounded-xl"
            />
            
            <!-- Floating Elements -->
            <div class="absolute -top-6 -right-6 bg-white dark:bg-card p-3 rounded-lg shadow-lg flex items-center">
              <div class="bg-success-500 h-3 w-3 rounded-full mr-2"></div>
              <span class="text-sm font-medium text-slate-900 dark:text-foreground">5+ Tahun Pengalaman</span>
            </div>
            
            <div class="absolute -bottom-6 -left-6 bg-white dark:bg-card p-3 rounded-lg shadow-lg">
              <div class="flex items-center">
                <div class="flex -space-x-2 mr-2">
                  <div class="h-6 w-6 rounded-full bg-primary-600 dark:bg-primary"></div>
                  <div class="h-6 w-6 rounded-full bg-blue-600 dark:bg-blue-500"></div>
                  <div class="h-6 w-6 rounded-full bg-success-500"></div>
                </div>
                <span class="text-sm font-medium text-slate-900 dark:text-foreground">200+ Klien Puas</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Values Section -->
  <section class="py-24 bg-background relative overflow-hidden">
    <!-- Background Decorations -->
    <div class="absolute inset-0 z-0 pointer-events-none">
      <div class="absolute -top-20 -right-20 w-64 h-64 bg-primary/5 dark:bg-primary/10 rounded-full blur-3xl"></div>
      <div class="absolute bottom-10 -left-20 w-80 h-80 bg-blue-500/5 dark:bg-blue-500/10 rounded-full blur-3xl"></div>
    </div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
      <!-- Section Header -->
      <div class="max-w-3xl mx-auto text-center mb-16" data-aos="fade-up">
        <div class="inline-flex items-center justify-center gap-2 bg-primary/10 text-primary-600 dark:text-primary rounded-full px-4 py-1.5 font-medium text-sm mb-4">
          <span class="flex h-2 w-2 rounded-full bg-primary-600 dark:bg-primary"></span>
          Nilai-Nilai Kami
        </div>
        <h2 class="text-3xl md:text-4xl font-bold mb-6 text-slate-900 dark:text-foreground leading-tight">
          Nilai-Nilai yang Kami 
          <span class="relative inline-block">
            <span class="relative z-10">Pegang Teguh</span>
            <span class="absolute bottom-1 left-0 w-full h-3 bg-primary-100 dark:bg-primary/20"></span>
          </span>
        </h2>
        <p class="text-lg text-slate-600 dark:text-muted-foreground">
          Prinsip-prinsip yang menjadi pondasi Dilogif dalam memberikan layanan terbaik kepada klien.
        </p>
      </div>
      
      <!-- Values Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div 
          v-for="(value, index) in values" 
          :key="index"
          class="group bg-white dark:bg-card rounded-xl shadow-lg hover:shadow-xl transition-all overflow-hidden border border-slate-200 dark:border-border relative"
          :data-aos="'fade-up'"
          :data-aos-delay="100 + (index * 100)"
        >
          <!-- Card Header with Gradient Bar -->
          <div class="h-1.5 w-full bg-gradient-to-r from-primary-600 to-blue-600 dark:from-primary dark:to-blue-500"></div>
          
          <!-- Card Content -->
          <div class="p-8">
            <div class="p-3 rounded-lg bg-primary-50 dark:bg-primary/20 text-primary-600 dark:text-primary w-14 h-14 flex items-center justify-center mb-6">
              <component :is="value.icon" class="w-8 h-8" />
            </div>
            
            <h3 class="text-xl font-bold mb-3 text-slate-900 dark:text-foreground group-hover:text-primary-600 dark:group-hover:text-primary transition-colors">
              {{ value.title }}
            </h3>
            <p class="text-slate-600 dark:text-muted-foreground">
              {{ value.description }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { h } from 'vue'
import { onMounted } from 'vue'

// Values with SVG Icon Components
const values = [
  {
    title: 'Kualitas',
    description: 'Kami berkomitmen untuk memberikan layanan berkualitas tinggi yang memenuhi standar industri dan kebutuhan klien.',
    icon: {
      render() {
        return h('svg', {
          xmlns: 'http://www.w3.org/2000/svg',
          width: '24',
          height: '24',
          viewBox: '0 0 24 24',
          fill: 'none',
          stroke: 'currentColor',
          'stroke-width': '2',
          'stroke-linecap': 'round',
          'stroke-linejoin': 'round'
        }, [
          h('polygon', { points: '12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26' })
        ])
      }
    }
  },
  {
    title: 'Kolaborasi',
    description: 'Kami bekerja bersama klien sebagai mitra, mendengarkan kebutuhan mereka dan menciptakan solusi yang sesuai.',
    icon: {
      render() {
        return h('svg', {
          xmlns: 'http://www.w3.org/2000/svg',
          width: '24',
          height: '24',
          viewBox: '0 0 24 24',
          fill: 'none',
          stroke: 'currentColor',
          'stroke-width': '2',
          'stroke-linecap': 'round',
          'stroke-linejoin': 'round'
        }, [
          h('path', { d: 'M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2' }),
          h('circle', { cx: '9', cy: '7', r: '4' }),
          h('path', { d: 'M23 21v-2a4 4 0 0 0-3-3.87' }),
          h('path', { d: 'M16 3.13a4 4 0 0 1 0 7.75' })
        ])
      }
    }
  },
  {
    title: 'Inovasi',
    description: 'Kami terus mencari cara baru dan lebih baik untuk menyelesaikan masalah dan menciptakan nilai bagi klien kami.',
    icon: {
      render() {
        return h('svg', {
          xmlns: 'http://www.w3.org/2000/svg',
          width: '24',
          height: '24',
          viewBox: '0 0 24 24',
          fill: 'none',
          stroke: 'currentColor',
          'stroke-width': '2',
          'stroke-linecap': 'round',
          'stroke-linejoin': 'round'
        }, [
          h('path', { d: 'M20 11.08V8l-6-6H6a2 2 0 0 0-2 2v16c0 1.1.9 2 2 2h6' }),
          h('path', { d: 'M14 3v5h5' }),
          h('path', { d: 'M18 20.24c.12.56.29 1.4.29 2.26 0 2.4-1.04 3.07-3.04 3.07-1.26 0-2.67-.97-2.67-2.35 0-1.19.95-2.11 2.13-2.11.88 0 1.63.71 1.63 1.63 0 .79-.65 1.44-1.44 1.44' })
        ])
      }
    }
  }
]

onMounted(() => {
  if (typeof window !== 'undefined' && window.AOS) {
    window.AOS.init({
      duration: 800,
      once: true
    })
  }
})
</script> 