<template>
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
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          FAQ
        </div>
        <h2 class="text-3xl md:text-4xl font-bold mb-6 text-slate-900 dark:text-foreground leading-tight">
          Pertanyaan Yang Sering 
          <span class="relative inline-block">
            <span class="relative z-10">Ditanyakan</span>
            <span class="absolute bottom-1 left-0 w-full h-3 bg-primary-100 dark:bg-primary/20"></span>
          </span>
        </h2>
        <p class="text-lg text-slate-600 dark:text-muted-foreground">
          Beberapa pertanyaan umum tentang layanan kami yang mungkin membantu Anda
        </p>
      </div>
      
      <div class="max-w-3xl mx-auto">
        <!-- FAQ Accordion -->
        <div class="space-y-5" data-aos="fade-up" data-aos-delay="100">
          <div 
            v-for="(faq, index) in faqs" 
            :key="index" 
            class="rounded-xl border border-slate-200 dark:border-border overflow-hidden bg-white dark:bg-card shadow-sm hover:shadow-md transition-all duration-300"
            :class="{ 'ring-1 ring-primary-600 dark:ring-primary': faq.isOpen }"
          >
            <button 
              class="w-full px-6 py-5 text-left flex justify-between items-center transition-colors"
              :class="{ 'bg-primary-50 dark:bg-primary/10': faq.isOpen }"
              @click="toggleFaq(index)"
            >
              <span class="font-semibold text-slate-900 dark:text-foreground">{{ faq.question }}</span>
              <div 
                class="flex items-center justify-center w-8 h-8 rounded-full bg-slate-100 dark:bg-muted transition-all duration-300"
                :class="{ 'bg-primary-50 dark:bg-primary/10 text-primary-600 dark:text-primary': faq.isOpen }"
              >
                <svg 
                  class="w-5 h-5 transform transition-transform duration-300" 
                  :class="{ 'rotate-180': faq.isOpen }"
                  fill="none" 
                  stroke="currentColor" 
                  viewBox="0 0 24 24"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </div>
            </button>
            <div 
              :class="{ 'max-h-0': !faq.isOpen, 'max-h-96': faq.isOpen }"
              class="overflow-hidden transition-all duration-300 ease-in-out"
            >
              <div class="p-6 border-t border-slate-200 dark:border-border">
                <p class="text-slate-600 dark:text-muted-foreground">{{ faq.answer }}</p>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Additional Help Section -->
        <div class="mt-12 text-center" data-aos="fade-up" data-aos-delay="200">
          <p class="text-slate-600 dark:text-muted-foreground mb-4">
            Masih memiliki pertanyaan lain? Jangan ragu untuk menghubungi kami.
          </p>
          <div class="flex flex-wrap justify-center gap-4">
            <a 
              href="/contact" 
              class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-primary-50 dark:bg-primary/10 text-primary-600 dark:text-primary hover:bg-primary-100 dark:hover:bg-primary/20 transition-colors"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
              Kontak Kami
            </a>
            <a 
              href="#" 
              class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-primary-50 dark:bg-primary/10 text-primary-600 dark:text-primary hover:bg-primary-100 dark:hover:bg-primary/20 transition-colors"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              Pusat Bantuan
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const faqs = ref([
  {
    question: 'Bagaimana sistem pembayarannya?',
    answer: 'Kami menerima pembayaran melalui transfer bank (BCA, BNI, Mandiri, BRI) dan e-wallet (OVO, DANA, GoPay, LinkAja). Setelah pesanan dikonfirmasi, Anda akan menerima informasi pembayaran melalui email atau WhatsApp.',
    isOpen: false
  },
  {
    question: 'Berapa lama waktu pengerjaan project?',
    answer: 'Waktu pengerjaan bervariasi tergantung pada kompleksitas project. Untuk website sederhana biasanya membutuhkan waktu 1-2 minggu, sedangkan untuk project yang lebih kompleks mungkin membutuhkan 3-4 minggu. Kami akan memberikan estimasi waktu yang lebih akurat setelah berdiskusi tentang kebutuhan spesifik Anda.',
    isOpen: false
  },
  {
    question: 'Apakah semua project dikerjakan oleh tim Dilogif?',
    answer: 'Ya, semua project dikerjakan oleh tim internal Dilogif. Kami tidak melakukan outsourcing pekerjaan sehingga kualitas dan konsistensi output dapat terjaga dengan baik. Tim kami terdiri dari profesional berpengalaman di bidang desain, pengembangan, dan digital marketing.',
    isOpen: false
  },
  {
    question: 'Apakah tim Dilogif online 24 jam?',
    answer: 'Tim support kami beroperasi dari Senin-Jumat, pukul 09.00-17.00 WIB. Namun untuk keadaan darurat, kami menyediakan kontak emergency yang dapat dihubungi di luar jam kerja. Kami berkomitmen untuk merespon setiap pertanyaan dalam waktu 1x24 jam pada hari kerja.',
    isOpen: false
  },
  {
    question: 'Apakah ada garansi untuk layanan yang diberikan?',
    answer: 'Ya, kami memberikan garansi untuk layanan yang kami berikan. Untuk website dan aplikasi, kami menyediakan garansi bug fixing selama 30 hari setelah project selesai. Untuk layanan digital marketing, kami berkomitmen untuk mencapai KPI yang telah disepakati atau kami akan memberikan layanan tambahan secara gratis.',
    isOpen: false
  }
])

const toggleFaq = (index) => {
  // Close all other faqs first
  faqs.value.forEach((faq, i) => {
    if (i !== index) faq.isOpen = false
  })
  
  // Toggle the clicked faq
  faqs.value[index].isOpen = !faqs.value[index].isOpen
}

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