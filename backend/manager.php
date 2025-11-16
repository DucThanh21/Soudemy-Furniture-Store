<?php
ob_start();
session_start();

// Load shared frontend config + Database
require_once __DIR__ . '/../frontend/config/config.php';
require_once __DIR__ . '/../frontend/config/database.php';

// --- Initialize DB and current user/session safely ---
$database = new Database();
$db = $database->getConnection();

// Determine login state and load user
$isLoggedIn = false;
$user = null;
if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    try {
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $isLoggedIn = (bool)$user;
    } catch (Exception $e) {
        // DB error — treat as not logged in
        $isLoggedIn = false;
        $user = null;
    }
}

// Enforce admin-only access
if (!$isLoggedIn || ($user['role'] ?? '') !== 'admin') {
    // redirect to frontend login (use absolute path to avoid relative issues)
    header("Location: /Do_an/frontend/login.php");
    exit;
}

// If here, user is admin — include frontend header so nav uses session/user
include __DIR__ . '/../frontend/includes/header.php';

// --- ensure frontend CSS/JS load correctly when this page is served from /backend/ ---
echo '<!-- extra asset links to ensure admin UI loads correctly from backend -->' . PHP_EOL;
echo '<link rel="stylesheet" href="/Do_an/frontend/css/styles.css">' . PHP_EOL;
echo '<link rel="stylesheet" href="/Do_an/frontend/css/admin.css">' . PHP_EOL;
// expose base paths for frontend scripts so admin JS can build correct URLs
echo '<script>window.APP_BASE = "/Do_an/frontend/"; window.BACKEND_BASE = "/Do_an/backend/";</script>' . PHP_EOL;
// load the admin front-end script explicitly (absolute path)
echo '<script src="/Do_an/frontend/js/admin.js" defer></script>' . PHP_EOL;

$pageTitle = "Admin Panel - Soudemy";

