import { createContext, useContext, useState, ReactNode, useEffect } from 'react'
import { toast } from 'sonner'

interface CartItem {
    id: string | number
    name: string
    price: number
    quantity: number
    image?: string
}

interface CartContextType {
    items: CartItem[]
    addItem: (item: Omit<CartItem, 'quantity'>, quantity?: number) => void
    removeItem: (id: string | number) => void
    updateQuantity: (id: string | number, quantity: number) => void
    clearCart: () => void
    checkout: () => void
    totalItems: number
    totalPrice: number
}

const CART_STORAGE_KEY = 'scandiweb-cart'

const CartContext = createContext<CartContextType | undefined>(undefined)

export const useCart = () => {
    const context = useContext(CartContext)
    if (!context) {
        throw new Error('useCart must be used within a CartProvider')
    }
    return context
}

// Load cart from localStorage
const loadCartFromStorage = (): CartItem[] => {
    if (typeof window === 'undefined') return []
    try {
        const stored = localStorage.getItem(CART_STORAGE_KEY)
        return stored ? JSON.parse(stored) : []
    } catch (error) {
        console.error('Failed to load cart from storage:', error)
        return []
    }
}

// Save cart to localStorage
const saveCartToStorage = (items: CartItem[]) => {
    if (typeof window === 'undefined') return
    try {
        localStorage.setItem(CART_STORAGE_KEY, JSON.stringify(items))
    } catch (error) {
        console.error('Failed to save cart to storage:', error)
    }
}

export const CartProvider = ({ children }: { children: ReactNode }) => {
    const [items, setItems] = useState<CartItem[]>(loadCartFromStorage)

    // Persist cart to localStorage whenever it changes
    useEffect(() => {
        saveCartToStorage(items)
    }, [items])

    const addItem = (newItem: Omit<CartItem, 'quantity'>, quantity: number = 1) => {
        setItems(prev => {
            const existingItem = prev.find(item => item.id === newItem.id)
            if (existingItem) {
                return prev.map(item =>
                    item.id === newItem.id
                        ? { ...item, quantity: item.quantity + quantity }
                        : item
                )
            }
            return [...prev, { ...newItem, quantity }]
        })
        
        // Show add to cart toast
        toast.success(`${newItem.name} added to cart! 🛒`, {
            description: `${quantity} item${quantity > 1 ? 's' : ''} • $${(newItem.price * quantity).toFixed(2)}`,
            duration: 3000,
        })
    }

    const removeItem = (id: string | number) => {
        setItems(prev => prev.filter(item => item.id !== id))
    }

    const updateQuantity = (id: string | number, quantity: number) => {
        if (quantity <= 0) {
            removeItem(id)
            return
        }
        setItems(prev => prev.map(item =>
            item.id === id ? { ...item, quantity } : item
        ));
    }

    const clearCart = () => {
        setItems([])
    }

    const checkout = () => {
        if (items.length === 0) {
            toast.error("Cart is empty", {
                description: "Add some items to your cart before checking out.",
                duration: 3000,
            })
            return
        }

        // Simulate checkout process
        const orderTotal = totalPrice
        const orderItems = items.length
        
        // Clear the cart
        setItems([])
        
        // Show success message
        toast.success("Order placed successfully! 🎉", {
            description: `${orderItems} item${orderItems > 1 ? 's' : ''} ordered • Total: $${orderTotal.toFixed(2)}`,
            duration: 5000,
            action: {
                label: "View Order",
                onClick: () => {
                    toast.info("Order tracking coming soon!", {
                        description: "You'll receive an email confirmation shortly.",
                        duration: 3000,
                    })
                },
            },
        })
    }

    const totalItems = items.reduce((sum, item) => sum + item.quantity, 0)
    const totalPrice = items.reduce((sum, item) => sum + (item.price * item.quantity), 0)

    const value: CartContextType = {
        items,
        addItem,
        removeItem,
        updateQuantity,
        clearCart,
        checkout,
        totalItems,
        totalPrice
    }

    return (
        <CartContext.Provider value={value}>
            {children}
        </CartContext.Provider>
    )
}