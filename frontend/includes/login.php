<?php
ob_start();
session_start();

// Use central DB config
require_once __DIR__ . '/../config/database.php';

// If already logged in, go to frontend home (use SITE_URL when available)
$homeRedirect = defined('SITE_URL') ? rtrim(SITE_URL, '/') . '/index.php' : '/Do_an/frontend/index.php';
if (isset($_SESSION['user_id'])) {
    header('Location: ' . $homeRedirect);
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        if ($db) {
            $query = "SELECT * FROM users WHERE email = ? LIMIT 1";
            $stmt = $db->prepare($query);
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'] ?? $user['email'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['full_name'] = $user['full_name'] ?? '';
                
                $cartQuery = "SELECT SUM(quantity) as total FROM cart WHERE user_id = ?";
                $cartStmt = $db->prepare($cartQuery);
                $cartStmt->execute([$user['id']]);
                $cartData = $cartStmt->fetch(PDO::FETCH_ASSOC);
                $_SESSION['cart_count'] = $cartData['total'] ?? 0;
                
                ob_end_clean();
                header('Location: ' . $homeRedirect);
                exit;
            } else {
                $error = "Invalid email or password!";
            }
        } else {
            $error = "Database connection error.";
        }
    } catch (Exception $e) {
        error_log("Login error: " . $e->getMessage());
        $error = "An error occurred in the system.";
    }
}

$pageTitle = "Login - Soudemy";
include 'header.php';
?>
<!-- HTML SECTION KEPT AS IS -->
<div class="auth-container">
    <div class="auth-form">
        <h2>Login</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" placeholder="Enter password" required>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form>
        
        <div class="auth-links">
            <p>Don't have an account? <a href="register.php">Sign up now</a></p>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>