const prefixApi = `api/${import.meta.env.VITE_API_VERSION}`

export const resource = (name: string) => ({
  index: `/${prefixApi}/${name}`,
  store: `/${prefixApi}/${name}`,
  show: (id: number | string) => `/${prefixApi}/${name}/${id}`,
  update: (id: number | string) => `/${prefixApi}/${name}/${id}`,
  destroy: (id: number | string) => `/${prefixApi}/${name}/${id}`,
})
