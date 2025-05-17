<template>
  <section class="py-24 bg-background relative overflow-hidden">
    <!-- Background decorations -->
    <div class="absolute inset-0 z-0">
      <div class="absolute top-40 left-0 w-full h-full">
        <div class="absolute -top-10 right-0 w-80 h-80 bg-primary/5 dark:bg-primary/20 rounded-full blur-3xl"></div>
        <div class="absolute top-60 -left-20 w-72 h-72 bg-blue-500/5 dark:bg-blue-500/20 rounded-full blur-3xl"></div>
      </div>
    </div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
      <!-- Section Header -->
      <div class="max-w-3xl mx-auto text-center mb-16" data-aos="fade-up">
        <div class="inline-flex items-center justify-center gap-2 bg-primary/10 text-primary-600 dark:text-primary rounded-full px-4 py-1.5 font-medium text-sm mb-4">
          <span class="flex h-2 w-2 rounded-full bg-primary-600 dark:bg-primary"></span>
          Layanan Kami
        </div>
        <h2 class="text-3xl md:text-4xl font-bold mb-6 text-slate-900 dark:text-foreground leading-tight">
          Solusi Profesional untuk 
          <span class="relative inline-block">
            <span class="relative z-10">Bisnis Anda</span>
            <span class="absolute bottom-1 left-0 w-full h-3 bg-primary-100 dark:bg-primary/20"></span>
          </span>
        </h2>
        <p class="text-lg text-slate-600 dark:text-muted-foreground">
          Jadikan bisnis Anda semakin dikenal publik dengan layanan digital marketing profesional
        </p>
      </div>
      
      <!-- Services Grid -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div 
          v-for="(service, index) in services" 
          :key="service.title"
          class="group bg-white dark:bg-card rounded-xl shadow-lg hover:shadow-xl transition-all overflow-hidden border border-slate-200 dark:border-border relative"
          :data-aos="'fade-up'"
          :data-aos-delay="100 + (index * 100)"
        >
          <!-- Service Header with Gradient Bar -->
          <div class="h-1.5 w-full bg-gradient-to-r from-primary-600 to-blue-600 dark:from-primary dark:to-blue-500"></div>
          
          <!-- Service Content -->
          <div class="p-8">
            <div class="flex items-center justify-between mb-6">
              <div class="p-3 rounded-lg bg-primary-50 dark:bg-primary/20 text-primary-600 dark:text-primary">
                <component 
                  :is="service.iconComponent || 'div'" 
                  class="w-6 h-6"
                  v-if="service.iconComponent"
                />
                <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                </svg>
              </div>
              <div class="opacity-0 group-hover:opacity-100 transition-opacity">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary-600 dark:text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
              </div>
            </div>
            
            <div>
              <h3 class="text-xl font-bold mb-3 text-slate-900 dark:text-foreground group-hover:text-primary-600 dark:group-hover:text-primary transition-colors">{{ service.title }}</h3>
              <p class="text-slate-600 dark:text-muted-foreground mb-6">
                {{ service.description }}
              </p>
            </div>
            
            <!-- Service Image -->
            <div class="relative h-48 mb-6 overflow-hidden rounded-lg">
              <img 
                :src="service.image" 
                :alt="service.title" 
                class="w-full h-full object-cover object-center transition-transform duration-500 group-hover:scale-105"
              />
              <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
            </div>
            
            <!-- Call to Action -->
            <a href="#" class="inline-flex items-center text-primary-600 dark:text-primary font-medium group-hover:underline">
              Pelajari Lebih Lanjut
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 transition-transform group-hover:translate-x-1" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
              </svg>
            </a>
          </div>
        </div>
      </div>
      
      <!-- CTA Section -->
      <div class="relative mt-24 bg-primary-600 dark:bg-primary rounded-2xl overflow-hidden">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-8 p-8 md:p-12">
          <div class="max-w-xl">
            <h3 class="text-2xl md:text-3xl font-bold text-white mb-4">
              Siap Mengembangkan Bisnis Anda?
            </h3>
            <p class="text-white/90 mb-6 md:mb-0 leading-relaxed">
              Konsultasikan kebutuhan digital marketing bisnis Anda dengan tim ahli kami untuk mendapatkan hasil terbaik.
            </p>
          </div>
          <div class="flex flex-wrap gap-4">
            <button 
              class="bg-white text-primary-600 hover:bg-white/90 px-8 py-3.5 rounded-xl shadow-lg hover:shadow-white/25 transform hover:-translate-y-0.5 transition-all duration-200 flex items-center gap-2 font-medium"
              @click="$router.push({ name: 'contact' })"
            >
              <RocketLaunchIcon class="w-5 h-5" />
              <span>Mulai Sekarang</span>
            </button>
            <button 
              class="px-8 py-3.5 rounded-xl border-2 border-white text-white font-medium hover:bg-white/10 transition-all duration-200 flex items-center gap-2 group"
              @click="$router.push({ name: 'portfolio' })"
            >
              <FolderIcon class="w-5 h-5" />
              <span>Lihat Portfolio</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { onMounted } from 'vue'
import { RocketLaunchIcon, FolderIcon } from '@heroicons/vue/24/outline'

const services = [
  {
    title: 'Web Development',
    description: 'Website modern dan responsif yang menarik untuk meningkatkan kredibilitas bisnis Anda.',
    image: '/images/web-design.svg'
  },
  {
    title: 'Branding & Logo',
    description: 'Logo dan identitas brand yang profesional untuk meningkatkan kesadaran merek.',
    image: '/images/logo-design.svg'
  },
  {
    title: 'Content Creation',
    description: 'Konten berkualitas tinggi yang menarik untuk meningkatkan engagement dengan pelanggan Anda.',
    image: '/images/content-creation.svg'
  }
]

onMounted(() => {
  // Cek apakah AOS sudah tersedia di window
  if (typeof window !== 'undefined' && window.AOS) {
    window.AOS.init({
      duration: 800,
      once: true
    })
  }
})
</script> 