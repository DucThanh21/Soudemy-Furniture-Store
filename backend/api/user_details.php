<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../frontend/config/database.php';

// Ensure only authenticated admins can access
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

try {
    $stmt = $db->prepare('SELECT role FROM users WHERE id = ? LIMIT 1');
    $stmt->execute([$_SESSION['user_id']]);
    $currentUser = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$currentUser || ($currentUser['role'] ?? '') !== 'admin') {
        echo json_encode(['success' => false, 'message' => 'Access denied']);
        exit;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Unable to verify permissions']);
    exit;
}

$userId = isset($_GET['user_id']) ? (int) $_GET['user_id'] : 0;
if ($userId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
    exit;
}

try {
    $userStmt = $db->prepare('SELECT id, full_name, email, phone, role, created_at, avatar FROM users WHERE id = ? LIMIT 1');
    $userStmt->execute([$userId]);
    $user = $userStmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit;
    }

    // Get user's orders
    $ordersStmt = $db->prepare('SELECT id, total_amount, status, payment_method, created_at FROM orders WHERE user_id = ? ORDER BY created_at DESC');
    $ordersStmt->execute([$userId]);
    $orders = $ordersStmt->fetchAll(PDO::FETCH_ASSOC);

    // Get user's cart items (pending/waiting items)
    $cartStmt = $db->prepare('
        SELECT c.id, c.quantity, c.created_at as added_at,
               p.id as product_id, p.name as product_name, p.price, p.image, p.category
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = ?
        ORDER BY c.created_at DESC
    ');
    $cartStmt->execute([$userId]);
    $cartItems = $cartStmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'user' => $user,
        'orders' => $orders,
        'cart_items' => $cartItems,
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Failed to load user details']);
}
