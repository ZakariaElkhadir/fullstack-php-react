"use client"

import { useState, useEffect, useCallback } from "react"
import { motion, AnimatePresence, useReducedMotion } from "framer-motion"
import { Button } from "@/components/ui/button"
import { ChevronLeft, ChevronRight, Play, Star, Check, Heart } from "lucide-react"

interface Slide {
  id: number
  title: string
  description: string
  originalPrice: string
  salePrice: string
  offer: string
  rating: number
  reviews: number
  features: string[]
  images: [string, string]
  category: "clothes" | "tech"
}

const slides: Slide[] = [
  {
    id: 2,
    title: "AURORA X",
    description: "Revolutionary high-fidelity audio with immersive spatial processing and real‑time adaptive noise reduction for the ultimate listening experience.",
    originalPrice: "$399.99",
    salePrice: "$259.99",
    offer: "35% OFF",
    rating: 4.9,
    reviews: 1923,
    features: ["Spatial Audio", "Quick Charge", "Smart Assistant"],
    images: [
      "https://images.unsplash.com/photo-1630794180018-433d915c34ac?q=80&w=1400&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D",
      "https://images.unsplash.com/photo-1505740420928-5e560c06d30e?q=80&w=1200&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D",
    ],
    category: "tech",
  },
  {
  id: 3,
  title: "URBAN EDGE",
  description: "Premium men's streetwear collection featuring ultra-soft organic cotton blend with modern fit and sustainable craftsmanship for everyday comfort.",
  originalPrice: "$89.99",
  salePrice: "$54.99",
  offer: "38% OFF",
  rating: 4.7,
  reviews: 3214,
  features: ["Organic Cotton", "Sustainable", "Modern Fit"],
  images: [
    "https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?q=80&w=1200&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D",
    "https://images.unsplash.com/photo-1556906781-9a412961c28c?q=80&w=900&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D",
  ],
  category: "clothes",
},
]

