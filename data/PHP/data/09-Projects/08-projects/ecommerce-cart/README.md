# Project 3: E-commerce Cart 🛒

A comprehensive shopping cart system with product management, cart functionality, and checkout simulation.

## 🎯 Learning Objectives

After completing this project, you will:
- Implement complex object-oriented programming
- Build session-based shopping cart functionality
- Create product catalog and inventory management
- Implement order processing workflow
- Handle payment integration simulation
- Build user authentication for e-commerce
- Create admin panel for product management

## 🛠️ Features

### Product Features
- ✅ Product catalog with categories
- ✅ Product search and filtering
- ✅ Product details and reviews
- ✅ Inventory management
- ✅ Product image gallery
- ✅ Price calculations with discounts

### Cart Features
- ✅ Add/remove items from cart
- ✅ Update quantities
- ✅ Cart persistence with sessions
- ✅ Price calculations
- ✅ Coupon/discount codes
- ✅ Tax and shipping calculations

### User Features
- ✅ User registration and login
- ✅ Order history
- ✅ Saved addresses
- ✅ Wishlist functionality
- ✅ Profile management

### Admin Features
- ✅ Product management
- ✅ Order management
- ✅ Inventory tracking
- ✅ Sales reports
- ✅ Customer management

## 📁 Project Structure

```
ecommerce-cart/
├── README.md           # This file
├── index.php          # Main entry point
├── config/
│   ├── database.php   # Database configuration
│   └── config.php     # Application settings
├── classes/
│   ├── Product.php    # Product class
│   ├── Cart.php       # Shopping cart class
│   ├── User.php       # User class
│   ├── Order.php      # Order class
│   └── Database.php   # Database handler
├── admin/
│   ├── index.php      # Admin dashboard
│   ├── products.php   # Product management
│   └── orders.php     # Order management
├── assets/
│   ├── css/
│   │   └── style.css  # Main stylesheet
│   ├── js/
│   │   └── cart.js    # Cart JavaScript
│   └── images/       # Product images
└── database/
    └── setup.sql      # Database schema
```

## 🚀 Getting Started

### Prerequisites
- PHP 7.4 or higher
- MySQL/MariaDB database
- Web server (Apache, Nginx)
- GD extension for image processing

### Database Setup

1. **Create Database**
   ```sql
   CREATE DATABASE ecommerce_cart;
   ```

2. **Import Schema**
   Run the SQL commands from `database/setup.sql`

### Configuration

1. **Database Configuration**
   Edit `config/database.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'ecommerce_cart');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   ```

### Running the Application

1. **Navigate to project directory**
   ```bash
   cd php-learning-guide/08-projects/ecommerce-cart
   ```

2. **Start PHP server**
   ```bash
   php -S localhost:8000
   ```

3. **Access the application**
   - Main site: `http://localhost:8000`
   - Admin panel: `http://localhost:8000/admin`

## 📖 Database Schema

### Products Table
```sql
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    category_id INT,
    sku VARCHAR(100) UNIQUE NOT NULL,
    stock_quantity INT DEFAULT 0,
    image_url VARCHAR(255),
    status ENUM('active', 'inactive', 'discontinued') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);
```

### Categories Table
```sql
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    parent_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES categories(id)
);
```

### Orders Table
```sql
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    tax_amount DECIMAL(10,2) DEFAULT 0,
    shipping_amount DECIMAL(10,2) DEFAULT 0,
    discount_amount DECIMAL(10,2) DEFAULT 0,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    shipping_address TEXT,
    billing_address TEXT,
    payment_method VARCHAR(50),
    payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

### Order Items Table
```sql
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);
```

## 🔧 Core Classes

### Product Class
```php
<?php
class Product {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function create($name, $description, $price, $categoryId, $sku, $stockQuantity, $imageUrl = null) {
        $data = [
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'category_id' => $categoryId,
            'sku' => $sku,
            'stock_quantity' => $stockQuantity,
            'image_url' => $imageUrl
        ];
        
        return $this->db->insert('products', $data);
    }
    
    public function getAll($categoryId = null, $limit = 20, $offset = 0) {
        $sql = "SELECT p.*, c.name as category_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.status = 'active'";
        
        $params = [];
        
        if ($categoryId) {
            $sql .= " AND p.category_id = ?";
            $params[] = $categoryId;
        }
        
        $sql .= " ORDER BY p.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    public function getById($id) {
        $sql = "SELECT p.*, c.name as category_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.id = ?";
        $stmt = $this->db->query($sql, [$id]);
        return $stmt->fetch();
    }
    
    public function updateStock($productId, $quantity) {
        return $this->db->update('products', 
            ['stock_quantity' => $quantity], 
            'id = ?', 
            [$productId]
        );
    }
    
