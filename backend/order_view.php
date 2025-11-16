<?php
ob_start();
session_start();

require_once __DIR__ . '/../frontend/config/config.php';
require_once __DIR__ . '/../frontend/config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../frontend/login.php');
    exit;
}

$database = new Database();
$db = $database->getConnection();

$userId = $_SESSION['user_id'];
$stmt = $db->prepare('SELECT role FROM users WHERE id = ? LIMIT 1');
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || ($user['role'] ?? '') !== 'admin') {
    header('HTTP/1.1 403 Forbidden');
    echo 'Unauthorized';
    exit;
}

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
if ($order_id <= 0) {
    header('Location: manager.php');
    exit;
}

$order = null;
$order_items = [];
try {
    $orderStmt = $db->prepare(
        'SELECT o.*, u.full_name, u.email, u.phone, u.avatar 
         FROM orders o 
         LEFT JOIN users u ON o.user_id = u.id 
         WHERE o.id = ?'
    );
    $orderStmt->execute([$order_id]);
    $order = $orderStmt->fetch(PDO::FETCH_ASSOC);

    if ($order) {
        $itemsStmt = $db->prepare(
            'SELECT oi.*, p.name AS product_name, p.image AS product_image, p.price AS product_price 
             FROM order_items oi 
             LEFT JOIN products p ON oi.product_id = p.id 
             WHERE oi.order_id = ?'
        );
        $itemsStmt->execute([$order_id]);
        $order_items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (Exception $e) {
    $order = null;
}

if (!$order) {
    header('Location: manager.php');
    exit;
}

$paymentLabels = [
    'cod' => 'Thanh toán khi nhận hàng',
    'banking' => 'Chuyển khoản ngân hàng',
    'momo' => 'Ví MoMo'
];

function formatCurrency($value) {
    return '$' . number_format((float)$value, 2);
}

function formatDateTime($value) {
    if (!$value) {
        return 'N/A';
    }
    return date('d/m/Y H:i', strtotime($value));
}

$paymentMethod = $paymentLabels[$order['payment_method'] ?? ''] ?? ($order['payment_method'] ?? 'Khác');
$statusLabel = ucfirst($order['status'] ?? 'pending');
$statusClass = 'status-' . strtolower(str_replace(' ', '-', $order['status'] ?? 'pending'));

include __DIR__ . '/../frontend/includes/header.php';
?>

<section class="order-view-section">
    <div class="order-view-header">
        <div>
            <h1>Đơn hàng #<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></h1>
            <p class="order-status <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></p>
        </div>
        <a href="manager.php" class="back-link">← Quay lại bảng điều khiển</a>
    </div>

    <div class="order-view-grid">
        <div class="order-card">
            <h2>Thông tin đơn hàng</h2>
            <p><strong>Ngày đặt:</strong> <?php echo formatDateTime($order['created_at']); ?></p>
            <p><strong>Tổng tiền:</strong> <?php echo formatCurrency($order['total_amount']); ?></p>
            <p><strong>Phương thức thanh toán:</strong> <?php echo htmlspecialchars($paymentMethod); ?></p>
            <p><strong>Địa chỉ giao hàng:</strong> <?php echo nl2br(htmlspecialchars($order['shipping_address'] ?? 'Chưa có địa chỉ')); ?></p>
        </div>
        <div class="order-card">
            <h2>Thông tin khách hàng</h2>
            <div class="customer-profile">
                <?php if (!empty($order['avatar'])): ?>
                    <img src="<?php echo (strpos($order['avatar'], 'http') === 0) ? $order['avatar'] : '../' . ltrim($order['avatar'], '/'); ?>" alt="<?php echo htmlspecialchars($order['full_name'] ?? ''); ?>">
                <?php else: ?>
                    <div class="customer-avatar"><i class="fa fa-user"></i></div>
                <?php endif; ?>
                <div>
                    <strong><?php echo htmlspecialchars($order['full_name'] ?? 'Khách hàng'); ?></strong>
                    <p><?php echo htmlspecialchars($order['email'] ?? '-'); ?></p>
                    <p><?php echo htmlspecialchars($order['phone'] ?? '-'); ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="order-items-card">
        <div class="order-items-header">
            <h2>Sản phẩm đã mua</h2>
            <span><?php echo count($order_items); ?> sản phẩm</span>
        </div>
        <div class="order-items-table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Số lượng</th>
                        <th>Đơn giá</th>
                        <th>Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order_items as $item):
                        $qty = (int)$item['quantity'];
                        $price = (float)($item['product_price'] ?? $item['price'] ?? 0);
                        $lineTotal = $qty * $price;
                        $productImage = $item['product_image'];
                        $imageSrc = $productImage ? ((strpos($productImage, 'http') === 0) ? $productImage : '../frontend/' . ltrim($productImage, '/')) : '';
                    ?>
                    <tr>
                        <td>
                            <div class="order-product">
                                <?php if ($imageSrc): ?>
                                    <img src="<?php echo htmlspecialchars($imageSrc); ?>" alt="<?php echo htmlspecialchars($item['product_name'] ?? ''); ?>">
                                <?php else: ?>
                                    <div class="product-placeholder"><i class="fa fa-box"></i></div>
                                <?php endif; ?>
                                <span><?php echo htmlspecialchars($item['product_name'] ?? 'Sản phẩm'); ?></span>
                            </div>
                        </td>
                        <td><?php echo $qty; ?></td>
                        <td><?php echo formatCurrency($price); ?></td>
                        <td><?php echo formatCurrency($lineTotal); ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($order_items)): ?>
                    <tr>
                        <td colspan="4" class="empty-row">Không có sản phẩm nào.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<style>
