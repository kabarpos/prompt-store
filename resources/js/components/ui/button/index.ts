import { cva, type VariantProps } from 'class-variance-authority'

export { default as Button } from './Button.vue'
export { default as ButtonLoader } from './ButtonLoader.vue'
export { buttonTheme, getButtonClasses, getSpinnerClasses } from './theme'

export const buttonVariants = cva(
  'inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-white transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 dark:ring-offset-slate-950',
  {
    variants: {
      variant: {
        default:
          'bg-primary-600 text-white hover:bg-primary-700 dark:bg-primary-600 dark:text-white dark:hover:bg-primary-700',
        primary:
          'bg-primary-500 text-white hover:bg-primary-600 dark:bg-primary-500 dark:text-white dark:hover:bg-primary-600',
        destructive:
          'bg-danger-600 text-white hover:bg-danger-700 dark:bg-danger-600 dark:text-white dark:hover:bg-danger-700',
        outline:
          'border border-slate-300 bg-transparent text-slate-700 hover:bg-slate-100 hover:text-slate-900 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-slate-50',
        secondary:
          'bg-secondary-600 text-white hover:bg-secondary-700 dark:bg-secondary-700 dark:text-white dark:hover:bg-secondary-800',
        ghost:
          'text-primary-600 hover:bg-primary-100 hover:text-primary-700 dark:text-primary-400 dark:hover:bg-primary-950/50 dark:hover:text-primary-300',
        link: 
          'text-primary-600 underline-offset-4 hover:underline dark:text-primary-400',
        success:
          'bg-success-600 text-white hover:bg-success-700 dark:bg-success-600 dark:text-white dark:hover:bg-success-700',
        warning:
          'bg-warning-500 text-white hover:bg-warning-600 dark:bg-warning-600 dark:text-white dark:hover:bg-warning-700',
        action:
          'bg-primary-600 text-white hover:bg-primary-700 dark:bg-primary-600 dark:text-white dark:hover:bg-primary-700',
        'action-secondary':
          'bg-secondary-600 text-white hover:bg-secondary-700 dark:bg-secondary-700 dark:text-white dark:hover:bg-secondary-800',
        'action-danger':
          'bg-danger-600 text-white hover:bg-danger-700 dark:bg-danger-700 dark:text-white dark:hover:bg-danger-800',
        'action-success':
          'bg-success-600 text-white hover:bg-success-700 dark:bg-success-700 dark:text-white dark:hover:bg-success-800',
        'action-warning':
          'bg-warning-500 text-white hover:bg-warning-600 dark:bg-warning-600 dark:text-white dark:hover:bg-warning-700',
        'action-outline':
          'border border-slate-300 bg-transparent text-slate-700 hover:bg-slate-100 hover:text-slate-900 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-slate-50',
      },
      size: {
        default: 'h-9 px-4 py-2',
        sm: 'h-8 rounded-md px-3 text-xs',
        lg: 'h-10 rounded-md px-6',
        icon: 'h-9 w-9 p-2',
      },
    },
    defaultVariants: {
      variant: 'default',
      size: 'default',
    },
  }
)

export type ButtonVariants = VariantProps<typeof buttonVariants>
