"use client"

import { useState, useEffect, useCallback } from "react"
import { motion, AnimatePresence, useReducedMotion } from "framer-motion"
import { Button } from "@/components/ui/button"
import { ChevronLeft, ChevronRight, Play } from "lucide-react"

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
    title: "PHANTOM V",
    description:
      "With ultra silent sound blaz, you can hear your favorite audio even in super noisy crowd.",
    offer: "40% OFF",
    images: [
      "https://images.unsplash.com/photo-1740711152088-88a009e877bb?q=80&w=1200&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D",
      "https://images.unsplash.com/photo-1591047139829-d91aecb6caea?q=80&w=900&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D",
    ],
    category: "tech",
  },
  {
    id: 2,
    title: "AURORA X",
    description: "High-fidelity audio with immersive processing and real‑time noise reduction.",
    offer: "35% OFF",
    images: [
      "https://images.unsplash.com/photo-1630794180018-433d915c34ac?q=80&w=1400&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D",
      "https://images.unsplash.com/photo-1505740420928-5e560c06d30e?q=80&w=1200&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D",
    ],
    category: "tech",
  },
]

export default function HeroSection() {
  const [currentSlide, setCurrentSlide] = useState(0)
  const [isAutoPlaying, setIsAutoPlaying] = useState(true)
  const shouldReduceMotion = useReducedMotion()

  const totalSlides = slides.length

  const nextSlide = useCallback(() => {
    setCurrentSlide((prev) => (prev + 1) % totalSlides)
    setIsAutoPlaying(false)
    const resume = setTimeout(() => setIsAutoPlaying(true), 10000)
    return () => clearTimeout(resume)
  }, [totalSlides])

  const prevSlide = useCallback(() => {
    setCurrentSlide((prev) => (prev - 1 + totalSlides) % totalSlides)
    setIsAutoPlaying(false)
    const resume = setTimeout(() => setIsAutoPlaying(true), 10000)
    return () => clearTimeout(resume)
  }, [totalSlides])

  const goToSlide = useCallback((index: number) => {
    setCurrentSlide(index)
    setIsAutoPlaying(false)
    const resume = setTimeout(() => setIsAutoPlaying(true), 10000)
    return () => clearTimeout(resume)
  }, [])

  useEffect(() => {
    if (!isAutoPlaying || shouldReduceMotion) return
    const interval = setInterval(() => {
      setCurrentSlide((prev) => (prev + 1) % totalSlides)
    }, 6500)
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
    duration: shouldReduceMotion ? 0 : 0.7,
    ease: "easeInOut" as const,
  }

  return (
    <section
      className="relative w-full overflow-hidden min-h-[78vh] lg:min-h-[86vh] bg-[#0b0d10] text-white"
      onMouseEnter={() => setIsAutoPlaying(false)}
      onMouseLeave={() => setIsAutoPlaying(true)}
      aria-roledescription="carousel"
      aria-label="Featured products"
    >
      {/* Dark gradient background with glow accent */}
      <div className="absolute inset-0 bg-[radial-gradient(1000px_600px_at_20%_30%,rgba(129,140,248,0.25),transparent_60%),radial-gradient(800px_500px_at_70%_20%,rgba(99,102,241,0.18),transparent_55%)]" aria-hidden />
      <div className="absolute inset-0 bg-gradient-to-b from-white/[0.02] to-transparent" aria-hidden />

      <AnimatePresence mode="wait">
        <motion.div
          key={currentSlide}
          initial={{ opacity: 0 }}
          animate={{ opacity: 1 }}
          exit={{ opacity: 0 }}
          transition={baseTransition}
          className="absolute inset-0"
        >
          {/* Content container */}
          <div className="relative h-full mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10 lg:py-14 grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10">
            {/* Left: copy + CTA */}
            <div className="lg:col-span-5 flex items-center">
              <div className="w-full max-w-xl">
                <motion.div
                  initial={{ opacity: 0, y: shouldReduceMotion ? 0 : 18 }}
                  animate={{ opacity: 1, y: 0 }}
                  transition={{ ...baseTransition, delay: shouldReduceMotion ? 0 : 0.1 }}
                  className="text-xs tracking-[0.2em] text-white/60 mb-3"
                >
                  SERIES
                </motion.div>

                <motion.h1
                  initial={{ opacity: 0, y: shouldReduceMotion ? 0 : 20 }}
                  animate={{ opacity: 1, y: 0 }}
                  transition={{ ...baseTransition, delay: shouldReduceMotion ? 0 : 0.2 }}
                  className="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight"
                >
                  {currentSlideData.title}
                </motion.h1>

                <motion.p
                  initial={{ opacity: 0, y: shouldReduceMotion ? 0 : 20 }}
                  animate={{ opacity: 1, y: 0 }}
                  transition={{ ...baseTransition, delay: shouldReduceMotion ? 0 : 0.28 }}
                  className="mt-4 text-sm sm:text-base text-white/70 max-w-md"
                >
                  {currentSlideData.description}
                </motion.p>

                <motion.div
                  initial={{ opacity: 0, y: shouldReduceMotion ? 0 : 20 }}
                  animate={{ opacity: 1, y: 0 }}
                  transition={{ ...baseTransition, delay: shouldReduceMotion ? 0 : 0.35 }}
                  className="mt-6 flex items-center gap-3"
                >
                  <Button
                    size="lg"
                    className="rounded-xl bg-indigo-500 hover:bg-indigo-600 text-white px-6 shadow-[0_10px_40px_rgba(99,102,241,0.35)] focus-visible:ring-2 focus-visible:ring-indigo-300"
                    aria-label="Add to cart"
                  >
                    Add to cart
                  </Button>
                  <Button
                    variant="outline"
                    size="lg"
                    className="rounded-xl border-white/15 bg-white/5 hover:bg-white/10 text-white backdrop-blur-sm"
                    aria-label="Watch reel"
                  >
                    <Play className="h-4 w-4 mr-2" /> Watch reel
                  </Button>
                </motion.div>

                <motion.div
                  initial={{ opacity: 0 }}
                  animate={{ opacity: 1 }}
                  transition={{ ...baseTransition, delay: 0.5 }}
                  className="mt-6 text-xs text-white/50"
                  aria-label={`Limited time offer ${currentSlideData.offer}`}
                >
                  {currentSlideData.offer} · Free express shipping
                </motion.div>
              </div>
            </div>

            {/* Right: product spotlight */}
            <div className="lg:col-span-7 relative">
              <div className="absolute -inset-6 sm:-inset-8 rounded-[28px] bg-gradient-to-tr from-indigo-500/20 via-fuchsia-500/10 to-transparent blur-3xl" aria-hidden />
              <motion.div
                initial={{ opacity: 0, scale: shouldReduceMotion ? 1 : 0.96, y: shouldReduceMotion ? 0 : 14 }}
                animate={{ opacity: 1, scale: 1, y: 0 }}
                transition={{ ...baseTransition, delay: shouldReduceMotion ? 0 : 0.25 }}
                className="relative rounded-[24px] bg-[linear-gradient(180deg,rgba(255,255,255,0.06),rgba(255,255,255,0.02))] border border-white/10 shadow-[inset_0_1px_0_rgba(255,255,255,0.08),0_20px_80px_rgba(0,0,0,0.45)] p-3 sm:p-4 lg:p-6"
              >
                <div className="grid grid-cols-2 gap-3 sm:gap-4 lg:gap-6">
                  {currentSlideData.images.map((image, index) => (
                    <div key={index} className="relative overflow-hidden rounded-2xl bg-black/40">
                      <motion.img
                        src={image}
                        alt={`${currentSlideData.category} item ${index + 1}`}
                        className="w-full h-full object-cover aspect-[16/11] sm:aspect-[4/3]"
                        loading="lazy"
                        decoding="async"
                        sizes="(max-width: 640px) 50vw, (max-width: 1024px) 40vw, 36vw"
                        whileHover={shouldReduceMotion ? undefined : { scale: 1.03 }}
                        transition={shouldReduceMotion ? undefined : { duration: 0.3 }}
                      />
                      <div className="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/30 via-transparent to-transparent" aria-hidden />
                    </div>
                  ))}
                </div>
              </motion.div>

              {/* Vertical pagination */}
              <div className="absolute right-0 top-1/2 -translate-y-1/2 flex flex-col items-center gap-2 pr-1 sm:pr-2">
                {slides.map((_, index) => (
                  <button
                    key={index}
                    onClick={() => goToSlide(index)}
                    className={`h-2.5 w-1.5 rounded-full transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-300 ${
                      index === currentSlide ? "bg-indigo-500 h-7" : "bg-white/30 hover:bg-white/60"
                    }`}
                    aria-label={`Go to slide ${index + 1}`}
                    aria-current={index === currentSlide ? "true" : undefined}
                  />
                ))}
              </div>
            </div>
          </div>
        </motion.div>
      </AnimatePresence>

      {/* Prev/Next */}
      <Button
        variant="outline"
        size="icon"
        onClick={prevSlide}
        className="absolute left-4 sm:left-6 top-1/2 -translate-y-1/2 z-20 border-white/15 bg-white/5 hover:bg-white/10 text-white backdrop-blur-sm"
        aria-label="Previous slide"
      >
        <ChevronLeft className="h-5 w-5" />
      </Button>
      <Button
        variant="outline"
        size="icon"
        onClick={nextSlide}
        className="absolute right-4 sm:right-6 top-1/2 -translate-y-1/2 z-20 border-white/15 bg-white/5 hover:bg-white/10 text-white backdrop-blur-sm"
        aria-label="Next slide"
      >
        <ChevronRight className="h-5 w-5" />
      </Button>

      {/* Auto-play indicator */}
      <div className="absolute top-4 sm:top-6 right-4 sm:right-6 z-20">
        <div
          className={`w-2 h-2 rounded-full ${isAutoPlaying ? "bg-emerald-400" : "bg-zinc-500"} ${shouldReduceMotion ? "" : "animate-pulse"}`}
          aria-label={isAutoPlaying ? "Autoplay on" : "Autoplay off"}
        />
      </div>
    </section>
  )
}
