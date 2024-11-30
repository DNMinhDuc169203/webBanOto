<?php
if (!isset($_GET['id'])) {
    header('Location: ?page=taikhoan');
    exit();
}

$idtk = $_GET['id'];

// Lấy thông tin tài khoản
$stmt = $conn->prepare("
    SELECT tk.*, kh.hovaten, kh.diachi, kh.ngaysinh
    FROM taikhoan tk
    JOIN khachhang kh ON tk.idkh = kh.idkh
    WHERE tk.idtk = ?
");
$stmt->execute([$idtk]);
$account = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$account) {
    echo "<script>alert('Tài khoản không tồn tại!'); window.location.href='?page=taikhoan';</script>";
    exit();
}

// Xử lý cập nhật mật khẩu
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        // Kiểm tra mật khẩu mới và xác nhận mật khẩu
        if ($new_password !== $confirm_password) {
            throw new Exception("Mật khẩu mới và xác nhận mật khẩu không khớp!");
        }
        
        // Cập nhật mật khẩu mới
        $stmt = $conn->prepare("UPDATE taikhoan SET matkhau = ? WHERE idtk = ? AND oauth_provider = 'local'");
        $stmt->execute([$new_password, $idtk]);
        
        echo "<script>alert('Cập nhật mật khẩu thành công!'); window.location.href='?page=taikhoan';</script>";
    } catch(Exception $e) {
        echo "<script>alert('Lỗi: " . $e->getMessage() . "');</script>";
    }
}
?>

<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Đổi mật khẩu tài khoản</h5>
                </div>
                <div class="card-body">
                    <!-- Hiển thị thông tin tài khoản -->
                    <div class="mb-4">
                        <p><strong>Tên đăng nhập:</strong> <?php echo $account['tendangnhap']; ?></p>
                        <p><strong>Họ tên:</strong> <?php echo $account['hovaten']; ?></p>
                        <p><strong>Mật khẩu hiện tại:</strong> <?php echo $account['matkhau']; ?></p>
                    </div>

                    <?php if($account['oauth_provider'] == 'local'): ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label for="new_password" class="form-label">Mật khẩu mới</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="new_password" 
                                       name="new_password" 
                                       required>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Xác nhận mật khẩu mới</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="confirm_password" 
                                       name="confirm_password" 
                                       required>
                            </div>
                            <div class="d-flex justify-content-between">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Lưu thay đổi
                                </button>
                                <a href="?page=taikhoan" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Quay lại
                                </a>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            Không thể đổi mật khẩu cho tài khoản đăng nhập bằng <?php echo $account['oauth_provider']; ?>
                        </div>
                        <a href="?page=taikhoan" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>