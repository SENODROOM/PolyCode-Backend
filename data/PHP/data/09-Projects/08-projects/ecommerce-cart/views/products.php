<?php
// Products listing page
$product = new Product();
$categoryId = $_GET['category'] ?? null;
$search = $_GET['search'] ?? '';

if ($search) {
    $products = $product->search($search);
    $pageTitle = "Search Results for: " . htmlspecialchars($search);
} elseif ($categoryId) {
    $products = $product->getAll($categoryId);
    $pageTitle = "Products in Category";
} else {
    $products = $product->getAll();
    $pageTitle = "All Products";
}

$categories = $product->getCategories();
?>

<div class="card">
    <h1><?= $pageTitle ?></h1>
    
    <!-- Search Form -->
    <form method="get" style="margin-bottom: 20px;">
        <input type="hidden" name="action" value="products">
        <div style="display: flex; gap: 10px;">
            <input type="text" name="search" placeholder="Search products..." value="<?= htmlspecialchars($search) ?>" style="flex: 1;">
            <button type="submit" class="btn">Search</button>
        </div>
    </form>
    
    <!-- Category Filter -->
    <form method="get" style="margin-bottom: 20px;">
        <input type="hidden" name="action" value="products">
        <div style="display: flex; gap: 10px; align-items: center;">
            <label for="category">Filter by Category:</label>
            <select name="category" id="category" onchange="this.form.submit()">
                <option value="">All Categories</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['id'] ?>" <?= $categoryId == $category['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <a href="?action=products" class="btn btn-secondary">Clear</a>
        </div>
    </form>
</div>

<div class="card">
    <?php if (empty($products)): ?>
        <p>No products found.</p>
    <?php else: ?>
        <p>Found <?= count($products) ?> products</p>
        
        <div class="product-grid">
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-image">
                    <div class="product-info">
                        <h3 class="product-title"><?= htmlspecialchars($product['name']) ?></h3>
                        <div class="product-price">$<?= number_format($product['price'], 2) ?></div>
                        <div class="product-description"><?= htmlspecialchars(substr($product['description'], 0, 100)) ?>...</div>
                        <div style="font-size: 0.9rem; color: #666; margin-bottom: 10px;">
                            Category: <?= htmlspecialchars($product['category_name']) ?><br>
                            Stock: <?= $product['stock_quantity'] ?> available
                        </div>
                        <div style="display: flex; gap: 10px;">
                            <a href="?action=product_detail&id=<?= $product['id'] ?>" class="btn">View Details</a>
                            
                            <form method="post" style="display: inline;">
                                <input type="hidden" name="form_type" value="add_to_cart">
                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn btn-success" <?= $product['stock_quantity'] <= 0 ? 'disabled' : '' ?>>
                                    <?= $product['stock_quantity'] <= 0 ? 'Out of Stock' : 'Add to Cart' ?>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
