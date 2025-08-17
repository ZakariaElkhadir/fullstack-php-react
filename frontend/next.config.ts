// next.config.ts
import type { NextConfig } from "next";

const nextConfig: NextConfig = {
  images: {
    remotePatterns: [
      {
        protocol: "https",
        hostname: "images.unsplash.com",
      },
      // Add other domains if needed
    ],
    // Or alternatively use the domains array (legacy approach):
    // domains: ['images.unsplash.com'],
  },
};

export default nextConfig;