export default function HeroSection() {
  const [currentSlide, setCurrentSlide] = useState(0)
  const [isAutoPlaying, setIsAutoPlaying] = useState(true)
  const [isLiked, setIsLiked] = useState(false)
  const shouldReduceMotion = useReducedMotion()

  const totalSlides = slides.length

  const nextSlide = useCallback(() => {
    setCurrentSlide((prev) => (prev + 1) % totalSlides)
    setIsAutoPlaying(false)
    const resume = setTimeout(() => setIsAutoPlaying(true), 12000)
    return () => clearTimeout(resume)
  }, [totalSlides])

  const prevSlide = useCallback(() => {
    setCurrentSlide((prev) => (prev - 1 + totalSlides) % totalSlides)
    setIsAutoPlaying(false)
    const resume = setTimeout(() => setIsAutoPlaying(true), 12000)
    return () => clearTimeout(resume)
  }, [totalSlides])

  const goToSlide = useCallback((index: number) => {
    setCurrentSlide(index)
    setIsAutoPlaying(false)
    const resume = setTimeout(() => setIsAutoPlaying(true), 12000)
    return () => clearTimeout(resume)
  }, [])

  useEffect(() => {
    if (!isAutoPlaying || shouldReduceMotion) return
    const interval = setInterval(() => {
      setCurrentSlide((prev) => (prev + 1) % totalSlides)
    }, 7500)
    return () => clearInterval(interval)
  }, [isAutoPlaying, shouldReduceMotion, totalSlides])

  useEffect(() => {
    const onKeyDown = (e: KeyboardEvent) => {
      if (e.key === "ArrowRight") nextSlide()
      if (e.key === "ArrowLeft") prevSlide()
      if (e.key === " ") {
        e.preventDefault()
        setIsAutoPlaying((p) => !p)
      }
    }
    window.addEventListener("keydown", onKeyDown)
    return () => window.removeEventListener("keydown", onKeyDown)
  }, [nextSlide, prevSlide])

  const currentSlideData = slides[currentSlide]

  const baseTransition = {
    duration: shouldReduceMotion ? 0 : 0.8,
    ease: [0.25, 0.1, 0.25, 1] as const,
  }

  const staggerChildren = {
    animate: {
      transition: {
        staggerChildren: shouldReduceMotion ? 0 : 0.1,
      },
    },
  }

  const slideUpVariants = {
    initial: { opacity: 0, y: shouldReduceMotion ? 0 : 24 },
    animate: { opacity: 1, y: 0 },
  }

  return (
    <section
      className="relative w-full overflow-hidden min-h-[85vh] lg:min-h-[92vh] bg-gradient-to-br from-[#0a0b0f] via-[#0d1117] to-[#0f1419] text-white"
      onMouseEnter={() => setIsAutoPlaying(false)}
      onMouseLeave={() => setIsAutoPlaying(true)}
      aria-roledescription="carousel"
      aria-label="Featured products"
    >
      {/* Enhanced background with multiple gradient layers */}
      <div className="absolute inset-0">
        <div className="absolute inset-0 bg-[radial-gradient(1200px_800px_at_25%_35%,rgba(34,197,94,0.28),transparent_65%)]" />
        <div className="absolute inset-0 bg-[radial-gradient(900px_600px_at_75%_25%,rgba(16,185,129,0.2),transparent_60%)]" />
        <div className="absolute inset-0 bg-[radial-gradient(600px_400px_at_50%_80%,rgba(6,182,212,0.15),transparent_55%)]" />
        <div className="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-black/20" />
        <div className="absolute inset-0 opacity-30 bg-[radial-gradient(circle_at_center,rgba(255,255,255,0.02)_1px,transparent_1px)] bg-[length:60px_60px]" />
      </div>

      <AnimatePresence mode="wait">
        <motion.div
          key={currentSlide}
          initial={{ opacity: 0 }}
          animate={{ opacity: 1 }}
          exit={{ opacity: 0 }}
          transition={baseTransition}
          className="absolute inset-0"
          variants={staggerChildren}
        >
          {/* Main content container */}
          <div className="relative h-full mx-auto max-w-7xl px-3 xs:px-4 sm:px-6 lg:px-8 py-8 xs:py-10 sm:py-12 lg:py-16">
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6 xs:gap-8 sm:gap-12 lg:gap-16 h-full items-center">
              
              {/* Left: Enhanced Content Section */}
              <motion.div
                variants={staggerChildren}
                initial="initial"
                animate="animate"
                className="space-y-4 xs:space-y-5 sm:space-y-6 lg:space-y-8 max-w-2xl order-2 lg:order-1"
              >
                {/* Product category badge */}
                <motion.div variants={slideUpVariants} transition={baseTransition}>
                  <span className="inline-flex items-center px-3 xs:px-4 py-1.5 xs:py-2 rounded-full bg-gradient-to-r from-green-500/20 to-emerald-500/20 border border-green-500/30 text-xs xs:text-sm font-medium text-green-300 backdrop-blur-sm">
                    <span className="w-1.5 h-1.5 xs:w-2 xs:h-2 bg-green-400 rounded-full mr-2 animate-pulse" />
                    {currentSlideData.category.toUpperCase()} SERIES
                  </span>
                </motion.div>

                {/* Product title */}
                <motion.h1
                  variants={slideUpVariants}
                  transition={{ ...baseTransition, delay: 0.1 }}
                  className="text-2xl xs:text-3xl sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-black tracking-tight bg-gradient-to-r from-white via-gray-100 to-gray-300 bg-clip-text text-transparent leading-[1.1] sm:leading-tight"
                >
                  {currentSlideData.title}
                </motion.h1>

                {/* Rating and reviews */}
                <motion.div
                  variants={slideUpVariants}
                  transition={{ ...baseTransition, delay: 0.15 }}
                  className="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4"
                >
                  <div className="flex items-center gap-1">
                    {[...Array(5)].map((_, i) => (
                      <Star
                        key={i}
                        className={`w-4 h-4 sm:w-5 sm:h-5 ${
                          i < Math.floor(currentSlideData.rating)
                            ? "text-yellow-400 fill-yellow-400"
                            : "text-gray-600"
                        }`}
                      />
                    ))}
                    <span className="ml-2 text-base sm:text-lg font-semibold text-white">
                      {currentSlideData.rating}
                    </span>
                  </div>
                  <span className="text-gray-400 hidden sm:block">•</span>
                  <span className="text-sm sm:text-base text-gray-300">
                    {currentSlideData.reviews.toLocaleString()} reviews
                  </span>
                </motion.div>

                {/* Product description */}
                <motion.p
                  variants={slideUpVariants}
                  transition={{ ...baseTransition, delay: 0.2 }}
                  className="text-sm xs:text-base sm:text-lg lg:text-xl text-gray-300 leading-relaxed max-w-xl"
                >
                  {currentSlideData.description}
                </motion.p>

                {/* Key features */}
                <motion.div
                  variants={slideUpVariants}
                  transition={{ ...baseTransition, delay: 0.25 }}
                  className="flex flex-wrap gap-2 xs:gap-3"
                >
                  {currentSlideData.features.map((feature, index) => (
                    <div
                      key={index}
                      className="flex items-center gap-1.5 xs:gap-2 px-2.5 xs:px-3 sm:px-4 py-1.5 xs:py-2 bg-white/5 rounded-lg xs:rounded-xl border border-white/10 backdrop-blur-sm"
                    >
                      <Check className="w-3 h-3 xs:w-4 xs:h-4 text-green-400" />
                      <span className="text-xs xs:text-sm font-medium text-gray-200">{feature}</span>
                    </div>
                  ))}
                </motion.div>

                {/* Pricing section */}
                <motion.div
                  variants={slideUpVariants}
                  transition={{ ...baseTransition, delay: 0.3 }}
                  className="space-y-1.5 xs:space-y-2 sm:space-y-3"
                >
                  <div className="flex flex-col xs:flex-row xs:items-center gap-1.5 xs:gap-2 sm:gap-4">
                    <span className="text-2xl xs:text-3xl sm:text-4xl font-bold text-white">
                      {currentSlideData.salePrice}
                    </span>
                    <span className="text-base xs:text-lg sm:text-xl text-gray-500 line-through">
                      {currentSlideData.originalPrice}
                    </span>
                    <span className="px-2 py-0.5 xs:px-2.5 xs:py-1 sm:px-3 sm:py-1 bg-gradient-to-r from-red-500 to-red-600 text-white text-xs xs:text-sm font-bold rounded-full w-fit">
                      {currentSlideData.offer}
                    </span>
                  </div>
                  <p className="text-xs xs:text-sm text-green-400 font-medium">
                    Save ${(parseFloat(currentSlideData.originalPrice.slice(1)) - parseFloat(currentSlideData.salePrice.slice(1))).toFixed(2)} • Free express shipping
                  </p>
                </motion.div>

                {/* Action buttons */}
                <motion.div
                  variants={slideUpVariants}
                  transition={{ ...baseTransition, delay: 0.35 }}
                  className="flex flex-col xs:flex-row gap-3 xs:gap-4 pt-1 xs:pt-2"
                >
                  <Button
                    size="lg"
                    className="group relative overflow-hidden rounded-xl xs:rounded-2xl bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-400 hover:to-emerald-500 text-white px-6 xs:px-8 py-3 xs:py-4 text-base xs:text-lg font-semibold shadow-[0_15px_40px_rgba(34,197,94,0.4)] hover:shadow-[0_20px_50px_rgba(34,197,94,0.5)] transition-all duration-300 hover:scale-105 w-full xs:w-auto"
                    aria-label="Add to cart"
                  >
                    <span className="relative z-10">Add to Cart</span>
                    <div className="absolute inset-0 bg-gradient-to-r from-white/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300" />
                  </Button>
                  
                  <div className="flex gap-2 xs:gap-3">
                    <Button
                      variant="outline"
                      size="lg"
                      className="rounded-xl xs:rounded-2xl border-white/20 bg-white/5 hover:bg-white/10 text-white backdrop-blur-sm px-4 xs:px-6 py-3 xs:py-4 hover:scale-105 transition-all duration-300 flex-1 xs:flex-none"
                      aria-label="Watch product video"
                    >
                      <Play className="w-4 h-4 xs:w-5 xs:h-5 mr-1 xs:mr-2" /> 
                      <span className="text-sm xs:text-base">Demo</span>
                    </Button>
                    
                    <Button
                      variant="outline"
                      size="lg"
                      onClick={() => setIsLiked(!isLiked)}
                      className={`rounded-xl xs:rounded-2xl border-white/20 backdrop-blur-sm px-3 xs:px-4 py-3 xs:py-4 hover:scale-105 transition-all duration-300 ${
                        isLiked 
                          ? "bg-red-500/20 border-red-400/40 text-red-300" 
                          : "bg-white/5 hover:bg-white/10 text-white"
                      }`}
                      aria-label={isLiked ? "Remove from wishlist" : "Add to wishlist"}
                    >
                      <Heart className={`w-4 h-4 xs:w-5 xs:h-5 ${isLiked ? "fill-red-400" : ""}`} />
                    </Button>
                  </div>
                </motion.div>
              </motion.div>

              {/* Right: Revolutionary Image Layout */}
              <motion.div
                variants={slideUpVariants}
                transition={{ ...baseTransition, delay: 0.2 }}
                className="relative h-full min-h-[400px] sm:min-h-[500px] lg:min-h-[700px] order-1 lg:order-2"
              >
                {/* Background glow effects */}
                <div className="absolute -inset-6 sm:-inset-12 bg-gradient-to-tr from-green-500/30 via-emerald-500/20 to-cyan-500/20 blur-3xl rounded-full" />
                <div className="absolute -inset-4 sm:-inset-8 bg-gradient-to-bl from-blue-500/20 via-purple-500/15 to-transparent blur-2xl rounded-full" />
                
                {/* Main product image - responsive sizing */}
                <motion.div
                  initial={{ opacity: 0, scale: 0.9, y: 20 }}
                  animate={{ opacity: 1, scale: 1, y: 0 }}
                  transition={{ ...baseTransition, delay: 0.3 }}
                  className="relative z-10 w-full h-[70%] sm:h-[75%] rounded-[24px] sm:rounded-[32px] overflow-hidden shadow-[0_20px_60px_rgba(0,0,0,0.5)] sm:shadow-[0_30px_100px_rgba(0,0,0,0.6)] group"
                  whileHover={shouldReduceMotion ? undefined : { scale: 1.02, rotateY: 2 }}
                >
                  <div className="absolute inset-0 bg-gradient-to-tr from-white/10 via-white/5 to-transparent z-10" />
                  <div className="absolute inset-0 bg-gradient-to-br from-transparent via-transparent to-black/40 z-10" />
                  <img
                    src={currentSlideData.images[0]}
                    alt={`${currentSlideData.title} main view`}
                    className="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                    loading="lazy"
                    decoding="async"
                    sizes="(max-width: 640px) 100vw, (max-width: 1024px) 100vw, 50vw"
                  />
                  
                  {/* Floating specs card - responsive positioning */}
                  <motion.div
                    initial={{ opacity: 0, x: 20 }}
                    animate={{ opacity: 1, x: 0 }}
                    transition={{ delay: 0.6 }}
                    className="absolute top-3 right-3 sm:top-6 sm:right-6 bg-black/40 backdrop-blur-md rounded-xl sm:rounded-2xl p-2 sm:p-4 border border-white/20"
                  >
                    <div className="text-xs text-gray-300 mb-1">SPECS</div>
                    <div className="text-xs sm:text-sm font-semibold text-white">
                      {currentSlideData.category === "tech" ? "Premium Performance" : "Premium Fabric"}
                    </div>
                  </motion.div>
                </motion.div>

                {/* Secondary image - mobile-friendly positioning */}
                <motion.div
                  initial={{ opacity: 0, scale: 0.8, x: -30 }}
                  animate={{ opacity: 1, scale: 1, x: 0 }}
                  transition={{ ...baseTransition, delay: 0.5 }}
                  className="absolute bottom-0 left-0 sm:-bottom-4 sm:-left-4 lg:-bottom-8 lg:-left-8 w-32 h-24 sm:w-48 sm:h-36 lg:w-80 lg:h-60 rounded-[16px] sm:rounded-[20px] lg:rounded-[24px] overflow-hidden shadow-[0_15px_40px_rgba(0,0,0,0.4)] sm:shadow-[0_25px_80px_rgba(0,0,0,0.5)] group z-20"
                  whileHover={shouldReduceMotion ? undefined : { scale: 1.05, rotate: -1 }}
                >
                  <div className="absolute inset-0 bg-gradient-to-tr from-cyan-500/20 via-transparent to-purple-500/20 z-10" />
                  <img
                    src={currentSlideData.images[1]}
                    alt={`${currentSlideData.title} detail view`}
                    className="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                    loading="lazy"
                    decoding="async"
                    sizes="(max-width: 640px) 35vw, (max-width: 1024px) 40vw, 25vw"
                  />
                  
                  {/* Price badge on secondary image - responsive sizing */}
                  <motion.div
                    initial={{ opacity: 0, scale: 0.8 }}
                    animate={{ opacity: 1, scale: 1 }}
                    transition={{ delay: 0.8 }}
                    className="absolute -top-1 -right-1 sm:-top-2 sm:-right-2 lg:-top-3 lg:-right-3 bg-gradient-to-r from-red-500 to-red-600 text-white px-2 py-1 sm:px-3 sm:py-1.5 lg:px-4 lg:py-2 rounded-full text-xs sm:text-sm font-bold shadow-lg"
                  >
                    {currentSlideData.offer}
                  </motion.div>
                </motion.div>

                {/* Decorative geometric elements */}
                <motion.div
                  initial={{ opacity: 0, rotate: -45 }}
                  animate={{ opacity: 0.3, rotate: 0 }}
                  transition={{ delay: 0.7, duration: 1 }}
                  className="absolute top-1/4 right-0 w-32 h-32 border border-white/20 rounded-lg rotate-12 -z-10"
                />
                <motion.div
                  initial={{ opacity: 0, scale: 0 }}
                  animate={{ opacity: 0.2, scale: 1 }}
                  transition={{ delay: 0.9, duration: 0.8 }}
                  className="absolute bottom-1/3 right-1/4 w-24 h-24 bg-gradient-to-r from-green-400/30 to-blue-400/30 rounded-full blur-xl -z-10"
                />
              </motion.div>
            </div>
          </div>
        </motion.div>
      </AnimatePresence>

      {/* Enhanced Navigation Controls - Mobile optimized */}
      <Button
        variant="outline"
        size="icon"
        onClick={prevSlide}
        className="absolute left-2 xs:left-4 sm:left-6 top-1/2 -translate-y-1/2 z-30 w-10 h-10 xs:w-12 xs:h-12 rounded-xl xs:rounded-2xl border-white/20 bg-black/20 hover:bg-black/40 text-white backdrop-blur-md shadow-lg hover:scale-110 transition-all duration-300"
        aria-label="Previous slide"
      >
        <ChevronLeft className="h-5 w-5 xs:h-6 xs:w-6" />
      </Button>
      <Button
        variant="outline"
        size="icon"
        onClick={nextSlide}
        className="absolute right-2 xs:right-4 sm:right-6 top-1/2 -translate-y-1/2 z-30 w-10 h-10 xs:w-12 xs:h-12 rounded-xl xs:rounded-2xl border-white/20 bg-black/20 hover:bg-black/40 text-white backdrop-blur-md shadow-lg hover:scale-110 transition-all duration-300"
        aria-label="Next slide"
      >
        <ChevronRight className="h-5 w-5 xs:h-6 xs:w-6" />
      </Button>

      {/* Mobile-optimized Auto-play indicator */}
      <div className="absolute top-3 xs:top-4 sm:top-6 lg:top-8 right-3 xs:right-4 sm:right-6 lg:right-8 z-30">
        <div className="flex items-center gap-2 xs:gap-3 px-2 xs:px-3 sm:px-4 py-1.5 xs:py-2 bg-black/20 backdrop-blur-md rounded-lg xs:rounded-xl sm:rounded-2xl border border-white/20">
          <div
            className={`w-2 h-2 xs:w-3 xs:h-3 rounded-full transition-all duration-300 ${
              isAutoPlaying 
                ? "bg-green-400 shadow-[0_0_12px_rgba(34,197,94,0.6)]" 
                : "bg-gray-500"
            } ${shouldReduceMotion ? "" : isAutoPlaying ? "animate-pulse" : ""}`}
            aria-label={isAutoPlaying ? "Autoplay on" : "Autoplay off"}
          />
          <span className="text-xs font-medium text-white/80 hidden xs:inline">
            {isAutoPlaying ? "AUTO" : "PAUSE"}
          </span>
          <div className="text-xs text-white/60">
            {currentSlide + 1}/{totalSlides}
          </div>
        </div>
      </div>

      {/* Mobile-optimized Progress indicator */}
      <div className="absolute bottom-4 xs:bottom-6 left-1/2 -translate-x-1/2 z-30">
        <div className="flex items-center gap-1.5 xs:gap-2 px-3 xs:px-4 py-1.5 xs:py-2 bg-black/20 backdrop-blur-md rounded-full border border-white/20">
          {slides.map((_, index) => (
            <button
              key={index}
              onClick={() => goToSlide(index)}
              className={`relative overflow-hidden rounded-full transition-all duration-300 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-green-400/50 ${
                index === currentSlide 
                  ? "w-6 xs:w-8 h-1.5 xs:h-2 bg-gradient-to-r from-green-400 to-emerald-500" 
                  : "w-1.5 h-1.5 xs:w-2 xs:h-2 bg-white/40 hover:bg-white/70 hover:scale-125"
              }`}
              aria-label={`Go to slide ${index + 1}`}
              aria-current={index === currentSlide ? "true" : undefined}
            />
          ))}
        </div>
      </div>
    </section>
  )
}
