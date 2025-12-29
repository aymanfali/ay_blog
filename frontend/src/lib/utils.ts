import type { ClassValue } from 'clsx'
import { clsx } from 'clsx'
import { twMerge } from 'tailwind-merge'

export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs))
}

// utils.ts
export function valueUpdater<T>(updaterOrValue: T | ((prev: T) => T), targetRef: { value: T }) {
  if (typeof updaterOrValue === 'function') {
    // @ts-expect-error updaterOrValue might not be callable if T is not a function type,
    // but the function signature allows T or (prev: T) => T, so this is intentional.
    targetRef.value = updaterOrValue(targetRef.value)
  } else {
    targetRef.value = updaterOrValue
  }
}
