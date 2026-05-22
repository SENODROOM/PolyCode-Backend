<?php
// Product detail page
$product = new Product();
$productId = $_GET['id'] ?? 0;
$product = $product->getById($productId);

if (!$product) {
    echo '<div class="card"><p>Product not found.</p></div>';
    return;
}
?>

<div class="card">
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
        <div>
            <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" style="width: 100%; border-radius: 8px;">
        </div>
        
        <div>
            <h1><?= htmlspecialchars($product['name']) ?></h1>
            
            <div style="font-size: 1.5rem; font-weight: bold; color: #007bff; margin: 15px 0;">
                $<?= number_format($product['price'], 2) ?>
            </div>
            
            <div style="margin: 15px 0;">
                <strong>Category:</strong> <?= htmlspecialchars($product['category_name']) ?>
            </div>
            
            <div style="margin: 15px 0;">
                <strong>Stock:</strong> 
                <span style="color: <?= $product['stock_quantity'] > 10 ? '#28a745' : '#dc3545' ?>;">
                    <?= $product['stock_quantity'] ?> units available
                </span>
            </div>
            
            <div style="margin: 15px 0;">
                <strong>SKU:</strong> <?= htmlspecialchars($product['sku']) ?>
            </div>
            
            <div style="margin: 20px 0;">
                <h3>Description</h3>
                <p><?= htmlspecialchars($product['description']) ?></p>
            </div>
            
            <?php if ($product['stock_quantity'] > 0): ?>
                <form method="post" style="margin: 20px 0;">
                    <input type="hidden" name="form_type" value="add_to_cart">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    
                    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                        <label for="quantity"><strong>Quantity:</strong></label>
                        <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?= $product['stock_quantity'] ?>" style="width: 80px; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                    </div>
                    
                    <div style="display: flex; gap: 10px;">
                        <button type="submit" class="btn btn-success" style="font-size: 1.1rem; padding: 12px 24px;">
                            Add to Cart
                        </button>
                        <a href="?action=products" class="btn btn-secondary">Back to Products</a>
                    </div>
                </form>
            <?php else: ?>
                <div style="margin: 20px 0;">
                    <button disabled class="btn btn-secondary" style="font-size: 1.1rem; padding: 12px 24px;">
                        Out of Stock
                    </button>
                    <a href="?action=products" class="btn btn-secondary" style="margin-left: 10px;">Back to Products</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="card">
    <h3>Product Details</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 15px;">
        <div>
            <strong>Product ID:</strong><br>
            <?= $product['id'] ?>
        </div>
        <div>
            <strong>SKU:</strong><br>
            <?= htmlspecialchars($product['sku']) ?>
        </div>
        <div>
            <strong>Category:</strong><br>
            <?= htmlspecialchars($product['category_name']) ?>
        </div>
        <div>
            <strong>Availability:</strong><br>
            <?= $product['stock_quantity'] > 0 ? 'In Stock' : 'Out of Stock' ?>
        </div>
        <div>
            <strong>Added:</strong><br>
            <?= date('M j, Y', strtotime($product['created_at'])) ?>
        </div>
    </div>
</div>

<div class="card">
    <h3>Shipping & Returns</h3>
    <ul>
        <li>Free shipping on orders over $100</li>
        <li>Standard shipping: 3-5 business days</li>
        <li>Express shipping: 1-2 business days</li>
        <li>30-day return policy</li>
        <li>Full refund for defective items</li>
    </ul>
</div>

<div class="card">
    <h3>Related Products</h3>
    <p>Check out these similar items that other customers love:</p>
    
    <?php
    // Get related products (same category, excluding current product)
    $relatedProducts = $product->getAll($product['category_id'], 4);
    $relatedProducts = array_filter($relatedProducts, function($p) use ($product) {
        return $p['id'] != $product['id'];
    });
    ?>
    
    <?php if (!empty($relatedProducts)): ?>
        <div class="product-grid" style="margin-top: 15px;">
            <?php foreach (array_slice($relatedProducts, 0, 3) as $related): ?>
                <div class="product-card">
                    <img src="<?= htmlspecialchars($related['image_url']) ?>" alt="<?= htmlspecialchars($related['name']) ?>" class="product-image">
                    <div class="product-info">
                        <h4><?= htmlspecialchars($related['name']) ?></h4>
                        <div class="product-price">$<?= number_format($related['price'], 2) ?></div>
                        <a href="?action=product_detail&id=<?= $related['id'] ?>" class="btn">View Details</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
