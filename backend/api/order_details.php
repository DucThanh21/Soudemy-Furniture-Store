<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../frontend/config/database.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$database = new Database();
$db = $database->getConnection();

// Check user role
$stmt = $db->prepare("SELECT role FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || $user['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Not authorized']);
    exit;
}

// Get order ID from query
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

if ($order_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid order ID']);
    exit;
}

try {
    // Get order details (include user basic info via join)
    $query = "SELECT o.*, u.full_name, u.email, u.phone, u.avatar, u.created_at as user_created_at, u.role as user_role
              FROM orders o
              JOIN users u ON o.user_id = u.id
              WHERE o.id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$order) {
        echo json_encode(['success' => false, 'message' => 'Order not found']);
        exit;
    }
    
    // *** SỬA QUERY LẤY ORDER ITEMS - BỎ JOIN products ***
    // Vì products có thể bị xóa sau khi order, ta lưu thông tin vào order_items
    $query = "SELECT 
                oi.id,
                oi.product_id,
                oi.quantity,
                oi.price,
                COALESCE(p.name, CONCAT('Product #', oi.product_id)) as product_name,
                COALESCE(p.image, 'images/default-product.png') as product_image
              FROM order_items oi
              LEFT JOIN products p ON oi.product_id = p.id
              WHERE oi.order_id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$order_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get more user details and summary (safe: use user_id from the order)
    $user_id = isset($order['user_id']) ? intval($order['user_id']) : 0;
    $user = null;
    $user_summary = ['total_orders' => 0, 'total_spent' => 0];
    $recent_orders = [];

    if ($user_id > 0) {
        $stmt = $db->prepare("SELECT id, full_name, email, phone, avatar, created_at, role FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // summary
        $stmt = $db->prepare("SELECT COUNT(*) as cnt, COALESCE(SUM(total_amount),0) as spent FROM orders WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $summaryRow = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($summaryRow) {
            $user_summary['total_orders'] = intval($summaryRow['cnt']);
            $user_summary['total_spent'] = floatval($summaryRow['spent']);
        }

        // recent orders for this user (limit 5)
        $stmt = $db->prepare("SELECT id, total_amount, status, created_at FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
        $stmt->execute([$user_id]);
        $recent_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    echo json_encode([
        'success' => true,
        'order' => $order,
        'items' => $items,
        'user' => $user,
        'user_summary' => $user_summary,
        'recent_orders' => $recent_orders
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
