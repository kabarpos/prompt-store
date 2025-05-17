<template>
  <MainLayout>
    <HomeHero />
    <WhyChooseUs />
    <GoDigital />
    <Services />
    <FaqSection />
  </MainLayout>
</template>

<script setup>
import { ref } from 'vue'
import MainLayout from '@/components/layout/MainLayout.vue'
import HomeHero from '@/components/home/HomeHero.vue'
import WhyChooseUs from '@/components/home/WhyChooseUs.vue'
import GoDigital from '@/components/home/GoDigital.vue'
import Services from '@/components/home/Services.vue'
import FaqSection from '@/components/home/FaqSection.vue'

const faqs = ref([
  {
    question: 'Bagaimana sistem pembayarannya?',
    answer: 'Kami menerima pembayaran melalui transfer bank dan e-wallet.',
    isOpen: false
  },
  {
    question: 'Berapa lama waktu pengerjaan project?',
    answer: 'Waktu pengerjaan tergantung pada kompleksitas project, biasanya 2-4 minggu.',
    isOpen: false
  },
  {
    question: 'Apakah semua project dikerjakan dilogif?',
    answer: 'Ya, semua project dikerjakan oleh tim internal dilogif.',
    isOpen: false
  },
  {
    question: 'Tim Dilogif online 24 jam?',
    answer: 'Tim support kami online dari Senin-Jumat, 09.00-17.00 WIB.',
    isOpen: false
  }
])

const toggleFaq = (index) => {
  faqs.value[index].isOpen = !faqs.value[index].isOpen
}
</script> 