"use client";
import React from "react";
import { gql, useQuery } from "@apollo/client";
import Image from "next/image";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Skeleton } from "@/components/ui/skeleton";

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
}
interface ProductCardProp {
  selectedCategory: string;
  onCategoriesLoaded?: (categories: string[]) => void;
}
const GET_PRODUCTS = gql`
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

const ProductCard = ({
  selectedCategory,
  onCategoriesLoaded,
}: ProductCardProp) => {
  const { loading, error, data } = useQuery(GET_PRODUCTS);

  const availableCategories = React.useMemo(() => {
    if (!data?.products) return [];
    const categories = data.products
      .map((product: Product) => product.category)
      .filter(
        (category: string | undefined): category is string =>
          category !== undefined && category !== null && category.trim() !== ""
      )
      .filter(
        (category: string, index: number, array: string[]) =>
          array.indexOf(category) === index
      );

    return categories;
  }, [data?.products]);

  React.useEffect(() => {
    if (availableCategories.length > 0 && onCategoriesLoaded) {
      onCategoriesLoaded(availableCategories);
    }
  }, [availableCategories, onCategoriesLoaded]);

  const filteredProducts = React.useMemo(() => {
    if (!data?.products) return [];

    if (selectedCategory.toLowerCase() === "all") return data.products;

    return data.products.filter(
      (product: Product) =>
        product.category?.toLowerCase() === selectedCategory.toLowerCase()
    );
  }, [data?.products, selectedCategory]);

  if (loading) {
    return (
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 p-6">
        {Array.from({ length: 8 }).map((_, index) => (
          <Card
            key={index}
            className="overflow-hidden border border-green-light/20 min-h-[420px] hover:shadow-2xl hover:shadow-black/25 hover:-translate-y-2 transition-all duration-300 cursor-pointer"
          >
            <Skeleton className="h-56 w-full bg-warm-gray" />
            <CardHeader className="pb-3">
              <Skeleton className="h-6 w-3/4 bg-green-sage/20" />
              <Skeleton className="h-4 w-1/2 bg-green-sage/20" />
            </CardHeader>
            <CardContent className="pt-0">
              <Skeleton className="h-4 w-full mb-2 bg-green-sage/20" />
              <Skeleton className="h-4 w-2/3 bg-green-sage/20" />
            </CardContent>
          </Card>
        ))}
      </div>
    );
  }

  if (error) {
    return (
      <div className="flex items-center justify-center min-h-[400px]">
        <Card className="max-w-md mx-auto border border-error-red/30 bg-warm-cream">
          <CardHeader>
            <CardTitle className="text-error-red">
              Error Loading Products
            </CardTitle>
          </CardHeader>
          <CardContent>
            <p className="text-green-dark/70">{error.message}</p>
          </CardContent>
        </Card>
      </div>
    );
  }

  return (
    <section
      id="products"
      className="width-full relative min-h-screen overflow-hidden bg-gradient-to-br from-slate-900 via-green-dark to-slate-800"
    >
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

      <div className="relative z-10 container mx-auto p-6 pt-20">
        <h1 className="text-6xl font-bold mb-8 text-center text-white drop-shadow-2xl">
          Check Out Our Products
        </h1>
        <p className="text-lg text-center text-white/80 mb-12">
          {selectedCategory.toLowerCase() === "all"
            ? `Showing all products (${filteredProducts.length})`
            : `Showing ${selectedCategory} products (${filteredProducts.length})`}
        </p>
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          {filteredProducts?.map((product: Product) => (
            <Card
              key={product.id}
              className={`overflow-hidden min-h-[420px] flex flex-col transition-all duration-300 border hover:shadow-2xl hover:shadow-black/25 hover:-translate-y-2 cursor-pointer ${
                product.inStock
                  ? "border-green-sage/20 bg-white"
                  : "border-green-sage/10 bg-green-sage/5 cursor-not-allowed "
              }`}
            >
              {/* Image Section */}
              <div className="relative h-56 bg-warm-gray w-full flex-shrink-0">
                {product.gallery && product.gallery.length > 0 ? (
                  <Image
                    src={product.gallery[0]}
                    alt={product.name}
                    fill
                    className={`object-cover transition-all duration-300 ease-in-out ${
                      !product.inStock
                        ? "opacity-60 grayscale-[50%] sepia-[20%] hue-rotate-[75deg]"
                        : ""
                    }`}
                  />
                ) : (
                  <div
                    className={`flex items-center justify-center h-full bg-warm-gray ${
                      !product.inStock ? "opacity-60" : ""
                    }`}
                  >
                    <span
                      className={`${
                        product.inStock
                          ? "text-green-sage"
                          : "text-green-sage/50"
                      }`}
                    >
                      No Image
                    </span>
                  </div>
                )}
                <div className="absolute top-3 right-3">
                  <Badge
                    variant={product.inStock ? "default" : "destructive"}
                    className={
                      product.inStock
                        ? "bg-green-success text-white hover:bg-green-success/90"
                        : "bg-error-red  text-white hover:bg-error-red/90"
                    }
                  >
                    {product.inStock ? "In Stock" : "Out of Stock"}
                  </Badge>
                </div>
              </div>

              {/* Content Section - Flex grow to fill remaining space */}
              <div className="flex flex-col flex-1">
                <CardHeader className="pb-3 flex-shrink-0">
                  <CardTitle
                    className={`text-lg font-semibold leading-tight mb-3 ${
                      product.inStock ? "text-green-dark" : "text-green-sage/70"
                    }`}
                  >
                    {product.name}
                  </CardTitle>
                  <div className="flex gap-2 flex-wrap">
                    {product.category && (
                      <Badge
                        variant="secondary"
                        className={`text-xs ${
                          product.inStock
                            ? "bg-green-sage/10 text-green-dark border-green-sage/20 hover:bg-green-sage/20"
                            : "bg-green-sage/5 text-green-sage/60 border-green-sage/10"
                        }`}
                      >
                        {product.category}
                      </Badge>
                    )}
                    {product.brand && (
                      <Badge
                        variant="outline"
                        className={`text-xs ${
                          product.inStock
                            ? "border-green-light text-green-light hover:bg-green-light/10"
                            : "border-green-sage/30 text-green-sage/60 hover:bg-green-sage/5"
                        }`}
                      >
                        {product.brand}
                      </Badge>
                    )}
                  </div>
                </CardHeader>

                {/* Price and Button Section - Pushed to bottom */}
                <CardContent className="pt-0 mt-auto">
                  <div className="flex flex-col gap-4">
                    <div className="flex items-center justify-between">
                      <span
                        className={`text-2xl font-bold ${
                          product.inStock
                            ? "text-green-dark"
                            : "text-green-sage/60"
                        }`}
                      >
                        {product.prices && product.prices.length > 0
                          ? `${
                              product.prices[0].symbol || product.prices[0].code
                            }${product.prices[0].amount.toFixed(2)}`
                          : "Price not available"}
                      </span>
                    </div>
                    <button
                      onClick={() => {
                        if (product.inStock) {
                          window.location.href = `/product/${product.id}`;
                        }
                      }}
                      className={`w-full py-3 px-4 rounded-md transition-colors font-medium ${
                        product.inStock
                          ? "bg-green-light text-white hover:bg-green-dark active:bg-green-dark/90 cursor-pointer"
                          : "bg-green-sage/20 text-green-sage/70 hover:bg-green-sage/30 cursor-not-allowed"
                      }`}
                    >
                      {product.inStock ? "View Details" : "Out of Stock"}
                    </button>
                  </div>
                </CardContent>
              </div>
            </Card>
          ))}
        </div>
      </div>
    </section>
  );
};

export default ProductCard;
