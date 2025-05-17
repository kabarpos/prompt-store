import { ref } from 'vue'

type EventCallback = (data: any) => void
type EventSubscribers = Map<string, Set<EventCallback>>

const cartCount = ref(0)
const subscribers: EventSubscribers = new Map()

export function initializeCartCount(count: number): void {
  cartCount.value = count
  notifySubscribers('cartCount', count)
}

export function updateCartCount(count: number): void {
  cartCount.value = count
  notifySubscribers('cartCount', count)
}

export function getCartCount(): number {
  return cartCount.value
}

export function subscribe(event: string, callback: EventCallback): void {
  if (!subscribers.has(event)) {
    subscribers.set(event, new Set())
  }
  subscribers.get(event)?.add(callback)
}

export function unsubscribe(event: string, callback: EventCallback): void {
  if (subscribers.has(event)) {
    subscribers.get(event)?.delete(callback)
  }
}

function notifySubscribers(event: string, data: any): void {
  if (subscribers.has(event)) {
    subscribers.get(event)?.forEach(callback => callback(data))
  }
} 