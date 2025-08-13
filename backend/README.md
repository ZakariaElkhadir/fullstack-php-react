# E-commerce Backend API

A modern, PHP-based e-commerce backend featuring a comprehensive GraphQL API for product catalog management. Built with clean architecture principles and designed for scalability.

## 🚀 Features

- **GraphQL API** - Complete GraphQL implementation for flexible data querying
- **Multi-category Products** - Support for different product types (Clothes, Tech, etc.)
- **Product Attributes** - Flexible attribute system with support for text and swatch types
- **Multi-currency Support** - Handle products with multiple currency pricing
- **Image Galleries** - Support for multiple product images
- **Category Management** - Hierarchical category system
- **Order Management** - GraphQL mutations for order processing
- **CORS Support** - Cross-origin resource sharing enabled
- **Database Abstraction** - Clean PDO-based database layer

## 🛠 Tech Stack

- **PHP 8+** - Modern PHP with type declarations
- **GraphQL** - webonyx/graphql-php for GraphQL implementation
- **FastRoute** - nikic/fast-route for routing
- **MySQL/MariaDB** - Relational database
- **Composer** - Dependency management
- **PDO** - Database abstraction layer

## 📋 Requirements

- PHP 8.0 or higher
- MySQL 5.7+ or MariaDB 10.3+
- Composer
- Apache/Nginx web server
- PHP Extensions:
  - PDO
  - PDO_MySQL
  - mbstring
  - json

## 🔧 Installation

### 1. Clone and Install Dependencies

```bash
git clone [repository-url]
cd backend
composer install
```

### 2. Database Setup

```bash
# Create the database and tables
mysql -u root -p < create_db.sql

# Or manually create database:
mysql -u root -p
CREATE DATABASE ecommerce_catalog;
```

### 3. Environment Configuration

Create a `.env` file in the root directory:

```env
DB_HOST=localhost
DB_PORT=3306
DB_NAME=ecommerce_catalog
DB_USER=your_username
DB_PASS=your_password
DB_CHARSET=utf8mb4
```

### 4. Import Sample Data (Optional)

```bash
# Import sample product data
php import.php
```

### 5. Web Server Configuration

#### Apache
Ensure your document root points to the `public/` directory and mod_rewrite is enabled.

#### Nginx
```nginx
server {
    listen 80;
    root /path/to/backend/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

## 📚 API Documentation

### GraphQL Endpoint

**URL:** `POST /graphql`  
**Content-Type:** `application/json`

### Available Queries

#### Get All Products
```graphql
query {
  products {
    id
    name
    inStock
    brand
    description
    category
    prices {
      amount
      currency {
        label
        symbol
      }
    }
    gallery
    attributes {
      id
      name
      type
      items {
        id
        displayValue
        value
      }
    }
  }
}
```

#### Get Products by Category
```graphql
query {
  products(category: "clothes") {
    id
    name
    inStock
    brand
    # ... other fields
  }
}
```

#### Get Product by ID
```graphql
query {
  product(id: "huarache-x-stussy-le") {
    id
    name
    description
    prices {
      amount
      currency {
        label
        symbol
      }
    }
    gallery
    attributes {
      id
      name
      type
      items {
        id
        displayValue
        value
      }
    }
  }
}
```

#### Get Categories
```graphql
query {
  categories {
    name
    products {
      id
      name
    }
  }
}
```

### Available Mutations

#### Place Order
```graphql
mutation {
  placeOrder(order: {
    items: [
      {
        productId: "huarache-x-stussy-le"
        quantity: 2
        selectedAttributes: [
          {
            attributeId: "Size"
            value: "40"
          }
        ]
      }
    ]
  }) {
    success
    message
    orderId
  }
}
```

## 🏗 Architecture

### Project Structure

```
backend/
├── public/
│   └── index.php              # Entry point
├── src/
│   ├── Config/
│   │   └── Database.php       # Database configuration
│   ├── Controller/
│   │   └── GraphQL.php        # GraphQL controller
│   ├── GraphQL/
│   │   ├── Resolvers/         # GraphQL resolvers
│   │   ├── Schema/            # Schema builder
│   │   └── Types/             # GraphQL type definitions
│   └── Models/
│       ├── AbstractProduct.php    # Base product class
│       ├── ClothesProduct.php     # Clothes-specific product
│       ├── TechProduct.php        # Tech-specific product
│       └── ...                    # Other models
├── composer.json
├── create_db.sql             # Database schema
├── data.json                 # Sample data
└── import.php                # Data import script
```

### Database Schema

The database follows a normalized structure with the following main tables:

- **products** - Core product information
- **categories** - Product categories
- **product_galleries** - Product images
- **product_prices** - Multi-currency pricing
- **attribute_sets** - Attribute definitions (Size, Color, etc.)
- **attribute_items** - Specific attribute values
- **product_attributes** - Links products to their attributes
- **currencies** - Supported currencies

### GraphQL Types

The API implements several GraphQL types:

- **ProductInterface** - Common product fields
- **ClothesProductType** - Clothes-specific implementation
- **TechProductType** - Technology products implementation
- **CategoryInterface** - Category structure
- **AttributeInterface** - Product attributes
- **SwatchAttributeType** - Color/visual attributes

## 🧪 Testing

### Run GraphQL Tests
```bash
# Test complete GraphQL functionality
php final_graphql_test.php

# Test specific product types
php test_clothesproduct.php

# Test GraphQL endpoint
php test_graphql_endpoint.php
```

### Manual Testing

You can test the GraphQL endpoint using any GraphQL client or curl:

```bash
curl -X POST http://localhost/graphql \
  -H "Content-Type: application/json" \
  -d '{"query":"{ products { id name inStock } }"}'
```

## 🔒 Security Features

- **PDO Prepared Statements** - SQL injection prevention
- **Input Validation** - GraphQL schema validation
- **CORS Configuration** - Controlled cross-origin access
- **Error Handling** - Secure error reporting

## 🚀 Deployment

### Production Setup

1. **Environment Variables**: Set up production `.env` file
2. **Database**: Create production database and run migrations
3. **Web Server**: Configure Apache/Nginx with proper security headers
4. **PHP**: Enable OPcache for better performance
5. **HTTPS**: Configure SSL/TLS certificates

### Performance Optimization

- Enable PHP OPcache
- Use database connection pooling
- Implement GraphQL query caching
- Optimize database indexes
- Enable gzip compression

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 🔗 Related Projects

- **Frontend**: React-based e-commerce frontend (link to frontend repo)
- **Admin Panel**: Administrative interface for catalog management

## 📞 Support

For support and questions:
- Create an issue in the repository
- Check existing documentation
- Review test files for usage examples

---

**Built with ❤️ using PHP and GraphQL**
