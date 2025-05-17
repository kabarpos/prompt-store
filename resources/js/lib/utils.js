/**
 * Menggabungkan beberapa class names menjadi satu string
 * Ini adalah utility function yang dibutuhkan oleh komponen Shadcn UI
 * @param  {...any} classes - list class name yang akan digabungkan
 * @returns {string} - class name yang sudah digabungkan
 */
export function cn(...classes) {
  return classes.filter(Boolean).join(" ");
}

/**
 * Helper untuk debug komponen di console
 * @param {string} name - nama komponen
 * @param {object} props - properties komponen
 * @param {object} additionalInfo - informasi tambahan yang perlu didebug
 */
export function debugComponent(name, props, additionalInfo) {
  console.log(`Debug [${name}]`, {
    ...props,
    ...(additionalInfo || {})
  });
} 