    public function search($query, $limit = 20) {
        $searchTerm = "%$query%";
        $sql = "SELECT p.*, c.name as category_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.status = 'active' AND 
                      (p.name LIKE ? OR p.description LIKE ? OR p.sku LIKE ?)
                ORDER BY p.name ASC
                LIMIT ?";
        
        $stmt = $this->db->query($sql, [$searchTerm, $searchTerm, $searchTerm, $limit]);
        return $stmt->fetchAll();
    }
}
?>
```

### Cart Class
```php
<?php
class Cart {
    private $sessionId;
    private $userId;
    private $db;
    
    public function __construct($sessionId = null, $userId = null) {
        $this->sessionId = $sessionId ?? session_id();
        $this->userId = $userId;
        $this->db = Database::getInstance();
        
        // Initialize cart if not exists
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }
    
    public function addItem($productId, $quantity = 1) {
        $product = $this->getProduct($productId);
        
        if (!$product) {
            return ['success' => false, 'message' => 'Product not found'];
        }
        
        if ($product['stock_quantity'] < $quantity) {
            return ['success' => false, 'message' => 'Insufficient stock'];
        }
        
        $cartKey = 'product_' . $productId;
        
        if (isset($_SESSION['cart'][$cartKey])) {
            $newQuantity = $_SESSION['cart'][$cartKey]['quantity'] + $quantity;
            
            if ($product['stock_quantity'] < $newQuantity) {
                return ['success' => false, 'message' => 'Insufficient stock'];
            }
            
            $_SESSION['cart'][$cartKey]['quantity'] = $newQuantity;
        } else {
            $_SESSION['cart'][$cartKey] = [
                'product_id' => $productId,
                'quantity' => $quantity,
                'added_at' => date('Y-m-d H:i:s')
            ];
        }
        
        return ['success' => true, 'message' => 'Item added to cart'];
    }
    
    public function updateItem($productId, $quantity) {
        if ($quantity <= 0) {
            return $this->removeItem($productId);
        }
        
        $product = $this->getProduct($productId);
        
        if (!$product) {
            return ['success' => false, 'message' => 'Product not found'];
        }
        
        if ($product['stock_quantity'] < $quantity) {
            return ['success' => false, 'message' => 'Insufficient stock'];
        }
        
        $cartKey = 'product_' . $productId;
        
        if (isset($_SESSION['cart'][$cartKey])) {
            $_SESSION['cart'][$cartKey]['quantity'] = $quantity;
            return ['success' => true, 'message' => 'Cart updated'];
        }
        
        return ['success' => false, 'message' => 'Item not in cart'];
    }
    
    public function removeItem($productId) {
        $cartKey = 'product_' . $productId;
        
        if (isset($_SESSION['cart'][$cartKey])) {
            unset($_SESSION['cart'][$cartKey]);
            return ['success' => true, 'message' => 'Item removed from cart'];
        }
        
        return ['success' => false, 'message' => 'Item not in cart'];
    }
    
    public function getItems() {
        $items = [];
        
        foreach ($_SESSION['cart'] as $cartKey => $cartItem) {
            $product = $this->getProduct($cartItem['product_id']);
            
            if ($product) {
                $items[] = [
                    'product_id' => $product['id'],
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'image_url' => $product['image_url'],
                    'quantity' => $cartItem['quantity'],
                    'subtotal' => $product['price'] * $cartItem['quantity'],
                    'stock_quantity' => $product['stock_quantity']
                ];
            }
        }
        
        return $items;
    }
    
    public function getSubtotal() {
        $subtotal = 0;
        
        foreach ($this->getItems() as $item) {
            $subtotal += $item['subtotal'];
        }
        
        return $subtotal;
    }
    
    public function getTotal($taxRate = 0.08, $shippingCost = 0) {
        $subtotal = $this->getSubtotal();
        $tax = $subtotal * $taxRate;
        $total = $subtotal + $tax + $shippingCost;
        
        return [
            'subtotal' => $subtotal,
            'tax' => $tax,
            'shipping' => $shippingCost,
            'total' => $total
        ];
    }
    
    public function getItemCount() {
        $count = 0;
        
        foreach ($_SESSION['cart'] as $cartItem) {
            $count += $cartItem['quantity'];
        }
        
        return $count;
    }
    
    public function clear() {
        $_SESSION['cart'] = [];
        return ['success' => true, 'message' => 'Cart cleared'];
    }
    
