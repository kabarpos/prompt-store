# Panduan Membuat Komponen Baru

Gunakan panduan ini sebagai template untuk membuat komponen baru di aplikasi Dilogif.

## Struktur Komponen

```vue
<script setup>
import { ref, computed } from 'vue';

// Props komponen
const props = defineProps({
  variant: {
    type: String,
    default: 'default',
    validator: (value) => ['default', 'primary', 'destructive', 'outline', 'ghost'].includes(value)
  },
  size: {
    type: String,
    default: 'default',
    validator: (value) => ['default', 'sm', 'lg', 'icon'].includes(value)
  },
  disabled: {
    type: Boolean,
    default: false
  },
});

// Emit events
const emit = defineEmits(['click']);

// Computed classes untuk styling
const classes = computed(() => {
  return {
    'base-component': true,
    [`variant-${props.variant}`]: true,
    [`size-${props.size}`]: true,
    'disabled': props.disabled,
  }
});
</script>

<template>
  <div :class="classes" @click="!disabled && emit('click')">
    <slot></slot>
  </div>
</template>
```

## Checklist Komponen Baru

Saat membuat komponen baru, pastikan anda:

1. [ ] Menggunakan CSS Variables dari tema utama
2. [ ] Mendukung mode gelap (dark mode)
3. [ ] Memiliki dokumentasi props
4. [ ] Accessible (a11y) - memiliki atribut aria yang sesuai
5. [ ] Mengimplementasikan variasi ukuran
6. [ ] Memiliki state disabled bila diperlukan
7. [ ] Responsif di berbagai ukuran layar

## Contoh Penggunaan Props

* `variant`: Menentukan tampilan visual (primary, secondary, dll)
* `size`: Ukuran komponen (sm, default, lg, dll)
* `disabled`: Kondisi non-aktif
* `loading`: Menampilkan status loading

## Slot

* Default slot: Konten utama komponen
* Named slots: Gunakan untuk area tertentu dalam komponen
  ```html
  <slot name="header"></slot>
  <slot></slot>
  <slot name="footer"></slot>
  ```

## Contoh Styling Komponen

```vue
<script setup>
// ... kode lainnya

const classes = computed(() => ({
  'inline-flex items-center justify-center': true,
  'font-medium transition-colors focus-visible:outline-none': true,
  'disabled:opacity-50 disabled:pointer-events-none': props.disabled,
  'h-10 py-2 px-4 text-sm': props.size === 'default',
  'h-9 px-3 text-xs': props.size === 'sm',
  'h-11 px-8 text-base': props.size === 'lg',
  'h-10 w-10': props.size === 'icon',
  
  // Variasi utama
  'bg-primary text-primary-foreground hover:bg-primary/90': props.variant === 'primary',
  'bg-secondary text-secondary-foreground hover:bg-secondary/80': props.variant === 'secondary',
  'border border-input bg-background hover:bg-accent hover:text-accent-foreground': props.variant === 'outline',
  'bg-destructive text-destructive-foreground hover:bg-destructive/90': props.variant === 'destructive',
  'hover:bg-accent hover:text-accent-foreground': props.variant === 'ghost',
  'text-primary underline-offset-4 hover:underline': props.variant === 'link',
}));
</script>
```

## Mendukung Dark Mode

Untuk mendukung dark mode, gunakan class utilities dari Tailwind dengan prefix `dark:`:

```vue
<template>
  <div class="bg-white text-gray-900 dark:bg-gray-800 dark:text-white">
    <slot></slot>
  </div>
</template>
```

## Rekomendasi Tata Nama

* Gunakan PascalCase untuk nama komponen
* Gunakan camelCase untuk props dan methods
* Gunakan kebab-case untuk class CSS

## Testing Komponen

Gunakan Vitest untuk menguji komponen:

```js
import { mount } from '@vue/test-utils';
import { describe, it, expect } from 'vitest';
import YourComponent from './YourComponent.vue';

describe('YourComponent', () => {
  it('renders properly', () => {
    const wrapper = mount(YourComponent, {
      props: {
        variant: 'primary',
      },
    });
    expect(wrapper.text()).toContain('Your Component Content');
  });
});
``` 