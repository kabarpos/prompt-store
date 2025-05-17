const plugin = require('tailwindcss/plugin');

/**
 * Plugin untuk tambahan utility dan variasi yang dibutuhkan oleh UI components
 */
module.exports = plugin(function({ addUtilities, addVariant, theme, e }) {
  // Tambahkan utility untuk transform-origin (menggantikan origin-(--XXX))
  const transformOrigins = {
    '.origin-top-start': {
      'transform-origin': 'top left',
    },
    '.origin-top-end': {
      'transform-origin': 'top right',
    },
    '.origin-bottom-start': {
      'transform-origin': 'bottom left',
    },
    '.origin-bottom-end': {
      'transform-origin': 'bottom right',
    },
  };
  
  // Tambahkan utility untuk max-height yang sering digunakan di dropdown
  const maximumHeights = {
    '.max-h-dropdown': {
      'max-height': '16rem',
    },
    '.max-h-dropdown-sm': {
      'max-height': '12rem',
    },
    '.max-h-dropdown-lg': {
      'max-height': '24rem',
    },
    '.max-h-dropdown-xl': {
      'max-height': '32rem',
    },
    '.max-h-modal': {
      'max-height': 'calc(100vh - 2rem)',
    },
  };
  
  // Tambahkan utility untuk ring styling yang banyak digunakan
  const ringUtilities = {
    '.ring': {
      '--tw-ring-offset-shadow': 'var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color)',
      '--tw-ring-shadow': 'var(--tw-ring-inset) 0 0 0 calc(var(--tw-ring-width) + var(--tw-ring-offset-width)) var(--tw-ring-color)',
      'box-shadow': 'var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow, 0 0 #0000)',
    },
    '.ring-offset-white': {
      '--tw-ring-offset-color': 'white',
    },
    '.ring-offset-transparent': {
      '--tw-ring-offset-color': 'transparent',
    },
    '.ring-offset-black': {
      '--tw-ring-offset-color': 'black',
    },
    '.ring-offset-current': {
      '--tw-ring-offset-color': 'currentColor',
    },
  };
  
  // Hardcoded secondary colors untuk menghindari error dengan theme()
  const secondaryColors = {
    '50': '#f8fafc',
    '100': '#f1f5f9',
    '200': '#e2e8f0',
    '300': '#cbd5e1',
    '400': '#94a3b8',
    '500': '#64748b',
    '600': '#475569',
    '700': '#334155',
    '800': '#1e293b',
    '900': '#0f172a',
    '950': '#020617',
  };
  
  for (const shade in secondaryColors) {
    ringUtilities[`.ring-offset-secondary-${shade}`] = {
      '--tw-ring-offset-color': secondaryColors[shade],
    };
  }
  
  // Tambahkan variant for state yang berasal dari CSS variables
  addVariant('open', '&[data-state="open"]');
  addVariant('closed', '&[data-state="closed"]');
  addVariant('expanded', '&[data-state="expanded"]');
  addVariant('collapsed', '&[data-state="collapsed"]');
  
  // Tambahkan variant untuk data attributes
  addVariant('side-top', '&[data-side="top"]');
  addVariant('side-right', '&[data-side="right"]');
  addVariant('side-bottom', '&[data-side="bottom"]');
  addVariant('side-left', '&[data-side="left"]');
  
  // Tambahkan semua utility yang diperlukan
  addUtilities(transformOrigins);
  addUtilities(maximumHeights);
  addUtilities(ringUtilities);
}); 