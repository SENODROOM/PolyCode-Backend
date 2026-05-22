<?php
// Shopping cart page
$cartItems = $cart->getItems();
$totals = $cart->getTotal();
?>

<div class="card">
    <h1>Shopping Cart</h1>
    
    <?php if (empty($cartItems)): ?>
        <p>Your cart is empty.</p>
        <a href="?action=products" class="btn">Continue Shopping</a>
    <?php else: ?>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cartItems as $item): ?>
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                <div>
                                    <strong><?= htmlspecialchars($item['name']) ?></strong>
                                    <br><small>Stock: <?= $item['stock_quantity'] ?></small>
                                </div>
                            </div>
                        </td>
                        <td>$<?= number_format($item['price'], 2) ?></td>
                        <td>
                            <form method="post" style="display: inline;">
                                <input type="hidden" name="form_type" value="update_cart">
                                <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                                <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" max="<?= $item['stock_quantity'] ?>" class="quantity-input">
                                <button type="submit" class="btn btn-sm" style="padding: 5px 10px;">Update</button>
                            </form>
                        </td>
                        <td>$<?= number_format($item['subtotal'], 2) ?></td>
                        <td>
                            <form method="post" style="display: inline;">
                                <input type="hidden" name="form_type" value="remove_from_cart">
                                <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                                <button type="submit" class="btn btn-danger btn-sm" style="padding: 5px 10px;">Remove</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="cart-summary">
            <h3>Order Summary</h3>
            <div class="summary-row">
                <span>Subtotal:</span>
                <span>$<?= number_format($totals['subtotal'], 2) ?></span>
            </div>
            <div class="summary-row">
                <span>Tax (8%):</span>
                <span>$<?= number_format($totals['tax'], 2) ?></span>
            </div>
            <div class="summary-row">
                <span>Shipping:</span>
                <span>
                    <?= $totals['subtotal'] > 100 ? 'FREE' : '$' . number_format($totals['shipping'], 2) ?>
                    <?php if ($totals['subtotal'] <= 100): ?>
                        <br><small>Free shipping on orders over $100</small>
                    <?php endif; ?>
                </span>
            </div>
            <div class="summary-row total">
                <span>Total:</span>
                <span>$<?= number_format($totals['total'], 2) ?></span>
            </div>
            
            <div style="margin-top: 20px; display: flex; gap: 10px;">
                <a href="?action=products" class="btn btn-secondary">Continue Shopping</a>
                
                <?php if (isLoggedIn()): ?>
                    <form method="post" style="display: inline;">
                        <input type="hidden" name="form_type" value="checkout">
                        <button type="submit" class="btn btn-success">Proceed to Checkout</button>
                    </form>
                <?php else: ?>
                    <a href="?action=login" class="btn btn-success">Login to Checkout</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php if (!empty($cartItems)): ?>
    <div class="card">
        <h3>Shopping Tips</h3>
        <ul>
            <li>Add $<?= max(0, 100.01 - $totals['subtotal']) ?> more to qualify for free shipping</li>
            <li>Stock is limited - complete your order soon</li>
            <li>All prices include applicable taxes</li>
            <li>30-day return policy on all items</li>
        </ul>
    </div>
<?php endif; ?>
