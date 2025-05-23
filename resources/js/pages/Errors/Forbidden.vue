<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="text-center px-6">
      <h1 class="text-9xl font-bold text-red-600">403</h1>
      <h2 class="mt-4 text-3xl font-medium">Akses Ditolak</h2>
      <p class="mt-6 text-lg text-gray-600">
        Maaf, Anda tidak memiliki izin untuk mengakses halaman ini.
      </p>
      <div class="mt-10">
        <Link
          :href="route('home')"
          class="px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors duration-200"
        >
          Kembali ke Halaman Utama
        </Link>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
</script> 