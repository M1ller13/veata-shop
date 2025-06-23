<?php $this->layout('layouts/main') ?>

<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/orders">My Orders</a></li>
            <li class="breadcrumb-item active">Order #<?= $order['id'] ?></li>
        </ol>
    </nav>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Order Details</h5>
                    <span class="badge bg-<?= getStatusColor($order['status']) ?>">
                        <?= ucfirst($order['status']) ?>
                    </span>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-sm-6">
                            <h6 class="mb-2">Order Information</h6>
                            <p class="mb-1">Order Date: <?= date('M d, Y H:i', strtotime($order['created_at'])) ?></p>
                            <p class="mb-1">Payment Method: <?= ucfirst($order['payment_method']) ?></p>
                        </div>
                        <div class="col-sm-6">
                            <h6 class="mb-2">Shipping Information</h6>
                            <p class="mb-0"><?= nl2br(htmlspecialchars($order['shipping_address'])) ?></p>
                        </div>
                    </div>
                    
                    <h6 class="mb-3">Order Items</h6>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($order['items'] as $item): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php if ($item['product_image']): ?>
                                                    <img src="/uploads/products/<?= $item['product_image'] ?>" 
                                                         alt="<?= htmlspecialchars($item['product_name']) ?>" 
                                                         class="me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                                <?php endif; ?>
                                                <div>
                                                    <h6 class="mb-0"><?= htmlspecialchars($item['product_name']) ?></h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>$<?= number_format($item['price'], 2) ?></td>
                                        <td><?= $item['quantity'] ?></td>
                                        <td>$<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Total</strong></td>
                                    <td><strong>$<?= number_format($order['total_amount'], 2) ?></strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Order Status</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <?php
                        $statuses = ['pending', 'processing', 'shipped', 'delivered'];
                        $currentIndex = array_search($order['status'], $statuses);
                        ?>
                        
                        <?php foreach ($statuses as $index => $status): ?>
                            <div class="timeline-item">
                                <div class="timeline-marker <?= $index <= $currentIndex ? 'bg-primary' : 'bg-secondary' ?>"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-0"><?= ucfirst($status) ?></h6>
                                    <?php if ($index <= $currentIndex): ?>
                                        <small class="text-muted">
                                            <?php if ($status === $order['status']): ?>
                                                Current status
                                            <?php else: ?>
                                                Completed
                                            <?php endif; ?>
                                        </small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    padding-bottom: 1.5rem;
}

.timeline-item:last-child {
    padding-bottom: 0;
}

.timeline-marker {
    position: absolute;
    left: -30px;
    width: 15px;
    height: 15px;
    border-radius: 50%;
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: -23px;
    top: 15px;
    height: calc(100% - 15px);
    width: 2px;
    background-color: #dee2e6;
}
</style>

<?php
function getStatusColor($status) {
    return match($status) {
        'pending' => 'warning',
        'processing' => 'info',
        'shipped' => 'primary',
        'delivered' => 'success',
        'cancelled' => 'danger',
        default => 'secondary'
    };
}
?> 