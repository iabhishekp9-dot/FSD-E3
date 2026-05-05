<?php
require_once 'config.php';

function getProducts($conn) {
    $result = mysqli_query($conn, "SELECT * FROM products ORDER BY id");
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function addToCart($productId, $quantity = 1) {
    $_SESSION['cart'][$productId] = ($_SESSION['cart'][$productId] ?? 0) + $quantity;
}

function removeFromCart($productId) {
    unset($_SESSION['cart'][$productId]);
}

function updateCartQuantity($productId, $quantity) {
    if ($quantity <= 0) {
        unset($_SESSION['cart'][$productId]);
    } else {
        $_SESSION['cart'][$productId] = $quantity;
    }
}

function getCartItems($conn) {
    $items = [];
    if (!empty($_SESSION['cart'])) {
        $ids = implode(',', array_keys($_SESSION['cart']));
        $result = mysqli_query($conn, "SELECT * FROM products WHERE id IN ($ids)");
        while ($product = mysqli_fetch_assoc($result)) {
            $product['quantity'] = $_SESSION['cart'][$product['id']];
            $product['subtotal'] = $product['price'] * $product['quantity'];
            $items[] = $product;
        }
    }
    return $items;
}

function getCartTotal($conn) {
    $total = 0;
    foreach (getCartItems($conn) as $item) {
        $total += $item['subtotal'];
    }
    return $total;
}

function clearCart() {
    $_SESSION['cart'] = [];
}

function generateProductImage($productName, $size = 200) {
    $colors = [
        'Headphones' => '#667eea', 'Watch' => '#764ba2', 'Speaker' => '#f43f5e',
        'Drone' => '#10b981', 'Laptop' => '#f59e0b', 'Backpack' => '#3b82f6',
        'Sunglasses' => '#8b5cf6', 'Wallet' => '#ec4899', 'Shoes' => '#14b8a6',
        'Boots' => '#f97316', 'Sneakers' => '#6366f1', 'Coffee' => '#a855f7',
        'Lamp' => '#ef4444', 'Yoga' => '#84cc16', 'Dumbbells' => '#06b6d4'
    ];
    
    $color = '#667eea';
    foreach ($colors as $key => $c) {
        if (stripos($productName, $key) !== false) {
            $color = $c;
            break;
        }
    }
    
    $letter = strtoupper(substr($productName, 0, 1));
    
    $svg = '<svg width="' . $size . '" height="' . $size . '" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
        <rect width="200" height="200" fill="' . $color . '"/>
        <circle cx="100" cy="80" r="40" fill="rgba(255,255,255,0.2)"/>
        <text x="100" y="120" font-family="Arial" font-size="80" fill="white" text-anchor="middle" dominant-baseline="middle">' . $letter . '</text>
        <text x="100" y="170" font-family="Arial" font-size="14" fill="white" text-anchor="middle">' . $productName . '</text>
    </svg>';
    
    return 'data:image/svg+xml,' . rawurlencode($svg);
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function loginUser($conn, $username, $password) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE username = ? OR email = ?");
    mysqli_stmt_bind_param($stmt, "ss", $username, $username);
    mysqli_stmt_execute($stmt);
    $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['full_name'] = $user['full_name'];
        return true;
    }
    return false;
}

function registerUser($conn, $username, $email, $password, $full_name) {
    $check = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ? OR email = ?");
    mysqli_stmt_bind_param($check, "ss", $username, $email);
    mysqli_stmt_execute($check);
    mysqli_stmt_store_result($check);
    
    if (mysqli_stmt_num_rows($check) > 0) {
        return false;
    }
    
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $stmt = mysqli_prepare($conn, "INSERT INTO users (username, email, password, full_name) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssss", $username, $email, $hashed, $full_name);
    return mysqli_stmt_execute($stmt);
}

function logout() {
    session_destroy();
    header('Location: index.php');
    exit;
}
?>