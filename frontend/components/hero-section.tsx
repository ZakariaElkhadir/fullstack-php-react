"use client"

import { useState, useEffect } from "react"
import { motion, AnimatePresence } from "framer-motion"
import { Button } from "@/components/ui/button"
import { ChevronLeft, ChevronRight } from "lucide-react"

interface Slide {
  id: number
  title: string
  description: string
  offer: string
  images: [string, string]
  category: "clothes" | "tech"
}

const slides: Slide[] = [
  {
    id: 1,
    title: "Fashion Forward",
    description:
      "Discover the latest trends in fashion with our curated collection of premium clothing and accessories.",
    offer: "40% OFF",
    images: [
      "https://images.unsplash.com/photo-1740711152088-88a009e877bb?q=80&w=880&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D",
      "https://images.unsplash.com/photo-1591047139829-d91aecb6caea?q=80&w=736&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D",
    ],
    category: "clothes",
  },
  {
    id: 2,
    title: "Tech Revolution",
    description: "Experience cutting-edge technology with our premium selection of gadgets and innovative devices.",
    offer: "35% OFF",
    images: [
      "https://images.unsplash.com/photo-1630794180018-433d915c34ac?q=80&w=1632&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D",
      "https://images.unsplash.com/photo-1505740420928-5e560c06d30e?q=80&w=1470&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D",
    ],
    category: "tech",
  },
]

