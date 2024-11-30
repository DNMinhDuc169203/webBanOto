<?php
session_start();
require_once 'config.php';

// Thêm các constants cho Google và Facebook OAuth
define('GOOGLE_CLIENT_ID', '949168644612-84ql45lv2dtljsq64u7ll4uh9mgq5p8i.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', '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');
define('FACEBOOK_APP_ID', 'your_facebook_app_id');
define('FACEBOOK_APP_SECRET', 'your_facebook_app_secret');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    try {
        $stmt = $conn->prepare("SELECT tk.*, kh.hovaten, kh.idkh 
                               FROM taikhoan tk 
                               JOIN khachhang kh ON tk.idkh = kh.idkh 
                               WHERE tk.tendangnhap = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && $password === $user['matkhau']) {
            $_SESSION['user'] = [
                'id' => $user['idkh'],
                'name' => $user['hovaten'],
            ];

            // Load giỏ hàng từ database
            try {
                $stmt = $conn->prepare("
                    SELECT g.*, o.tenxe, o.giaxe, o.img 
                    FROM giohang g
                    JOIN oto o ON g.idoto = o.idoto
                    WHERE g.idkh = ?
                ");
                $stmt->execute([$user['idkh']]);
                $_SESSION['carts'][$user['idkh']] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch(PDOException $e) {
                error_log($e->getMessage());
            }

            header('Location: index.php');
            exit();
        } else {
            $error = "Tên đăng nhập hoặc mật khẩu không đúng";
        }
    } catch(PDOException $e) {
        $error = "Lỗi: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Thêm Facebook SDK -->
    <script async defer crossorigin="anonymous" 
            src="https://connect.facebook.net/vi_VN/sdk.js#xfbml=1&version=v18.0&appId=your_actual_facebook_app_id">
    </script>
    <!-- Thêm các meta tags này -->
    <meta http-equiv="Cross-Origin-Opener-Policy" content="same-origin-allow-popups">
    <meta http-equiv="Cross-Origin-Embedder-Policy" content="require-corp">
    <script src="https://accounts.google.com/gsi/client" async defer></script>
</head>
<body>
    <header class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <div class="navbar-nav ms-auto">
                <?php if (!isset($_SESSION['user'])): ?>
                    
                    <a class="nav-link" href="login.php">Đăng nhập</a>
                <?php else: ?>
                    <span class="nav-link">Xin chào, <?php echo htmlspecialchars($_SESSION['user']['name']); ?></span>
                    <a class="nav-link" href="logout.php">Đăng xuất</a>
                <?php endif; ?>
            </div>
        </div>
    </header>
   

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Đăng nhập</h3>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">Tên đăng nhập</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Mật khẩu</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Đăng nhập</button>
                            </div>
                        </form>
                        <div class="text-center mt-4">
                            <p class="text-muted">Hoặc đăng nhập bằng</p>
                            <div class="d-flex justify-content-center gap-3">
                                <!-- Nút đăng nhập Google -->
                                <div id="g_id_onload"
                                 data-client_id="949168644612-84ql45lv2dtljsq64u7ll4uh9mgq5p8i.apps.googleusercontent.com"
                                 data-callback="handleCredentialResponse">
                                </div>
                                <div class="g_id_signin"
                                     data-type="standard"
                                     data-size="large"
                                     data-theme="outline"
                                     data-text="sign_in_with"
                                     data-shape="rectangular"
                                     data-logo_alignment="left">
                                </div>
                                
                                <!-- Nút đăng nhập Facebook -->
                                <div class="fb-login-button" 
                                     data-width=""
                                     data-size="large"
                                     data-button-type="continue_with"
                                     data-layout="rounded"
                                     data-auto-logout-link="false"
                                     data-use-continue-as="false"
                                     data-onlogin="checkLoginState();">
                                </div>
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <p>Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Thêm JavaScript cho xử lý đăng nhập -->
    <script src="js/login.js"></script>
    <script>
        // Khởi tạo Google Sign-In khi trang đã load
        window.onload = function() {
            initGoogleSignIn();
        };
    </script>
</body>
</html> 