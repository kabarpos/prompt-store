<template>
  <div>
    <slot />
  </div>
</template>

<script setup>
// Komponen tambahan untuk memperbaiki styling form admin panel
// Meminimalisir pengulangan kode CSS di banyak halaman
</script>

<style>
/* Styles untuk form element focus */
.form-control:focus {
  outline: none !important;
  box-shadow: none !important;
  border-color: #0ea5e9 !important;
}

/* Styles untuk Multiselect dropdown */
.multiselect.is-active {
  outline: none !important;
  box-shadow: none !important;
  border-color: #0ea5e9 !important;
}

/* Styles untuk date picker */
input[type="date"]:focus,
input[type="datetime-local"]:focus {
  outline: none !important;
  box-shadow: none !important;
  border-color: #0ea5e9 !important;
}

/* Styles untuk dark mode */
.dark .form-control:focus,
.dark .multiselect.is-active,
.dark input[type="date"]:focus,
.dark input[type="datetime-local"]:focus {
  border-color: #38bdf8 !important;
}

/* Custom admin form styling */
.custom-select-trigger:focus,
.custom-select-trigger:focus-visible,
.custom-select-trigger:active,
.custom-select-trigger:hover,
.custom-select-trigger:-moz-focusring {
  outline: none !important;
  box-shadow: none !important;
  border-color: #0ea5e9 !important;
}

/* Style untuk input admin yang lain */
div:focus,
div:focus-visible,
div:focus-within,
div:active {
  outline: none !important;
  outline-color: transparent !important;
  box-shadow: none !important;
  border-color: #0ea5e9 !important;
}

/* Fix untuk Firefox */
div:-moz-focusring,
.custom-select-trigger:-moz-focusring {
  outline: none !important;
  outline-color: transparent !important;
}

/* Fix untuk Safari dan Chrome */
.custom-select-trigger::-webkit-focus-inner {
  border: 0 !important;
}

/* Fix tambahan untuk Chrome */
*:focus {
  outline-color: transparent !important;
}

/* Dark mode adjustment */
.dark .custom-select-trigger:focus,
.dark .custom-select-trigger:active,
.dark div:focus,
.dark div:active {
  border-color: #38bdf8 !important;
}
</style> 