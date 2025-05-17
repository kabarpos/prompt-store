import { cva, type VariantProps } from 'class-variance-authority'

export { default as Alert } from './Alert.vue'
export { default as AlertDescription } from './AlertDescription.vue'
export { default as AlertTitle } from './AlertTitle.vue'

export const alertVariants = cva(
  'relative w-full rounded-lg border px-4 py-3 text-sm grid has-[>svg]:grid-cols-[16px_1fr] grid-cols-[0_1fr] has-[>svg]:gap-x-3 gap-y-0.5 items-start [&>svg]:size-4 [&>svg]:translate-y-0.5 [&>svg]:text-current shadow-sm',
  {
    variants: {
      variant: {
        default: 'bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-50 border-slate-200 dark:border-slate-700',
        primary: 'bg-primary-50 dark:bg-primary-950 text-primary-800 dark:text-primary-300 border-primary-200 dark:border-primary-800 [&>svg]:text-primary-500',
        destructive: 'bg-danger-50 dark:bg-danger-950 text-danger-800 dark:text-danger-300 border-danger-200 dark:border-danger-800 [&>svg]:text-danger-500',
        success: 'bg-success-50 dark:bg-success-950 text-success-800 dark:text-success-300 border-success-200 dark:border-success-800 [&>svg]:text-success-500',
        warning: 'bg-warning-50 dark:bg-warning-950 text-warning-800 dark:text-warning-300 border-warning-200 dark:border-warning-800 [&>svg]:text-warning-500',
      },
    },
    defaultVariants: {
      variant: 'default',
    },
  },
)

export type AlertVariants = VariantProps<typeof alertVariants>
