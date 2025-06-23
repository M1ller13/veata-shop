<?php

namespace App\Controllers;

use App\Models\Order;
use App\Models\Cart;

class OrderController extends Controller {
    private $orderModel;
    private $cartModel;
    
    public function __construct() {
        parent::__construct();
        $this->orderModel = new Order();
        $this->cartModel = new Cart();
    }
    
    public function checkout() {
        $this->requireAuth();
        
        $items = $this->cartModel->getItems($_SESSION['user_id']);
        if (empty($items)) {
            $this->redirect('/cart');
        }
        
        $total = $this->cartModel->getTotal($_SESSION['user_id']);
        
        return $this->view('orders/checkout', [
            'items' => $items,
            'total' => $total
        ]);
    }
    
    public function create() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/cart/checkout');
        }
        
        $data = $this->validate([
            'shipping_address' => 'required',
            'payment_method' => 'required|in:credit_card,paypal'
        ]);
        
        if ($data === false) {
            return $this->view('orders/checkout', [
                'items' => $this->cartModel->getItems($_SESSION['user_id']),
                'total' => $this->cartModel->getTotal($_SESSION['user_id']),
                'errors' => $this->getErrors()
            ]);
        }
        
        try {
            $orderId = $this->orderModel->createFromCart($_SESSION['user_id'], $data);
            
            if ($orderId) {
                $_SESSION['success'] = 'Order placed successfully!';
                $this->redirect('/orders/' . $orderId);
            } else {
                throw new \Exception('Failed to create order');
            }
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Failed to place order. Please try again.';
            $this->redirect('/cart/checkout');
        }
    }
    
    public function show($id) {
        $this->requireAuth();
        
        $order = $this->orderModel->getWithItems($id);
        
        if (!$order || $order['user_id'] !== $_SESSION['user_id']) {
            $_SESSION['error'] = 'Order not found';
            $this->redirect('/orders');
        }
        
        return $this->view('orders/show', [
            'order' => $order
        ]);
    }
    
    public function index() {
        $this->requireAuth();
        
        $page = $_GET['page'] ?? 1;
        $orders = $this->orderModel->getByUser($_SESSION['user_id'], $page);
        
        return $this->view('orders/index', [
            'orders' => $orders
        ]);
    }
    
    public function adminIndex() {
        $this->requireAdmin();
        
        $page = $_GET['page'] ?? 1;
        $status = $_GET['status'] ?? null;
        
        $orders = $this->orderModel->getAll($page, 10, $status);
        
        return $this->view('admin/orders/index', [
            'orders' => $orders,
            'currentStatus' => $status
        ]);
    }
    
    public function adminShow($id) {
        $this->requireAdmin();
        
        $order = $this->orderModel->getWithItems($id);
        
        if (!$order) {
            $_SESSION['error'] = 'Order not found';
            $this->redirect('/admin/orders');
        }
        
        return $this->view('admin/orders/show', [
            'order' => $order
        ]);
    }
    
    public function updateStatus($id) {
        $this->requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/orders');
        }
        
        $data = $this->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled'
        ]);
        
        if ($data === false) {
            $_SESSION['error'] = 'Invalid status';
            $this->redirect('/admin/orders/' . $id);
        }
        
        if ($this->orderModel->updateStatus($id, $data['status'])) {
            $_SESSION['success'] = 'Order status updated successfully';
        } else {
            $_SESSION['error'] = 'Failed to update order status';
        }
        
        $this->redirect('/admin/orders/' . $id);
    }
} 