import { ShoppingCart } from "lucide-react";
import Link from "next/link";
import Image from "next/image";
interface HeaderProps {
  selectedCategory: string;
  onCategoryChange: (category: string) => void;
  availableCategories?: string[];
}

const Header = ({ selectedCategory, onCategoryChange, availableCategories = [] }: HeaderProps) => {
  // Helper function to capitalize first letter
  const capitalizeFirst = (str: string) => {
    return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
  };

  // Function to handle category change and scroll to products
  const handleCategoryClick = (category: string) => {
    onCategoryChange(category);
    
    // Smooth scroll to products section with offset for header
    setTimeout(() => {
      const productsSection = document.getElementById('products');
      if (productsSection) {
        const headerHeight = 100; // Approximate header height
        const elementPosition = productsSection.getBoundingClientRect().top;
        const offsetPosition = elementPosition + window.pageYOffset - headerHeight;

        window.scrollTo({
          top: offsetPosition,
          behavior: 'smooth'
        });
      }
    }, 100); // Small delay to ensure state update
  };

  // Create categories array with "All" first, then available categories
  const categories = ["All", ...availableCategories.map(capitalizeFirst)];
  
  return (
    <header className="bg-green-dark/90 backdrop-blur-lg border-b border-green-light/20 m-0 p-0 flex justify-center shadow-lg">
      <div className="container grid grid-cols-3 items-center py-4">
        <div className="categories justify-self-start">
          <ul className="flex space-x-4">
            {categories.map((category) => (
              <li key={category}>
                <button
                  onClick={() => handleCategoryClick(category)}
                  className={`px-4 py-2 rounded-lg transition-all duration-200 font-medium ${
                    selectedCategory === category
                      ? "bg-green-success text-white shadow-md transform scale-105"
                      : "text-green-sage hover:text-white hover:bg-green-light/20 backdrop-blur-sm"
                  }`}
                >
                  {category}
                </button>
              </li>
            ))}
          </ul>
        </div>
        <Link href="/">
          <div className="logo justify-self-center">
            <Image
              src="/logo.png"
              alt="Pixel Cart Logo"
              width={80}
              height={80}
            />
          </div>
        </Link>
        <div className="cart justify-self-end">
          <ShoppingCart className="w-6 h-6 text-green-sage hover:text-green-success transition-colors cursor-pointer" />
        </div>
      </div>
    </header>
  );
};

export default Header;
