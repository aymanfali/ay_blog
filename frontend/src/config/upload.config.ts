export const UPLOAD_CONFIG = {
  MAX_FILE_SIZE_MB: Number(import.meta.env.VITE_MAX_UPLOAD_SIZE_MB ?? 5),
} as const
