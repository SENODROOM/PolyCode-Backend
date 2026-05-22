<?php
// Home page view
$product = new Product();
$featuredProducts = $product->getAll(null, 6);
$categories = $product->getCategories();
?>

<div class="card">
    <h1>Welcome to <?= APP_NAME ?></h1>
    <p>Discover amazing products at great prices!</p>
</div>

<div class="card">
    <h2>Featured Products</h2>
    <div class="product-grid">
        <?php foreach ($featuredProducts as $product): ?>
            <div class="product-card">
                <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-image">
                <div class="product-info">
                    <h3 class="product-title"><?= htmlspecialchars($product['name']) ?></h3>
                    <div class="product-price">$<?= number_format($product['price'], 2) ?></div>
                    <div class="product-description"><?= htmlspecialchars(substr($product['description'], 0, 100)) ?>...</div>
                    <div style="display: flex; gap: 10px;">
                        <a href="?action=product_detail&id=<?= $product['id'] ?>" class="btn">View Details</a>
                        
                        <form method="post" style="display: inline;">
                            <input type="hidden" name="form_type" value="add_to_cart">
                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="btn btn-success">Add to Cart</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <div style="text-align: center; margin-top: 20px;">
        <a href="?action=products" class="btn btn-secondary">View All Products</a>
    </div>
</div>

<div class="card">
    <h2>Shop by Category</h2>
    <div class="product-grid">
        <?php foreach ($categories as $category): ?>
            <div class="product-card" style="text-align: center;">
                <div style="padding: 40px 20px;">
                    <h3><?= htmlspecialchars($category['name']) ?></h3>
                    <p><?= htmlspecialchars($category['description'] ?? 'Browse products in this category') ?></p>
                    <a href="?action=products&category=<?= $category['id'] ?>" class="btn">Browse</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="card">
    <h2>Why Shop With Us?</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 20px;">
        <div style="text-align: center;">
            <h3>🚚 Fast Shipping</h3>
            <p>Free shipping on orders over $100</p>
        </div>
        <div style="text-align: center;">
            <h3>💰 Best Prices</h3>
            <p>Competitive prices on all products</p>
        </div>
        <div style="text-align: center;">
            <h3>🔒 Secure Shopping</h3>
            <p>Safe and secure payment processing</p>
        </div>
        <div style="text-align: center;">
            <h3>📞 Customer Support</h3>
            <p>24/7 customer service available</p>
        </div>
    </div>
</div>
