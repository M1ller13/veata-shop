<?php $this->setLayout('layouts/admin'); ?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Products</h1>
        <a href="/admin/products/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Product
        </a>
    </div>

    <?php if ($this->hasFlash('success')): ?>
        <div class="alert alert-success">
            <?= $this->flash('success') ?>
        </div>
    <?php endif; ?>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="/admin/products" method="GET" class="row g-3">
                <div class="col-md-3">
                    <input type="text" 
                           class="form-control" 
                           name="search" 
                           placeholder="Search products..." 
                           value="<?= $this->escape($search ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="category">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" 
                                    <?= ($category ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                <?= $this->escape($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="stock">
                        <option value="">All Stock</option>
                        <option value="in" <?= ($stock ?? '') == 'in' ? 'selected' : '' ?>>In Stock</option>
                        <option value="out" <?= ($stock ?? '') == 'out' ? 'selected' : '' ?>>Out of Stock</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
                <div class="col-md-2">
                    <a href="/admin/products" class="btn btn-outline-secondary w-100">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Products Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($products['data'])): ?>
                            <tr>
                                <td colspan="8" class="text-center">No products found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($products['data'] as $product): ?>
                                <tr>
                                    <td><?= $product['id'] ?></td>
                                    <td>
                                        <?php if ($product['image']): ?>
                                            <img src="/uploads/<?= $this->escape($product['image']) ?>" 
                                                 alt="<?= $this->escape($product['name']) ?>"
                                                 class="img-thumbnail"
                                                 style="width: 50px; height: 50px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                                 style="width: 50px; height: 50px;">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $this->escape($product['name']) ?></td>
                                    <td><?= $this->escape($product['category_name']) ?></td>
                                    <td>$<?= number_format($product['price'], 2) ?></td>
                                    <td><?= $product['stock'] ?></td>
                                    <td>
                                        <?php if ($product['stock'] > 0): ?>
                                            <span class="badge bg-success">In Stock</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Out of Stock</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="/admin/products/<?= $product['id'] ?>/edit" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal<?= $product['id'] ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>

                                        <!-- Delete Modal -->
                                        <div class="modal fade" 
                                             id="deleteModal<?= $product['id'] ?>" 
                                             tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Confirm Delete</h5>
                                                        <button type="button" 
                                                                class="btn-close" 
                                                                data-bs-dismiss="modal">
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        Are you sure you want to delete this product?
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" 
                                                                class="btn btn-secondary" 
                                                                data-bs-dismiss="modal">
                                                            Cancel
                                                        </button>
                                                        <form action="/admin/products/<?= $product['id'] ?>/delete" 
                                                              method="POST" 
                                                              class="d-inline">
                                                            <?= $this->csrfField() ?>
                                                            <button type="submit" 
                                                                    class="btn btn-danger">
                                                                Delete
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($products['total_pages'] > 1): ?>
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $products['total_pages']; $i++): ?>
                            <li class="page-item <?= $i == $products['current_page'] ? 'active' : '' ?>">
                                <a class="page-link" 
                                   href="/admin/products?page=<?= $i ?><?= $search ? '&search=' . urlencode($search) : '' ?><?= $category ? '&category=' . $category : '' ?><?= $stock ? '&stock=' . $stock : '' ?>">
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