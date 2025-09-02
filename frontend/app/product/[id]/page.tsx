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
import {
  ArrowLeft,
  ShoppingCart,
  Heart,
  Share2,
  Minus,
  Plus,
} from "lucide-react";

interface Price {
  amount: number;
  code: string;
  label?: string;
  symbol?: string;
}

interface Product {
  id: string;
  name: string;
  inStock: boolean;
  prices: Price[];
  category?: string;
  gallery: string[];
  brand?: string;
  attributes: string[];
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
      attributes
    }
  }
`;

const ProductDetailsPage = () => {
  const router = useRouter();
  const params = useParams();

  const productId = params?.id as string;
  const [quantity, setQuantity] = useState(1);
  const [selectedImageIndex, setSelectedImageIndex] = useState(0);
  const [loading, setLoading] = useState(false);

  const { loading: queryLoading, error, data } = useQuery(GET_ALL_PRODUCTS);

  const product = React.useMemo(() => {
    if (!data?.products || !productId) return null;
    return data.products.find((p: Product) => p.id === productId);
  }, [data?.products, productId]);

  const handleAddToCart = async () => {
    if (!product?.inStock) return;

    setLoading(true);
    try {
      console.log(`Added ${quantity} of product ${product.id} to cart`);

      alert(`Added ${quantity} ${product.name}(s) to cart!`);
    } catch (error) {
      console.error("Failed to add to cart:", error);
      alert("Failed to add to cart. Please try again.");
    } finally {
      setLoading(false);
    }
  };

  const handleQuantityChange = (change: number) => {
    setQuantity(Math.max(1, quantity + change));
  };

  if (queryLoading) {
    return (
      <div className="min-h-screen bg-gradient-to-br from-slate-900 via-green-dark to-slate-800">
        <div className="container mx-auto p-6 pt-20">
          <Skeleton className="h-10 w-32 mb-6 bg-green-sage/20" />
          <div className="grid md:grid-cols-2 gap-8">
            <div className="space-y-4">
              <Skeleton className="h-96 w-full bg-green-sage/20" />
              <div className="flex gap-2">
                {Array.from({ length: 4 }).map((_, i) => (
                  <Skeleton key={i} className="h-20 w-20 bg-green-sage/20" />
                ))}
              </div>
            </div>
            <div className="space-y-6">
              <Skeleton className="h-12 w-3/4 bg-green-sage/20" />
              <Skeleton className="h-8 w-1/2 bg-green-sage/20" />
              <Skeleton className="h-24 w-full bg-green-sage/20" />
              <Skeleton className="h-12 w-full bg-green-sage/20" />
            </div>
          </div>
        </div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="min-h-screen bg-gradient-to-br from-slate-900 via-green-dark to-slate-800 flex items-center justify-center">
        <Card className="max-w-md mx-auto border border-error-red/30 bg-warm-cream">
          <CardHeader>
            <CardTitle className="text-error-red">
              Error Loading Product
            </CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <p className="text-green-dark/70">{error.message}</p>
            <div className="flex gap-2">
              <Button
                onClick={() => router.back()}
                variant="outline"
                className="border-green-sage text-green-sage hover:bg-green-sage/10"
              >
                <ArrowLeft className="w-4 h-4 mr-2" />
                Go Back
              </Button>
              <Link href="/">
                <Button className="bg-green-light text-white hover:bg-green-dark">
                  Home
                </Button>
              </Link>
            </div>
          </CardContent>
        </Card>
      </div>
    );
  }

  if (!product) {
    return (
      <div className="min-h-screen bg-gradient-to-br from-slate-900 via-green-dark to-slate-800 flex items-center justify-center">
        <Card className="max-w-md mx-auto border border-error-red/30 bg-warm-cream">
          <CardHeader>
            <CardTitle className="text-error-red">Product Not Found</CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <p className="text-green-dark/70">
              The product you are looking for does not exist.
            </p>
            <div className="flex gap-2">
              <Button
                onClick={() => router.back()}
                variant="outline"
                className="border-green-sage text-green-sage hover:bg-green-sage/10"
              >
                <ArrowLeft className="w-4 h-4 mr-2" />
                Go Back
              </Button>
              <Link href="/">
                <Button className="bg-green-light text-white hover:bg-green-dark">
                  Home
                </Button>
              </Link>
            </div>
          </CardContent>
        </Card>
      </div>
    );
  }

  // ...existing code... (rest of the component remains the same)
  return (
    <div className="min-h-screen bg-gradient-to-br from-slate-900 via-green-dark to-slate-800 relative overflow-hidden">
      {/* Left light reflection */}
      <div className="absolute top-0 left-0 w-1/2 h-full bg-gradient-to-r from-green-success/30 via-green-light/15 to-transparent blur-3xl transform -skew-y-12 -translate-x-1/4"></div>

      {/* Right light reflection */}
      <div className="absolute top-0 right-0 w-1/2 h-full bg-gradient-to-l from-green-sage/35 via-green-success/20 to-transparent blur-2xl transform skew-y-12 translate-x-1/4"></div>

      {/* Top center glow */}
      <div className="absolute top-10 left-1/2 transform -translate-x-1/2 w-3/4 h-96 bg-gradient-radial from-green-light/25 via-green-success/10 to-transparent blur-2xl rounded-full"></div>

      {/* Bottom light reflection */}
      <div className="absolute bottom-0 left-1/4 w-1/2 h-1/3 bg-gradient-radial from-green-sage/20 to-transparent blur-xl rounded-full"></div>

      {/* Light beams */}
      <div className="absolute inset-0 bg-gradient-to-br from-transparent via-green-success/8 to-transparent"></div>
      <div className="absolute inset-0 bg-gradient-to-bl from-green-light/5 via-transparent to-green-sage/5"></div>

      {/* Glass morphism overlay */}
      <div className="absolute inset-0 bg-gradient-to-b from-white/8 via-transparent to-black/15 backdrop-blur-[2px]"></div>

      {/* Subtle geometric glow */}
      <div className="absolute top-1/4 right-1/4 w-64 h-64 bg-gradient-radial from-green-success/15 to-transparent blur-2xl rounded-full transform rotate-45"></div>
      <div className="absolute bottom-1/4 left-1/6 w-48 h-48 bg-gradient-radial from-green-light/20 to-transparent blur-xl rounded-full"></div>

      <div className="relative z-10 container mx-auto px-6 py-8">
        {/* Modern Navigation */}
        <div className="flex items-center justify-between mb-8 bg-white/10 backdrop-blur-lg rounded-xl p-4 shadow-lg border border-white/20">
          <div className="flex items-center gap-4">
            <Button
              onClick={() => router.back()}
              variant="ghost"
              size="sm"
              className="text-white hover:text-green-light hover:bg-white/10 transition-all duration-200"
            >
              <ArrowLeft className="w-4 h-4 mr-2" />
              Back
            </Button>
            <div className="text-white/80 text-sm font-medium">
              <Link
                href="/"
                className="hover:text-green-light transition-colors"
              >
                Home
              </Link>
              <span className="mx-2 text-white/40">/</span>
              <Link
                href="/#products"
                className="hover:text-green-light transition-colors"
              >
                Products
              </Link>
              <span className="mx-2 text-white/40">/</span>
              <span className="text-green-light font-semibold">
                {product.name}
              </span>
            </div>
          </div>
        </div>

        {/* Modern Product Details */}
        <div className="grid lg:grid-cols-2 gap-8">
          {/* Image Gallery Card */}
          <div className="bg-white/95 backdrop-blur-lg rounded-2xl shadow-2xl border border-white/20 overflow-hidden">
            <div className="p-6">
              <div className="relative aspect-square bg-gradient-to-br from-green-50 to-green-100/50 rounded-xl overflow-hidden shadow-inner">
                {product.gallery && product.gallery.length > 0 ? (
                  <Image
                    src={product.gallery[selectedImageIndex]}
                    alt={product.name}
                    fill
                    className={`object-cover transition-all duration-500 hover:scale-105 ${
                      !product.inStock ? "opacity-50 grayscale" : ""
                    }`}
                    priority
                  />
                ) : (
                  <div className="flex items-center justify-center h-full">
                    <div className="text-center text-green-sage/60">
                      <div className="w-16 h-16 mx-auto mb-2 bg-green-sage/20 rounded-full flex items-center justify-center">
                        <span className="text-2xl">📷</span>
                      </div>
                      <span className="text-sm font-medium">
                        No Image Available
                      </span>
                    </div>
                  </div>
                )}

                {/* Stock overlay */}
                {!product.inStock && (
                  <div className="absolute inset-0 bg-black/30 flex items-center justify-center">
                    <div className="bg-error-red/90 text-white px-4 py-2 rounded-lg font-semibold">
                      Out of Stock
                    </div>
                  </div>
                )}
              </div>

              {/* Thumbnail Gallery */}
              {product.gallery && product.gallery.length > 1 && (
                <div className="flex gap-3 mt-4 overflow-x-auto pb-2">
                  {product.gallery.map((image: string, index: number) => (
                    <button
                      key={index}
                      onClick={() => setSelectedImageIndex(index)}
                      className={`relative w-16 h-16 flex-shrink-0 rounded-lg overflow-hidden border-2 transition-all duration-200 ${
                        selectedImageIndex === index
                          ? "border-green-success shadow-md scale-110"
                          : "border-green-sage/20 hover:border-green-sage/40"
                      }`}
                    >
                      <Image
                        src={image}
                        alt={`${product.name} ${index + 1}`}
                        fill
                        className="object-cover"
                      />
                    </button>
                  ))}
                </div>
              )}
            </div>
          </div>

          {/* Product Info Card */}
          <div className="bg-white/95 backdrop-blur-lg rounded-2xl shadow-2xl border border-white/20 p-8">
            <div className="space-y-6">
              {/* Header Section */}
              <div>
                <h1 className="text-3xl lg:text-4xl font-bold text-green-dark mb-4 leading-tight">
                  {product.name}
                </h1>

                <div className="flex flex-wrap items-center gap-3 mb-6">
                  <Badge
                    variant={product.inStock ? "default" : "destructive"}
                    className={`px-3 py-1 text-sm font-medium ${
                      product.inStock
                        ? "bg-green-success/10 text-green-success border-green-success/20"
                        : "bg-error-red/10 text-error-red border-error-red/20"
                    }`}
                  >
                    {product.inStock ? "✓ In Stock" : "✗ Out of Stock"}
                  </Badge>

                  {product.category && (
                    <Badge
                      variant="secondary"
                      className="bg-green-sage/10 text-green-dark border-green-sage/20 hover:bg-green-sage/20 transition-colors"
                    >
                      {product.category}
                    </Badge>
                  )}

                  {product.brand && (
                    <Badge
                      variant="outline"
                      className="border-green-light text-green-light hover:bg-green-light/10 transition-colors"
                    >
                      {product.brand}
                    </Badge>
                  )}
                </div>

                {/* Price Section */}
                <div className="bg-gradient-to-r from-green-success/10 to-green-light/10 rounded-xl p-6 border border-green-success/20">
                  <div className="text-4xl font-bold text-green-dark mb-2">
                    {product.prices && product.prices.length > 0
                      ? `${
                          product.prices[0].symbol || product.prices[0].code
                        }${product.prices[0].amount.toFixed(2)}`
                      : "Price not available"}
                  </div>
                  <p className="text-green-sage text-sm">
                    Inclusive of all taxes
                  </p>
                </div>
              </div>

              {/* Features Section */}
              {product.attributes && product.attributes.length > 0 && (
                <div className="bg-green-50/50 rounded-xl p-6 border border-green-sage/10">
                  <h3 className="text-xl font-semibold text-green-dark mb-4 flex items-center">
                    <span className="w-2 h-2 bg-green-success rounded-full mr-3"></span>
                    Key Features
                  </h3>
                  <ul className="space-y-3">
                    {product.attributes.map(
                      (attribute: string, index: number) => (
                        <li
                          key={index}
                          className="flex items-start text-green-dark/80"
                        >
                          <span className="w-1.5 h-1.5 bg-green-success rounded-full mt-2 mr-3 flex-shrink-0"></span>
                          <span className="text-sm leading-relaxed">
                            {attribute}
                          </span>
                        </li>
                      )
                    )}
                  </ul>
                </div>
              )}

              {/* Purchase Section */}
              <div className="bg-gradient-to-r from-green-light/5 to-green-success/5 rounded-xl p-6 border border-green-sage/10">
                <div className="space-y-6">
                  {/* Quantity Selector */}
                  <div className="flex items-center gap-4">
                    <label className="text-green-dark font-semibold">
                      Quantity:
                    </label>
                    <div className="flex items-center bg-white border-2 border-green-sage/20 rounded-lg overflow-hidden">
                      <Button
                        variant="ghost"
                        size="sm"
                        onClick={() => handleQuantityChange(-1)}
                        disabled={quantity <= 1}
                        className="text-green-dark hover:text-green-success hover:bg-green-success/10 border-r border-green-sage/20 rounded-none"
                      >
                        <Minus className="w-4 h-4" />
                      </Button>
                      <div className="px-6 py-2 text-green-dark font-semibold min-w-[4rem] text-center bg-green-50/50">
                        {quantity}
                      </div>
                      <Button
                        variant="ghost"
                        size="sm"
                        onClick={() => handleQuantityChange(1)}
                        className="text-green-dark hover:text-green-success hover:bg-green-success/10 border-l border-green-sage/20 rounded-none"
                      >
                        <Plus className="w-4 h-4" />
                      </Button>
                    </div>
                  </div>

                  {/* Action Buttons */}
                  <div className="flex gap-4">
                    <Button
                      onClick={handleAddToCart}
                      disabled={!product.inStock || loading}
                      className={`flex-1 py-4 text-lg font-semibold rounded-xl transition-all duration-200 ${
                        product.inStock
                          ? "bg-green-success text-white hover:bg-green-success/90 hover:shadow-lg transform hover:-translate-y-0.5"
                          : "bg-green-sage/20 text-green-sage/60 cursor-not-allowed"
                      }`}
                    >
                      <ShoppingCart className="w-5 h-5 mr-2" />
                      {loading
                        ? "Adding..."
                        : product.inStock
                        ? "Add to Cart"
                        : "Out of Stock"}
                    </Button>

                    <Button
                      variant="outline"
                      size="icon"
                      className="border-2 border-green-sage/30 text-green-sage hover:text-green-success hover:bg-green-success/10 hover:border-green-success/30 transition-all duration-200 p-4 rounded-xl"
                    >
                      <Heart className="w-5 h-5" />
                    </Button>

                    <Button
                      variant="outline"
                      size="icon"
                      className="border-2 border-green-sage/30 text-green-sage hover:text-green-success hover:bg-green-success/10 hover:border-green-success/30 transition-all duration-200 p-4 rounded-xl"
                    >
                      <Share2 className="w-5 h-5" />
                    </Button>
                  </div>
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
