"use client";
import { Geist, Geist_Mono } from "next/font/google";
import { ApolloProvider } from "@apollo/client";
import client from "@/lib/apolloClient";
import Header from "@/components/Header";
import {
  CategoryProvider,
  useCategoryContext,
} from "@/contexts/CategoryContext";
import { CartProvider } from "@/contexts/CartContext";
import { Toaster } from "sonner";
import "./globals.css";

const geistSans = Geist({
  variable: "--font-geist-sans",
  subsets: ["latin"],
});

const geistMono = Geist_Mono({
  variable: "--font-geist-mono",
  subsets: ["latin"],
});

function LayoutContent({ children }: { children: React.ReactNode }) {
  const { selectedCategory, setSelectedCategory, availableCategories } =
    useCategoryContext();

  return (
    <>
      <Header
        selectedCategory={selectedCategory}
        onCategoryChange={setSelectedCategory}
        availableCategories={availableCategories}
      />
      <div className="min-h-screen">{children}</div>
    </>
  );
}

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="en">
      <body
        className={`${geistSans.variable} ${geistMono.variable} antialiased`}
      >
        <ApolloProvider client={client}>
          <CartProvider>
            <CategoryProvider>
              <LayoutContent>{children}</LayoutContent>
              <Toaster 
                position="top-right"
                expand={true}
                richColors={true}
                closeButton={true}
                toastOptions={{
                  style: {
                    background: 'rgba(255, 255, 255, 0.95)',
                    backdropFilter: 'blur(16px)',
                    border: '1px solid rgba(34, 197, 94, 0.2)',
                    borderRadius: '12px',
                    color: '#065f46',
                    fontSize: '14px',
                    fontWeight: '500',
                  },
                  className: 'mobile-toast',
                }}
              />
            </CategoryProvider>
          </CartProvider>
        </ApolloProvider>
      </body>
    </html>
  );
}
