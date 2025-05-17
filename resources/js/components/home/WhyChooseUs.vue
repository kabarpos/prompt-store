<template>
  <section class="py-24 relative overflow-hidden bg-background">
    <!-- Decoration Elements -->
    <div class="absolute inset-0 z-0 opacity-20">
      <div class="absolute -bottom-5 -right-5 w-96 h-96 bg-primary/10 dark:bg-primary/30 rounded-full blur-3xl"></div>
      <div class="absolute top-40 -left-40 w-80 h-80 bg-blue-500/10 dark:bg-blue-500/20 rounded-full blur-3xl"></div>
    </div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
      <!-- Section header with decorative elements -->
      <div class="max-w-3xl mx-auto text-center mb-16" data-aos="fade-up">
        <div class="inline-flex items-center justify-center gap-2 bg-primary/10 text-primary-600 dark:text-primary rounded-full px-4 py-1.5 font-medium text-sm mb-4">
          <span class="flex h-2 w-2 rounded-full bg-primary-600 dark:bg-primary"></span>
          Keunggulan Kami
        </div>
        <h2 class="text-3xl md:text-4xl font-bold mb-6 text-slate-900 dark:text-foreground leading-tight">
          Mengapa Bisnis Anda <span class="relative inline-block">
            <span class="relative z-10">Harus Dikenal</span>
            <span class="absolute bottom-1 left-0 w-full h-3 bg-primary-100 dark:bg-primary/20"></span>
          </span> Banyak Orang?
        </h2>
        <p class="text-lg text-slate-600 dark:text-muted-foreground">
          Semakin banyak orang yang mengenal bisnis Anda, semakin besar peluang Anda untuk mendapatkan pelanggan baru.
        </p>
      </div>
      
      <!-- Cards Grid -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Card 1 -->
        <div 
          class="group p-8 rounded-xl bg-white dark:bg-card shadow-lg hover:shadow-xl transition-all border border-slate-200 dark:border-border relative overflow-hidden"
          data-aos="fade-up"
          data-aos-delay="100"
        >
          <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-primary-600 to-blue-600 dark:from-primary dark:to-blue-500 transform origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></div>
          
          <div class="mb-6">
            <div class="w-16 h-16 bg-gradient-to-tr from-primary-600 to-blue-600 dark:from-primary dark:to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
              </svg>
            </div>
          </div>
          
          <div class="space-y-4">
            <h3 class="text-xl font-bold text-slate-900 dark:text-foreground group-hover:text-primary-600 dark:group-hover:text-primary transition-colors">Tingkatkan Peringkat</h3>
            <p class="text-slate-600 dark:text-muted-foreground">
              Bisnis yang dikenal luas akan lebih mudah meningkatkan peringkat di mesin pencari, sehingga lebih mudah ditemukan calon pelanggan.
            </p>
            <div class="pt-4">
              <a href="#" class="inline-flex items-center text-primary-600 dark:text-primary font-medium group-hover:underline">
                Pelajari Lebih Lanjut
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 transition-transform group-hover:translate-x-1" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
              </a>
            </div>
          </div>
        </div>
        
        <!-- Card 2 -->
        <div 
          class="group p-8 rounded-xl bg-white dark:bg-card shadow-lg hover:shadow-xl transition-all border border-slate-200 dark:border-border relative overflow-hidden"
          data-aos="fade-up"
          data-aos-delay="200"
        >
          <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-primary-600 to-blue-600 dark:from-primary dark:to-blue-500 transform origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></div>
          
          <div class="mb-6">
            <div class="w-16 h-16 bg-gradient-to-tr from-primary-600 to-blue-600 dark:from-primary dark:to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
              </svg>
            </div>
          </div>
          
          <div class="space-y-4">
            <h3 class="text-xl font-bold text-slate-900 dark:text-foreground group-hover:text-primary-600 dark:group-hover:text-primary transition-colors">Tingkatkan Omzet</h3>
            <p class="text-slate-600 dark:text-muted-foreground">
              Semakin banyak orang yang mengenal bisnis Anda, semakin tinggi potensi konversi menjadi pelanggan, yang akan meningkatkan omzet Anda.
            </p>
            <div class="pt-4">
              <a href="#" class="inline-flex items-center text-primary-600 dark:text-primary font-medium group-hover:underline">
                Pelajari Lebih Lanjut
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 transition-transform group-hover:translate-x-1" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
              </a>
            </div>
          </div>
        </div>
        
        <!-- Card 3 -->
        <div 
          class="group p-8 rounded-xl bg-white dark:bg-card shadow-lg hover:shadow-xl transition-all border border-slate-200 dark:border-border relative overflow-hidden"
          data-aos="fade-up"
          data-aos-delay="300"
        >
          <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-primary-600 to-blue-600 dark:from-primary dark:to-blue-500 transform origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></div>
          
          <div class="mb-6">
            <div class="w-16 h-16 bg-gradient-to-tr from-primary-600 to-blue-600 dark:from-primary dark:to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
              </svg>
            </div>
          </div>
          
          <div class="space-y-4">
            <h3 class="text-xl font-bold text-slate-900 dark:text-foreground group-hover:text-primary-600 dark:group-hover:text-primary transition-colors">Kembangkan Bisnis</h3>
            <p class="text-slate-600 dark:text-muted-foreground">
              Dengan dikenal lebih luas, bisnis Anda memiliki lebih banyak peluang untuk tumbuh, berinovasi, dan memperluas jangkauan pasar.
            </p>
            <div class="pt-4">
              <a href="#" class="inline-flex items-center text-primary-600 dark:text-primary font-medium group-hover:underline">
                Pelajari Lebih Lanjut
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 transition-transform group-hover:translate-x-1" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
              </a>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Metrics Row -->
      <div class="mt-20 grid grid-cols-1 md:grid-cols-3 gap-8" data-aos="fade-up" data-aos-delay="400">
        <div class="text-center p-6 rounded-xl border border-slate-200 dark:border-border bg-white dark:bg-card/50 backdrop-blur-sm">
          <div class="text-4xl font-bold text-primary-600 dark:text-primary mb-2">200+</div>
          <p class="text-slate-600 dark:text-muted-foreground">Proyek Selesai</p>
        </div>
        <div class="text-center p-6 rounded-xl border border-slate-200 dark:border-border bg-white dark:bg-card/50 backdrop-blur-sm">
          <div class="text-4xl font-bold text-primary-600 dark:text-primary mb-2">98%</div>
          <p class="text-slate-600 dark:text-muted-foreground">Pelanggan Puas</p>
        </div>
        <div class="text-center p-6 rounded-xl border border-slate-200 dark:border-border bg-white dark:bg-card/50 backdrop-blur-sm">
          <div class="text-4xl font-bold text-primary-600 dark:text-primary mb-2">15+</div>
          <p class="text-slate-600 dark:text-muted-foreground">Tahun Pengalaman</p>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
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