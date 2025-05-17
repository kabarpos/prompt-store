import { Ziggy } from './ziggy';
import { route } from 'ziggy-js';

// Deklarasi tipe untuk window.Ziggy
declare global {
  interface Window {
    Ziggy: typeof Ziggy;
  }
}

// ZiggyVue plugin
export const ZiggyVue = {
  install: (app: any) => {
    app.config.globalProperties.$route = route;
    app.config.globalProperties.route = route;
    app.provide('route', route);
    
    // Buat ziggy tersedia secara global
    window.Ziggy = Ziggy;
  }
}; 