    private function getProduct($productId) {
        $sql = "SELECT * FROM products WHERE id = ? AND status = 'active'";
        $stmt = $this->db->query($sql, [$productId]);
        return $stmt->fetch();
    }
}
?>
```

### Order Class
```php
<?php
class Order {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function create($userId, $cartItems, $shippingAddress, $billingAddress, $paymentMethod) {
        try {
            $this->db->getConnection()->beginTransaction();
            
            // Calculate totals
            $subtotal = 0;
            foreach ($cartItems as $item) {
                $subtotal += $item['subtotal'];
            }
            
            $taxAmount = $subtotal * 0.08; // 8% tax
            $shippingAmount = $subtotal > 100 ? 0 : 10; // Free shipping over $100
            $totalAmount = $subtotal + $taxAmount + $shippingAmount;
            
            // Generate order number
            $orderNumber = 'ORD-' . date('Y-m-d') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
            
            // Create order
            $orderData = [
                'user_id' => $userId,
                'order_number' => $orderNumber,
                'total_amount' => $totalAmount,
                'tax_amount' => $taxAmount,
                'shipping_amount' => $shippingAmount,
                'shipping_address' => json_encode($shippingAddress),
                'billing_address' => json_encode($billingAddress),
                'payment_method' => $paymentMethod
            ];
            
            $orderId = $this->db->insert('orders', $orderData);
            
            // Create order items
            foreach ($cartItems as $item) {
                $orderItemData = [
                    'order_id' => $orderId,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'total_price' => $item['subtotal']
                ];
                
                $this->db->insert('order_items', $orderItemData);
                
                // Update product stock
                $product = new Product();
                $currentStock = $item['stock_quantity'];
                $newStock = $currentStock - $item['quantity'];
                $product->updateStock($item['product_id'], $newStock);
            }
            
            $this->db->getConnection()->commit();
            
            return [
                'success' => true,
                'order_id' => $orderId,
                'order_number' => $orderNumber
            ];
            
        } catch (Exception $e) {
            $this->db->getConnection()->rollBack();
            return ['success' => false, 'message' => 'Order creation failed'];
        }
    }
    
    public function getById($id) {
        $sql = "SELECT o.*, u.username, u.email
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                WHERE o.id = ?";
        $stmt = $this->db->query($sql, [$id]);
        $order = $stmt->fetch();
        
        if ($order) {
            // Get order items
            $sql = "SELECT oi.*, p.name as product_name, p.image_url
                    FROM order_items oi
                    LEFT JOIN products p ON oi.product_id = p.id
                    WHERE oi.order_id = ?";
            $stmt = $this->db->query($sql, [$id]);
            $order['items'] = $stmt->fetchAll();
            
            // Decode addresses
            $order['shipping_address'] = json_decode($order['shipping_address'], true);
            $order['billing_address'] = json_decode($order['billing_address'], true);
        }
        
        return $order;
    }
    
    public function getByUserId($userId, $limit = 10) {
        $sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT ?";
        $stmt = $this->db->query($sql, [$userId, $limit]);
        return $stmt->fetchAll();
    }
    
    public function updateStatus($orderId, $status) {
        return $this->db->update('orders', ['status' => $status], 'id = ?', [$orderId]);
    }
    
    public function getAll($status = null, $limit = 20) {
        $sql = "SELECT o.*, u.username
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id";
        
        $params = [];
        
        if ($status) {
            $sql .= " WHERE o.status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY o.created_at DESC LIMIT ?";
        $params[] = $limit;
        
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
    }
}
?>
```

## 🎯 Challenges and Enhancements

### Easy Challenges
1. **Product Reviews**: Add customer review system
2. **Wishlist**: Implement wishlist functionality
3. **Product Comparison**: Add product comparison feature
4. **Recently Viewed**: Track recently viewed products

### Intermediate Challenges
1. **Payment Integration**: Integrate real payment gateways
2. **Shipping Calculator**: Calculate shipping based on location
3. **Discount System**: Advanced discount and coupon system
4. **Email Notifications**: Send order confirmation emails

### Advanced Challenges
1. **Inventory Management**: Advanced inventory with stock alerts
2. **Analytics Dashboard**: Sales and product analytics
3. **Multi-language Support**: Internationalization
4. **API Development**: REST API for mobile app

## 🧪 Testing Your Application

### Manual Testing Checklist
- [ ] Product browsing and search
- [ ] Add items to cart
- [ ] Update cart quantities
- [ ] Remove items from cart
- [ ] Checkout process
- [ ] User registration and login
- [ ] Order history
- [ ] Admin product management
- [ ] Stock management

### E-commerce Testing
- [ ] Price calculations accuracy
- [ ] Tax calculations
- [ ] Stock updates
- [ ] Order processing workflow
- [ ] Payment simulation
- [ ] User experience flow

## 📚 What You've Learned

After completing this project, you've mastered:
- ✅ Complex OOP implementation
- ✅ Session management for e-commerce
- ✅ Database transactions
- ✅ Shopping cart logic
- ✅ Order processing workflow
- ✅ Inventory management
- ✅ Price calculations
- ✅ User authentication
- ✅ Admin panel development

## 🚀 Next Steps

1. **Add Payment Gateway**: Integrate Stripe or PayPal
2. **Implement Shipping**: Real shipping calculation
3. **Add Analytics**: Sales tracking and reporting
4. **Mobile App**: Create mobile application
5. **Deploy**: Launch to production

---

**Ready for the next project?** ➡️ [Contact Manager](../contact-manager/README.md)
