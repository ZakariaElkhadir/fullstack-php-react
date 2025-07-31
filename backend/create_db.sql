-- Create database
CREATE
DATABASE IF NOT EXISTS ecommerce_catalog;
USE ecommerce_catalog;

-- Categories table
CREATE TABLE IF NOT EXISTS categories
(
    id         INT PRIMARY KEY AUTO_INCREMENT,
    name       VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products table
CREATE TABLE IF NOT EXISTS products
(
    id            VARCHAR(100) PRIMARY KEY,
    name          VARCHAR(255) NOT NULL,
    description   TEXT,
    brand         VARCHAR(100),
    category_name VARCHAR(100),
    in_stock      BOOLEAN   DEFAULT TRUE,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_name) REFERENCES categories (name) ON UPDATE CASCADE
);

-- Product galleries
CREATE TABLE IF NOT EXISTS product_galleries
(
    id         INT PRIMARY KEY AUTO_INCREMENT,
    product_id VARCHAR(100) NOT NULL,
    image_url  TEXT         NOT NULL,
    sort_order INT DEFAULT 0,
    FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE
);

-- Currencies
CREATE TABLE IF NOT EXISTS currencies
(
    code   VARCHAR(10) PRIMARY KEY,
    label  VARCHAR(50) NOT NULL,
    symbol VARCHAR(10) NOT NULL
);

-- Product prices
CREATE TABLE IF NOT EXISTS product_prices
(
    id            INT PRIMARY KEY AUTO_INCREMENT,
    product_id    VARCHAR(100)   NOT NULL,
    amount        DECIMAL(10, 2) NOT NULL,
    currency_code VARCHAR(10)    NOT NULL,
    FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE,
    FOREIGN KEY (currency_code) REFERENCES currencies (code)
);

-- Attribute sets (like "Size", "Color")
CREATE TABLE IF NOT EXISTS attribute_sets
(
    id   VARCHAR(100) PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    type VARCHAR(50)  NOT NULL -- 'text', 'swatch', etc.
);

-- Attribute items (like "Small", "Medium", "Red", "Blue")
CREATE TABLE IF NOT EXISTS attribute_items
(
    id               VARCHAR(100) PRIMARY KEY,
    attribute_set_id VARCHAR(100) NOT NULL,
    display_value    VARCHAR(100) NOT NULL,
    value            VARCHAR(100) NOT NULL,
    FOREIGN KEY (attribute_set_id) REFERENCES attribute_sets (id) ON DELETE CASCADE
);

-- Product attributes (linking products to their available attributes)
CREATE TABLE IF NOT EXISTS product_attributes
(
    id               INT PRIMARY KEY AUTO_INCREMENT,
    product_id       VARCHAR(100) NOT NULL,
    attribute_set_id VARCHAR(100) NOT NULL,
    FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE,
    FOREIGN KEY (attribute_set_id) REFERENCES attribute_sets (id) ON DELETE CASCADE,
    UNIQUE KEY unique_product_attribute (product_id, attribute_set_id)
);

-- Indexes for better performance
CREATE INDEX idx_products_category ON products (category_name);
CREATE INDEX idx_products_brand ON products (brand);
CREATE INDEX idx_product_galleries_product ON product_galleries (product_id);
CREATE INDEX idx_product_prices_product ON product_prices (product_id);
CREATE INDEX idx_attribute_items_set ON attribute_items (attribute_set_id);
