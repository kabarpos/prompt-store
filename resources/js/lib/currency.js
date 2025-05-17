/**
 * Format angka ke format mata uang Rupiah
 * 
 * @param {number} amount - Jumlah dalam angka
 * @param {string} currencyCode - Kode mata uang (default: IDR)
 * @param {string} locale - Locale untuk format (default: id-ID)
 * @returns {string} String terformat
 */
export function formatCurrency(amount, currencyCode = 'IDR', locale = 'id-ID') {
  return new Intl.NumberFormat(locale, {
    style: 'currency',
    currency: currencyCode,
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(amount);
}

/**
 * Format ke mata uang dengan opsi kustom
 * 
 * @param {number} amount - Jumlah dalam angka
 * @param {Object} options - Opsi formatting
 * @returns {string} String terformat
 */
export function formatPrice(amount, options = {}) {
  const defaults = {
    currencyCode: 'IDR',
    locale: 'id-ID',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
    compact: false
  };
  
  const config = { ...defaults, ...options };
  
  if (config.compact) {
    // Format singkat (misalnya 1.5jt)
    if (amount >= 1000000000) {
      return `${(amount / 1000000000).toFixed(1)}M ${config.currencyCode}`;
    } else if (amount >= 1000000) {
      return `${(amount / 1000000).toFixed(1)}jt ${config.currencyCode}`;
    } else if (amount >= 1000) {
      return `${(amount / 1000).toFixed(1)}rb ${config.currencyCode}`;
    }
  }
  
  return new Intl.NumberFormat(config.locale, {
    style: 'currency',
    currency: config.currencyCode,
    minimumFractionDigits: config.minimumFractionDigits,
    maximumFractionDigits: config.maximumFractionDigits
  }).format(amount);
}

// Mata uang yang didukung
const currencies = {
  IDR: {
    code: 'IDR',
    symbol: 'Rp',
    name: 'Rupiah Indonesia',
    locale: 'id-ID'
  },
  USD: {
    code: 'USD',
    symbol: '$',
    name: 'US Dollar',
    locale: 'en-US'
  }
};

// Export default untuk backward compatibility
export default currencies; 