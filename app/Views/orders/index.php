<?php $this->layout('layouts/main') ?>

<div class="container py-4">
    <h1 class="mb-4">My Orders</h1>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($_SESSION['success']) ?>
            <?php unset($_SESSION['success']) ?>
        </div>
    <?php endif; ?>
    
    <?php if (empty($orders['items'])): ?>
        <div class="alert alert-info">
            You haven't placed any orders yet.
            <a href="/products" class="alert-link">Start shopping</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Order ID</th>
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
                            <td><?= date('M d, Y', strtotime($order['created_at'])) ?></td>
                            <td><?= $order['total_items'] ?> items</td>
                            <td>$<?= number_format($order['total_amount'], 2) ?></td>
                            <td>
                                <span class="badge bg-<?= getStatusColor($order['status']) ?>">
                                    <?= ucfirst($order['status']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="/orders/<?= $order['id'] ?>" class="btn btn-sm btn-primary">View Details</a>
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
                            <a class="page-link" href="?page=<?= $orders['current_page'] - 1 ?>">Previous</a>
                        </li>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $orders['last_page']; $i++): ?>
                        <li class="page-item <?= $i === $orders['current_page'] ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    
                    <?php if ($orders['current_page'] < $orders['last_page']): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $orders['current_page'] + 1 ?>">Next</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
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