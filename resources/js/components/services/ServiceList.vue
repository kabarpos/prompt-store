<template>
  <section class="py-24 bg-background relative overflow-hidden">
    <!-- Background Decorations -->
    <div class="absolute inset-0 z-0 pointer-events-none">
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
          Solusi Digital untuk 
          <span class="relative inline-block">
            <span class="relative z-10">Bisnis Anda</span>
            <span class="absolute bottom-1 left-0 w-full h-3 bg-primary-100 dark:bg-primary/20"></span>
          </span>
        </h2>
        <p class="text-lg text-slate-600 dark:text-muted-foreground">
          Explore layanan komprehensif kami yang dirancang untuk memenuhi kebutuhan bisnis Anda
        </p>
      </div>

      <!-- Services Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
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
                <component :is="service.icon" class="w-6 h-6" />
              </div>
              <div class="opacity-0 group-hover:opacity-100 transition-opacity">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary-600 dark:text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
              </div>
            </div>
            
            <div>
              <h3 class="text-xl font-bold mb-3 text-slate-900 dark:text-foreground group-hover:text-primary-600 dark:group-hover:text-primary transition-colors">
                {{ service.title }}
              </h3>
              <p class="text-slate-600 dark:text-muted-foreground mb-6">
                {{ service.description }}
              </p>
            </div>
            
            <!-- Features List -->
            <ul class="space-y-3 mb-6">
              <li 
                v-for="feature in service.features" 
                :key="feature"
                class="flex items-center text-slate-600 dark:text-muted-foreground"
              >
                <CheckIcon class="h-5 w-5 text-primary-600 dark:text-primary mr-2 flex-shrink-0" />
                <span class="text-sm">{{ feature }}</span>
              </li>
            </ul>
            
            <!-- Call to Action -->
            <a 
              href="#" 
              class="inline-flex items-center text-primary-600 dark:text-primary font-medium group-hover:underline"
            >
              Pelajari Lebih Lanjut
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 transition-transform group-hover:translate-x-1" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
              </svg>
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { CheckIcon } from '@heroicons/vue/24/outline'
import {
  CodeBracketIcon,
  DevicePhoneMobileIcon,
  PaintBrushIcon,
  CloudIcon,
  MegaphoneIcon,
  LightBulbIcon
} from '@heroicons/vue/24/outline'
import { onMounted } from 'vue'

const services = [
  {
    title: 'Web Development',
    description: 'Website modern dan responsif yang menarik untuk meningkatkan kredibilitas bisnis Anda.',
    icon: CodeBracketIcon,
    features: [
      'Responsive Design',
      'Custom CMS Development',
      'E-commerce Solutions',
      'API Integration'
    ]
  },
  {
    title: 'Mobile Apps',
    description: 'Aplikasi mobile native dan cross-platform yang powerful untuk menjangkau pengguna lebih luas.',
    icon: DevicePhoneMobileIcon,
    features: [
      'iOS Development',
      'Android Development',
      'Cross-platform Solutions',
      'App Maintenance'
    ]
  },
  {
    title: 'UI/UX Design',
    description: 'Desain antarmuka yang indah dan intuitif untuk pengalaman pengguna yang optimal.',
    icon: PaintBrushIcon,
    features: [
      'User Research',
      'Wireframing',
      'Prototyping',
      'Visual Design'
    ]
  },
  {
    title: 'Cloud Solutions',
    description: 'Infrastruktur cloud yang scalable untuk mendukung pertumbuhan bisnis Anda.',
    icon: CloudIcon,
    features: [
      'Cloud Migration',
      'Server Management',
      'DevOps Services',
      'Security Solutions'
    ]
  },
  {
    title: 'Digital Marketing',
    description: 'Strategi pemasaran digital yang efektif untuk meningkatkan visibility brand Anda.',
    icon: MegaphoneIcon,
    features: [
      'SEO Optimization',
      'Social Media Marketing',
      'Content Strategy',
      'Analytics & Reporting'
    ]
  },
  {
    title: 'Consulting',
    description: 'Konsultasi bisnis dan teknologi dari tim ahli untuk solusi yang tepat.',
    icon: LightBulbIcon,
    features: [
      'Technology Assessment',
      'Digital Strategy',
      'Process Optimization',
      'Team Training'
    ]
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