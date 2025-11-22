"use client";
import React, { createContext, useContext, useState, ReactNode, useEffect } from "react";

interface CategoryContextType {
  selectedCategory: string;
  setSelectedCategory: (category: string) => void;
  availableCategories: string[];
  setAvailableCategories: (categories: string[]) => void;
  handleCategoriesLoaded: (categories: string[]) => void;
}

const CATEGORY_STORAGE_KEY = 'scandiweb-categories';
const SELECTED_CATEGORY_KEY = 'scandiweb-selected-category';

const CategoryContext = createContext<CategoryContextType | undefined>(
  undefined
);

// Load categories from localStorage
const loadCategoriesFromStorage = (): string[] => {
  if (typeof window === 'undefined') return [];
  try {
    const stored = localStorage.getItem(CATEGORY_STORAGE_KEY);
    return stored ? JSON.parse(stored) : [];
  } catch (error) {
    console.error('Failed to load categories from storage:', error);
    return [];
  }
};

// Load selected category from localStorage
const loadSelectedCategoryFromStorage = (): string => {
  if (typeof window === 'undefined') return 'All';
  try {
    const stored = localStorage.getItem(SELECTED_CATEGORY_KEY);
    return stored || 'All';
  } catch (error) {
    console.error('Failed to load selected category from storage:', error);
    return 'All';
  }
};

// Save categories to localStorage
const saveCategoriesToStorage = (categories: string[]) => {
  if (typeof window === 'undefined') return;
  try {
    localStorage.setItem(CATEGORY_STORAGE_KEY, JSON.stringify(categories));
  } catch (error) {
    console.error('Failed to save categories to storage:', error);
  }
};

// Save selected category to localStorage
const saveSelectedCategoryToStorage = (category: string) => {
  if (typeof window === 'undefined') return;
  try {
    localStorage.setItem(SELECTED_CATEGORY_KEY, category);
  } catch (error) {
    console.error('Failed to save selected category to storage:', error);
  }
};

export const CategoryProvider = ({ children }: { children: ReactNode }) => {
  const [selectedCategory, setSelectedCategoryState] = useState(loadSelectedCategoryFromStorage);
  const [availableCategories, setAvailableCategoriesState] = useState<string[]>(loadCategoriesFromStorage);

  // Persist selected category to localStorage
  useEffect(() => {
    saveSelectedCategoryToStorage(selectedCategory);
  }, [selectedCategory]);

  // Persist available categories to localStorage
  useEffect(() => {
    saveCategoriesToStorage(availableCategories);
  }, [availableCategories]);

  const setSelectedCategory = (category: string) => {
    setSelectedCategoryState(category);
  };

  const setAvailableCategories = (categories: string[]) => {
    setAvailableCategoriesState(categories);
  };

  const handleCategoriesLoaded = (categories: string[]) => {
    setAvailableCategoriesState(categories);
  };

  return (
    <CategoryContext.Provider
      value={{
        selectedCategory,
        setSelectedCategory,
        availableCategories,
        setAvailableCategories,
        handleCategoriesLoaded,
      }}
    >
      {children}
    </CategoryContext.Provider>
  );
};

export const useCategoryContext = () => {
  const context = useContext(CategoryContext);
  if (context === undefined) {
    throw new Error(
      "useCategoryContext must be used within a CategoryProvider"
    );
  }
  return context;
};
