<?php
session_start();
require_once '../config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Get the requested page/action
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-2 d-none d-md-block bg-dark sidebar min-vh-100">
            <div class="position-sticky">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="index.php">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="?page=oto">
                            <i class="fas fa-car"></i> Quản lý Ô tô
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="?page=thuonghieu">
                            <i class="fas fa-trademark"></i> Thương hiệu
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="?page=nhasanxuat">
                            <i class="fas fa-industry"></i> Nhà sản xuất
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="?page=khachhang">
                            <i class="fas fa-users"></i> Khách hàng
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="?page=donhang">
                            <i class="fas fa-shopping-cart"></i> Đơn hàng
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="?page=taikhoan">
                            <i class="fas fa-users-cog"></i> Quản lý Tài khoản
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="logout.php">
                            <i class="fas fa-sign-out-alt"></i> Đăng xuất
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main content -->
        <main class="col-md-10 ms-sm-auto px-md-4">
            <?php
            // Trang có thể truy cập
            $valid_pages = [
                'dashboard', 
                'oto', 
                'oto_edit',
                'thuonghieu', 
                'nhasanxuat', 
                'khachhang', 
                'donhang',
                'taikhoan',     // Thêm trang taikhoan
                'taikhoan_edit' // Thêm trang sửa tài khoản
            ];
            
            if (in_array($page, $valid_pages)) {
                include "pages/{$page}.php";
            } else {
                include "pages/404.php";
            }
            ?>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