// Check if user is admin
if (!$isLoggedIn || $user['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}
$database = new Database();
$db = $database->getConnection();

// Get statistics
$users_count = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
$products_count = $db->query("SELECT COUNT(*) FROM products")->fetchColumn();
$orders_count = $db->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$total_revenue = $db->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE status = 'completed'")->fetchColumn();

// Get recent orders
$query = "SELECT o.*, u.full_name, u.email 
          FROM orders o 
          JOIN users u ON o.user_id = u.id 
          ORDER BY o.created_at DESC 
          LIMIT 5";
$recent_orders = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="admin-section">
    <div class="container">
        <h1 class="page-title">Admin Dashboard</h1>
        
        <!-- Statistics -->
        <div class="admin-stats">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $users_count; ?></h3>
                    <p>Total Users</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa fa-cube"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $products_count; ?></h3>
                    <p>Total Products</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa fa-shopping-cart"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $orders_count; ?></h3>
                    <p>Total Orders</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa fa-dollar-sign"></i>
                </div>
                <div class="stat-info">
                    <h3>$<?php echo number_format($total_revenue, 2); ?></h3>
                    <p>Total Revenue</p>
                </div>
            </div>
        </div>
        
        <!-- Admin Tabs -->
        <div class="admin-tabs">
            <div class="tab-navigation">
                <button class="tab-btn active" data-tab="dashboard">Dashboard</button>
                <button class="tab-btn" data-tab="users">Users</button>
                <button class="tab-btn" data-tab="products">Products</button>
                <button class="tab-btn" data-tab="orders">Orders</button>
                <button class="tab-btn" data-tab="coupons">Coupons</button>
                <button class="tab-btn" data-tab="banners">Banners</button>
            </div>
            
            <div class="tab-content active" id="dashboard">
                <div class="dashboard-content">
                    <p>Welcome to the Soudemy Admin Dashboard. Use the tabs above to manage users, products, orders, coupons, and banners.</p>
                </div>
            </div>
            
            <div class="tab-content" id="users">
                <div class="tab-header">
                    <h3>User Management</h3>
                    <button class="btn btn-primary" id="addUserBtn">Add User</button>
                </div>
                <div class="users-table">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Role</th>
                                <th>Registered</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $users = $db->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($users as $user_item):
                            ?>
                            <tr>
                                <td><?php echo $user_item['id']; ?></td>
                                <td>
                                    <div class="user-info">
                                        <?php if (!empty($user_item['avatar']) && file_exists($user_item['avatar'])): ?>
                                        <img src="<?php echo $user_item['avatar']; ?>" alt="<?php echo $user_item['full_name']; ?>" class="user-avatar">
                                        <?php else: ?>
                                        <div class="user-avatar default-avatar">
                                            <i class="fa fa-user"></i>
                                        </div>
                                        <?php endif; ?>
                                        <span><?php echo $user_item['full_name']; ?></span>
                                    </div>
                                </td>
                                <td><?php echo $user_item['email']; ?></td>
                                <td><?php echo $user_item['phone'] ?: 'N/A'; ?></td>
                                <td>
                                    <span class="role-badge role-<?php echo $user_item['role']; ?>">
                                        <?php echo ucfirst($user_item['role']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($user_item['created_at'])); ?></td>
                                <td>
                                    <button class="btn-action view-user-details" data-user-id="<?php echo $user_item['id']; ?>" title="View Details" aria-label="View user details">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                    <button class="btn-action edit-user" data-user-id="<?php echo $user_item['id']; ?>">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <button class="btn-action delete-user" data-user-id="<?php echo $user_item['id']; ?>">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>        
                    </table>
                </div>
            </div>
            
            <div class="tab-content" id="products">
                <div class="tab-header">
                    <h3>Product Management</h3>
                    <button class="btn btn-primary" id="addProductBtn">Add Product</button>
                </div>
                <div class="products-table">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Rating</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $products = $db->query("SELECT * FROM products ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($products as $product):
                            ?>
                            <tr>
                                <td><?php echo $product['id']; ?></td>
                                <td>
                                    <img src="../frontend/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" class="product-thumb">
                                </td>
                                <td><?php echo $product['name']; ?></td>
                                <td>
                                    <span class="category-badge"><?php echo ucfirst($product['category']); ?></span>
                                </td>
                                <td>$<?php echo number_format($product['price'], 2); ?></td>
                                <td><?php echo $product['stock']; ?></td>
                                <td>
                                    <div class="product-rating">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fa <?php echo $i <= $product['rating'] ? 'fa-star' : 'fa-star-o'; ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                </td>
                                <td>
                                    <button
                                        class="btn-action edit-product"
                                        data-product-id="<?php echo $product['id']; ?>"
                                        data-product-name="<?php echo htmlspecialchars($product['name'], ENT_QUOTES); ?>"
                                        data-product-category="<?php echo htmlspecialchars($product['category'], ENT_QUOTES); ?>"
                                        data-product-price="<?php echo htmlspecialchars($product['price'], ENT_QUOTES); ?>"
                                        data-product-stock="<?php echo htmlspecialchars($product['stock'], ENT_QUOTES); ?>"
                                        data-product-description="<?php echo htmlspecialchars($product['description'] ?? '', ENT_QUOTES); ?>"
                                        data-product-image="<?php echo htmlspecialchars($product['image'], ENT_QUOTES); ?>"
                                    >
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <button class="btn-action delete-product" data-product-id="<?php echo $product['id']; ?>">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="tab-content" id="banners">
                <div class="tab-header">
                    <h3>Banner Management</h3>
                    <button class="btn btn-primary" id="addBannerBtn">Add Banner</button>
                </div>
                <div class="banners-grid">
                    <?php
                    $banners = $db->query("SELECT * FROM banners ORDER BY position, created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($banners as $banner):
                    ?>
                    <div class="banner-card">
                        <img src="../<?php echo $banner['image']; ?>" alt="<?php echo $banner['title']; ?>">
                        <div class="banner-info">
                            <h4><?php echo $banner['title']; ?></h4>
                            <p><?php echo $banner['description']; ?></p>
                            <div class="banner-meta">
                                <span>Position: <?php echo $banner['position']; ?></span>
                                <span class="status <?php echo $banner['is_active'] ? 'active' : 'inactive'; ?>">
                                    <?php echo $banner['is_active'] ? 'Active' : 'Inactive'; ?>
                                </span>
                            </div>
                            <div class="banner-actions">
                                <button class="btn-action edit-banner" data-banner-id="<?php echo $banner['id']; ?>">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="btn-action delete-banner" data-banner-id="<?php echo $banner['id']; ?>">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="tab-content" id="coupons">
                <div class="tab-header">
                    <h3>Coupon Management</h3>
                    <button class="btn btn-primary" id="addCouponBtn">Add Coupon</button>
                </div>
                <div class="coupons-table">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Coupon Code</th>
                                <th>Discount</th>
                                <th>Min Order</th>
                                <th>Usage</th>
                                <th>Validity</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $coupons = $db->query("SELECT * FROM coupons ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
                            if (empty($coupons)): ?>
                                <tr>
                                    <td colspan="8" style="text-align: center; padding: 20px;">
                                        No coupons found. <a href="javascript:void(0)" onclick="openModal('couponModal')">Create your first coupon</a>
                                    </td>
                                </tr>
                            <?php else:
                            foreach ($coupons as $coupon):
                            ?>
                            <tr>
                                <td><?php echo $coupon['id'] ?? ''; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($coupon['code'] ?? ''); ?></strong>
                                </td>
                                <td>
                                    <?php 
                                    $discountType = $coupon['discount_type'] ?? '';
                                    $discountValue = $coupon['discount_value'] ?? 0;
                                    if ($discountType == 'percentage') {
                                        echo $discountValue . '%';
                                    } else {
                                        echo '$' . number_format($discountValue, 2);
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php 
                                    $minOrder = $coupon['min_order_amount'] ?? 0;
                                    echo $minOrder > 0 ? '$' . number_format($minOrder, 2) : 'No minimum'; 
                                    ?>
                                </td>
                                <td>
                                    <?php 
                                    $usedCount = $coupon['used_count'] ?? 0;
                                    $usageLimit = $coupon['usage_limit'] ?? null;
                                    $usage_text = $usedCount . ' used';
                                    if ($usageLimit) {
                                        $usage_text .= ' / ' . $usageLimit . ' limit';
                                    } else {
                                        $usage_text .= ' (no limit)';
                                    }
                                    echo $usage_text;
                                    ?>
                                </td>
                                <td>
                                    <?php 
                                    $startDate = $coupon['start_date'] ?? '';
                                    $endDate = $coupon['end_date'] ?? '';
                                    
                                    if (!empty($startDate) && !empty($endDate)) {
                                        $now = time();
                                        $start = strtotime($startDate);
                                        $end = strtotime($endDate);
                                        
                                        if ($now < $start) {
                                            echo '<span class="status-badge status-pending">Starts ' . date('M d, Y', $start) . '</span>';
                                        } elseif ($now > $end) {
                                            echo '<span class="status-badge status-expired">Expired</span>';
                                        } else {
                                            echo '<span class="status-badge status-active">Valid until ' . date('M d, Y', $end) . '</span>';
                                        }
                                    } else {
                                        echo '<span class="status-badge status-inactive">No dates set</span>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php 
                                    $isActive = $coupon['is_active'] ?? 0;
                                    $statusClass = $isActive ? 'active' : 'inactive';
                                    $statusText = $isActive ? 'Active' : 'Inactive';
                                    ?>
                                    <span class="status-badge status-<?php echo $statusClass; ?>">
                                        <?php echo $statusText; ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn-action edit-coupon" data-coupon-id="<?php echo $coupon['id'] ?? ''; ?>">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <button class="btn-action delete-coupon" data-coupon-id="<?php echo $coupon['id'] ?? ''; ?>">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; 
                            endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Orders Tab -->
            <div class="tab-content" id="orders">
                <div class="tab-header">
                    <h3>Order Management</h3>
                    <button class="btn btn-primary" id="refreshOrdersBtn">
                        <i class="fa fa-refresh"></i> Refresh
                    </button>
                </div>
                <div class="orders-table" id="ordersTableContainer">
                    <table>
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $all_orders = $db->query("SELECT o.*, u.full_name, u.email FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
                            if (empty($all_orders)): ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 20px;">No orders found.</td>
                                </tr>
                            <?php else:
                            foreach ($all_orders as $order):
                            ?>
                            <tr>
                                <td><a class="order-link" href="order_view.php?order_id=<?php echo $order['id']; ?>">#<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></a></td>
                                <td>
                                    <div><?php echo $order['full_name']; ?></div>
                                    <small><?php echo $order['email']; ?></small>
                                </td>
                                <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                <td>
                                    <?php
                                    $statusKey = strtolower((string)($order['status'] ?? 'pending'));
                                    $statusTextMap = [
                                        'completed' => 'Completed',
                                        'processing' => 'Processing',
                                        'pending' => 'Pending'
                                    ];
                                    $statusText = $statusTextMap[$statusKey] ?? ucfirst($statusKey);
                                    ?>
                                    <span class="status-badge status-<?php echo str_replace(' ', '-', $statusKey); ?>">
                                        <?php echo $statusText; ?>
                                    </span>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                            </tr>
                            <?php endforeach;
                            endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Product Modal -->
<div id="productModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3 id="productModalTitle">Add New Product</h3>
        <form id="productForm" enctype="multipart/form-data">
            <input type="hidden" name="id" value="">
            <div class="form-group">
                <label>Product Name *</label>
                <input type="text" name="name" placeholder="Enter product name" required>
            </div>
            <div class="form-group">
                <label>Category *</label>
                <select name="category" required>
                    <option value="">-- Select category --</option>
                    <option value="sofa">Sofa</option>
                    <option value="table">Table</option>
                    <option value="lamp">Lamp</option>
                    <option value="bed">Bed</option>
                    <option value="bookshelf">Bookshelf</option>
                </select>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Price (USD) *</label>
                    <input type="number" name="price" step="0.01" min="0.01" placeholder="0.00" required>
                </div>
                <div class="form-group">
                    <label>Stock *</label>
                    <input type="number" name="stock" min="0" placeholder="0" required>
                </div>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="4" placeholder="Enter product description"></textarea>
            </div>
            <div class="form-group">
                <label id="productImageLabel">Product Image *</label>
                <div id="productCurrentImage" style="display:none; margin-bottom:10px;">
                    <strong>Current image:</strong>
                    <div style="margin-top:8px;">
                        <img id="productCurrentImagePreview" src="" alt="Current product" style="max-width:120px; border:1px solid #ddd; border-radius:6px; padding:4px; background:#fff;">
                    </div>
                </div>
                <input type="file" name="image" id="productImage" accept="image/jpeg,image/png,image/gif,image/webp">
                <small>Formats: JPG, PNG, GIF, WebP. Maximum size: 5MB</small>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary" id="productSubmitBtn">Save Product</button>
            </div>
        </form>
    </div>
</div>

<!-- User Modal -->
<div id="userModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3 id="userModalTitle">Add User</h3>
        <form id="userForm">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="full_name" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <div class="form-group">
                <label>Phone</label>
                <input type="tel" name="phone">
            </div>
            <div class="form-group">
                <label>Role</label>
                <select name="role" required>
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save User</button>
            </div>
        </form>
    </div>
</div>

<!-- User Details Modal -->
<div id="userDetailsModal" class="modal">
    <div class="modal-content" style="max-width: 1000px;">
        <span class="close">&times;</span>
        <h3>User Details</h3>
        <div id="userDetailsContent">
            <p>Loading user details...</p>
        </div>
    </div>
</div>

<!-- Order Details Modal -->
<div id="orderDetailsModal" class="modal">
    <div class="modal-content" style="max-width: 800px;">
        <span class="close">&times;</span>
        <h3>Order Details</h3>
        <div id="orderDetailsContent">
            <p>Loading order details...</p>
        </div>
    </div>
</div>

<!-- Coupon Modal -->
<div id="couponModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3 id="couponModalTitle">Add Coupon</h3>
        <form id="couponForm">
            <div class="form-group">
                <label>Coupon Code *</label>
                <input type="text" name="code" required placeholder="e.g., SALE20">
            </div>
            
            <div class="form-group">
                <label>Discount Type *</label>
                <select name="discount_type" required>
                    <option value="percentage">Percentage (%)</option>
                    <option value="fixed">Fixed Amount ($)</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Discount Value *</label>
                <input type="number" name="discount_value" step="0.01" required placeholder="e.g., 20 for 20% or $20">
            </div>
            
            <div class="form-group">
                <label>Minimum Order Amount</label>
                <input type="number" name="min_order_amount" step="0.01" placeholder="0 for no minimum">
            </div>
            
            <div class="form-group">
                <label>Maximum Discount Amount (for percentage only)</label>
                <input type="number" name="max_discount_amount" step="0.01" placeholder="Leave empty for no limit">
            </div>
            
            <div class="form-group">
                <label>Usage Limit</label>
                <input type="number" name="usage_limit" placeholder="Leave empty for unlimited">
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Start Date *</label>
                    <input type="datetime-local" name="start_date" required>
                </div>
                
                <div class="form-group">
                    <label>End Date *</label>
                    <input type="datetime-local" name="end_date" required>
                </div>
            </div>
            
            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_active" value="1" checked> Active Coupon
                </label>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save Coupon</button>
            </div>
        </form>
    </div>
</div>

<style>
/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    z-index: 10000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
}

.modal-content {
    background-color: white;
    margin: 5% auto;
    padding: 30px;
    border-radius: 8px;
    width: 90%;
    max-width: 600px;
    max-height: 80vh;
    overflow-y: auto;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.modal .close {
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    color: #aaa;
    line-height: 1;
}

.modal .close:hover {
    color: #000;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    box-sizing: border-box;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

.form-actions {
    text-align: center;
    margin-top: 30px;
}

.btn-primary {
    background: #007bff;
    color: white;
    border: none;
    padding: 12px 30px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
    font-weight: bold;
}

.btn-primary:hover {
    background: #0056b3;
}

/* Tab Styles */
.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

.tab-btn.active {
    background: #007bff;
    color: white;
}
.user-info {
    display: flex;
    align-items: center;
    gap: 10px;
}

.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}

.default-avatar {
    background: #000;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
}

.default-avatar .fa-user {
    margin: 0;
}

.role-badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: bold;
}

.role-admin {
    background: #dc3545;
    color: white;
}

.role-user {
    background: #28a745;
    color: white;
}

.btn-action {
    background: none;
    border: none;
    cursor: pointer;
    padding: 5px;
    margin: 0 2px;
    color: #666;
}

.btn-action:hover {
    color: #000;
}

/* Status Badge Styles */
.status-badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: bold;
}

.status-completed {
    background: #d4edda;
    color: #155724;
}

.status-pending {
    background: #fff3cd;
    color: #856404;
}

.status-processing {
    background: #cfe2ff;
    color: #084298;
}

.status-completed-dash {
    background: #d4edda;
    color: #155724;
}

.status-pending-dash {
    background: #fff3cd;
    color: #856404;
}

.status-processing-dash {
    background: #cfe2ff;
    color: #084298;
}

.order-link {
    color: #0b69d3;
    font-weight: 600;
    text-decoration: none;
}

.order-link:hover {
    text-decoration: underline;
}
</style>

<script>
// Global modal management
function openModal(modalId) {
    console.log('Opening modal:', modalId);
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'block';
    } else {
        console.error('Modal not found:', modalId);
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
    }
}

function viewOrderDetails(orderId) {
    console.log('Fetching order details for order ID:', orderId);
    
    // Show the modal immediately with a loading message
    document.getElementById('orderDetailsContent').innerHTML = '<div style="text-align:center; padding:40px;"><i class="fa fa-spinner fa-spin" style="font-size:48px; color:#007bff;"></i><p style="margin-top:15px;">Loading order information...</p></div>';
    openModal('orderDetailsModal');
    
    fetch(`/Do_an/backend/api/order_details.php?order_id=${orderId}`)
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response ok:', response.ok);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('API response:', data);
            if (data.success) {
                const order = data.order || {};
                const items = Array.isArray(data.items) ? data.items : [];
                const user = data.user || null;
                const userSummary = data.user_summary || { total_orders: 0, total_spent: 0 };
                const recentOrders = Array.isArray(data.recent_orders) ? data.recent_orders : [];

                // Build the product list
                let itemsHtml = '';
                if (items.length > 0) {
                    itemsHtml = '<table style="width:100%; border-collapse:collapse;">';
                    itemsHtml += '<thead><tr style="background:#f5f5f5;">';
                    itemsHtml += '<th style="padding:10px; border:1px solid #ddd; text-align:left;">Product</th>';
                    itemsHtml += '<th style="padding:10px; border:1px solid #ddd; text-align:center;">Quantity</th>';
                    itemsHtml += '<th style="padding:10px; border:1px solid #ddd; text-align:right;">Price</th>';
                    itemsHtml += '<th style="padding:10px; border:1px solid #ddd; text-align:right;">Total</th>';
                    itemsHtml += '</tr></thead><tbody>';

                    items.forEach(item => {
                        const lineTotal = parseFloat(item.price || 0) * parseFloat(item.quantity || 0);
                        const productThumb = item.product_image ? `<img src="../frontend/${item.product_image}" alt="${item.product_name}" style="width:48px; height:48px; object-fit:cover; border-radius:6px; border:1px solid #eee; margin-right:8px;">` : '';
                        itemsHtml += `<tr>
                            <td style="padding:10px; border:1px solid #ddd; display:flex; align-items:center;">${productThumb}<span>${item.product_name || 'N/A'}</span></td>
                            <td style="padding:10px; border:1px solid #ddd; text-align:center;">${item.quantity || 0}</td>
                            <td style="padding:10px; border:1px solid #ddd; text-align:right;">$${parseFloat(item.price || 0).toFixed(2)}</td>
                            <td style="padding:10px; border:1px solid #ddd; text-align:right;">$${lineTotal.toFixed(2)}</td>
                        </tr>`;
                    });

                    itemsHtml += '</tbody></table>';
                } else {
                    itemsHtml = '<p style="margin:0; color:#666;">No items in this order.</p>';
                }

                const statusMap = {
                    completed: { text: 'Completed', className: 'status-completed' },
                    processing: { text: 'Processing', className: 'status-processing' },
                    pending: { text: 'Pending', className: 'status-pending' }
                };
                const normalizedStatus = String(order.status || 'pending').toLowerCase();
                const statusMeta = statusMap[normalizedStatus] || statusMap.pending;
                const statusText = statusMeta.text;
                const statusClass = statusMeta.className;

                const paymentMethod = order.payment_method === 'cod'
                    ? 'Cash on Delivery'
                    : (order.payment_method || 'N/A');
                const orderDate = order.created_at ? new Date(order.created_at).toLocaleString('en-US') : 'N/A';
                const phoneNumber = order.phone || (user ? user.phone : '') || 'N/A';

                const userHtml = user ? `
                    <div style="padding:12px; border-radius:8px; background:#fff; border:1px solid #eef2f6;">
                        <div style="display:flex; gap:12px; align-items:center; margin-bottom:12px;">
                            <div style="width:64px; height:64px; border-radius:50%; overflow:hidden; background:#f0f0f0; display:flex; align-items:center; justify-content:center;">
                                ${user.avatar ? `<img src="${user.avatar}" alt="${user.full_name}" style="width:100%; height:100%; object-fit:cover;">` : `<i class="fa fa-user" style="font-size:28px; color:#777;"></i>`}
                            </div>
                            <div>
                                <strong style="font-size:16px;">${user.full_name || user.email || 'N/A'}</strong>
                                <div style="font-size:13px; color:#666;">${user.email || 'N/A'}</div>
                                <div style="font-size:13px; color:#666;">${phoneNumber}</div>
                            </div>
                        </div>

                        <div style="display:grid; grid-template-columns: repeat(2, 1fr); gap:8px; margin-bottom:12px; font-size:13px; color:#444;">
                            <div><strong>Registered:</strong><div style="color:#666;">${user.created_at ? new Date(user.created_at).toLocaleDateString('en-US') : 'N/A'}</div></div>
                            <div><strong>Role:</strong><div style="color:#666;">${user.role || 'N/A'}</div></div>
                            <div><strong>Total Orders:</strong><div style="color:#666;">${userSummary.total_orders}</div></div>
                            <div><strong>Total Spent:</strong><div style="color:#666;">$${parseFloat(userSummary.total_spent || 0).toFixed(2)}</div></div>
                        </div>

                        <div style="margin-top:8px;">
                            <strong style="display:block; margin-bottom:8px;">Recent Orders</strong>
                            ${recentOrders.length === 0
                                ? '<div style="color:#666;">No recent orders</div>'
                                : '<ul style="padding-left:18px; margin:0;">' + recentOrders.map(ro => `<li style="margin-bottom:6px;">#${String(ro.id).padStart(6,'0')} — $${parseFloat(ro.total_amount || 0).toFixed(2)} — ${ro.status} <small style="color:#666;">(${ro.created_at ? new Date(ro.created_at).toLocaleDateString('en-US') : 'N/A'})</small></li>`).join('') + '</ul>'}
                        </div>
                    </div>
                ` : '<div style="color:#666;">User information not available.</div>';

                const contentHtml = `
                    <div style="display:grid; grid-template-columns:320px 1fr; gap:18px;">
                        <div>${userHtml}</div>
                        <div>
                            <div style="margin-bottom:18px; padding:16px; border-radius:8px; background:#fff; border:1px solid #eef2f6;">
                                <h4 style="margin:0 0 12px 0; color:#333;">Order Information #${String(order.id || 0).padStart(6, '0')}</h4>
                                <div style="display:grid; grid-template-columns:repeat(2, minmax(0,1fr)); gap:12px;">
                                    <div><strong>Customer:</strong><div style="color:#000;">${order.full_name || (user && user.full_name) || 'N/A'}</div></div>
                                    <div><strong>Payment:</strong><div style="color:#000;">${paymentMethod}</div></div>
                                    <div><strong>Email:</strong><div style="color:#000;">${order.email || (user && user.email) || 'N/A'}</div></div>
                                    <div><strong>Phone:</strong><div style="color:#000;">${phoneNumber}</div></div>
                                    <div><strong>Order Date:</strong><div style="color:#000;">${orderDate}</div></div>
                                </div>
                                <div style="margin-top:12px;"><strong>Shipping Address:</strong>
                                    <div style="margin-top:6px; padding:10px; background:#f9f9f9; border-left:3px solid #007bff;">${order.shipping_address || 'N/A'}</div>
                                </div>
                            </div>

                            <div style="margin-bottom:18px; padding:16px; border-radius:8px; background:#fff; border:1px solid #eef2f6;">
                                <h4 style="margin:0 0 12px 0; color:#333;">Purchased Products</h4>
                                ${itemsHtml}
                                <div style="text-align:right; margin-top:12px; font-size:16px;"><strong>Total:</strong> <span style="color:#007bff; font-weight:bold;">$${parseFloat(order.total_amount || 0).toFixed(2)}</span></div>
                            </div>

                            <div style="padding:16px; border-radius:8px; background:#fff; border:1px solid #eef2f6;">
                                <h4 style="margin:0 0 12px 0; color:#333;">Order Status</h4>
                                <div style="margin-bottom:12px;"><span class="status-badge ${statusClass}" style="padding:8px 16px;">${statusText}</span></div>
                                <label style="display:block; margin-bottom:8px;"><strong>Update Status:</strong></label>
                                <select id="orderStatusSelect" style="padding:10px; border:1px solid #ddd; border-radius:4px; width:100%; margin-bottom:12px; font-size:14px;">
                                    <option value="pending" ${normalizedStatus === 'pending' ? 'selected' : ''}>Pending</option>
                                    <option value="processing" ${normalizedStatus === 'processing' ? 'selected' : ''}>Processing</option>
                                    <option value="completed" ${normalizedStatus === 'completed' ? 'selected' : ''}>Completed</option>
                                </select>
                                <button onclick="updateOrderStatus(${order.id || ''})" style="width:100%; padding:10px 20px; background:#28a745; color:white; border:none; border-radius:4px; cursor:pointer; font-weight:bold;">Save Status</button>
                            </div>
                        </div>
                    </div>
                `;

                document.getElementById('orderDetailsContent').innerHTML = contentHtml;
                openModal('orderDetailsModal');
            } else {
                alert('Error loading order details: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error: ' + error.message);
        });
}

function updateOrderStatus(orderId) {
    const newStatus = document.getElementById('orderStatusSelect').value;
    
    if (!newStatus) {
        alert('Please choose a status');
        return;
    }
    
    fetch('/Do_an/backend/api/update_order_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `order_id=${orderId}&status=${newStatus}`
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            alert('Order status updated successfully!');
            // Close modal
            document.getElementById('orderDetailsModal').style.display = 'none';
            // Reload page to refresh the list
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Update failed'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error: ' + error.message);
    });
}

// View user details function
function viewUserDetails(userId) {
    console.log('Loading user details for ID:', userId);
    
    fetch(`/Do_an/backend/api/user_details.php?user_id=${userId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const user = data.user || {};
                const orders = Array.isArray(data.orders) ? data.orders : [];
                const cartItems = Array.isArray(data.cart_items) ? data.cart_items : [];
                
                // Calculate summary statistics
                const totalOrders = orders.length;
                const totalSpent = orders.reduce((sum, order) => sum + parseFloat(order.total_amount || 0), 0);
                const pendingOrders = orders.filter(o => (o.status || '').toLowerCase() === 'pending').length;
                const completedOrders = orders.filter(o => (o.status || '').toLowerCase() === 'completed').length;
                
                // Build cart items section
                let cartHtml = '';
                let cartTotal = 0;
                if (cartItems.length > 0) {
                    cartHtml = '<table style="width:100%; border-collapse:collapse; margin-top:12px;">';
                    cartHtml += '<thead><tr style="background:#fff3cd;">';
                    cartHtml += '<th style="padding:10px; border:1px solid #ddd; text-align:left;">Product</th>';
                    cartHtml += '<th style="padding:10px; border:1px solid #ddd; text-align:center;">Quantity</th>';
                    cartHtml += '<th style="padding:10px; border:1px solid #ddd; text-align:right;">Price</th>';
                    cartHtml += '<th style="padding:10px; border:1px solid #ddd; text-align:right;">Subtotal</th>';
                    cartHtml += '<th style="padding:10px; border:1px solid #ddd; text-align:left;">Added</th>';
                    cartHtml += '</tr></thead><tbody>';
                    
                    cartItems.forEach(item => {
                        const itemPrice = parseFloat(item.price || 0);
                        const itemQty = parseInt(item.quantity || 0);
                        const itemSubtotal = itemPrice * itemQty;
                        cartTotal += itemSubtotal;
                        
                        const productThumb = item.image ? `<img src="../frontend/${item.image}" alt="${item.product_name}" style="width:40px; height:40px; object-fit:cover; border-radius:6px; border:1px solid #eee; margin-right:8px;">` : '';
                        
                        cartHtml += `<tr>
                            <td style="padding:10px; border:1px solid #ddd; display:flex; align-items:center;">${productThumb}<span>${item.product_name || 'N/A'}</span></td>
                            <td style="padding:10px; border:1px solid #ddd; text-align:center;">${itemQty}</td>
                            <td style="padding:10px; border:1px solid #ddd; text-align:right;">$${itemPrice.toFixed(2)}</td>
                            <td style="padding:10px; border:1px solid #ddd; text-align:right; font-weight:bold;">$${itemSubtotal.toFixed(2)}</td>
                            <td style="padding:10px; border:1px solid #ddd;">${item.added_at ? new Date(item.added_at).toLocaleDateString('en-US') : 'N/A'}</td>
                        </tr>`;
                    });
                    
                    cartHtml += `<tr style="background:#fff3cd; font-weight:bold;">
                        <td colspan="3" style="padding:12px; border:1px solid #ddd; text-align:right;">Cart Total:</td>
                        <td style="padding:12px; border:1px solid #ddd; text-align:right; color:#856404; font-size:16px;">$${cartTotal.toFixed(2)}</td>
                        <td style="padding:12px; border:1px solid #ddd;"></td>
                    </tr>`;
                    cartHtml += '</tbody></table>';
                } else {
                    cartHtml = '<p style="margin-top:12px; color:#666; text-align:center;">Cart is empty</p>';
                }
                
                // Build orders table
                let ordersHtml = '';
                if (orders.length > 0) {
                    ordersHtml = '<table style="width:100%; border-collapse:collapse; margin-top:12px;">';
                    ordersHtml += '<thead><tr style="background:#f5f5f5;">';
                    ordersHtml += '<th style="padding:10px; border:1px solid #ddd; text-align:left;">Order ID</th>';
                    ordersHtml += '<th style="padding:10px; border:1px solid #ddd; text-align:left;">Date</th>';
                    ordersHtml += '<th style="padding:10px; border:1px solid #ddd; text-align:right;">Amount</th>';
                    ordersHtml += '<th style="padding:10px; border:1px solid #ddd; text-align:left;">Payment</th>';
                    ordersHtml += '<th style="padding:10px; border:1px solid #ddd; text-align:center;">Status</th>';
                    ordersHtml += '<th style="padding:10px; border:1px solid #ddd; text-align:center;">Action</th>';
                    ordersHtml += '</tr></thead><tbody>';
                    
                    orders.forEach(order => {
                        const statusMap = {
                            completed: { text: 'Completed', className: 'status-completed' },
                            processing: { text: 'Processing', className: 'status-processing' },
                            pending: { text: 'Pending', className: 'status-pending' }
                        };
                        const normalizedStatus = String(order.status || 'pending').toLowerCase();
                        const statusMeta = statusMap[normalizedStatus] || statusMap.pending;
                        const paymentMethod = order.payment_method === 'cod' ? 'COD' : (order.payment_method || 'N/A');
                        
                        ordersHtml += `<tr>
                            <td style="padding:10px; border:1px solid #ddd;">#${String(order.id).padStart(6, '0')}</td>
                            <td style="padding:10px; border:1px solid #ddd;">${order.created_at ? new Date(order.created_at).toLocaleDateString('en-US') : 'N/A'}</td>
                            <td style="padding:10px; border:1px solid #ddd; text-align:right;">$${parseFloat(order.total_amount || 0).toFixed(2)}</td>
                            <td style="padding:10px; border:1px solid #ddd;">${paymentMethod}</td>
                            <td style="padding:10px; border:1px solid #ddd; text-align:center;"><span class="status-badge ${statusMeta.className}">${statusMeta.text}</span></td>
                            <td style="padding:10px; border:1px solid #ddd; text-align:center;">
                                <button onclick="closeModal('userDetailsModal'); viewOrderDetails(${order.id})" style="padding:6px 12px; background:#007bff; color:white; border:none; border-radius:4px; cursor:pointer;">
                                    <i class="fa fa-eye"></i> View
                                </button>
                            </td>
                        </tr>`;
                    });
                    
                    ordersHtml += '</tbody></table>';
                } else {
                    ordersHtml = '<p style="margin-top:12px; color:#666; text-align:center;">No orders yet</p>';
                }
                
                const registeredDate = user.created_at ? new Date(user.created_at).toLocaleDateString('en-US', { 
                    year: 'numeric', month: 'long', day: 'numeric' 
                }) : 'N/A';
                
                // Statistics with cart count
                const cartItemsCount = cartItems.length;
                
                const contentHtml = `
                    <div style="padding:20px;">
                        <!-- User Profile Card -->
                        <div style="margin-bottom:24px; padding:20px; border-radius:8px; background:#f8f9fa; border:1px solid #e9ecef;">
                            <div style="display:flex; gap:20px; align-items:center; margin-bottom:20px;">
                                <div style="width:80px; height:80px; border-radius:50%; overflow:hidden; background:#e0e0e0; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                    ${user.avatar ? `<img src="${user.avatar}" alt="${user.full_name}" style="width:100%; height:100%; object-fit:cover;">` : `<i class="fa fa-user" style="font-size:36px; color:#777;"></i>`}
                                </div>
                                <div style="flex:1;">
                                    <h3 style="margin:0 0 8px 0; font-size:24px; color:#333;">${user.full_name || 'N/A'}</h3>
                                    <div style="font-size:14px; color:#666; margin-bottom:4px;"><i class="fa fa-envelope" style="margin-right:8px;"></i>${user.email || 'N/A'}</div>
                                    <div style="font-size:14px; color:#666; margin-bottom:4px;"><i class="fa fa-phone" style="margin-right:8px;"></i>${user.phone || 'N/A'}</div>
                                    <div style="font-size:14px; color:#666;"><i class="fa fa-calendar" style="margin-right:8px;"></i>Registered: ${registeredDate}</div>
                                </div>
                                <div>
                                    <span class="role-badge role-${user.role}" style="padding:8px 16px; border-radius:4px; font-weight:bold; font-size:14px;">
                                        ${user.role ? user.role.toUpperCase() : 'USER'}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Statistics -->
                        <div style="display:grid; grid-template-columns:repeat(5, 1fr); gap:16px; margin-bottom:24px;">
                            <div style="padding:16px; border-radius:8px; background:#fff; border:1px solid #e9ecef; text-align:center;">
                                <div style="font-size:28px; font-weight:bold; color:#007bff; margin-bottom:4px;">${totalOrders}</div>
                                <div style="font-size:13px; color:#666;">Total Orders</div>
                            </div>
                            <div style="padding:16px; border-radius:8px; background:#fff; border:1px solid #e9ecef; text-align:center;">
                                <div style="font-size:28px; font-weight:bold; color:#28a745; margin-bottom:4px;">$${totalSpent.toFixed(2)}</div>
                                <div style="font-size:13px; color:#666;">Total Spent</div>
                            </div>
                            <div style="padding:16px; border-radius:8px; background:#fff; border:1px solid #e9ecef; text-align:center;">
                                <div style="font-size:28px; font-weight:bold; color:#ffc107; margin-bottom:4px;">${pendingOrders}</div>
                                <div style="font-size:13px; color:#666;">Pending</div>
                            </div>
                            <div style="padding:16px; border-radius:8px; background:#fff; border:1px solid #e9ecef; text-align:center;">
                                <div style="font-size:28px; font-weight:bold; color:#28a745; margin-bottom:4px;">${completedOrders}</div>
                                <div style="font-size:13px; color:#666;">Completed</div>
                            </div>
                            <div style="padding:16px; border-radius:8px; background:#fff3cd; border:1px solid #ffc107; text-align:center;">
                                <div style="font-size:28px; font-weight:bold; color:#856404; margin-bottom:4px;">${cartItemsCount}</div>
                                <div style="font-size:13px; color:#856404; font-weight:bold;">In Cart</div>
                            </div>
                        </div>
                        
                        <!-- Cart Items Section (if any) -->
                        ${cartItemsCount > 0 ? `
                        <div style="padding:20px; border-radius:8px; background:#fff; border:2px solid #ffc107; margin-bottom:24px;">
                            <h4 style="margin:0 0 16px 0; color:#856404; font-size:18px;">
                                <i class="fa fa-shopping-basket" style="margin-right:8px;"></i>Current Cart Items (Waiting - Not Ordered Yet)
                            </h4>
                            ${cartHtml}
                        </div>
                        ` : ''}
                        
                        <!-- Orders Section -->
                        <div style="padding:20px; border-radius:8px; background:#fff; border:1px solid #e9ecef;">
                            <h4 style="margin:0 0 16px 0; color:#333; font-size:18px;">
                                <i class="fa fa-shopping-cart" style="margin-right:8px;"></i>Order History
                            </h4>
                            ${ordersHtml}
                        </div>
                    </div>
                `;
                
                document.getElementById('userDetailsContent').innerHTML = contentHtml;
                openModal('userDetailsModal');
            } else {
                alert('Error loading user details: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error: ' + error.message);
        });
}

// Load orders from the server
function loadOrders() {
    console.log('Loading orders...');
    const container = document.getElementById('ordersTableContainer');
    if (!container) {
        console.error('Orders table container not found');
        return;
    }

    container.innerHTML = '<div style="text-align:center; padding:40px;"><i class="fa fa-spinner fa-spin" style="font-size:48px; color:#007bff;"></i><p style="margin-top:15px;">Loading orders...</p></div>';

    fetch('/Do_an/backend/api/get_orders.php')
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success && Array.isArray(data.orders)) {
                if (data.orders.length === 0) {
                    container.innerHTML = '<div style="text-align:center; padding:40px; color:#666;"><p>No orders yet.</p></div>';
                    return;
                }

                const rowsHtml = data.orders.map(order => {
                    const paymentLabel = order.payment_method === 'cod' ? 'Cash on Delivery' : (order.payment_method || 'N/A');
                    const statusKey = (order.status || 'pending').toLowerCase().replace(/\s+/g, '-');
                    const statusText = order.status ? order.status.charAt(0).toUpperCase() + order.status.slice(1) : 'Pending';
                    const createdAt = order.created_at ? new Date(order.created_at).toLocaleDateString('en-US') : 'N/A';

                    return `
                        <tr>
                            <td><a class="order-link" href="/Do_an/backend/order_view.php?order_id=${order.id}">#${String(order.id).padStart(6, '0')}</a></td>
                            <td>${order.full_name || order.email || 'Guest'}</td>
                            <td>$${parseFloat(order.total_amount || 0).toFixed(2)}</td>
                            <td>${paymentLabel}</td>
                            <td><span class="status-badge status-${statusKey}">${statusText}</span></td>
                            <td>${createdAt}</td>
                        </tr>`;
                }).join('');

                container.innerHTML = `
                    <table>
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Payment</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${rowsHtml}
                        </tbody>
                    </table>`;
            } else {
                container.innerHTML = '<div style="text-align:center; padding:40px; color:red;"><p>Error: ' + (data.message || 'Unable to load orders') + '</p></div>';
            }
        })
        .catch(error => {
            console.error('Error loading orders:', error);
            container.innerHTML = '<div style="text-align:center; padding:40px; color:red;"><p>Error: ' + error.message + '</p><button class="btn btn-primary" onclick="loadOrders()">Retry</button></div>';
        });
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    console.log('Initializing admin panel...');
    
    // Tab navigation
    document.querySelectorAll('.tab-btn').forEach(button => {
        button.addEventListener('click', function() {
            const tabId = this.dataset.tab;
            
            // Remove active class from all tabs and contents
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
            
            // Add active class to current tab and content
            this.classList.add('active');
            document.getElementById(tabId).classList.add('active');
            
            // Load orders when switching to the Orders tab
            if (tabId === 'orders') {
                loadOrders();
            }
        });
    });
    
    // Modal close buttons
    document.querySelectorAll('.modal .close').forEach(closeBtn => {
        closeBtn.addEventListener('click', function() {
            this.closest('.modal').style.display = 'none';
        });
    });
    
    // Close modal when clicking outside
    window.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal')) {
            e.target.style.display = 'none';
        }
    });
    
    // Add Product button
    document.getElementById('addProductBtn')?.addEventListener('click', function() {
        console.log('Add product button clicked');
        openModal('productModal');
    });
    
    // Add User button
    document.getElementById('addUserBtn')?.addEventListener('click', function() {
        console.log('Add user button clicked');
        openModal('userModal');
    });
    
    // Add Coupon button
    document.getElementById('addCouponBtn')?.addEventListener('click', function() {
        console.log('Add coupon button clicked');
        openModal('couponModal');
    });
    
    // Add Banner button
    document.getElementById('addBannerBtn')?.addEventListener('click', function() {
        console.log('Add banner button clicked');
        alert('Add banner functionality will be implemented soon');
    });
    
    // Product form submission
    document.getElementById('productForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        console.log('Product form submitted');
        alert('Product added successfully! (This is a demo)');
        closeModal('productModal');
    });
    
    // User form submission
    document.getElementById('userForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        console.log('User form submitted');
        alert('User added successfully! (This is a demo)');
        closeModal('userModal');
    });
    
    // Coupon form submission
    document.getElementById('couponForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        console.log('Coupon form submitted');
        
        const formData = new FormData(this);
        
        // Simple validation
        const code = formData.get('code');
        const discountValue = formData.get('discount_value');
        const startDate = formData.get('start_date');
        const endDate = formData.get('end_date');
        
        if (!code || !discountValue || !startDate || !endDate) {
            alert('Please fill in all required fields');
            return;
        }
        
        // Show loading
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = 'Saving...';
        submitBtn.disabled = true;
        
        // Simulate API call
        setTimeout(() => {
            alert('Coupon created successfully! (This is a demo)');
            closeModal('couponModal');
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }, 1000);
    });
    
    // Edit and Delete buttons - basic functionality
    document.querySelectorAll('.edit-product, .edit-user, .edit-coupon, .edit-banner').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.productId || this.dataset.userId || this.dataset.couponId || this.dataset.bannerId;
            alert('Edit functionality for ID: ' + id + ' will be implemented soon');
        });
    });

    document.querySelectorAll('.view-user-details').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.dataset.userId;
            viewUserDetails(userId);
        });
    });
    
    document.querySelectorAll('.delete-product, .delete-user, .delete-coupon, .delete-banner').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.productId || this.dataset.userId || this.dataset.couponId || this.dataset.bannerId;
            if (confirm('Are you sure you want to delete this item?')) {
                alert('Delete functionality for ID: ' + id + ' will be implemented soon');
            }
        });
    });
    
    // Refresh orders button
    document.getElementById('refreshOrdersBtn')?.addEventListener('click', function() {
        loadOrders();
    });
    
    // Load orders immediately if the Orders tab is active
    if (document.getElementById('orders').classList.contains('active')) {
        loadOrders();
    }
    
    console.log('Admin panel initialized successfully');
});
</script>

<?php
// Include the shared frontend footer for consistent layout
include __DIR__ . '/../frontend/includes/footer.php';
?>