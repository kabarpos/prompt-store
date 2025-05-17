<script setup>
const mapLocation = {
  address: 'Jl. Example No. 123, Jakarta',
  lat: -6.2088, // Ganti dengan koordinat yang sesuai
  lng: 106.8456 // Ganti dengan koordinat yang sesuai
}
</script>

<template>
  <section class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-8">
        <h2 class="text-3xl font-bold text-gray-900">Our Location</h2>
        <p class="mt-4 text-lg text-gray-500">{{ mapLocation.address }}</p>
      </div>
      <div class="aspect-w-16 aspect-h-9">
        <iframe
          class="w-full h-[400px] rounded-lg shadow-lg"
          frameborder="0"
          style="border:0"
          :src="'https://www.google.com/maps/embed/v1/place?key=YOUR_API_KEY&q=' + encodeURIComponent(mapLocation.address)"
          allowfullscreen>
        </iframe>
      </div>
    </div>
  </section>
</template> 