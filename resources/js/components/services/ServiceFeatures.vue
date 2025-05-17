<template>
  <section class="py-24 bg-background relative overflow-hidden">
    <!-- Background Decorations -->
    <div class="absolute inset-0 z-0 pointer-events-none">
      <div class="absolute -top-20 -right-20 w-64 h-64 bg-primary/5 dark:bg-primary/10 rounded-full blur-3xl"></div>
      <div class="absolute bottom-10 -left-20 w-80 h-80 bg-blue-500/5 dark:bg-blue-500/10 rounded-full blur-3xl"></div>
    </div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-center">
        <!-- Text Content -->
        <div data-aos="fade-right" data-aos-duration="1000">
          <div class="inline-flex items-center px-3 py-1 rounded-full bg-primary/10 dark:bg-primary/20 backdrop-blur-sm mb-6 text-primary-600 dark:text-primary text-sm font-medium">
            <span class="flex h-2 w-2 rounded-full bg-primary-600 dark:bg-primary mr-2"></span>
            Keunggulan Kami
          </div>
          
          <h2 class="text-3xl md:text-4xl font-bold mb-6 text-slate-900 dark:text-foreground leading-tight">
            Mengapa Memilih 
            <span class="relative inline-block">
              <span class="relative z-10">Layanan Kami</span>
              <span class="absolute bottom-1 left-0 w-full h-3 bg-primary-100 dark:bg-primary/20"></span>
            </span>
          </h2>
          
          <p class="text-lg mb-8 text-slate-600 dark:text-muted-foreground leading-relaxed">
            Kami menggabungkan keahlian, inovasi, dan dedikasi untuk memberikan hasil yang luar biasa bagi klien kami.
          </p>

          <!-- Features List -->
          <div class="space-y-6">
            <div 
              v-for="(feature, index) in features" 
              :key="feature.name"
              class="flex items-start p-4 rounded-xl bg-white dark:bg-card border border-slate-200 dark:border-border transition-all hover:shadow-md"
              :data-aos="'fade-up'"
              :data-aos-delay="100 + (index * 100)"
            >
              <div class="p-2 rounded-lg bg-primary-50 dark:bg-primary/20 text-primary-600 dark:text-primary mr-4">
                <component :is="feature.icon" class="h-5 w-5" />
              </div>
              <div>
                <h3 class="font-semibold text-slate-900 dark:text-foreground mb-1">
                  {{ feature.name }}
                </h3>
                <p class="text-slate-600 dark:text-muted-foreground text-sm">
                  {{ feature.description }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Image Section -->
        <div class="relative" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="200">
          <div class="absolute inset-0 bg-gradient-to-tr from-primary-100 to-blue-100 dark:from-primary/20 dark:to-blue-500/20 rounded-2xl transform rotate-3"></div>
          <div class="relative bg-white dark:bg-card backdrop-blur-sm p-2 rounded-2xl shadow-2xl border border-slate-200 dark:border-border">
            <img
              src="/images/services-features.jpg"
              alt="Service features"
              class="w-full h-auto rounded-xl"
            />
            
            <!-- Floating Elements -->
            <div class="absolute -top-6 -right-6 bg-white dark:bg-card p-3 rounded-lg shadow-lg flex items-center">
              <div class="bg-success-500 h-3 w-3 rounded-full mr-2"></div>
              <span class="text-sm font-medium text-slate-900 dark:text-foreground">100% Satisfaction</span>
            </div>
            
            <div class="absolute -bottom-6 -left-6 bg-white dark:bg-card p-3 rounded-lg shadow-lg">
              <div class="flex items-center">
                <div class="flex -space-x-2 mr-2">
                  <div class="h-6 w-6 rounded-full bg-primary-600 dark:bg-primary"></div>
                  <div class="h-6 w-6 rounded-full bg-blue-600 dark:bg-blue-500"></div>
                  <div class="h-6 w-6 rounded-full bg-success-500"></div>
                </div>
                <span class="text-sm font-medium text-slate-900 dark:text-foreground">Expert Team</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import {
  CheckCircleIcon,
  ClockIcon,
  UserGroupIcon,
  ChartBarIcon,
  ShieldCheckIcon,
  SparklesIcon
} from '@heroicons/vue/24/outline'
import { onMounted } from 'vue'

const features = [
  {
    name: 'Tim Ahli',
    description: 'Tim kami terdiri dari profesional berpengalaman dengan keahlian di bidangnya masing-masing.',
    icon: UserGroupIcon
  },
  {
    name: 'Jaminan Kualitas',
    description: 'Kami menjaga standar kualitas tertinggi dalam semua proyek dan layanan kami.',
    icon: CheckCircleIcon
  },
  {
    name: 'Pengerjaan Tepat Waktu',
    description: 'Kami memahami pentingnya tenggat waktu dan memastikan pengiriman tepat waktu untuk semua proyek.',
    icon: ClockIcon
  },
  {
    name: 'Hasil Terukur',
    description: 'Kami fokus memberikan hasil yang terukur yang berkontribusi pada pertumbuhan bisnis Anda.',
    icon: ChartBarIcon
  },
  {
    name: 'Keamanan Prioritas',
    description: 'Kami memprioritaskan keamanan dan menerapkan praktik terbaik untuk melindungi data dan sistem Anda.',
    icon: ShieldCheckIcon
  },
  {
    name: 'Inovasi',
    description: 'Kami selalu update dengan teknologi dan tren terbaru untuk memberikan solusi yang cutting-edge.',
    icon: SparklesIcon
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