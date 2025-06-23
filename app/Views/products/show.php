<?php $this->setLayout('layouts/main'); ?>

<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/products">Products</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= $this->escape($product['name']) ?></li>
        </ol>
    </nav>

    <div class="row">
        <!-- Product Image -->
        <div class="col-md-6">
            <?php if ($product['image']): ?>
                <img src="/uploads/<?= $this->escape($product['image']) ?>" 
                     class="img-fluid rounded" 
                     alt="<?= $this->escape($product['name']) ?>">
            <?php else: ?>
                <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                     style="height: 400px;">
                    <span class="text-muted">No image available</span>
                </div>
            <?php endif; ?>
        </div>

        <!-- Product Details -->
        <div class="col-md-6">
            <h1 class="mb-3"><?= $this->escape($product['name']) ?></h1>
            
            <p class="text-muted mb-3">
                Category: <?= $this->escape($product['category_name']) ?>
            </p>
            
            <div class="mb-4">
                <h2 class="text-primary mb-0">$<?= number_format($product['price'], 2) ?></h2>
                <?php if ($product['stock'] > 0): ?>
                    <span class="badge bg-success">In Stock</span>
                <?php else: ?>
                    <span class="badge bg-danger">Out of Stock</span>
                <?php endif; ?>
            </div>

            <div class="mb-4">
                <h5>Description</h5>
                <p><?= nl2br($this->escape($product['description'])) ?></p>
            </div>

            <?php if ($product['stock'] > 0): ?>
                <form action="/cart/add" method="POST" class="mb-4">
                    <?= $this->csrfField() ?>
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    
                    <div class="row g-3 align-items-center">
                        <div class="col-auto">
                            <label for="quantity" class="col-form-label">Quantity:</label>
                        </div>
                        <div class="col-auto">
                            <input type="number" 
                                   class="form-control" 
                                   id="quantity" 
                                   name="quantity" 
                                   value="1" 
                                   min="1" 
                                   max="<?= $product['stock'] ?>">
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </form>
            <?php endif; ?>

            <!-- Additional Information -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Product Information</h5>
                    <ul class="list-unstyled mb-0">
                        <li><strong>SKU:</strong> <?= $this->escape($product['sku'] ?? 'N/A') ?></li>
                        <li><strong>Stock:</strong> <?= $product['stock'] ?> units</li>
                        <li><strong>Added:</strong> <?= date('F j, Y', strtotime($product['created_at'])) ?></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    <?php if (!empty($relatedProducts)): ?>
        <div class="mt-5">
            <h3 class="mb-4">Related Products</h3>
            <div class="row row-cols-1 row-cols-md-4 g-4">
                <?php foreach ($relatedProducts as $related): ?>
                    <div class="col">
                        <div class="card h-100">
                            <?php if ($related['image']): ?>
                                <img src="/uploads/<?= $this->escape($related['image']) ?>" 
                                     class="card-img-top" 
                                     alt="<?= $this->escape($related['name']) ?>">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?= $this->escape($related['name']) ?></h5>
                                <p class="card-text text-primary">
                                    $<?= number_format($related['price'], 2) ?>
                                </p>
                                <a href="/products/<?= $related['id'] ?>" 
                                   class="btn btn-outline-primary">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div> 