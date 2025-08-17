"use client";
import Header from "@/components/Header";
import { useState } from "react";
import HeroSection from "@/components/hero-section";
import ProductCard from "@/components/ProductCard";
export default function Home() {
  const [selectedCategory, setSelectedCategory] = useState("All");
  const [availableCategories, setAvailableCategories] = useState<string[]>([]);

  const handleCategoriesLoaded = (categories: string[]) => {
    setAvailableCategories(categories);
  };

  return (
    <>
      <Header
        selectedCategory={selectedCategory}
        onCategoryChange={setSelectedCategory}
        availableCategories={availableCategories}
      />

      <main className="min-h-screen">
        <HeroSection />
        <ProductCard 
          selectedCategory={selectedCategory} 
          onCategoriesLoaded={handleCategoriesLoaded}
        />
      </main>
    </>
  );
}
