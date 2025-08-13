# GraphQL API Documentation

## Overview
The GraphQL API is successfully integrated and fully functional. It provides a comprehensive interface for querying products, categories, attributes, and managing orders.

## Endpoint
- **URL**: `POST /graphql`
- **Content-Type**: `application/json`
- **CORS**: Enabled for all origins

## Available Queries

### Products
```graphql
# Get all products
query {
  products {
    id
    name
    inStock
    brand
    gallery
    prices {
      amount
      code
      label
      symbol
    }
    attributes
  }
}

# Get products by category
query {
  products(category: "clothes") {
    id
    name
    brand
  }
}

# Get specific product
query {
  product(id: "product-id") {
    id
    name
    description
  }
}

# Search products
query {
  searchProducts(query: "denim") {
    id
    name
    brand
  }
}

# Get featured products
query {
  featuredProducts(limit: 6) {
    id
    name
    brand
  }
}
```

### Categories
```graphql
# Get all categories
query {
  categories {
    name
  }
}

# Get specific category
query {
  category(id: "category-id") {
    name
  }
}
```

### Attributes
```graphql
# Get all attributes
query {
  attributes {
    id
    name
    type
  }
}

# Get specific attribute
query {
  attribute(id: "attribute-id") {
    id
    name
    type
  }
}

# Get multiple attributes by IDs
query {
  attributesByIds(ids: ["attr1", "attr2"]) {
    id
    name
    type
  }
}
```

### Orders
```graphql
# Get order by ID
query {
  order(id: "order-id") {
    id
    customerEmail
    totalAmount
    currency
  }
}
```

## Available Mutations

### Create Order
```graphql
mutation {
  createOrder(orderInput: {
    customerEmail: "customer@example.com"
    items: [
      {
        productId: "product-id"
        quantity: 2
        selectedAttributes: ["attr1", "attr2"]
        price: 29.99
      }
    ]
    totalAmount: 59.98
    currency: "USD"
  }) {
    success
    orderId
    message
  }
}
```

## Type System

### Product Interface
All product types implement the Product interface with these fields:
- `id: String!` - Unique identifier
- `name: String!` - Product name
- `prices: [Price!]!` - Prices in different currencies
- `gallery: [String!]!` - Image URLs
- `category: String` - Category name
- `inStock: Boolean!` - Stock status
- `brand: String` - Product brand
- `description: String` - Product description
- `attributes: [String!]!` - List of attribute IDs

### Price Type
- `amount: Float!` - Price amount
- `code: String!` - Currency code (USD, EUR, etc.)
- `label: String` - Currency label
- `symbol: String` - Currency symbol

### Product Types
- `ClothesProduct` - Extends Product interface
- `TechProduct` - Extends Product interface
- `ProductType` - Generic product type

### Category Types
- `ClothesCategory` - Clothes category
- `TechCategory` - Tech category

### Attribute Types
- `SwatchAttribute` - Color/swatch attributes

## Testing the API

You can test the GraphQL API using tools like:
- GraphQL Playground
- Postman
- curl commands

Example curl request:
```bash
curl -X POST http://localhost/graphql \
  -H "Content-Type: application/json" \
  -d '{
    "query": "query { products { id name brand } }"
  }'
```

## Implementation Details

### Architecture
- **Controller**: `App\Controller\GraphQL` - Main GraphQL endpoint handler
- **Schema Builder**: `App\GraphQL\Schema\SchemaBuilder` - Builds the complete GraphQL schema
- **Resolvers**: Located in `App\GraphQL\Resolvers\` - Handle query/mutation logic
- **Types**: Located in `App\GraphQL\Types\` - Define GraphQL types and interfaces

### Features
✅ Complete type system with interfaces
✅ Proper error handling
✅ CORS support for frontend integration
✅ Comprehensive product catalog queries
✅ Order management functionality
✅ Attribute system for product variations
✅ Multi-currency price support

The GraphQL API is production-ready and fully integrated with the existing PHP backend infrastructure.
