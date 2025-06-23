<?php $this->layout('layouts/admin') ?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Orders</h1>
    </div>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($_SESSION['success']) ?>
            <?php unset($_SESSION['success']) ?>
        </div>
    <?php endif; ?>
    
    <div class="card">
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <form action="/admin/orders" method="GET" class="d-flex gap-2">
                        <select name="status" class="form-select" style="width: auto;">
                            <option value="">All Statuses</option>
                            <option value="pending" <?= $currentStatus === 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="processing" <?= $currentStatus === 'processing' ? 'selected' : '' ?>>Processing</option>
                            <option value="shipped" <?= $currentStatus === 'shipped' ? 'selected' : '' ?>>Shipped</option>
                            <option value="delivered" <?= $currentStatus === 'delivered' ? 'selected' : '' ?>>Delivered</option>
                            <option value="cancelled" <?= $currentStatus === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                        </select>
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <?php if ($currentStatus): ?>
                            <a href="/admin/orders" class="btn btn-secondary">Clear</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
            
            <?php if (empty($orders['items'])): ?>
                <div class="alert alert-info">
                    No orders found.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders['items'] as $order): ?>
                                <tr>
                                    <td>#<?= $order['id'] ?></td>
                                    <td><?= htmlspecialchars($order['user_name']) ?></td>
                                    <td><?= date('M d, Y H:i', strtotime($order['created_at'])) ?></td>
                                    <td><?= $order['total_items'] ?> items</td>
                                    <td>$<?= number_format($order['total_amount'], 2) ?></td>
                                    <td>
                                        <span class="badge bg-<?= getStatusColor($order['status']) ?>">
                                            <?= ucfirst($order['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="/admin/orders/<?= $order['id'] ?>" class="btn btn-sm btn-primary">View Details</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <?php if ($orders['last_page'] > 1): ?>
                    <nav aria-label="Page navigation" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php if ($orders['current_page'] > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $orders['current_page'] - 1 ?><?= $currentStatus ? '&status=' . $currentStatus : '' ?>">Previous</a>
                                </li>
                            <?php endif; ?>
                            
                            <?php for ($i = 1; $i <= $orders['last_page']; $i++): ?>
                                <li class="page-item <?= $i === $orders['current_page'] ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?><?= $currentStatus ? '&status=' . $currentStatus : '' ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($orders['current_page'] < $orders['last_page']): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $orders['current_page'] + 1 ?><?= $currentStatus ? '&status=' . $currentStatus : '' ?>">Next</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

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