export default function HeroSection() {
  const [currentSlide, setCurrentSlide] = useState(0)
  const [isAutoPlaying, setIsAutoPlaying] = useState(true)

  // Auto-slide functionality
  useEffect(() => {
    if (!isAutoPlaying) return

    const interval = setInterval(() => {
      setCurrentSlide((prev) => (prev + 1) % slides.length)
    }, 5000)

    return () => clearInterval(interval)
  }, [isAutoPlaying])

  const nextSlide = () => {
    setCurrentSlide((prev) => (prev + 1) % slides.length)
    setIsAutoPlaying(false)
    setTimeout(() => setIsAutoPlaying(true), 10000) // Resume auto-play after 10s
  }

  const prevSlide = () => {
    setCurrentSlide((prev) => (prev - 1 + slides.length) % slides.length)
    setIsAutoPlaying(false)
    setTimeout(() => setIsAutoPlaying(true), 10000) // Resume auto-play after 10s
  }

  const goToSlide = (index: number) => {
    setCurrentSlide(index)
    setIsAutoPlaying(false)
    setTimeout(() => setIsAutoPlaying(true), 10000) // Resume auto-play after 10s
  }

  const currentSlideData = slides[currentSlide]

  return (
    <section className="relative h-screen w-full overflow-hidden">
      <AnimatePresence mode="wait">
        <motion.div
          key={currentSlide}
          initial={{ opacity: 0 }}
          animate={{ opacity: 1 }}
          exit={{ opacity: 0 }}
          transition={{ duration: 0.8, ease: "easeInOut" }}
          className="absolute inset-0"
        >
          {/* Background gradient based on category */}
          <div
            className={`absolute inset-0 ${
              currentSlideData.category === "clothes"
                ? "bg-gradient-to-br from-rose-50 via-pink-50 to-purple-50"
                : "bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50"
            }`}
          />

          <div className="relative h-full flex flex-col lg:flex-row">
            {/* Content Section */}
            <div className="flex-1 flex items-center justify-center p-6 lg:p-12 z-10">
              <div className="max-w-lg text-center lg:text-left">
                <motion.div
                  initial={{ opacity: 0, y: 30 }}
                  animate={{ opacity: 1, y: 0 }}
                  transition={{ duration: 0.8, delay: 0.2 }}
                  className={`inline-block px-4 py-2 rounded-full text-sm font-semibold mb-6 ${
                    currentSlideData.category === "clothes" ? "bg-rose-100 text-rose-800" : "bg-blue-100 text-blue-800"
                  }`}
                >
                  {currentSlideData.category === "clothes" ? "Fashion" : "Technology"}
                </motion.div>

                <motion.h1
                  initial={{ opacity: 0, y: 30 }}
                  animate={{ opacity: 1, y: 0 }}
                  transition={{ duration: 0.8, delay: 0.3 }}
                  className="text-4xl lg:text-6xl font-bold text-gray-900 mb-6 leading-tight"
                >
                  {currentSlideData.title}
                </motion.h1>

                <motion.p
                  initial={{ opacity: 0, y: 30 }}
                  animate={{ opacity: 1, y: 0 }}
                  transition={{ duration: 0.8, delay: 0.4 }}
                  className="text-lg text-gray-600 mb-8 leading-relaxed"
                >
                  {currentSlideData.description}
                </motion.p>

                <motion.div
                  initial={{ opacity: 0, y: 30 }}
                  animate={{ opacity: 1, y: 0 }}
                  transition={{ duration: 0.8, delay: 0.5 }}
                  className="flex flex-col sm:flex-row items-center gap-4"
                >
                  <div
                    className={`text-5xl font-black ${
                      currentSlideData.category === "clothes" ? "text-rose-600" : "text-blue-600"
                    }`}
                  >
                    {currentSlideData.offer}
                  </div>
                  <Button
                    size="lg"
                    className={`px-8 py-3 text-lg font-semibold ${
                      currentSlideData.category === "clothes"
                        ? "bg-rose-600 hover:bg-rose-700"
                        : "bg-blue-600 hover:bg-blue-700"
                    }`}
                  >
                    Shop Now
                  </Button>
                </motion.div>
              </div>
            </div>

            {/* Images Section */}
            <div className="flex-1 relative p-6 lg:p-12">
              <div className="h-full grid grid-cols-1 sm:grid-cols-2 gap-4 lg:gap-6">
                {currentSlideData.images.map((image, index) => (
                  <motion.div
                    key={index}
                    initial={{ opacity: 0, scale: 0.9, y: 20 }}
                    animate={{ opacity: 1, scale: 1, y: 0 }}
                    transition={{ duration: 0.8, delay: 0.6 + index * 0.1 }}
                    className="relative group overflow-hidden rounded-2xl shadow-2xl"
                  >
                    <motion.img
                      src={image}
                      alt={`${currentSlideData.category} ${index + 1}`}
                      className="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                      whileHover={{ scale: 1.05 }}
                      transition={{ duration: 0.3 }}
                    />
                    <div className="absolute inset-0 bg-black/10 group-hover:bg-black/5 transition-colors duration-300" />
                  </motion.div>
                ))}
              </div>
            </div>
          </div>
        </motion.div>
      </AnimatePresence>

      {/* Navigation Controls */}
      <div className="absolute bottom-8 left-1/2 transform -translate-x-1/2 flex items-center gap-4 z-20">
        {/* Pagination Dots */}
        <div className="flex gap-2">
          {slides.map((_, index) => (
            <button
              key={index}
              onClick={() => goToSlide(index)}
              className={`w-3 h-3 rounded-full transition-all duration-300 ${
                index === currentSlide
                  ? currentSlideData.category === "clothes"
                    ? "bg-rose-600 w-8"
                    : "bg-blue-600 w-8"
                  : "bg-white/50 hover:bg-white/70"
              }`}
            />
          ))}
        </div>
      </div>

      {/* Previous/Next Buttons */}
      <Button
        variant="outline"
        size="icon"
        onClick={prevSlide}
        className="absolute left-6 top-1/2 transform -translate-y-1/2 z-20 bg-white/80 hover:bg-white border-white/20 backdrop-blur-sm"
      >
        <ChevronLeft className="h-5 w-5" />
      </Button>

      <Button
        variant="outline"
        size="icon"
        onClick={nextSlide}
        className="absolute right-6 top-1/2 transform -translate-y-1/2 z-20 bg-white/80 hover:bg-white border-white/20 backdrop-blur-sm"
      >
        <ChevronRight className="h-5 w-5" />
      </Button>

      {/* Auto-play indicator */}
      <div className="absolute top-6 right-6 z-20">
        <div className={`w-2 h-2 rounded-full ${isAutoPlaying ? "bg-green-500" : "bg-gray-400"} animate-pulse`} />
      </div>
    </section>
  )
}
