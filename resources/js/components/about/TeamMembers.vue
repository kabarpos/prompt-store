<script setup>
import { Link } from '@inertiajs/vue3'
import { onMounted } from 'vue'

const team = [
  {
    name: 'Ahmad Fauzi',
    position: 'CEO & Founder',
    image: '/images/team/ahmad.jpg',
    bio: 'Berpengalaman lebih dari 10 tahun dalam industri teknologi dan digital marketing.'
  },
  {
    name: 'Sarah Wijaya',
    position: 'Head of Design',
    image: '/images/team/sarah.jpg',
    bio: 'Spesialis UI/UX dengan passion dalam menciptakan pengalaman digital yang memukau.'
  },
  {
    name: 'Budi Santoso',
    position: 'Technical Lead',
    image: '/images/team/budi.jpg',
    bio: 'Expert dalam pengembangan aplikasi web dan mobile dengan teknologi terkini.'
  },
  {
    name: 'Linda Kusuma',
    position: 'Digital Marketing Manager',
    image: '/images/team/linda.jpg',
    bio: 'Ahli strategi digital marketing dengan fokus pada pertumbuhan bisnis.'
  },
  {
    name: 'Reza Pratama',
    position: 'Full Stack Developer',
    image: '/images/team/reza.jpg',
    bio: 'Developer berpengalaman dengan keahlian dalam berbagai teknologi web modern.'
  },
  {
    name: 'Maya Putri',
    position: 'Content Strategist',
    image: '/images/team/maya.jpg',
    bio: 'Kreator konten dengan kemampuan storytelling yang kuat untuk brand digital.'
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

<template>
  <section class="py-24 bg-background relative overflow-hidden">
    <!-- Background Decorations -->
    <div class="absolute inset-0 z-0 pointer-events-none">
      <div class="absolute -top-40 -right-40 w-96 h-96 bg-primary/5 dark:bg-primary/10 rounded-full blur-3xl"></div>
      <div class="absolute -bottom-20 -left-40 w-80 h-80 bg-blue-500/5 dark:bg-blue-500/10 rounded-full blur-3xl"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
      <!-- Section Header -->
      <div class="max-w-3xl mx-auto text-center mb-16" data-aos="fade-up">
        <div class="inline-flex items-center justify-center gap-2 bg-primary/10 text-primary-600 dark:text-primary rounded-full px-4 py-1.5 font-medium text-sm mb-4">
          <span class="flex h-2 w-2 rounded-full bg-primary-600 dark:bg-primary"></span>
          Tim Kami
        </div>
        <h2 class="text-3xl md:text-4xl font-bold mb-6 text-slate-900 dark:text-foreground leading-tight">
          Bertemu dengan 
          <span class="relative inline-block">
            <span class="relative z-10">Tim Ahli</span>
            <span class="absolute bottom-1 left-0 w-full h-3 bg-primary-100 dark:bg-primary/20"></span>
          </span>
          Kami
        </h2>
        <p class="text-lg text-slate-600 dark:text-muted-foreground">
          Tim berpengalaman yang siap membantu mengembangkan bisnis digital Anda
        </p>
      </div>

      <!-- Team Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <div 
          v-for="(member, index) in team" 
          :key="index"
          class="group relative"
          :data-aos="'fade-up'"
          :data-aos-delay="100 + (index * 100)"
        >
          <!-- Card -->
          <div class="bg-white dark:bg-card rounded-xl shadow-lg hover:shadow-xl transition-all overflow-hidden border border-slate-200 dark:border-border">
            <!-- Image Container -->
            <div class="relative aspect-[4/3] overflow-hidden">
              <div class="absolute inset-0 bg-gradient-to-tr from-primary-100 to-blue-100 dark:from-primary/20 dark:to-blue-500/20"></div>
              <img 
                :src="member.image" 
                :alt="member.name"
                class="w-full h-full object-cover object-center transform group-hover:scale-105 transition-transform duration-500"
              />
            </div>

            <!-- Content -->
            <div class="p-6">
              <h3 class="text-xl font-bold mb-1 text-slate-900 dark:text-foreground group-hover:text-primary-600 dark:group-hover:text-primary transition-colors">
                {{ member.name }}
              </h3>
              <p class="text-sm font-medium text-primary-600 dark:text-primary mb-4">
                {{ member.position }}
              </p>
              <p class="text-slate-600 dark:text-muted-foreground text-sm">
                {{ member.bio }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Call to Action -->
      <div class="mt-20 text-center" data-aos="fade-up" data-aos-delay="400">
        <div class="max-w-3xl mx-auto">
          <h3 class="text-2xl md:text-3xl font-bold mb-6 text-slate-900 dark:text-foreground">
            Siap untuk Memulai Perjalanan Digital Anda?
          </h3>
          <p class="text-lg text-slate-600 dark:text-muted-foreground mb-8">
            Tim kami siap membantu mewujudkan visi digital bisnis Anda
          </p>
          <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <Link
              href="/contact"
              class="inline-flex items-center justify-center px-6 py-3 rounded-lg text-sm font-medium bg-primary-600 text-white hover:bg-primary-700 dark:bg-primary-600 dark:text-white dark:hover:bg-primary-700 transition-all shadow-lg hover:shadow-primary-600/25"
            >
              Hubungi Kami
            </Link>
            <Link
              href="/services"
              class="inline-flex items-center justify-center px-6 py-3 rounded-lg text-sm font-medium bg-white text-primary-600 hover:bg-primary-50 dark:bg-card dark:text-primary dark:hover:bg-primary/10 transition-all border border-primary-200 dark:border-primary/20"
            >
              Lihat Layanan
            </Link>
          </div>
        </div>
      </div>
    </div>
  </section>
</template> 