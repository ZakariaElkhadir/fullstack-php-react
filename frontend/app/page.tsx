"use client";
import { useCategoryContext } from "@/contexts/CategoryContext";
import HeroSection from "@/components/hero-section";
import ProductCard from "@/components/ProductCard";

export default function Home() {
  const { selectedCategory, handleCategoriesLoaded } = useCategoryContext();

  return (
    <main>
      <HeroSection />
      <ProductCard 
        selectedCategory={selectedCategory} 
        onCategoriesLoaded={handleCategoriesLoaded}
      />
    </main>
  );
}
