# GraphQL API cURL Commands

## Prerequisites
1. Start  PHP server: `php -S localhost:8000 -t public`
2. Install jq for better JSON formatting: `sudo apt-get install jq` (optional)

## Basic Commands

### 1. Get All Products
```bash
curl -X POST http://localhost:8000/graphql \
  -H "Content-Type: application/json" \
  -d '{
    "query": "query { products { id name brand inStock gallery prices { amount code label symbol } attributes } }"
  }'
```

### 2. Get Products by Category
```bash
# Clothes products
curl -X POST http://localhost:8000/graphql \
  -H "Content-Type: application/json" \
  -d '{
    "query": "query { products(category: \"clothes\") { id name brand inStock prices { amount code } } }"
  }'

# Tech products
curl -X POST http://localhost:8000/graphql \
  -H "Content-Type: application/json" \
  -d '{
    "query": "query { products(category: \"tech\") { id name brand inStock prices { amount code } } }"
  }'
```

### 3. Get Specific Product
```bash
curl -X POST http://localhost:8000/graphql \
  -H "Content-Type: application/json" \
  -d '{
    "query": "query { product(id: \"classic-denim-jacket\") { id name brand description inStock gallery prices { amount code label symbol } attributes } }"
  }'
```

### 4. Search Products
```bash
curl -X POST http://localhost:8000/graphql \
  -H "Content-Type: application/json" \
  -d '{
    "query": "query { searchProducts(query: \"denim\") { id name brand } }"
  }'
```

### 5. Get Featured Products
```bash
curl -X POST http://localhost:8000/graphql \
  -H "Content-Type: application/json" \
  -d '{
    "query": "query { featuredProducts(limit: 6) { id name brand inStock } }"
  }'
```

### 6. Get All Categories
```bash
curl -X POST http://localhost:8000/graphql \
  -H "Content-Type: application/json" \
  -d '{
    "query": "query { categories { name } }"
  }'
```

### 7. Get All Attributes
```bash
curl -X POST http://localhost:8000/graphql \
  -H "Content-Type: application/json" \
  -d '{
    "query": "query { attributes { id name type } }"
  }'
```

### 8. Create Order (Mutation)
```bash
curl -X POST http://localhost:8000/graphql \
  -H "Content-Type: application/json" \
  -d '{
    "query": "mutation { createOrder(orderInput: { customerEmail: \"test@example.com\", items: [{ productId: \"classic-denim-jacket\", quantity: 2, selectedAttributes: [\"size-medium\"], price: 89.99 }], totalAmount: 179.98, currency: \"USD\" }) { success orderId message } }"
  }'
```

### 9. Get Order by ID
```bash
# Replace ORDER_ID with actual order ID from create order response
curl -X POST http://localhost:8000/graphql \
  -H "Content-Type: application/json" \
  -d '{
    "query": "query { order(id: \"ORDER_ID\") { id customerEmail totalAmount currency status items { productId quantity price } } }"
  }'
```

## Advanced Queries

### Complex Query - All Data
```bash
curl -X POST http://localhost:8000/graphql \
  -H "Content-Type: application/json" \
  -d '{
    "query": "query CompleteData { products { id name brand description inStock gallery category prices { amount code label symbol } attributes } categories { name } attributes { id name type } }"
  }'
```

### Schema Introspection
```bash
curl -X POST http://localhost:8000/graphql \
  -H "Content-Type: application/json" \
  -d '{
    "query": "query { __schema { queryType { name fields { name description } } mutationType { name fields { name description } } } }"
  }'
```

### Query with Variables
```bash
curl -X POST http://localhost:8000/graphql \
  -H "Content-Type: application/json" \
  -d '{
    "query": "query GetProduct($productId: String!) { product(id: $productId) { id name brand prices { amount code } } }",
    "variables": { "productId": "classic-denim-jacket" }
  }'
```

## Testing with jq (for pretty output)

Add `| jq '.'` to any command for formatted JSON:

```bash
curl -X POST http://localhost:8000/graphql \
  -H "Content-Type: application/json" \
  -d '{
    "query": "query { products { id name brand } }"
  }' | jq '.'
```

## Error Testing

### Invalid Query
```bash
curl -X POST http://localhost:8000/graphql \
  -H "Content-Type: application/json" \
  -d '{
    "query": "query { invalidField }"
  }'
```

### Missing Required Field
```bash
curl -X POST http://localhost:8000/graphql \
  -H "Content-Type: application/json" \
  -d '{
    "query": "query { product { id name } }"
  }'
```

## Quick Test Script

Save this as `quick_test.sh`:

```bash
#!/bin/bash
echo "Testing GraphQL API..."
curl -s -X POST http://localhost:8000/graphql \
  -H "Content-Type: application/json" \
  -d '{"query":"query{products{id name}}"}' | jq '.data.products | length'
echo "products found"
```

Make executable: `chmod +x quick_test.sh`
Run: `./quick_test.sh`
