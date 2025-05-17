/**
 * File konfigurasi tema untuk tombol
 * Menggunakan variabel ini akan membuat konsistensi warna dan memudahkan perubahan tema di satu tempat
 */

// Define tipe untuk ButtonTheme
export type ButtonTheme = keyof typeof buttonTheme;

export const buttonTheme = {
  // Warna utama (primary)
  primary: {
    bg: 'bg-blue-600 dark:bg-blue-600',
    hover: 'hover:bg-blue-700 dark:hover:bg-blue-700',
    text: 'text-white dark:text-white',
    border: 'border-blue-600 dark:border-blue-700',
    focusRing: 'focus:ring-blue-500/50 dark:focus:ring-blue-500/50',
    disabled: 'disabled:bg-blue-400 dark:disabled:bg-blue-500/60 disabled:cursor-not-allowed',
    loading: 'bg-blue-500 dark:bg-blue-600/80',
    spinner: 'border-white border-t-transparent'
  },
  
  // Warna sekunder
  secondary: {
    bg: 'bg-secondary-600 dark:bg-secondary-700',
    hover: 'hover:bg-secondary-700 dark:hover:bg-secondary-600',
    text: 'text-white dark:text-white',
    border: 'border-secondary-600 dark:border-secondary-700',
    focusRing: 'focus:ring-secondary-500/50 dark:focus:ring-secondary-500/50',
    disabled: 'disabled:bg-secondary-400 dark:disabled:bg-secondary-600/60 disabled:text-white disabled:cursor-not-allowed',
    loading: 'bg-secondary-500 dark:bg-secondary-700/80',
    spinner: 'border-white border-t-transparent'
  },
  
  // Outline style - transparan dengan border
  outline: {
    bg: 'bg-transparent dark:bg-transparent',
    hover: 'hover:bg-primary-50 dark:hover:bg-primary-950/50 hover:text-primary-700 dark:hover:text-primary-300',
    text: 'text-primary-600 dark:text-primary-400',
    border: 'border border-primary-300 dark:border-primary-700',
    focusRing: 'focus:ring-primary-300/50 dark:focus:ring-primary-700/50',
    disabled: 'disabled:bg-transparent disabled:text-primary-300 dark:disabled:text-primary-700/60 disabled:cursor-not-allowed disabled:border-primary-200 dark:disabled:border-primary-800/50',
    loading: 'bg-primary-50 dark:bg-primary-950/30',
    spinner: 'border-primary-500 dark:border-primary-400 border-t-transparent'
  },
  
  // Danger/destructive untuk actions berbahaya
  danger: {
    bg: 'bg-danger-600 dark:bg-danger-600',
    hover: 'hover:bg-danger-700 dark:hover:bg-danger-700',
    text: 'text-white dark:text-white',
    border: 'border-danger-600 dark:border-danger-700',
    focusRing: 'focus:ring-danger-500/50 dark:focus:ring-danger-500/50',
    disabled: 'disabled:bg-danger-400 dark:disabled:bg-danger-500/60 disabled:cursor-not-allowed',
    loading: 'bg-danger-500 dark:bg-danger-600/80',
    spinner: 'border-white border-t-transparent'
  },
  
  // Success untuk aksi berhasil/positif
  success: {
    bg: 'bg-success-600 dark:bg-success-600',
    hover: 'hover:bg-success-700 dark:hover:bg-success-700', 
    text: 'text-white dark:text-white',
    border: 'border-success-600 dark:border-success-700',
    focusRing: 'focus:ring-success-500/50 dark:focus:ring-success-500/50',
    disabled: 'disabled:bg-success-400 dark:disabled:bg-success-500/60 disabled:cursor-not-allowed',
    loading: 'bg-success-500 dark:bg-success-600/80',
    spinner: 'border-white border-t-transparent'
  },
  
  // Warning untuk peringatan
  warning: {
    bg: 'bg-warning-500 dark:bg-warning-600',
    hover: 'hover:bg-warning-600 dark:hover:bg-warning-700',
    text: 'text-white dark:text-white',
    border: 'border-warning-500 dark:border-warning-600',
    focusRing: 'focus:ring-warning-400/50 dark:focus:ring-warning-500/50',
    disabled: 'disabled:bg-warning-300 dark:disabled:bg-warning-500/60 disabled:cursor-not-allowed',
    loading: 'bg-warning-400 dark:bg-warning-600/80',
    spinner: 'border-white border-t-transparent'
  },
  
  // Ghost (transparan tanpa border)
  ghost: {
    bg: 'bg-transparent',
    hover: 'hover:bg-gray-100 dark:hover:bg-gray-800',
    text: 'text-gray-700 dark:text-gray-300',
    border: 'border-transparent',
    focusRing: 'focus:ring-gray-300/50 dark:focus:ring-gray-700/50',
    disabled: 'disabled:bg-transparent disabled:text-gray-400 dark:disabled:text-gray-600 disabled:cursor-not-allowed',
    loading: 'bg-gray-100 dark:bg-gray-800',
    spinner: 'border-gray-700 dark:border-gray-300 border-t-transparent'
  },
  
  // Link style
  link: {
    bg: 'bg-transparent',
    hover: 'hover:underline',
    text: 'text-primary-600 dark:text-primary-400',
    border: 'border-transparent',
    focusRing: 'focus:ring-primary-200/50 dark:focus:ring-primary-800/50',
    disabled: 'disabled:text-primary-300 dark:disabled:text-primary-700/60 disabled:cursor-not-allowed disabled:no-underline',
    loading: 'bg-transparent',
    spinner: 'border-primary-600 dark:border-primary-400 border-t-transparent'
  },
  
  // Action button style - primary action
  action: {
    bg: 'bg-primary-600 dark:bg-primary-600',
    hover: 'hover:bg-primary-700 dark:hover:bg-primary-700',
    text: 'text-white dark:text-white',
    border: 'border-primary-600 dark:border-primary-700',
    focusRing: 'focus:ring-primary-500/50 dark:focus:ring-primary-500/50',
    disabled: 'disabled:bg-primary-400 dark:disabled:bg-primary-500/60 disabled:cursor-not-allowed',
    loading: 'bg-primary-500 dark:bg-primary-600/80',
    spinner: 'border-white border-t-transparent'
  },
  
  // Action button style - secondary
  'action-secondary': {
    bg: 'bg-secondary-600 dark:bg-secondary-700',
    hover: 'hover:bg-secondary-700 dark:hover:bg-secondary-800',
    text: 'text-white dark:text-white',
    border: 'border-secondary-600 dark:border-secondary-700',
    focusRing: 'focus:ring-secondary-500/50 dark:focus:ring-secondary-600/50',
    disabled: 'disabled:bg-secondary-400 dark:disabled:bg-secondary-600/60 disabled:cursor-not-allowed',
    loading: 'bg-secondary-500 dark:bg-secondary-700/80',
    spinner: 'border-white border-t-transparent'
  },
  
  // Action button style - danger
  'action-danger': {
    bg: 'bg-danger-600 dark:bg-danger-700',
    hover: 'hover:bg-danger-700 dark:hover:bg-danger-800',
    text: 'text-white dark:text-white',
    border: 'border-danger-600 dark:border-danger-700',
    focusRing: 'focus:ring-danger-500/50 dark:focus:ring-danger-600/50',
    disabled: 'disabled:bg-danger-400 dark:disabled:bg-danger-600/60 disabled:cursor-not-allowed',
    loading: 'bg-danger-500 dark:bg-danger-700/80',
    spinner: 'border-white border-t-transparent'
  },
  
  // Action button style - success
  'action-success': {
    bg: 'bg-success-600 dark:bg-success-700',
    hover: 'hover:bg-success-700 dark:hover:bg-success-800',
    text: 'text-white dark:text-white',
    border: 'border-success-600 dark:border-success-700',
    focusRing: 'focus:ring-success-500/50 dark:focus:ring-success-600/50',
    disabled: 'disabled:bg-success-400 dark:disabled:bg-success-600/60 disabled:cursor-not-allowed',
    loading: 'bg-success-500 dark:bg-success-700/80',
    spinner: 'border-white border-t-transparent'
  },
  
  // Action button style - warning
  'action-warning': {
    bg: 'bg-warning-500 dark:bg-warning-600',
    hover: 'hover:bg-warning-600 dark:hover:bg-warning-700',
    text: 'text-white dark:text-white',
    border: 'border-warning-500 dark:border-warning-600',
    focusRing: 'focus:ring-warning-400/50 dark:focus:ring-warning-500/50',
    disabled: 'disabled:bg-warning-300 dark:disabled:bg-warning-500/60 disabled:cursor-not-allowed',
    loading: 'bg-warning-400 dark:bg-warning-600/80',
    spinner: 'border-white border-t-transparent'
  },
  
  // Action button style - outline
  'action-outline': {
    bg: 'bg-transparent dark:bg-transparent',
    hover: 'hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-800 dark:hover:text-slate-200',
    text: 'text-slate-700 dark:text-slate-300',
    border: 'border border-slate-300 dark:border-slate-600',
    focusRing: 'focus:ring-slate-300/50 dark:focus:ring-slate-700/50',
    disabled: 'disabled:bg-transparent disabled:text-slate-400 dark:disabled:text-slate-600 disabled:cursor-not-allowed disabled:border-slate-200 dark:disabled:border-slate-700',
    loading: 'bg-slate-50 dark:bg-slate-800/30',
    spinner: 'border-slate-600 dark:border-slate-400 border-t-transparent'
  },
  
  // Admin panel action button - primary
  admin: {
    bg: 'bg-primary-600 dark:bg-primary-700',
    hover: 'hover:bg-primary-700 dark:hover:bg-primary-800', 
    text: 'text-white dark:text-white',
    border: 'border-primary-600 dark:border-primary-700',
    focusRing: 'focus:ring-primary-500/50 dark:focus:ring-primary-600/50',
    disabled: 'disabled:bg-primary-400 dark:disabled:bg-primary-600/60 disabled:cursor-not-allowed',
    loading: 'bg-primary-500 dark:bg-primary-700/80',
    spinner: 'border-white border-t-transparent'
  },
  
  // Admin panel action button - secondary
  'admin-secondary': {
    bg: 'bg-secondary-600 dark:bg-secondary-700',
    hover: 'hover:bg-secondary-700 dark:hover:bg-secondary-800', 
    text: 'text-white dark:text-white',
    border: 'border-secondary-600 dark:border-secondary-700',
    focusRing: 'focus:ring-secondary-500/50 dark:focus:ring-secondary-600/50',
    disabled: 'disabled:bg-secondary-400 dark:disabled:bg-secondary-600/60 disabled:cursor-not-allowed',
    loading: 'bg-secondary-500 dark:bg-secondary-700/80',
    spinner: 'border-white border-t-transparent'
  },
  
  // Admin panel action button - destructive
  'admin-danger': {
    bg: 'bg-danger-600 dark:bg-danger-700',
    hover: 'hover:bg-danger-700 dark:hover:bg-danger-800', 
    text: 'text-white dark:text-white',
    border: 'border-danger-600 dark:border-danger-700',
    focusRing: 'focus:ring-danger-500/50 dark:focus:ring-danger-600/50',
    disabled: 'disabled:bg-danger-400 dark:disabled:bg-danger-600/60 disabled:cursor-not-allowed',
    loading: 'bg-danger-500 dark:bg-danger-700/80',
    spinner: 'border-white border-t-transparent'
  },
  
  // Admin panel action button - outline
  'admin-outline': {
    bg: 'bg-transparent dark:bg-transparent',
    hover: 'hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-800 dark:hover:text-slate-200',
    text: 'text-slate-700 dark:text-slate-300',
    border: 'border border-slate-300 dark:border-slate-600',
    focusRing: 'focus:ring-slate-300/50 dark:focus:ring-slate-700/50',
    disabled: 'disabled:bg-transparent disabled:text-slate-400 dark:disabled:text-slate-600 disabled:cursor-not-allowed disabled:border-slate-200 dark:disabled:border-slate-700',
    loading: 'bg-slate-50 dark:bg-slate-800/30',
    spinner: 'border-slate-600 dark:border-slate-400 border-t-transparent'
  }
};

// Helper untuk menggabungkan kelas berdasarkan variant
export function getButtonClasses(variant: keyof typeof buttonTheme = 'primary') {
  const theme = buttonTheme[variant];
  
  return `${theme.bg} ${theme.hover} ${theme.text} ${theme.focusRing} ${theme.disabled}`;
}

// Helper untuk mendapatkan warna spinner
export function getSpinnerClasses(variant: keyof typeof buttonTheme = 'primary') {
  return buttonTheme[variant].spinner;
}

// Export default primary variant
export default buttonTheme.primary;