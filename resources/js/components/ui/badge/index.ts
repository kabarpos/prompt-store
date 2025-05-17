import { cva, type VariantProps } from 'class-variance-authority'
import Badge from './Badge.vue'

export { Badge }

export const badgeVariants = cva(
  'inline-flex items-center justify-center rounded-md border px-2 py-0.5 text-xs font-medium w-fit whitespace-nowrap shrink-0 [&>svg]:size-3 gap-1 [&>svg]:pointer-events-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive transition-[color,box-shadow] overflow-hidden',
  {
    variants: {
      variant: {
        default:
          'border-transparent bg-primary-600 text-white [a&]:hover:bg-primary-700',
        secondary:
          'border-transparent bg-secondary-600 text-white [a&]:hover:bg-secondary-700',
        destructive:
         'border-transparent bg-danger-600 text-white [a&]:hover:bg-danger-700 focus-visible:ring-danger-500/50',
        outline:
          'border-primary-300 bg-transparent text-primary-700 [a&]:hover:bg-primary-50 dark:border-primary-700 dark:text-primary-400',
        success:
          'border-transparent bg-success-600 text-white [a&]:hover:bg-success-700 focus-visible:ring-success-500/50',
        warning:
          'border-transparent bg-warning-500 text-white [a&]:hover:bg-warning-600 focus-visible:ring-warning-400/50',
        ghost:
          'border-transparent bg-primary-100 text-primary-700 [a&]:hover:bg-primary-200',
      },
    },
    defaultVariants: {
      variant: 'default',
    },
  },
)
export type BadgeVariants = VariantProps<typeof badgeVariants>
