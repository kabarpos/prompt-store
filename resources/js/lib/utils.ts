import { type ClassValue, clsx } from 'clsx';
import { twMerge } from 'tailwind-merge';

/**
 * Combine multiple class names with clsx and tailwind-merge
 */
export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

/**
 * Format number to currency
 */
export function formatCurrency(amount: number): string {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(amount);
}

/**
 * Format price
 */
export function formatPrice(amount: number): string {
    return new Intl.NumberFormat('id-ID', {
        style: 'decimal',
        minimumFractionDigits: 0
    }).format(amount);
}

/**
 * Format date to Indonesian format
 */
export function formatDate(date: string | Date): string {
    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    }).format(new Date(date));
}

/**
 * Extract first letter from name for avatar
 */
export function getInitials(name: string): string {
    return name
        .split(' ')
        .map(n => n[0])
        .join('')
        .toUpperCase()
        .substring(0, 2);
}

// Helper untuk debug komponen di console
export function debugComponent(name: string, props: Record<string, any>, additionalInfo?: Record<string, any>) {
    console.log(`Debug [${name}]`, {
        ...props,
        ...(additionalInfo || {})
    });
}

// Helper untuk memastikan variant Button bekerja dengan baik
export function getVariantClasses(variant: string, allClasses: string): string {
    console.log(`Variant: ${variant}`, {
        allClasses,
        classesArray: allClasses.split(' ')
    });
    return allClasses;
}
