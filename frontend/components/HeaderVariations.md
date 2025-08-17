# Header Color Variations for Your Website

Based on your dark theme with green accents, here are the best header color options:

## Current Implementation (Recommended)
```tsx
// Dark green with glass effect - matches your design system
className="bg-green-dark/90 backdrop-blur-lg border-b border-green-light/20"
```

## Alternative Options:

### Option 2: Transparent Glass Effect
```tsx
className="bg-black/20 backdrop-blur-xl border-b border-white/10"
```
- Creates a floating glass effect
- Allows background to show through
- Very modern and elegant

### Option 3: Gradient Header
```tsx
className="bg-gradient-to-r from-green-dark via-slate-800 to-green-dark border-b border-green-light/20"
```
- Matches your product section gradient
- Creates visual continuity
- Dynamic and eye-catching

### Option 4: Full Green Theme
```tsx
className="bg-green-sage/90 backdrop-blur-lg border-b border-green-success/30"
```
- Lighter green for better contrast
- Still maintains the green theme
- Good for readability

### Option 5: Minimalist Dark
```tsx
className="bg-slate-900/95 backdrop-blur-lg border-b border-green-light/20"
```
- Clean and minimal
- Focuses attention on content
- Professional look

## Button Styling Matches:

For any of these headers, use these button styles:

**Active state:**
```tsx
"bg-green-success text-white shadow-md transform scale-105"
```

**Inactive state:**
```tsx
"text-green-sage hover:text-white hover:bg-green-light/20 backdrop-blur-sm"
```

## Why These Work:

1. **Color Harmony**: They use your existing green palette
2. **Contrast**: Good readability against dark backgrounds
3. **Modern**: Glass morphism and backdrop blur effects
4. **Cohesive**: Matches your product section design
5. **Professional**: Clean and sophisticated appearance

The current implementation (Option 1) is recommended because it:
- Uses your custom `--green-dark` color variable
- Maintains design consistency
- Provides excellent contrast for text
- Creates a sophisticated glass effect with backdrop blur
