<?php
// Lấy danh sách khách hàng kèm thông tin tài khoản
$stmt = $conn->prepare("SELECT kh.*, tk.tendangnhap, tk.email 
    FROM khachhang kh 
    LEFT JOIN taikhoan tk ON kh.idkh = tk.idkh 
    ORDER BY kh.idkh DESC");
$stmt->execute();
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Xử lý thêm khách hàng mới
if(isset($_POST['add_customer'])) {
    try {
        $conn->beginTransaction();
        
        $hovaten = $_POST['hovaten'];
        $ngaysinh = $_POST['ngaysinh'];
        $diachi = $_POST['diachi'];
        
        // Thêm thông tin khách hàng
        $stmt = $conn->prepare("INSERT INTO khachhang (hovaten, ngaysinh, diachi) VALUES (?, ?, ?)");
        $stmt->execute([$hovaten, $ngaysinh, $diachi]);
        $idkh = $conn->lastInsertId();
        
        // Nếu có thông tin tài khoản
        if(!empty($_POST['tendangnhap']) && !empty($_POST['matkhau'])) {
            $tendangnhap = $_POST['tendangnhap'];
            $matkhau = $_POST['matkhau'];
            $email = $_POST['email'] ?? null;
            
            // Kiểm tra tên đăng nhập đã tồn tại chưa
            $check = $conn->prepare("SELECT COUNT(*) FROM taikhoan WHERE tendangnhap = ?");
            $check->execute([$tendangnhap]);
            if($check->fetchColumn() > 0) {
                throw new Exception("Tên đăng nhập đã tồn tại!");
            }
            
            $stmt = $conn->prepare("INSERT INTO taikhoan (tendangnhap, matkhau, email, idkh) VALUES (?, ?, ?, ?)");
            $stmt->execute([$tendangnhap, $matkhau, $email, $idkh]);
        }
        
        $conn->commit();
        echo "<script>alert('Thêm khách hàng thành công!'); window.location.href='?page=khachhang';</script>";
    } catch(Exception $e) {
        $conn->rollBack();
        echo "<script>alert('Lỗi: " . $e->getMessage() . "'); window.location.href='?page=khachhang';</script>";
    }
}

// Xử lý sửa khách hàng
if(isset($_POST['edit_customer'])) {
    try {
        $conn->beginTransaction();
        
        $id = $_POST['edit_id'];
        $hovaten = $_POST['edit_hovaten'];
        $ngaysinh = $_POST['edit_ngaysinh'];
        $diachi = $_POST['edit_diachi'];
        
        // Cập nhật thông tin khách hàng
        $stmt = $conn->prepare("UPDATE khachhang SET hovaten = ?, ngaysinh = ?, diachi = ? WHERE idkh = ?");
        $stmt->execute([$hovaten, $ngaysinh, $diachi, $id]);
        
        $conn->commit();
        echo "<script>alert('Cập nhật thông tin khách hàng thành công!'); window.location.href='?page=khachhang';</script>";
    } catch(Exception $e) {
        $conn->rollBack();
        echo "<script>alert('Lỗi: " . $e->getMessage() . "'); window.location.href='?page=khachhang';</script>";
    }
}

// Xử lý xóa khách hàng
if(isset($_GET['delete'])) {
    try {
        $conn->beginTransaction();
        
        $idkh = $_GET['delete'];
        
        // Kiểm tra xem khách hàng có đơn hàng không
        $check = $conn->prepare("SELECT COUNT(*) FROM phieudathang WHERE idkh = ?");
        $check->execute([$idkh]);
        if($check->fetchColumn() > 0) {
            throw new Exception("Không thể xóa! Khách hàng này đã có đơn hàng trong hệ thống.");
        }
        
        // Xóa tài khoản trước (nếu có)
        $stmt = $conn->prepare("DELETE FROM taikhoan WHERE idkh = ?");
        $stmt->execute([$idkh]);
        
        // Xóa khách hàng
        $stmt = $conn->prepare("DELETE FROM khachhang WHERE idkh = ?");
        $stmt->execute([$idkh]);
        
        $conn->commit();
        echo "<script>alert('Xóa khách hàng thành công!'); window.location.href='?page=khachhang';</script>";
    } catch(Exception $e) {
        $conn->rollBack();
        echo "<script>alert('Lỗi: " . $e->getMessage() . "'); window.location.href='?page=khachhang';</script>";
    }
}
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Quản lý Khách hàng</h2>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCustomerModal">
            <i class="fas fa-plus"></i> Thêm khách hàng
        </button>
    </div>

    <!-- Bảng danh sách khách hàng -->
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Họ và tên</th>
                <th>Ngày sinh</th>
                <th>Địa chỉ</th>
                <th>Tài khoản</th>
                <th>Email</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($customers as $customer): ?>
            <tr>
                <td><?php echo $customer['idkh']; ?></td>
                <td><?php echo $customer['hovaten']; ?></td>
                <td><?php echo date('d/m/Y', strtotime($customer['ngaysinh'])); ?></td>
                <td><?php echo $customer['diachi']; ?></td>
                <td><?php echo $customer['tendangnhap'] ?? 'Chưa có tài khoản'; ?></td>
                <td><?php echo $customer['email'] ?? ''; ?></td>
                <td>
                    <button class="btn btn-sm btn-warning edit-customer" 
                            data-id="<?php echo $customer['idkh']; ?>"
                            data-name="<?php echo $customer['hovaten']; ?>"
                            data-birth="<?php echo $customer['ngaysinh']; ?>"
                            data-address="<?php echo $customer['diachi']; ?>"
                            data-bs-toggle="modal" 
                            data-bs-target="#editCustomerModal">
                        <i class="fas fa-edit"></i>
                    </button>
                    <a href="?page=khachhang&delete=<?php echo $customer['idkh']; ?>" 
                       class="btn btn-sm btn-danger"
                       onclick="return confirm('Bạn có chắc muốn xóa khách hàng này?')">
                        <i class="fas fa-trash"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Modal thêm khách hàng -->
    <div class="modal fade" id="addCustomerModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm khách hàng mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Họ và tên</label>
                            <input type="text" class="form-control" name="hovaten" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ngày sinh</label>
                            <input type="date" class="form-control" name="ngaysinh" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Địa chỉ</label>
                            <input type="text" class="form-control" name="diachi" required>
                        </div>
                        <hr>
                        <h6>Thông tin tài khoản (không bắt buộc)</h6>
                        <div class="mb-3">
                            <label class="form-label">Tên đăng nhập</label>
                            <input type="text" class="form-control" name="tendangnhap">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mật khẩu</label>
                            <input type="password" class="form-control" name="matkhau">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" name="add_customer" class="btn btn-primary">Thêm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal sửa khách hàng -->
    <div class="modal fade" id="editCustomerModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sửa thông tin khách hàng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="edit_id" id="edit_id">
                        <div class="mb-3">
                            <label class="form-label">Họ và tên</label>
                            <input type="text" class="form-control" name="edit_hovaten" id="edit_hovaten" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ngày sinh</label>
                            <input type="date" class="form-control" name="edit_ngaysinh" id="edit_ngaysinh" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Địa chỉ</label>
                            <input type="text" class="form-control" name="edit_diachi" id="edit_diachi" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" name="edit_customer" class="btn btn-primary">Lưu thay đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Xử lý sự kiện khi nhấn nút sửa
document.querySelectorAll('.edit-customer').forEach(button => {
    button.addEventListener('click', function() {
        const id = this.dataset.id;
        const name = this.dataset.name;
        const birth = this.dataset.birth;
        const address = this.dataset.address;
        
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_hovaten').value = name;
        document.getElementById('edit_ngaysinh').value = birth;
        document.getElementById('edit_diachi').value = address;
    });
});
</script> 