import React, { useState } from 'react';
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";

interface AttributeItem {
  id: string;
  displayValue: string;
  value: string;
}

interface Attribute {
  id: string;
  name: string;
  type: string;
  items: AttributeItem[];
}

interface AttributeSelectorProps {
  attributes: Attribute[];
  selectedAttributes: Record<string, string>;
  onAttributeChange: (attributeId: string, itemId: string) => void;
}

export const AttributeSelector: React.FC<AttributeSelectorProps> = ({
  attributes,
  selectedAttributes,
  onAttributeChange,
}) => {
  if (!attributes || attributes.length === 0) {
    return null;
  }

  return (
    <div className="space-y-6">
      {attributes.map((attribute) => (
        <div key={attribute.id} className="space-y-3">
          <div className="flex items-center gap-2">
            <h3 className="text-lg font-semibold text-green-dark">
              {attribute.name}
            </h3>
            {selectedAttributes[attribute.id] && (
              <Badge 
                variant="secondary" 
                className="bg-green-success/10 text-green-success border-green-success/20"
              >
                {attribute.items.find(item => item.id === selectedAttributes[attribute.id])?.displayValue}
              </Badge>
            )}
          </div>

          {attribute.type === 'swatch' ? (
            // Color swatches
            <div className="flex flex-wrap gap-3">
              {attribute.items.slice(0, 6).map((item) => { // Limit to 6 colors to avoid clutter
                const isSelected = selectedAttributes[attribute.id] === item.id;
                const colorValue = item.value.startsWith('#') ? item.value : 
                  getColorFromName(item.displayValue);
                
                return (
                  <button
                    key={item.id}
                    onClick={() => onAttributeChange(attribute.id, item.id)}
                    className={`w-12 h-12 rounded-full border-4 transition-all duration-200 hover:scale-110 ${
                      isSelected 
                        ? 'border-green-success shadow-lg ring-2 ring-green-success/30' 
                        : 'border-gray-200 hover:border-green-sage'
                    }`}
                    style={{ backgroundColor: colorValue }}
                    title={item.displayValue}
                    aria-label={`Select ${item.displayValue}`}
                  >
                    {isSelected && (
                      <div className="w-full h-full rounded-full flex items-center justify-center">
                        <svg className="w-6 h-6 text-white drop-shadow-sm" fill="currentColor" viewBox="0 0 20 20">
                          <path fillRule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clipRule="evenodd" />
                        </svg>
                      </div>
                    )}
                  </button>
                );
              })}
            </div>
          ) : (
            // Text-based attributes (Size, Storage, etc.)
            <div className="flex flex-wrap gap-2">
              {attribute.items.slice(0, 8).map((item) => { // Limit to 8 options to avoid clutter
                const isSelected = selectedAttributes[attribute.id] === item.id;
                const isRelevantForClothes = attribute.name === 'Size' && 
                  ['XS', 'S', 'M', 'L', 'XL', 'XXL', 'Small', 'Medium', 'Large', 'Extra Large', 'Extra Small'].some(size => 
                    item.displayValue.includes(size) || item.value.includes(size)
                  );
                
                // For size attribute, only show relevant sizes
                if (attribute.name === 'Size' && !isRelevantForClothes && item.displayValue.match(/^\d+$/)) {
                  // Skip numeric-only sizes for clothes (these are likely shoe sizes)
                  return null;
                }
                
                return (
                  <Button
                    key={item.id}
                    variant={isSelected ? "default" : "outline"}
                    size="sm"
                    onClick={() => onAttributeChange(attribute.id, item.id)}
                    className={`min-w-[60px] transition-all duration-200 ${
                      isSelected
                        ? 'bg-green-success text-white hover:bg-green-success/90 ring-2 ring-green-success/30'
                        : 'border-green-sage/30 text-green-dark hover:border-green-success hover:bg-green-success/10'
                    }`}
                  >
                    {item.displayValue}
                  </Button>
                );
              })}
            </div>
          )}
        </div>
      ))}
    </div>
  );
};

// Helper function to get color values from color names
function getColorFromName(colorName: string): string {
  const colorMap: Record<string, string> = {
    'Black': '#000000',
    'White': '#FFFFFF',
    'Red': '#FF0000',
    'Blue': '#0066CC',
    'Green': '#22C55E',
    'Yellow': '#FFFF00',
    'Orange': '#FFA500',
    'Purple': '#800080',
    'Pink': '#FFC0CB',
    'Brown': '#8B4513',
    'Gray': '#808080',
    'Grey': '#808080',
    'Navy': '#000080',
    'Silver': '#C0C0C0',
    'Gold': '#FFD700',
    'Rose Gold': '#E8B4B8',
    'Light Blue': '#87CEEB',
    'Dark Blue': '#003366',
    'Midnight Black': '#191970',
    'Pearl White': '#F8F6F0',
    'Ocean Blue': '#006994',
  };
  
  return colorMap[colorName] || '#6B7280'; // Default to gray if color not found
}