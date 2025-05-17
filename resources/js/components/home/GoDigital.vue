<template>
  <section class="py-24 relative overflow-hidden bg-background">
    <!-- Background Elements -->
    <div class="absolute inset-0 z-0 pointer-events-none">
      <div class="absolute top-0 right-0 w-1/2 h-full bg-gradient-to-l from-primary/5 to-transparent dark:from-primary/10 dark:to-transparent"></div>
      <div class="absolute -bottom-10 -left-10 w-80 h-80 bg-primary/5 dark:bg-primary/10 rounded-full blur-3xl"></div>
    </div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-12 lg:gap-20 items-center">
        <!-- Text Content -->
        <div data-aos="fade-right" data-aos-duration="1000">
          <div class="inline-flex items-center gap-2 bg-primary/10 text-primary-600 dark:text-primary rounded-full px-4 py-1.5 font-medium text-sm mb-6">
            <span class="flex h-2 w-2 rounded-full bg-primary-600 dark:bg-primary"></span>
            Digital Transformation
          </div>
          <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-slate-900 dark:text-foreground mb-6 leading-tight">
            Saatnya <span class="text-primary-600 dark:text-primary">Go Digital</span> untuk 
            <span class="relative inline-block">
              <span class="relative z-10">Bisnis Anda</span>
              <span class="absolute bottom-2 left-0 w-full h-3 bg-primary-100 dark:bg-primary/20"></span>
            </span>
          </h2>
          <p class="text-lg text-slate-600 dark:text-muted-foreground mb-8 leading-relaxed">
            Optimalkan pemasaran digital bisnis Anda dengan strategi yang tepat untuk mendapatkan omset berlipat ganda dan pertumbuhan berkelanjutan.
          </p>
          <div class="flex flex-wrap gap-4">
            <button
              class="bg-primary-600 hover:bg-primary-700 dark:bg-primary dark:hover:bg-primary-600 text-white px-8 py-3.5 rounded-xl shadow-lg hover:shadow-primary-600/25 transform hover:-translate-y-0.5 transition-all duration-200 flex items-center gap-2 font-medium group"
              @click="$router.push({ name: 'contact' })"
            >
              <ChatBubbleLeftRightIcon class="w-5 h-5" />
              <span>Konsultasi Gratis</span>
            </button>
            <button
              class="border-2 border-primary-600 dark:border-primary text-primary-600 dark:text-primary hover:bg-primary-50 dark:hover:bg-primary/10 px-8 py-3.5 rounded-xl hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200 flex items-center gap-2 font-medium group"
              @click="$router.push({ name: 'about' })"
            >
              <InformationCircleIcon class="w-5 h-5" />
              <span>Pelajari Lebih Lanjut</span>
            </button>
          </div>
          
          <!-- Stats -->
          <div class="grid grid-cols-3 gap-6 mt-12 pt-8 border-t border-slate-200 dark:border-border">
            <div>
              <div class="text-3xl font-bold text-primary-600 dark:text-primary">500+</div>
              <div class="text-sm text-slate-600 dark:text-muted-foreground mt-1">Klien Puas</div>
            </div>
            <div>
              <div class="text-3xl font-bold text-primary-600 dark:text-primary">95%</div>
              <div class="text-sm text-slate-600 dark:text-muted-foreground mt-1">Tingkat Keberhasilan</div>
            </div>
            <div>
              <div class="text-3xl font-bold text-primary-600 dark:text-primary">24/7</div>
              <div class="text-sm text-slate-600 dark:text-muted-foreground mt-1">Dukungan Tim</div>
            </div>
          </div>
        </div>
        
        <!-- Media Section -->
        <div class="relative" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="200">
          <!-- Video Card with Shadow Effect -->
          <div class="absolute -inset-4 bg-gradient-to-tr from-primary-100 to-blue-100 dark:from-primary/20 dark:to-blue-500/20 rounded-2xl blur-lg opacity-50 transform rotate-3"></div>
          <div class="relative bg-white dark:bg-card rounded-2xl overflow-hidden shadow-lg dark:shadow-primary/5 border border-slate-200 dark:border-border">
            <div class="aspect-w-16 aspect-h-9 relative overflow-hidden">
              <img 
                src="/images/hero-dashboard.svg" 
                alt="Digital Marketing Strategy" 
                class="w-full h-full object-cover object-center"
              />
              <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
              
              <!-- Play Button -->
              <div class="absolute inset-0 flex items-center justify-center group">
                <button 
                  class="w-16 h-16 bg-primary-600/90 dark:bg-primary/90 backdrop-blur-sm rounded-full flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 border-2 border-white/20"
                  @click.prevent
                >
                  <svg class="w-6 h-6 text-white ml-1" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M8 5v14l11-7z" />
                  </svg>
                </button>
              </div>
              
              <!-- Overlay Text -->
              <div class="absolute bottom-0 left-0 right-0 p-6">
                <h3 class="text-xl font-semibold text-white mb-2">Bagaimana Digital Marketing Bekerja?</h3>
                <p class="text-white/80 text-sm">Pelajari strategi digital marketing yang efektif untuk bisnis Anda</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Process Steps -->
      <div class="mt-24" data-aos="fade-up" data-aos-delay="300">
        <div class="text-center max-w-3xl mx-auto mb-16">
          <h3 class="text-2xl md:text-3xl font-bold text-slate-900 dark:text-foreground mb-4">Proses Kerja Sama dengan Kami</h3>
          <p class="text-slate-600 dark:text-muted-foreground">Kami berkomitmen untuk memberikan layanan terbaik dengan proses yang transparan dan profesional</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div class="bg-white dark:bg-card rounded-xl p-8 shadow-sm dark:shadow-primary/5 border border-slate-200 dark:border-border hover:shadow-md transition-shadow relative group">
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-primary-600 to-blue-600 dark:from-primary dark:to-blue-500 transform origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></div>
            <div class="w-16 h-16 flex items-center justify-center rounded-2xl bg-primary-50 dark:bg-primary/10 text-primary-600 dark:text-primary mb-6 group-hover:bg-primary-600 dark:group-hover:bg-primary group-hover:text-white transition-colors">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
              </svg>
            </div>
            <h3 class="text-xl font-bold text-slate-900 dark:text-foreground mb-3 group-hover:text-primary-600 dark:group-hover:text-primary transition-colors">Siap Konsultasi</h3>
            <p class="text-slate-600 dark:text-muted-foreground">
              Konsultasikan kebutuhan Anda melalui WhatsApp atau Google Meet. Kami menyediakan ruang diskusi yang nyaman dan informatif untuk memahami kebutuhan spesifik bisnis Anda.
            </p>
            <div class="mt-6 pt-4 border-t border-slate-200 dark:border-border">
              <a href="#" class="inline-flex items-center text-primary-600 dark:text-primary font-medium group-hover:underline">
                Jadwalkan Konsultasi
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 transition-transform group-hover:translate-x-1" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
              </a>
            </div>
          </div>
          
          <div class="bg-white dark:bg-card rounded-xl p-8 shadow-sm dark:shadow-primary/5 border border-slate-200 dark:border-border hover:shadow-md transition-shadow relative group">
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-primary-600 to-blue-600 dark:from-primary dark:to-blue-500 transform origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></div>
            <div class="w-16 h-16 flex items-center justify-center rounded-2xl bg-primary-50 dark:bg-primary/10 text-primary-600 dark:text-primary mb-6 group-hover:bg-primary-600 dark:group-hover:bg-primary group-hover:text-white transition-colors">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
              </svg>
            </div>
            <h3 class="text-xl font-bold text-slate-900 dark:text-foreground mb-3 group-hover:text-primary-600 dark:group-hover:text-primary transition-colors">Siap Membantu</h3>
            <p class="text-slate-600 dark:text-muted-foreground">
              Tim professional kami siap membantu Anda selama dan setelah proyek selesai. Kami menyediakan dukungan teknis dan konsultasi lanjutan untuk memastikan keberhasilan implementasi solusi digital Anda.
            </p>
            <div class="mt-6 pt-4 border-t border-slate-200 dark:border-border">
              <a href="#" class="inline-flex items-center text-primary-600 dark:text-primary font-medium group-hover:underline">
                Lihat Dukungan Kami
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 transition-transform group-hover:translate-x-1" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
              </a>
            </div>
          </div>
          
          <div class="bg-white dark:bg-card rounded-xl p-8 shadow-sm dark:shadow-primary/5 border border-slate-200 dark:border-border hover:shadow-md transition-shadow relative group">
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-primary-600 to-blue-600 dark:from-primary dark:to-blue-500 transform origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></div>
            <div class="w-16 h-16 flex items-center justify-center rounded-2xl bg-primary-50 dark:bg-primary/10 text-primary-600 dark:text-primary mb-6 group-hover:bg-primary-600 dark:group-hover:bg-primary group-hover:text-white transition-colors">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
              </svg>
            </div>
            <h3 class="text-xl font-bold text-slate-900 dark:text-foreground mb-3 group-hover:text-primary-600 dark:group-hover:text-primary transition-colors">Siap Amanah</h3>
            <p class="text-slate-600 dark:text-muted-foreground">
              Kami berkomitmen untuk menyelesaikan proyek tepat waktu dengan kualitas terbaik. Semua ketentuan dan jaminan dijelaskan di awal akad untuk transparansi dan kepercayaan mutual.
            </p>
            <div class="mt-6 pt-4 border-t border-slate-200 dark:border-border">
              <a href="#" class="inline-flex items-center text-primary-600 dark:text-primary font-medium group-hover:underline">
                Lihat Garansi Kami
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 transition-transform group-hover:translate-x-1" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ChatBubbleLeftRightIcon, InformationCircleIcon } from '@heroicons/vue/24/outline'
import { onMounted } from 'vue'

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

<style scoped>
.aspect-w-16 {
  position: relative;
  padding-bottom: 56.25%; /* 16:9 Aspect Ratio */
}

.aspect-w-16 > * {
  position: absolute;
  height: 100%;
  width: 100%;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
}
</style> 