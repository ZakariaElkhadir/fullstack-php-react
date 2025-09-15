"use client";
import React, { useState } from "react";
import { gql, useQuery } from "@apollo/client";
import { useRouter, useParams } from "next/navigation";
import Image from "next/image";
import Link from "next/link";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Skeleton } from "@/components/ui/skeleton";
import { useCart } from "@/contexts/CartContext";
import {
  ArrowLeft,
  ShoppingCart,
  Heart,
  Share2,
  Minus,
  Plus,
} from "lucide-react";
import { toast } from "sonner";
import { AttributeSelector } from "@/components/AttributeSelector";

interface Price {
  amount: number;
  code: string;
  label?: string;
  symbol?: string;
}

interface AttributeItem {
  id: string;
  displayValue: string;
  value: string;
}

interface Attribute {
  id: string;
  name: string;
  type: string;
  items: AttributeItem[];
}

interface Product {
  id: string;
  name: string;
  inStock: boolean;
  prices: Price[];
  category?: string;
  gallery: string[];
  brand?: string;
  attributes: Attribute[];
  description?: string;
  specifications?: Record<string, string>;
}

const GET_ALL_PRODUCTS = gql`
  query {
    products {
      id
      name
      inStock
      prices {
        amount
        code
        label
        symbol
      }
      category
      gallery
      brand
      attributes {
        id
        name
        type
        items {
          id
          displayValue
          value
        }
      }
    }
  }
`;

