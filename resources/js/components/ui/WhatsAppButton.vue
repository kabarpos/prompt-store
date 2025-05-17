<template>
  <div>
    <!-- WhatsApp Button -->
    <button 
      @click="isOpen = true" 
      class="fixed bottom-6 right-6 z-50 bg-[#25D366] text-white rounded-full p-4 shadow-lg hover:bg-[#128C7E] transition-colors focus:outline-none animate-pulse-slow"
      aria-label="Contact via WhatsApp"
    >
      <!-- WhatsApp Icon - Versi yang lebih sempurna -->
      <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 256 258">
        <defs>
          <linearGradient id="logosWhatsappIcon0" x1="50%" x2="50%" y1="100%" y2="0%">
            <stop offset="0%" stop-color="#1FAF38"/>
            <stop offset="100%" stop-color="#60D669"/>
          </linearGradient>
          <linearGradient id="logosWhatsappIcon1" x1="50%" x2="50%" y1="100%" y2="0%">
            <stop offset="0%" stop-color="#F9F9F9"/>
            <stop offset="100%" stop-color="#FFF"/>
          </linearGradient>
        </defs>
        <path fill="url(#logosWhatsappIcon0)" d="M5.463 127.456c-.006 21.677 5.658 42.843 16.428 61.499L4.433 252.697l65.232-17.104a122.994 122.994 0 0 0 58.8 14.97h.054c67.815 0 123.018-55.183 123.047-123.01c.013-32.867-12.775-63.773-36.009-87.025c-23.23-23.25-54.125-36.061-87.043-36.076c-67.823 0-123.022 55.18-123.05 123.004"/>
        <path fill="url(#logosWhatsappIcon1)" d="M1.07 127.416c-.007 22.457 5.86 44.38 17.014 63.704L0 257.147l67.571-17.717c18.618 10.151 39.58 15.503 60.91 15.511h.055c70.248 0 127.434-57.168 127.464-127.423c.012-34.048-13.236-66.065-37.3-90.15C194.633 13.286 162.633.014 128.536 0C58.276 0 1.099 57.16 1.071 127.416Zm40.24 60.376l-2.523-4.005c-10.606-16.864-16.204-36.352-16.196-56.363C22.614 69.029 70.138 21.52 128.576 21.52c28.3.012 54.896 11.044 74.9 31.06c20.003 20.018 31.01 46.628 31.003 74.93c-.026 58.395-47.551 105.91-105.943 105.91h-.042c-19.013-.01-37.66-5.116-53.922-14.765l-3.87-2.295l-40.098 10.513l10.706-39.082Z"/>
        <path fill="#FFF" d="M96.678 74.148c-2.386-5.303-4.897-5.41-7.166-5.503c-1.858-.08-3.982-.074-6.104-.074c-2.124 0-5.575.799-8.492 3.984c-2.92 3.188-11.148 10.892-11.148 26.561c0 15.67 11.413 30.813 13.004 32.94c1.593 2.123 22.033 35.307 54.405 48.073c26.904 10.609 32.379 8.499 38.218 7.967c5.84-.53 18.844-7.702 21.497-15.139c2.655-7.436 2.655-13.81 1.859-15.142c-.796-1.327-2.92-2.124-6.105-3.716c-3.186-1.593-18.844-9.298-21.763-10.361c-2.92-1.062-5.046-1.59-7.167 1.597c-2.122 3.184-8.223 10.356-10.082 12.48c-1.857 2.129-3.716 2.394-6.9.801c-3.187-1.598-13.444-4.957-25.613-15.806c-9.468-8.442-15.86-18.867-17.718-22.056c-1.858-3.184-.199-4.91 1.398-6.497c1.431-1.427 3.186-3.719 4.78-5.578c1.588-1.86 2.118-3.187 3.18-5.311c1.063-2.126.531-3.986-.264-5.579c-.798-1.593-6.987-17.343-9.819-23.64"/>
      </svg>
    </button>

    <!-- Modal Popup -->
    <div 
      v-if="isOpen" 
      class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
    >
      <div 
        class="bg-white rounded-lg shadow-xl w-full max-w-md p-6 relative"
        @click.stop
      >
        <!-- Close Button -->
        <button 
          @click="isOpen = false" 
          class="absolute top-4 right-4 text-gray-400 hover:text-gray-600"
          aria-label="Close"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>

        <!-- Form Header -->
        <div class="flex items-center gap-3 mb-6">
          <div class="bg-[#25D366] text-white p-2 rounded-full">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 256 258" class="w-6 h-6">
              <path fill="#FFF" d="M96.678 74.148c-2.386-5.303-4.897-5.41-7.166-5.503c-1.858-.08-3.982-.074-6.104-.074c-2.124 0-5.575.799-8.492 3.984c-2.92 3.188-11.148 10.892-11.148 26.561c0 15.67 11.413 30.813 13.004 32.94c1.593 2.123 22.033 35.307 54.405 48.073c26.904 10.609 32.379 8.499 38.218 7.967c5.84-.53 18.844-7.702 21.497-15.139c2.655-7.436 2.655-13.81 1.859-15.142c-.796-1.327-2.92-2.124-6.105-3.716c-3.186-1.593-18.844-9.298-21.763-10.361c-2.92-1.062-5.046-1.59-7.167 1.597c-2.122 3.184-8.223 10.356-10.082 12.48c-1.857 2.129-3.716 2.394-6.9.801c-3.187-1.598-13.444-4.957-25.613-15.806c-9.468-8.442-15.86-18.867-17.718-22.056c-1.858-3.184-.199-4.91 1.398-6.497c1.431-1.427 3.186-3.719 4.78-5.578c1.588-1.86 2.118-3.187 3.18-5.311c1.063-2.126.531-3.986-.264-5.579c-.798-1.593-6.987-17.343-9.819-23.64"/>
            </svg>
          </div>
          <h3 class="text-xl font-semibold text-gray-900">Hubungi Kami via WhatsApp</h3>
        </div>

        <!-- Form -->
        <form @submit.prevent="submitForm">
          <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
            <input 
              type="text" 
              id="name" 
              v-model="form.name" 
              placeholder="Masukkan nama Anda"
              class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#3B8BEE] focus:border-transparent"
              required
            />
          </div>

          <div class="mb-6">
            <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pesan</label>
            <textarea 
              id="message" 
              v-model="form.message" 
              placeholder="Tuliskan pesan Anda..."
              rows="4"
              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-slate-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-[#3B8BEE] focus:border-transparent resize-none"
              required
            ></textarea>
          </div>

          <button 
            type="submit" 
            class="w-full bg-[#25D366] text-white py-3 px-4 rounded-md font-medium hover:bg-[#128C7E] transition-colors focus:outline-none focus:ring-2 focus:ring-[#25D366] focus:ring-opacity-50"
          >
            Kirim via WhatsApp
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { router } from '@inertiajs/vue3'

const isOpen = ref(false)
const form = reactive({
  name: '',
  message: ''
})

async function submitForm() {
  try {
    console.log('Mengirim request ke API WhatsApp...')
    // Dapatkan nomor WhatsApp berikutnya dalam rotasi
    const response = await fetch(route('api.whatsapp.next'))
    console.log('Response status:', response.status)
    
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`)
    }
    
    const data = await response.json()
    console.log('WhatsApp API Response:', data)
    
    if (!data.success) {
      console.error('API Error:', data.message)
      throw new Error(data.message || 'Gagal mendapatkan nomor WhatsApp')
    }
    
    if (!data.phone_number) {
      console.error('Phone number missing in response:', data)
      throw new Error('Nomor WhatsApp tidak tersedia')
    }
    
    const whatsappNumber = data.phone_number
    console.log('WhatsApp number:', whatsappNumber)
    
    // Format pesan untuk WhatsApp
    const formattedMessage = encodeURIComponent(
      `*Pesan dari Website*\n\nNama: ${form.name}\nPesan: ${form.message}`
    )
    
    // Buat URL WhatsApp
    const whatsappUrl = `https://wa.me/${whatsappNumber.replace(/\+/g, '')}?text=${formattedMessage}`
    console.log('WhatsApp URL:', whatsappUrl)
    
    // Buka WhatsApp di tab baru
    window.open(whatsappUrl, '_blank')
    
    // Reset form dan tutup modal
    form.name = ''
    form.message = ''
    isOpen.value = false
  } catch (error) {
    console.error('Error detail:', {
      message: error.message,
      stack: error.stack
    })
    alert('Terjadi kesalahan saat mengirim pesan. Silakan coba lagi.')
  }
}
</script>

<style>
.animate-pulse-slow {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
  0%, 100% {
    opacity: 1;
    transform: scale(1);
  }
  50% {
    opacity: 0.85;
    transform: scale(1.05);
  }
}
</style> 