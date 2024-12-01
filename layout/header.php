<!DOCTYPE html>
<html lang="vi"
<head>
    <meta charset="UTF-8">
    <title>Trang web của bạn</title>
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }
        body {
            padding-top: 70px; /* Thêm padding-top để nội dung không bị che khuất bởi navbar */
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="../index.php">
            <img src="../image/logo.png" alt="Logo" height="40">
        </a>
        <!-- Hamburger button for mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <!-- Menu items -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="../index.php">Sản phẩm</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../view/introduce.php">Giới thiệu</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../view/contact.php">Liên hệ</a>
                </li>
                
            </ul>
        
            <div class="navbar-nav ms-auto">
                <?php if (!isset($_SESSION['user'])): ?>
                    <a class="nav-link" href="../view/cart.php">
                        <i class="fas fa-shopping-cart"></i>
                    </a>
                    <a class="nav-link" href="../log/login.php">Đăng nhập</a>
                    <a class="nav-link" href="../log/register.php">Đăng ký</a>
                    
                <?php else: ?>
                    <a class="nav-link" href="../view/cart.php">
                        <i class="fas fa-shopping-cart"></i>
                    </a>
                    <a class="nav-link" href="../view/orders.php">
                        <i class="fas fa-file-invoice"></i> Đơn hàng
                    </a>
                    <span class="nav-link">Xin chào, <?php echo htmlspecialchars($_SESSION['user']['name']); ?></span>
                   
                    <a class="nav-link" href="../log/logout.php">Đăng xuất</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>