const ProductDetailsPage = () => {
  const router = useRouter();
  const params = useParams();
  const {addItem} = useCart()

  const productId = params?.id as string;
  const [quantity, setQuantity] = useState(1);
  const [selectedImageIndex, setSelectedImageIndex] = useState(0);
  const [loading, setLoading] = useState(false);
  const [selectedAttributes, setSelectedAttributes] = useState<Record<string, string>>({});

  const { loading: queryLoading, error, data } = useQuery(GET_ALL_PRODUCTS);

  const product = React.useMemo(() => {
    if (!data?.products || !productId) return null;
    return data.products.find((p: Product) => p.id === productId);
  }, [data?.products, productId]);

  const handleAddToCart = async () => {
    if (!product?.inStock) return;

    setLoading(true);
    try {
      addItem({
        id: product.id,
        name: product.name,
        price: product.prices[0].amount,
        image: product.gallery[0],
      }, quantity);

      toast.success(`🎉 ${quantity} ${product.name}${quantity > 1 ? 's' : ''} ready for checkout!`, {
        description: `View your cart to complete the purchase`,
        duration: 3000,
        action: {
          label: "View Cart",
          onClick: () => {
            const cartButton = document.querySelector('[data-cart-trigger]') as HTMLElement;
            if (cartButton) cartButton.click();
          },
        },
      });
    } catch (error) {
      console.error("Failed to add to cart:", error);
      toast.error("Failed to add to cart ❌", {
        description: "Something went wrong. Please try again.",
        duration: 4000,
        action: {
          label: "Retry",
          onClick: () => handleAddToCart(),
        },
      });
    } finally {
      setLoading(false);
    }
  };

  const handleQuantityChange = (change: number) => {
    setQuantity(Math.max(1, quantity + change));
  };

  const handleAttributeChange = (attributeId: string, itemId: string) => {
    setSelectedAttributes(prev => ({
      ...prev,
      [attributeId]: itemId
    }));
  };

  if (queryLoading) {
    return (
      <div className="min-h-screen bg-[#0b0d10] relative overflow-hidden">
        {/* Dark gradient background with green glow accent */}
        <div className="absolute inset-0 bg-[radial-gradient(1000px_600px_at_20%_30%,rgba(34,197,94,0.25),transparent_60%),radial-gradient(800px_500px_at_70%_20%,rgba(107,138,115,0.18),transparent_55%)]" aria-hidden />
        <div className="absolute inset-0 bg-gradient-to-b from-white/[0.02] to-transparent" aria-hidden />
        {/* Additional green spotlight effects */}
        <div className="absolute inset-0 bg-[radial-gradient(600px_400px_at_80%_70%,rgba(34,197,94,0.15),transparent_50%),radial-gradient(500px_300px_at_10%_80%,rgba(107,138,115,0.12),transparent_45%)]" aria-hidden />
        
        <div className="relative z-10 container mx-auto p-4 sm:p-6 pt-16 sm:pt-20">
          {/* Navigation skeleton - Mobile Optimized */}
          <div className="flex items-center justify-between mb-6 sm:mb-8 bg-white/10 backdrop-blur-lg rounded-xl p-3 sm:p-4 shadow-lg border border-white/20">
            <Skeleton className="h-6 sm:h-8 w-16 sm:w-24 bg-green-sage/20 rounded-lg" />
            <Skeleton className="h-4 sm:h-6 w-32 sm:w-48 bg-green-sage/20 rounded-lg" />
          </div>
          
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 lg:gap-8">
            {/* Image gallery skeleton - Mobile First */}
            <div className="bg-white/95 backdrop-blur-lg rounded-2xl shadow-2xl border border-white/20 p-4 sm:p-6">
              <Skeleton className="h-64 sm:h-80 lg:h-96 w-full bg-gradient-to-br from-green-sage/10 to-green-success/10 rounded-xl mb-4 sm:mb-6" />
              <div className="flex gap-2 sm:gap-3">
                {Array.from({ length: 4 }).map((_, i) => (
                  <Skeleton key={i} className="h-12 w-12 sm:h-16 sm:w-16 lg:h-20 lg:w-20 bg-gradient-to-br from-green-sage/10 to-green-success/10 rounded-xl" />
                ))}
              </div>
            </div>
            
            {/* Product info skeleton - Mobile Optimized */}
            <div className="bg-white/95 backdrop-blur-lg rounded-2xl shadow-2xl border border-white/20 p-4 sm:p-6 lg:p-8">
              <div className="space-y-4 sm:space-y-6">
                <div>
                  <Skeleton className="h-3 sm:h-4 w-24 sm:w-32 bg-gradient-to-r from-green-sage/20 to-green-success/20 rounded-lg mb-2 sm:mb-3" />
                  <Skeleton className="h-8 sm:h-10 w-3/4 bg-gradient-to-r from-green-sage/20 to-green-success/20 rounded-lg mb-3 sm:mb-4" />
                  <div className="flex flex-wrap gap-2 sm:gap-3 mb-4 sm:mb-6">
                    <Skeleton className="h-6 sm:h-8 w-20 sm:w-24 bg-gradient-to-r from-green-sage/20 to-green-success/20 rounded-full" />
                    <Skeleton className="h-6 sm:h-8 w-24 sm:w-32 bg-gradient-to-r from-green-sage/20 to-green-success/20 rounded-full" />
                  </div>
                  <Skeleton className="h-20 sm:h-24 w-full bg-gradient-to-r from-green-sage/10 to-green-success/10 rounded-xl sm:rounded-2xl" />
                </div>
                
                <Skeleton className="h-24 sm:h-32 w-full bg-gradient-to-r from-green-sage/10 to-green-success/10 rounded-xl" />
                
                <div className="space-y-3 sm:space-y-4">
                  <Skeleton className="h-12 sm:h-16 w-full bg-gradient-to-r from-green-sage/10 to-green-success/10 rounded-xl sm:rounded-2xl" />
                  <div className="flex flex-col sm:flex-row gap-2 sm:gap-3">
                    <Skeleton className="h-10 sm:h-12 flex-1 bg-gradient-to-r from-green-sage/10 to-green-success/10 rounded-xl" />
                    <Skeleton className="h-10 sm:h-12 flex-1 bg-gradient-to-r from-green-sage/10 to-green-success/10 rounded-xl" />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="min-h-screen bg-[#0b0d10] relative overflow-hidden flex items-center justify-center">
        {/* Dark gradient background with green glow accent */}
        <div className="absolute inset-0 bg-[radial-gradient(1000px_600px_at_20%_30%,rgba(34,197,94,0.25),transparent_60%),radial-gradient(800px_500px_at_70%_20%,rgba(107,138,115,0.18),transparent_55%)]" aria-hidden />
        <div className="absolute inset-0 bg-gradient-to-b from-white/[0.02] to-transparent" aria-hidden />
        {/* Additional green spotlight effects */}
        <div className="absolute inset-0 bg-[radial-gradient(600px_400px_at_80%_70%,rgba(34,197,94,0.15),transparent_50%),radial-gradient(500px_300px_at_10%_80%,rgba(107,138,115,0.12),transparent_45%)]" aria-hidden />
        
        <div className="relative z-10 max-w-md mx-auto p-4 sm:p-6">
          <Card className="border-0 bg-white/95 backdrop-blur-lg shadow-2xl rounded-2xl overflow-hidden">
            <CardHeader className="text-center pb-4">
              <div className="w-12 h-12 sm:w-16 sm:h-16 mx-auto mb-3 sm:mb-4 bg-error-red/10 rounded-full flex items-center justify-center">
                <svg className="w-6 h-6 sm:w-8 sm:h-8 text-error-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
              </div>
              <CardTitle className="text-error-red text-lg sm:text-xl font-bold">
                Error Loading Product
              </CardTitle>
            </CardHeader>
            <CardContent className="space-y-4 sm:space-y-6 text-center">
              <p className="text-green-dark/70 leading-relaxed text-sm sm:text-base">{error.message}</p>
              <div className="flex flex-col gap-3">
                <Button
                  onClick={() => router.back()}
                  variant="outline"
                  className="w-full border-2 border-green-sage text-green-sage hover:bg-green-sage/10 hover:border-green-success transition-all duration-200 py-2.5 sm:py-3 rounded-xl min-h-[44px]"
                >
                  <ArrowLeft className="w-4 h-4 mr-2" />
                  Go Back
                </Button>
                <Link href="/">
                  <Button className="w-full bg-gradient-to-r from-green-success to-green-light text-white hover:from-green-success/90 hover:to-green-light/90 transition-all duration-200 py-2.5 sm:py-3 rounded-xl min-h-[44px]">
                    <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Home
                  </Button>
                </Link>
              </div>
            </CardContent>
          </Card>
        </div>
      </div>
    );
  }

  if (!product) {
    return (
      <div className="min-h-screen bg-[#0b0d10] relative overflow-hidden flex items-center justify-center">
        {/* Dark gradient background with green glow accent */}
        <div className="absolute inset-0 bg-[radial-gradient(1000px_600px_at_20%_30%,rgba(34,197,94,0.25),transparent_60%),radial-gradient(800px_500px_at_70%_20%,rgba(107,138,115,0.18),transparent_55%)]" aria-hidden />
        <div className="absolute inset-0 bg-gradient-to-b from-white/[0.02] to-transparent" aria-hidden />
        {/* Additional green spotlight effects */}
        <div className="absolute inset-0 bg-[radial-gradient(600px_400px_at_80%_70%,rgba(34,197,94,0.15),transparent_50%),radial-gradient(500px_300px_at_10%_80%,rgba(107,138,115,0.12),transparent_45%)]" aria-hidden />
        
        <div className="relative z-10 max-w-md mx-auto p-4 sm:p-6">
          <Card className="border-0 bg-white/95 backdrop-blur-lg shadow-2xl rounded-2xl overflow-hidden">
            <CardHeader className="text-center pb-4">
              <div className="w-12 h-12 sm:w-16 sm:h-16 mx-auto mb-3 sm:mb-4 bg-warning-orange/10 rounded-full flex items-center justify-center">
                <svg className="w-6 h-6 sm:w-8 sm:h-8 text-warning-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.29-1.009-5.824-2.709M15 6.291A7.962 7.962 0 0012 5c-2.34 0-4.29 1.009-5.824 2.709" />
                </svg>
              </div>
              <CardTitle className="text-warning-orange text-lg sm:text-xl font-bold">
                Product Not Found
              </CardTitle>
            </CardHeader>
            <CardContent className="space-y-4 sm:space-y-6 text-center">
              <p className="text-green-dark/70 leading-relaxed text-sm sm:text-base">
                The product you are looking for does not exist or may have been removed.
              </p>
              <div className="flex flex-col gap-3">
                <Button
                  onClick={() => router.back()}
                  variant="outline"
                  className="w-full border-2 border-green-sage text-green-sage hover:bg-green-sage/10 hover:border-green-success transition-all duration-200 py-2.5 sm:py-3 rounded-xl min-h-[44px]"
                >
                  <ArrowLeft className="w-4 h-4 mr-2" />
                  Go Back
                </Button>
                <Link href="/">
                  <Button className="w-full bg-gradient-to-r from-green-success to-green-light text-white hover:from-green-success/90 hover:to-green-light/90 transition-all duration-200 py-2.5 sm:py-3 rounded-xl min-h-[44px]">
                    <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Browse Products
                  </Button>
                </Link>
              </div>
            </CardContent>
          </Card>
        </div>
      </div>
    );
  }

  // ...existing code... (rest of the component remains the same)
  return (
    <div className="min-h-screen bg-[#0b0d10] relative overflow-hidden">
      {/* Dark gradient background with green glow accent */}
      <div className="absolute inset-0 bg-[radial-gradient(1000px_600px_at_20%_30%,rgba(34,197,94,0.25),transparent_60%),radial-gradient(800px_500px_at_70%_20%,rgba(107,138,115,0.18),transparent_55%)]" aria-hidden />
      <div className="absolute inset-0 bg-gradient-to-b from-white/[0.02] to-transparent" aria-hidden />
      {/* Additional green spotlight effects */}
      <div className="absolute inset-0 bg-[radial-gradient(600px_400px_at_80%_70%,rgba(34,197,94,0.15),transparent_50%),radial-gradient(500px_300px_at_10%_80%,rgba(107,138,115,0.12),transparent_45%)]" aria-hidden />

      <div className="relative z-10 container mx-auto px-4 sm:px-6 py-4 sm:py-8">
        {/* Modern Navigation - Mobile Optimized */}
        <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 sm:mb-8 bg-white/10 backdrop-blur-lg rounded-xl p-3 sm:p-4 shadow-lg border border-white/20">
          <div className="flex items-center gap-3">
            <Button
              onClick={() => router.back()}
              variant="ghost"
              size="sm"
              className="text-white hover:text-green-light hover:bg-white/10 transition-all duration-200 px-3 py-2"
            >
              <ArrowLeft className="w-4 h-4 mr-1 sm:mr-2" />
              <span className="hidden sm:inline">Back</span>
            </Button>
            <div className="text-white/80 text-xs sm:text-sm font-medium overflow-hidden">
              <div className="flex items-center flex-wrap gap-1">
                <Link
                  href="/"
                  className="hover:text-green-light transition-colors whitespace-nowrap"
                >
                  Home
                </Link>
                <span className="text-white/40 hidden sm:inline">/</span>
                <Link
                  href="/#products"
                  className="hover:text-green-light transition-colors whitespace-nowrap hidden sm:inline"
                >
                  Products
                </Link>
                <span className="text-white/40 hidden sm:inline">/</span>
                <span className="text-white font-semibold truncate max-w-[200px] sm:max-w-none">
                  {product.name}
                </span>
              </div>
            </div>
          </div>
        </div>

        {/* Modern Product Details - Mobile First Grid */}
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 lg:gap-8">
          {/* Enhanced Image Gallery Card - Mobile Optimized */}
          <div className="bg-white/95 backdrop-blur-lg rounded-2xl shadow-2xl border border-white/20 overflow-hidden order-1 lg:order-none">
            <div className="p-3 sm:p-6">
              {/* Main Image Container with Enhanced Features */}
              <div className="relative aspect-square bg-gradient-to-br from-green-50 to-green-100/50 rounded-xl overflow-hidden shadow-inner group">
                {product.gallery && product.gallery.length > 0 ? (
                  <>
                    <Image
                      src={product.gallery[selectedImageIndex]}
                      alt={product.name}
                      fill
                      className={`object-cover transition-all duration-700 group-hover:scale-110 ${
                        !product.inStock ? "opacity-50 grayscale" : ""
                      }`}
                      priority
                    />
                    
                    {/* Image Navigation Arrows - Touch Optimized */}
                    {product.gallery.length > 1 && (
                      <>
                        <button
                          onClick={() => setSelectedImageIndex((prev) => 
                            prev === 0 ? product.gallery.length - 1 : prev - 1
                          )}
                          className="absolute left-2 sm:left-4 top-1/2 -translate-y-1/2 w-8 h-8 sm:w-10 sm:h-10 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center shadow-lg hover:bg-white hover:scale-110 transition-all duration-300 opacity-80 sm:opacity-0 group-hover:opacity-100 touch-manipulation"
                          aria-label="Previous image"
                        >
                          <svg className="w-4 h-4 sm:w-5 sm:h-5 text-green-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
                          </svg>
                        </button>
                        <button
                          onClick={() => setSelectedImageIndex((prev) => 
                            prev === product.gallery.length - 1 ? 0 : prev + 1
                          )}
                          className="absolute right-2 sm:right-4 top-1/2 -translate-y-1/2 w-8 h-8 sm:w-10 sm:h-10 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center shadow-lg hover:bg-white hover:scale-110 transition-all duration-300 opacity-80 sm:opacity-0 group-hover:opacity-100 touch-manipulation"
                          aria-label="Next image"
                        >
                          <svg className="w-4 h-4 sm:w-5 sm:h-5 text-green-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                          </svg>
                        </button>
                      </>
                    )}
                    
                    {/* Image Counter - Mobile Optimized */}
                    {product.gallery.length > 1 && (
                      <div className="absolute top-2 sm:top-4 left-2 sm:left-4 bg-black/70 backdrop-blur-sm text-white px-2 sm:px-3 py-1 rounded-full text-xs sm:text-sm font-medium">
                        {selectedImageIndex + 1} / {product.gallery.length}
                      </div>
                    )}
                  </>
                ) : (
                  <div className="flex items-center justify-center h-full">
                    <div className="text-center text-green-sage/60">
                      <div className="w-16 h-16 sm:w-20 sm:h-20 mx-auto mb-3 bg-green-sage/20 rounded-full flex items-center justify-center">
                        <span className="text-2xl sm:text-3xl">📷</span>
                      </div>
                      <span className="text-sm sm:text-base font-medium">
                        No Image Available
                      </span>
                    </div>
                  </div>
                )}

                {/* Enhanced Stock overlay */}
                {!product.inStock && (
                  <div className="absolute inset-0 bg-black/40 flex items-center justify-center backdrop-blur-sm">
                    <div className="bg-error-red/95 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-xl font-semibold shadow-lg">
                      <div className="flex items-center gap-2">
                        <svg className="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span className="text-sm sm:text-base">Out of Stock</span>
                      </div>
                    </div>
                  </div>
                )}
              </div>

              {/* Enhanced Thumbnail Gallery - Touch Optimized */}
              {product.gallery && product.gallery.length > 1 && (
                <div className="mt-4 sm:mt-6">
                  <div className="flex gap-2 sm:gap-3 overflow-x-auto pb-2 scrollbar-hide">
                    {product.gallery.map((image: string, index: number) => (
                      <button
                        key={index}
                        onClick={() => setSelectedImageIndex(index)}
                        className={`relative w-16 h-16 sm:w-20 sm:h-20 flex-shrink-0 rounded-xl overflow-hidden border-2 transition-all duration-300 group touch-manipulation ${
                          selectedImageIndex === index
                            ? "border-green-success shadow-lg scale-105 ring-2 ring-green-success/30"
                            : "border-green-sage/20 hover:border-green-sage/50 hover:scale-105"
                        }`}
                      >
                        <Image
                          src={image}
                          alt={`${product.name} ${index + 1}`}
                          fill
                          className="object-cover transition-all duration-300 group-hover:scale-110"
                        />
                        {selectedImageIndex === index && (
                          <div className="absolute inset-0 bg-green-success/20 flex items-center justify-center">
                            <div className="w-5 h-5 sm:w-6 sm:h-6 bg-green-success rounded-full flex items-center justify-center">
                              <svg className="w-3 h-3 sm:w-4 sm:h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
                              </svg>
                            </div>
                          </div>
                        )}
                      </button>
                    ))}
                  </div>
                </div>
              )}
            </div>
          </div>

          {/* Product Info Card - Mobile Optimized */}
          <div className="bg-white/95 backdrop-blur-lg rounded-2xl shadow-2xl border border-white/20 p-4 sm:p-6 lg:p-8 order-2 lg:order-none">
            <div className="space-y-4 sm:space-y-6">
              {/* Enhanced Header Section - Mobile First */}
              <div>
                <div className="mb-3 sm:mb-4">
                  <div className="text-xs sm:text-sm text-green-sage/70 font-medium mb-2 flex flex-wrap gap-2 sm:gap-4">
                    {product.category && (
                      <span className="inline-flex items-center gap-1">
                        <svg className="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        <span className="text-xs sm:text-sm">{product.category}</span>
                      </span>
                    )}
                    {product.brand && (
                      <span className="inline-flex items-center gap-1">
                        <svg className="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span className="text-xs sm:text-sm">{product.brand}</span>
                      </span>
                    )}
                  </div>
                  <h1 className="text-2xl sm:text-3xl lg:text-4xl font-bold text-green-dark mb-3 sm:mb-4 leading-tight">
                    {product.name}
                  </h1>
                </div>

                <div className="flex flex-wrap items-center gap-2 sm:gap-3 mb-4 sm:mb-6">
                  <Badge
                    variant={product.inStock ? "default" : "destructive"}
                    className={`px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm font-semibold rounded-full shadow-sm ${
                      product.inStock
                        ? "bg-green-success/10 text-green-success border-green-success/20 hover:bg-green-success/20"
                        : "bg-error-red/10 text-error-red border-error-red/20 hover:bg-error-red/20"
                    }`}
                  >
                    <div className="flex items-center gap-1.5 sm:gap-2">
                      {product.inStock ? (
                        <svg className="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
                        </svg>
                      ) : (
                        <svg className="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                        </svg>
                      )}
                      <span className="text-xs sm:text-sm">{product.inStock ? "In Stock" : "Out of Stock"}</span>
                    </div>
                  </Badge>

                  {product.inStock && (
                    <Badge
                      variant="secondary"
                      className="px-2 sm:px-3 py-1 text-xs font-medium bg-green-light/10 text-green-light border-green-light/20 rounded-full"
                    >
                      <div className="flex items-center gap-1">
                        <svg className="w-2.5 h-2.5 sm:w-3 sm:h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        <span className="text-xs">Free Shipping</span>
                      </div>
                    </Badge>
                  )}
                </div>

                {/* Enhanced Price Section - Mobile Optimized */}
                <div className="bg-gradient-to-r from-green-success/10 to-green-light/10 rounded-xl sm:rounded-2xl p-4 sm:p-6 border border-green-success/20 shadow-sm">
                  <div className="flex flex-col sm:flex-row sm:items-baseline gap-2 sm:gap-3 mb-2">
                    <div className="text-3xl sm:text-4xl font-bold text-green-dark">
                      {product.prices && product.prices.length > 0
                        ? `${
                            product.prices[0].symbol || product.prices[0].code
                          }${product.prices[0].amount.toFixed(2)}`
                        : "Price not available"}
                    </div>
                    {product.prices && product.prices.length > 0 && (
                      <div className="text-base sm:text-lg text-green-sage/70 line-through">
                        ${(product.prices[0].amount * 1.2).toFixed(2)}
                      </div>
                    )}
                  </div>
                  <div className="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 text-xs sm:text-sm">
                    <p className="text-green-sage">
                      <span className="font-medium">Inclusive of all taxes</span>
                    </p>
                    {product.prices && product.prices.length > 0 && (
                      <Badge className="bg-green-success/20 text-green-success border-0 px-2 py-1 text-xs w-fit">
                        Save 20%
                      </Badge>
                    )}
                  </div>
                </div>
              </div>

              {/* Product Attributes Selection - Mobile Optimized */}
              {product.attributes && product.attributes.length > 0 && (
                <div className="bg-green-50/50 rounded-xl p-4 sm:p-6 border border-green-sage/10">
                  <h3 className="text-lg sm:text-xl font-semibold text-green-dark mb-3 sm:mb-4 flex items-center">
                    <span className="w-2 h-2 bg-green-success rounded-full mr-3"></span>
                    Choose Options
                  </h3>
                  <AttributeSelector
                    attributes={product.attributes}
                    selectedAttributes={selectedAttributes}
                    onAttributeChange={handleAttributeChange}
                  />
                </div>
              )}

              {/* Enhanced Purchase Section - Mobile Optimized */}
              <div className="bg-gradient-to-r from-green-light/5 to-green-success/5 rounded-xl sm:rounded-2xl p-4 sm:p-6 border border-green-sage/10 shadow-sm">
                <div className="space-y-4 sm:space-y-6">
                  {/* Enhanced Quantity Selector - Touch Optimized */}
                  <div className="space-y-2 sm:space-y-3">
                    <label className="text-green-dark font-semibold text-sm">
                      Quantity
                    </label>
                    <div className="flex items-center gap-3 sm:gap-4">
                      <div className="flex items-center bg-white border-2 border-green-sage/20 rounded-xl overflow-hidden shadow-sm">
                        <Button
                          variant="ghost"
                          size="sm"
                          onClick={() => handleQuantityChange(-1)}
                          disabled={quantity <= 1}
                          className="text-green-dark hover:text-green-success hover:bg-green-success/10 border-r border-green-sage/20 rounded-none p-3 sm:p-3 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed touch-manipulation min-h-[44px]"
                        >
                          <Minus className="w-4 h-4" />
                        </Button>
                        <div className="px-4 sm:px-6 py-3 text-green-dark font-bold min-w-[3rem] sm:min-w-[4rem] text-center bg-green-50/50 text-base sm:text-lg">
                          {quantity}
                        </div>
                        <Button
                          variant="ghost"
                          size="sm"
                          onClick={() => handleQuantityChange(1)}
                          className="text-green-dark hover:text-green-success hover:bg-green-success/10 border-l border-green-sage/20 rounded-none p-3 sm:p-3 transition-all duration-200 touch-manipulation min-h-[44px]"
                        >
                          <Plus className="w-4 h-4" />
                        </Button>
                      </div>
                      <div className="text-xs sm:text-sm text-green-sage/70">
                        {quantity > 1 ? `${quantity} items` : '1 item'}
                      </div>
                    </div>
                  </div>

                  {/* Enhanced Action Buttons - Mobile Optimized */}
                  <div className="space-y-3 sm:space-y-4">
                    <Button
                      onClick={handleAddToCart}
                      disabled={!product.inStock || loading}
                      className={`w-full py-3 sm:py-4 text-base sm:text-lg font-semibold rounded-xl transition-all duration-300 transform min-h-[48px] sm:min-h-[56px] touch-manipulation ${
                        product.inStock
                          ? "bg-gradient-to-r from-green-success to-green-light text-white hover:from-green-success/90 hover:to-green-light/90 hover:shadow-xl hover:shadow-green-success/25 hover:-translate-y-1 active:translate-y-0"
                          : "bg-green-sage/20 text-green-sage/60 cursor-not-allowed"
                      }`}
                    >
                      <div className="flex items-center justify-center gap-2 sm:gap-3">
                        {loading ? (
                          <>
                            <div className="w-4 h-4 sm:w-5 sm:h-5 border-2 border-white/30 border-t-white rounded-full animate-spin" />
                            <span className="text-sm sm:text-base">Adding to Cart...</span>
                          </>
                        ) : (
                          <>
                            <ShoppingCart className="w-4 h-4 sm:w-5 sm:h-5" />
                            <span className="text-sm sm:text-base">
                              {product.inStock ? "Add to Cart" : "Out of Stock"}
                            </span>
                            {product.inStock && (
                              <svg className="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                              </svg>
                            )}
                          </>
                        )}
                      </div>
                    </Button>

                    {/* Secondary Actions - Mobile First */}
                    <div className="flex flex-col sm:flex-row gap-3">
                      <Button
                        variant="outline"
                        className="flex-1 border-2 border-green-sage/30 text-green-sage hover:text-green-success hover:bg-green-success/10 hover:border-green-success/30 transition-all duration-200 py-2.5 sm:py-3 rounded-xl min-h-[44px] touch-manipulation"
                      >
                        <Heart className="w-4 h-4 mr-2" />
                        <span className="text-sm sm:text-base">Wishlist</span>
                      </Button>

                      <Button
                        variant="outline"
                        className="flex-1 border-2 border-green-sage/30 text-green-sage hover:text-green-success hover:bg-green-success/10 hover:border-green-success/30 transition-all duration-200 py-2.5 sm:py-3 rounded-xl min-h-[44px] touch-manipulation"
                      >
                        <Share2 className="w-4 h-4 mr-2" />
                        <span className="text-sm sm:text-base">Share</span>
                      </Button>
                    </div>
                  </div>

                  {/* Trust Indicators - Mobile Optimized */}
                  {product.inStock && (
                    <div className="pt-3 sm:pt-4 border-t border-green-sage/10">
                      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 sm:gap-4 text-xs sm:text-sm text-green-sage/70">
                        <div className="flex items-center gap-2">
                          <svg className="w-3 h-3 sm:w-4 sm:h-4 text-green-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                          </svg>
                          <span>30-day returns</span>
                        </div>
                        <div className="flex items-center gap-2">
                          <svg className="w-3 h-3 sm:w-4 sm:h-4 text-green-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                          </svg>
                          <span>Secure checkout</span>
                        </div>
                      </div>
                    </div>
                  )}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default ProductDetailsPage;
