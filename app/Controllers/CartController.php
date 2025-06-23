<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Cart;
use App\Models\Product;

class CartController extends Controller {
    private $cartModel;
    private $productModel;
    
    public function __construct() {
        parent::__construct();
        $this->cartModel = new Cart();
        $this->productModel = new Product();
    }
    
    public function index() {
        $this->requireAuth();
        
        $items = $this->cartModel->getItems($_SESSION['user_id']);
        $total = $this->cartModel->getTotal($_SESSION['user_id']);
        
        return $this->render('cart/index', [
            'items' => $items,
            'total' => $total
        ]);
    }
    
    public function add() {
        $this->requireAuth();
        
        if (!$this->isPost()) {
            $this->redirect('/cart');
        }
        
        $productId = $this->getPost('product_id');
        $quantity = (int)$this->getPost('quantity', 1);
        
        if ($quantity < 1) {
            $_SESSION['flash']['error'] = 'Invalid quantity';
            $this->redirect("/products/{$productId}");
        }
        
        $product = $this->productModel->find($productId);
        
        if (!$product) {
            $_SESSION['flash']['error'] = 'Product not found';
            $this->redirect('/products');
        }
        
        if ($product['stock'] < $quantity) {
            $_SESSION['flash']['error'] = 'Not enough stock available';
            $this->redirect("/products/{$productId}");
        }
        
        if ($this->cartModel->addItem($_SESSION['user_id'], $productId, $quantity)) {
            $_SESSION['flash']['success'] = 'Product added to cart';
        } else {
            $_SESSION['flash']['error'] = 'Failed to add product to cart';
        }
        
        $this->redirect("/products/{$productId}");
    }
    
    public function update() {
        $this->requireAuth();
        
        if (!$this->isPost()) {
            $this->redirect('/cart');
        }
        
        $productId = $this->getPost('product_id');
        $quantity = (int)$this->getPost('quantity');
        
        if ($quantity < 1) {
            $_SESSION['flash']['error'] = 'Invalid quantity';
            $this->redirect('/cart');
        }
        
        if ($this->cartModel->updateQuantity($_SESSION['user_id'], $productId, $quantity)) {
            $_SESSION['flash']['success'] = 'Cart updated';
        } else {
            $_SESSION['flash']['error'] = 'Failed to update cart';
        }
        
        $this->redirect('/cart');
    }
    
    public function remove() {
        $this->requireAuth();
        
        if (!$this->isPost()) {
            $this->redirect('/cart');
        }
        
        $productId = $this->getPost('product_id');
        
        if ($this->cartModel->removeItem($_SESSION['user_id'], $productId)) {
            $_SESSION['flash']['success'] = 'Item removed from cart';
        } else {
            $_SESSION['flash']['error'] = 'Failed to remove item';
        }
        
        $this->redirect('/cart');
    }
    
    public function clear() {
        $this->requireAuth();
        
        if (!$this->isPost()) {
            $this->redirect('/cart');
        }
        
        if ($this->cartModel->clearCart($_SESSION['user_id'])) {
            $_SESSION['flash']['success'] = 'Cart cleared';
        } else {
            $_SESSION['flash']['error'] = 'Failed to clear cart';
        }
        
        $this->redirect('/cart');
    }
    
    public function checkout() {
        $this->requireAuth();
        
        $items = $this->cartModel->getItems($_SESSION['user_id']);
        
        if (empty($items)) {
            $_SESSION['flash']['error'] = 'Your cart is empty';
            $this->redirect('/cart');
        }
        
        // Validate stock
        $stockErrors = $this->cartModel->validateStock($_SESSION['user_id']);
        if (!empty($stockErrors)) {
            $_SESSION['flash']['error'] = implode('<br>', $stockErrors);
            $this->redirect('/cart');
        }
        
        $total = $this->cartModel->getTotal($_SESSION['user_id']);
        
        return $this->render('cart/checkout', [
            'items' => $items,
            'total' => $total
        ]);
    }
} 