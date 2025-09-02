import { ShoppingCart } from "lucide-react";
import Link from "next/link";
import Image from "next/image";
import { useRouter, usePathname } from "next/navigation";

interface HeaderProps {
  selectedCategory: string;
  onCategoryChange: (category: string) => void;
  availableCategories?: string[];
}

const Header = ({ selectedCategory, onCategoryChange, availableCategories = [] }: HeaderProps) => {
  const router = useRouter();
  const pathname = usePathname();

  const capitalizeFirst = (str: string) => {
    return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
  };

  const handleCategoryClick = (category: string) => {
    onCategoryChange(category);
    
    if (pathname !== '/') {
      router.push('/#products');
      return;
    }
    
    setTimeout(() => {
      const productsSection = document.getElementById('products');
      if (productsSection) {
        const headerHeight = 100;
        const elementPosition = productsSection.getBoundingClientRect().top;
        const offsetPosition = elementPosition + window.pageYOffset - headerHeight;

        window.scrollTo({
          top: offsetPosition,
          behavior: 'smooth'
        });
      }
    }, 100);
  };

  const categories = ["All", ...availableCategories.map(capitalizeFirst)];
  
  return (
    <header className="bg-green-dark/95 backdrop-blur-lg border-b border-green-light/20 shadow-md sticky top-0 z-50">
      <div className="container mx-auto px-4 flex items-center justify-between h-16">
        {/* Logo */}
        <Link href="/" className="flex items-center">
          <Image
            src="/logo.png"
            alt="Pixel Cart Logo"
            width={40}
            height={40}
            className="hover:scale-105 transition-transform duration-200"
          />
        </Link>

        {/* Categories */}
        <nav className="hidden md:flex items-center space-x-2">
          {categories.map((category) => (
            <button
              key={category}
              onClick={() => handleCategoryClick(category)}
              className={`px-3 py-1.5 rounded-md text-sm font-medium transition-all duration-200 ${
                selectedCategory === category
                  ? "bg-green-success text-white shadow-sm"
                  : "text-green-sage hover:text-white hover:bg-green-light/20"
              }`}
            >
              {category}
            </button>
          ))}
        </nav>

        {/* Mobile Categories */}
        <div className="md:hidden flex items-center space-x-1 overflow-x-auto">
          {categories.slice(0, 3).map((category) => (
            <button
              key={category}
              onClick={() => handleCategoryClick(category)}
              className={`px-2 py-1 rounded text-xs font-medium whitespace-nowrap ${
                selectedCategory === category
                  ? "bg-green-success text-white"
                  : "text-green-sage hover:text-white hover:bg-green-light/20"
              }`}
            >
              {category}
            </button>
          ))}
        </div>

        {/* Cart */}
        <div className="flex items-center">
          <ShoppingCart className="w-5 h-5 text-green-sage hover:text-green-success transition-colors cursor-pointer" />
        </div>
      </div>
    </header>
  );
};

export default Header;
