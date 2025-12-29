// status.config.ts
export const STATUS_CONFIG = {
  active: {
    label: 'Active',
    class:
      'bg-green-50 text-green-700 border border-green-200 ' +
      'dark:bg-green-900/30 dark:text-green-300 dark:border-green-800',
  },
  inactive: {
    label: 'Inactive',
    class:
      'bg-red-50 text-red-700 border border-red-200 ' +
      'dark:bg-red-900/30 dark:text-red-300 dark:border-red-800',
  },
  pending: {
    label: 'Pending',
    class:
      'bg-amber-50 text-amber-700 border border-amber-200 ' +
      'dark:bg-amber-900/30 dark:text-amber-300 dark:border-amber-800',
  },
  draft: {
    label: 'Draft',
    class:
      'bg-slate-50 text-slate-700 border border-slate-200 ' +
      'dark:bg-slate-800/50 dark:text-slate-300 dark:border-slate-700',
  },
  published: {
    label: 'Published',
    class:
      'bg-blue-50 text-blue-700 border border-blue-200 ' +
      'dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-800',
  },
} as const
