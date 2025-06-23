<?php $this->layout('layouts/main') ?>

<div class="container py-4">
    <h1 class="mb-4">Checkout</h1>
    
    <?php if (isset($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Shipping Information</h5>
                </div>
                <div class="card-body">
                    <form action="/orders" method="POST">
                        <?= csrf_field() ?>
                        
                        <div class="mb-3">
                            <label for="shipping_address" class="form-label">Shipping Address</label>
                            <textarea class="form-control" id="shipping_address" name="shipping_address" rows="3" required><?= old('shipping_address') ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Payment Method</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="credit_card" value="credit_card" <?= old('payment_method') === 'credit_card' ? 'checked' : '' ?> required>
                                <label class="form-check-label" for="credit_card">
                                    Credit Card
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="paypal" value="paypal" <?= old('payment_method') === 'paypal' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="paypal">
                                    PayPal
                                </label>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Place Order</button>
                        <a href="/cart" class="btn btn-link">Back to Cart</a>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Order Summary</h5>
                </div>
                <div class="card-body">
                    <?php foreach ($items as $item): ?>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <h6 class="mb-0"><?= htmlspecialchars($item['name']) ?></h6>
                                <small class="text-muted">Quantity: <?= $item['quantity'] ?></small>
                            </div>
                            <span>$<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
                        </div>
                    <?php endforeach; ?>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Total</h5>
                        <h5 class="mb-0">$<?= number_format($total, 2) ?></h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 