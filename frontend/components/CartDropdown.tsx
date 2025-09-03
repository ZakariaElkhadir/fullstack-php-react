import React, { useState } from 'react';
import { ShoppingCart, X, Plus, Minus, Trash2 } from 'lucide-react';
import { useCart } from '@/contexts/CartContext';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import Image from 'next/image';
import Link from 'next/link';
import { toast } from "sonner";

const CartDropdown = () => {
  const [isOpen, setIsOpen] = useState(false);
  const { items, totalItems, totalPrice, updateQuantity, removeItem, clearCart } = useCart();

  const handleQuantityChange = (id: string | number, newQuantity: number) => {
    if (newQuantity <= 0) {
      removeItem(id);
      toast.error('Item removed from cart', {
        description: 'The item has been removed from your shopping cart',
        duration: 2000,
      });
    } else {
      updateQuantity(id, newQuantity);
      toast.success('Quantity updated', {
        description: `Updated to ${newQuantity} item${newQuantity > 1 ? 's' : ''}`,
        duration: 2000,
      });
    }
  };

  return (
    <div className="relative">
      {/* Cart Trigger Button */}
      <button
        onClick={() => setIsOpen(!isOpen)}
        data-cart-trigger
        className="flex items-center space-x-2 p-2 rounded-lg hover:bg-green-light/20 transition-colors relative"
      >
        <ShoppingCart className="w-5 h-5 text-green-sage hover:text-green-success transition-colors" />
        {totalItems > 0 && (
          <span className="absolute -top-1 -right-1 bg-green-success text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-semibold">
            {totalItems}
          </span>
        )}
        <span className="text-sm font-medium text-green-sage hidden sm:block">
          Cart
        </span>
      </button>

      {/* Dropdown Overlay */}
      {isOpen && (
        <div 
          className="fixed inset-0 bg-black/20 z-40" 
          onClick={() => setIsOpen(false)}
        />
      )}

      {/* Cart Dropdown */}
      {isOpen && (
        <div className="absolute right-0 top-full mt-2 w-96 max-w-[90vw] bg-white rounded-xl shadow-2xl border border-green-sage/20 z-50 max-h-[80vh] flex flex-col">
          {/* Header */}
          <div className="flex items-center justify-between p-4 border-b border-green-sage/10">
            <h3 className="text-lg font-semibold text-green-dark flex items-center">
              <ShoppingCart className="w-5 h-5 mr-2" />
              Shopping Cart ({totalItems})
            </h3>
            <button
              onClick={() => setIsOpen(false)}
              className="text-green-sage hover:text-green-dark transition-colors"
            >
              <X className="w-5 h-5" />
            </button>
          </div>

          {/* Cart Items */}
          <div className="flex-1 overflow-y-auto">
            {items.length === 0 ? (
              <div className="p-6 text-center">
                <ShoppingCart className="w-12 h-12 text-green-sage/40 mx-auto mb-3" />
                <p className="text-green-sage text-sm">Your cart is empty</p>
                <Button
                  onClick={() => setIsOpen(false)}
                  className="mt-3 bg-green-light text-white hover:bg-green-dark"
                  asChild
                >
                  <Link href="/#products">
                    Continue Shopping
                  </Link>
                </Button>
              </div>
            ) : (
              <div className="p-4 space-y-3">
                {items.map((item) => (
                  <Card key={item.id} className="border border-green-sage/10">
                    <CardContent className="p-3">
                      <div className="flex items-center space-x-3">
                        {/* Product Image */}
                        <div className="w-16 h-16 bg-green-50 rounded-lg overflow-hidden flex-shrink-0">
                          {item.image ? (
                            <Image
                              src={item.image}
                              alt={item.name}
                              width={64}
                              height={64}
                              className="w-full h-full object-cover"
                            />
                          ) : (
                            <div className="w-full h-full bg-green-sage/20 flex items-center justify-center">
                              <ShoppingCart className="w-6 h-6 text-green-sage/40" />
                            </div>
                          )}
                        </div>

                        {/* Product Details */}
                        <div className="flex-1 min-w-0">
                          <h4 className="text-sm font-medium text-green-dark truncate">
                            {item.name}
                          </h4>
                          <p className="text-sm font-semibold text-green-success">
                            ${item.price.toFixed(2)}
                          </p>
                          
                          {/* Quantity Controls */}
                          <div className="flex items-center space-x-2 mt-2">
                            <button
                              onClick={() => handleQuantityChange(item.id, item.quantity - 1)}
                              className="w-6 h-6 rounded-full bg-green-sage/10 hover:bg-green-sage/20 flex items-center justify-center transition-colors"
                            >
                              <Minus className="w-3 h-3 text-green-dark" />
                            </button>
                            <span className="text-sm font-medium text-green-dark min-w-[20px] text-center">
                              {item.quantity}
                            </span>
                            <button
                              onClick={() => handleQuantityChange(item.id, item.quantity + 1)}
                              className="w-6 h-6 rounded-full bg-green-sage/10 hover:bg-green-sage/20 flex items-center justify-center transition-colors"
                            >
                              <Plus className="w-3 h-3 text-green-dark" />
                            </button>
                          </div>
                        </div>

                        {/* Remove Button */}
                        <button
                          onClick={() => {
                            const itemName = item.name;
                            removeItem(item.id);
                            toast.error(`${itemName} removed`, {
                              description: 'Item has been removed from your cart',
                              duration: 3000,
                              action: {
                                label: "Undo",
                                onClick: () => {
                                  // Re-add the item (you might want to implement an undo feature)
                                  toast.info('Undo feature coming soon!', {
                                    duration: 1500,
                                  });
                                },
                              },
                            });
                          }}
                          className="text-red-500 hover:text-red-700 transition-colors p-1"
                        >
                          <Trash2 className="w-4 h-4" />
                        </button>
                      </div>
                    </CardContent>
                  </Card>
                ))}
              </div>
            )}
          </div>

          {/* Footer */}
          {items.length > 0 && (
            <div className="border-t border-green-sage/10 p-4 space-y-3">
              {/* Total */}
              <div className="flex justify-between items-center">
                <span className="text-lg font-semibold text-green-dark">Total:</span>
                <span className="text-lg font-bold text-green-success">
                  ${totalPrice.toFixed(2)}
                </span>
              </div>

              {/* Action Buttons */}
              <div className="flex space-x-2">
                <Button
                  onClick={() => {
                    const itemCount = items.length;
                    const total = totalPrice.toFixed(2);
                    clearCart();
                    toast.success('Cart cleared successfully', {
                      description: `Removed ${itemCount} item${itemCount > 1 ? 's' : ''} worth $${total}`,
                      duration: 3000,
                    });
                  }}
                  variant="outline"
                  className="flex-1 border-red-200 text-red-600 hover:bg-red-50"
                >
                  Clear Cart
                </Button>
                <Button
                  onClick={() => {
                    setIsOpen(false);
                    toast.info('Checkout feature coming soon!', {
                      description: `${totalItems} item${totalItems > 1 ? 's' : ''} worth $${totalPrice.toFixed(2)} ready for checkout`,
                      duration: 3000,
                    });
                  }}
                  className="flex-1 bg-green-light text-white hover:bg-green-dark"
                >
                  Checkout
                </Button>
              </div>
            </div>
          )}
        </div>
      )}
    </div>
  );
};

export default CartDropdown;
