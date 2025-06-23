<?php $this->setLayout('layouts/main'); ?>

<div class="container py-4">
    <div class="row">
        <!-- Sidebar with categories -->
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Categories</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="/products" class="list-group-item list-group-item-action <?= empty($category) ? 'active' : '' ?>">
                        All Products
                    </a>
                    <?php foreach ($categories as $cat): ?>
                        <a href="/products?category=<?= $cat['id'] ?>" 
                           class="list-group-item list-group-item-action <?= $category == $cat['id'] ? 'active' : '' ?>">
                            <?= $this->escape($cat['name']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <div class="col-md-9">
            <!-- Search bar -->
            <div class="card mb-4">
                <div class="card-body">
                    <form action="/products" method="GET" class="row g-3">
                        <div class="col-md-8">
                            <input type="text" 
                                   class="form-control" 
                                   name="search" 
                                   placeholder="Search products..." 
                                   value="<?= $this->escape($search) ?>">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">Search</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Products grid -->
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <?php if (empty($products['data'])): ?>
                    <div class="col-12">
                        <div class="alert alert-info">
                            No products found.
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($products['data'] as $product): ?>
                        <div class="col">
                            <div class="card h-100">
                                <?php if ($product['image']): ?>
                                    <img src="/uploads/<?= $this->escape($product['image']) ?>" 
                                         class="card-img-top" 
                                         alt="<?= $this->escape($product['name']) ?>">
                                <?php endif; ?>
                                <div class="card-body">
                                    <h5 class="card-title"><?= $this->escape($product['name']) ?></h5>
                                    <p class="card-text text-muted">
                                        <?= $this->escape($product['category_name']) ?>
                                    </p>
                                    <p class="card-text">
                                        <?= $this->escape(substr($product['description'], 0, 100)) ?>...
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="h5 mb-0">$<?= number_format($product['price'], 2) ?></span>
                                        <a href="/products/<?= $product['id'] ?>" 
                                           class="btn btn-primary">View Details</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if ($products['total_pages'] > 1): ?>
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $products['total_pages']; $i++): ?>
                            <li class="page-item <?= $i == $products['current_page'] ? 'active' : '' ?>">
                                <a class="page-link" 
                                   href="/products?page=<?= $i ?><?= $search ? '&search=' . urlencode($search) : '' ?><?= $category ? '&category=' . $category : '' ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>
</div> 