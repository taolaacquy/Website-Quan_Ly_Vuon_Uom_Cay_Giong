const baseUrl = import.meta.env.VITE_API_BASE_URL || '/backend/api/index.php'

type ApiOptions = Omit<RequestInit, 'body'> & { body?: unknown }

export async function api<T>(route: string, options: ApiOptions = {}): Promise<T> {
  const separator = baseUrl.includes('?') ? '&' : '?'
  const [routeName, query = ''] = route.split('?', 2)
  const response = await fetch(`${baseUrl}${separator}route=${encodeURIComponent(routeName)}${query ? `&${query}` : ''}`, {
    ...options,
    headers: { 'Content-Type': 'application/json', ...(options.headers || {}) },
    body: options.body === undefined ? undefined : JSON.stringify(options.body)
  })
  const data = await response.json().catch(() => ({}))
  if (!response.ok || !data || typeof data !== 'object') throw new Error(data.message || 'Không thể kết nối máy chủ.')
  return data as T
}
