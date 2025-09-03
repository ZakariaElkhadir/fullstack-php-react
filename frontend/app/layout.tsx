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
import { Toaster } from "@/components/ui/sonner";
import "./globals.css";
import "../styles/toast.css";

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
              <Toaster />
            </CategoryProvider>
          </CartProvider>
        </ApolloProvider>
      </body>
    </html>
  );
}
