<template>
  <section id="contact-form" class="py-24 bg-background relative overflow-hidden">
    <!-- Background Decorations -->
    <div class="absolute inset-0 z-0 pointer-events-none">
      <div class="absolute -top-20 -right-20 w-64 h-64 bg-primary/5 dark:bg-primary/10 rounded-full blur-3xl"></div>
      <div class="absolute bottom-10 -left-20 w-80 h-80 bg-blue-500/5 dark:bg-blue-500/10 rounded-full blur-3xl"></div>
    </div>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
      <!-- Section Header -->
      <div class="text-center mb-12" data-aos="fade-up">
        <div class="inline-flex items-center justify-center gap-2 bg-primary/10 text-primary-600 dark:text-primary rounded-full px-4 py-1.5 font-medium text-sm mb-4">
          <span class="flex h-2 w-2 rounded-full bg-primary-600 dark:bg-primary"></span>
          Formulir Kontak
        </div>
        <h2 class="text-3xl md:text-4xl font-bold mb-6 text-slate-900 dark:text-foreground leading-tight">
          Sampaikan 
          <span class="relative inline-block">
            <span class="relative z-10">Kebutuhan</span>
            <span class="absolute bottom-1 left-0 w-full h-3 bg-primary-100 dark:bg-primary/20"></span>
          </span>
          Anda
        </h2>
        <p class="text-lg text-slate-600 dark:text-muted-foreground">
          Isi formulir di bawah ini dan tim kami akan segera menghubungi Anda
        </p>
      </div>

      <!-- Contact Form -->
      <div class="bg-white dark:bg-card rounded-2xl p-8 shadow-xl border border-slate-200 dark:border-border backdrop-blur-sm" data-aos="fade-up" data-aos-delay="200">
        <form @submit.prevent="submit" class="space-y-6">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <!-- Name Input -->
            <div>
              <label for="name" class="block text-sm font-medium text-slate-900 dark:text-foreground mb-2">Nama Lengkap</label>
              <input 
                type="text" 
                id="name"
                v-model="form.name"
                required
                class="w-full px-4 py-3 rounded-lg bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 dark:focus:border-primary dark:focus:ring-primary/20 text-slate-900 dark:text-foreground placeholder-slate-400 dark:placeholder-slate-500"
                placeholder="Masukkan nama lengkap"
              >
              <div v-if="form.errors.name" class="mt-2 text-sm text-red-600 dark:text-red-500">{{ form.errors.name }}</div>
            </div>

            <!-- Email Input -->
            <div>
              <label for="email" class="block text-sm font-medium text-slate-900 dark:text-foreground mb-2">Email</label>
              <input 
                type="email" 
                id="email"
                v-model="form.email"
                required
                class="w-full px-4 py-3 rounded-lg bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 dark:focus:border-primary dark:focus:ring-primary/20 text-slate-900 dark:text-foreground placeholder-slate-400 dark:placeholder-slate-500"
                placeholder="Masukkan alamat email"
              >
              <div v-if="form.errors.email" class="mt-2 text-sm text-red-600 dark:text-red-500">{{ form.errors.email }}</div>
            </div>
          </div>

          <!-- Phone Input -->
          <div>
            <label for="phone" class="block text-sm font-medium text-slate-900 dark:text-foreground mb-2">Nomor Telepon</label>
            <input 
              type="tel" 
              id="phone"
              v-model="form.phone"
              class="w-full px-4 py-3 rounded-lg bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 dark:focus:border-primary dark:focus:ring-primary/20 text-slate-900 dark:text-foreground placeholder-slate-400 dark:placeholder-slate-500"
              placeholder="Masukkan nomor telepon"
            >
            <div v-if="form.errors.phone" class="mt-2 text-sm text-red-600 dark:text-red-500">{{ form.errors.phone }}</div>
          </div>

          <!-- Message Input -->
          <div>
            <label for="message" class="block text-sm font-medium text-slate-900 dark:text-foreground mb-2">Pesan</label>
            <textarea 
              id="message"
              v-model="form.message"
              rows="4"
              required
              class="w-full px-4 py-3 rounded-lg bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 dark:focus:border-primary dark:focus:ring-primary/20 text-slate-900 dark:text-foreground placeholder-slate-400 dark:placeholder-slate-500"
              placeholder="Tuliskan pesan atau kebutuhan Anda"
            ></textarea>
            <div v-if="form.errors.message" class="mt-2 text-sm text-red-600 dark:text-red-500">{{ form.errors.message }}</div>
          </div>

          <!-- Submit Button -->
          <button 
            type="submit"
            :disabled="form.processing"
            class="w-full inline-flex items-center justify-center px-6 py-3 rounded-lg text-sm font-medium bg-primary-600 text-white hover:bg-primary-700 dark:bg-primary-600 dark:text-white dark:hover:bg-primary-700 transition-all shadow-lg hover:shadow-primary-600/25 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ form.processing ? 'Mengirim...' : 'Kirim Pesan' }}
          </button>
        </form>
      </div>
    </div>
  </section>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import { onMounted } from 'vue'

const form = useForm({
  name: '',
  email: '',
  phone: '',
  message: ''
})

const submit = () => {
  form.post(route('contact.store'), {
    preserveScroll: true,
    onSuccess: () => {
      form.reset()
    }
  })
}

onMounted(() => {
  if (typeof window !== 'undefined' && window.AOS) {
    window.AOS.init({
      duration: 800,
      once: true
    })
  }
})
</script> 