<?php $this->setLayout('layouts/admin'); ?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Edit Product</h1>
        <a href="/admin/products" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to Products
        </a>
    </div>

    <?php if ($this->hasErrors()): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($this->errors() as $field => $errors): ?>
                    <?php foreach ($errors as $error): ?>
                        <li><?= $this->escape($error) ?></li>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form action="/admin/products/<?= $product['id'] ?>/edit" method="POST" enctype="multipart/form-data">
                <?= $this->csrfField() ?>

                <div class="row">
                    <!-- Basic Information -->
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="name" class="form-label">Product Name</label>
                            <input type="text" 
                                   class="form-control <?= $this->hasErrors('name') ? 'is-invalid' : '' ?>" 
                                   id="name" 
                                   name="name" 
                                   value="<?= $this->old('name', $product['name']) ?>" 
                                   required>
                            <?php if ($this->hasErrors('name')): ?>
                                <div class="invalid-feedback">
                                    <?= implode(', ', $this->errors('name')) ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control <?= $this->hasErrors('description') ? 'is-invalid' : '' ?>" 
                                      id="description" 
                                      name="description" 
                                      rows="5" 
                                      required><?= $this->old('description', $product['description']) ?></textarea>
                            <?php if ($this->hasErrors('description')): ?>
                                <div class="invalid-feedback">
                                    <?= implode(', ', $this->errors('description')) ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="price" class="form-label">Price</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" 
                                               class="form-control <?= $this->hasErrors('price') ? 'is-invalid' : '' ?>" 
                                               id="price" 
                                               name="price" 
                                               step="0.01" 
                                               min="0" 
                                               value="<?= $this->old('price', $product['price']) ?>" 
                                               required>
                                        <?php if ($this->hasErrors('price')): ?>
                                            <div class="invalid-feedback">
                                                <?= implode(', ', $this->errors('price')) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="stock" class="form-label">Stock</label>
                                    <input type="number" 
                                           class="form-control <?= $this->hasErrors('stock') ? 'is-invalid' : '' ?>" 
                                           id="stock" 
                                           name="stock" 
                                           min="0" 
                                           value="<?= $this->old('stock', $product['stock']) ?>" 
                                           required>
                                    <?php if ($this->hasErrors('stock')): ?>
                                        <div class="invalid-feedback">
                                            <?= implode(', ', $this->errors('stock')) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Product Details</h5>

                                <div class="mb-3">
                                    <label for="category_id" class="form-label">Category</label>
                                    <select class="form-select <?= $this->hasErrors('category_id') ? 'is-invalid' : '' ?>" 
                                            id="category_id" 
                                            name="category_id" 
                                            required>
                                        <option value="">Select Category</option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?= $category['id'] ?>" 
                                                    <?= $this->old('category_id', $product['category_id']) == $category['id'] ? 'selected' : '' ?>>
                                                <?= $this->escape($category['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if ($this->hasErrors('category_id')): ?>
                                        <div class="invalid-feedback">
                                            <?= implode(', ', $this->errors('category_id')) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="mb-3">
                                    <label for="sku" class="form-label">SKU</label>
                                    <input type="text" 
                                           class="form-control <?= $this->hasErrors('sku') ? 'is-invalid' : '' ?>" 
                                           id="sku" 
                                           name="sku" 
                                           value="<?= $this->old('sku', $product['sku'] ?? '') ?>">
                                    <?php if ($this->hasErrors('sku')): ?>
                                        <div class="invalid-feedback">
                                            <?= implode(', ', $this->errors('sku')) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="mb-3">
                                    <label for="image" class="form-label">Product Image</label>
                                    <?php if ($product['image']): ?>
                                        <div class="mb-2">
                                            <img src="/uploads/<?= $this->escape($product['image']) ?>" 
                                                 alt="Current product image" 
                                                 class="img-thumbnail" 
                                                 style="max-width: 200px;">
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" 
                                           class="form-control <?= $this->hasErrors('image') ? 'is-invalid' : '' ?>" 
                                           id="image" 
                                           name="image" 
                                           accept="image/*">
                                    <?php if ($this->hasErrors('image')): ?>
                                        <div class="invalid-feedback">
                                            <?= implode(', ', $this->errors('image')) ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="form-text">
                                        Leave empty to keep current image. Recommended size: 800x800px. Max file size: 2MB.
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Created</label>
                                    <p class="mb-0">
                                        <?= date('F j, Y g:i A', strtotime($product['created_at'])) ?>
                                    </p>
                                </div>

                                <?php if ($product['updated_at']): ?>
                                    <div class="mb-3">
                                        <label class="form-label">Last Updated</label>
                                        <p class="mb-0">
                                            <?= date('F j, Y g:i A', strtotime($product['updated_at'])) ?>
                                        </p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Product
                    </button>
                    <a href="/admin/products" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div> 