.order-view-section {
    padding: 40px 0;
}

.order-view-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    margin-bottom: 24px;
    gap: 16px;
}

.order-view-header h1 {
    margin: 0;
    font-size: 28px;
    color: #0f172a;
}

.order-status {
    display: inline-block;
    padding: 6px 14px;
    border-radius: 999px;
    font-size: 13px;
    margin-top: 6px;
    background: #d1e7dd;
    color: #0f5132;
}

.order-status.status-pending {
    background: #fff3cd;
    color: #664d03;
}

.order-status.status-processing {
    background: #cfe2ff;
    color: #084298;
}

.order-status.status-completed {
    background: #d4edda;
    color: #155724;
}

.back-link {
    color: #0b69d3;
    font-weight: 600;
    text-decoration: none;
}

.back-link:hover {
    text-decoration: underline;
}

.order-view-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 16px;
    margin-bottom: 32px;
}

.order-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
}

.order-card h2 {
    margin-top: 0;
    font-size: 18px;
    color: #0f172a;
}

.customer-profile {
    display: flex;
    align-items: center;
    gap: 14px;
}

.customer-profile img {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #e2e8f0;
}

.customer-avatar {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: #64748b;
}

.order-items-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
}

.order-items-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
}

.order-items-header h2 {
    margin: 0;
    font-size: 20px;
}

.order-items-table-wrapper {
    overflow-x: auto;
}

.order-items-table-wrapper table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
}

.order-items-table-wrapper th,
.order-items-table-wrapper td {
    padding: 14px 12px;
    border-bottom: 1px solid #e2e8f0;
    text-align: left;
}

.order-product {
    display: flex;
    align-items: center;
    gap: 12px;
}

.order-product img {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    object-fit: cover;
    border: 1px solid #e2e8f0;
}

.product-placeholder {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #64748b;
}

.empty-row {
    text-align: center;
    color: #64748b;
    font-style: italic;
}

@media (max-width: 768px) {
    .order-view-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .order-items-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 6px;
    }
}
</style>

<?php
include __DIR__ . '/../frontend/includes/footer.php';
?>