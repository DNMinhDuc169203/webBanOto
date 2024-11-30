<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $hovaten = $_POST['hovaten'];
    $ngaysinh = $_POST['ngaysinh'];
    $diachi = $_POST['diachi'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    try {
        // Kiểm tra username đã tồn tại chưa
        $stmt = $conn->prepare("SELECT * FROM taikhoan WHERE tendangnhap = ?");
        $stmt->execute([$username]);
        if ($stmt->rowCount() > 0) {
            $error = "Tên đăng nhập đã tồn tại";
        } else {
            // Thêm thông tin khách hàng
            $conn->beginTransaction();
            
            $stmt = $conn->prepare("INSERT INTO khachhang (hovaten, ngaysinh, diachi) VALUES (?, ?, ?)");
            $stmt->execute([$hovaten, $ngaysinh, $diachi]);
            $idkh = $conn->lastInsertId();
            
            // Thêm tài khoản
            $stmt = $conn->prepare("INSERT INTO taikhoan (tendangnhap, matkhau, idkh) VALUES (?, ?, ?)");
            $stmt->execute([$username, $password, $idkh]);
            
            $conn->commit();
            
            $_SESSION['success'] = "Đăng ký thành công! Vui lòng đăng nhập.";
            header('Location: login.php');
            exit();
        }
    } catch(PDOException $e) {
        $conn->rollBack();
        $error = "Lỗi: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Đăng ký tài khoản</h3>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label for="hovaten" class="form-label">Họ và tên</label>
                                <input type="text" class="form-control" id="hovaten" name="hovaten" required>
                            </div>
                            <div class="mb-3">
                                <label for="ngaysinh" class="form-label">Ngày sinh</label>
                                <input type="date" class="form-control" id="ngaysinh" name="ngaysinh" required>
                            </div>
                            <div class="mb-3">
                                <label for="diachi" class="form-label">Địa chỉ</label>
                                <input type="text" class="form-control" id="diachi" name="diachi" required>
                            </div>
                            <div class="mb-3">
                                <label for="username" class="form-label">Tên đăng nhập</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Mật khẩu</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Đăng ký</button>
                            </div>
                        </form>
                        <div class="text-center mt-3">
                            <p>Đã có tài khoản? <a href="login.php">Đăng nhập</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 