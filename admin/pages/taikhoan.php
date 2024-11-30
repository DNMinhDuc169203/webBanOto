<?php
// Lấy danh sách tài khoản kèm thông tin khách hàng
$stmt = $conn->prepare("
    SELECT tk.*, kh.hovaten, kh.diachi, kh.ngaysinh, 
           CASE 
               WHEN tk.oauth_provider != 'local' THEN CONCAT(tk.oauth_provider, ' (', tk.email, ')')
               ELSE tk.tendangnhap
           END as login_info
    FROM taikhoan tk
    JOIN khachhang kh ON tk.idkh = kh.idkh
    ORDER BY tk.idtk DESC
");
$stmt->execute();
$accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Xử lý reset mật khẩu
if(isset($_POST['reset_password'])) {
    try {
        $idtk = $_POST['idtk'];
        $new_password = "123123"; // Mật khẩu mặc định khi reset
        
        $stmt = $conn->prepare("UPDATE taikhoan SET matkhau = ? WHERE idtk = ? AND oauth_provider = 'local'");
        $stmt->execute([$new_password, $idtk]);
        
        echo "<script>alert('Reset mật khẩu thành công! Mật khẩu mới: 123123'); window.location.href='?page=taikhoan';</script>";
    } catch(Exception $e) {
        echo "<script>alert('Lỗi: " . $e->getMessage() . "');</script>";
    }
}

// Xử lý x\
if(isset($_POST['delete_account'])) {
    try {
        $idtk = $_POST['idtk'];
        $idkh = $_POST['idkh'];
        
        $conn->beginTransaction();
        
        // Xóa tài khoản
        $stmt = $conn->prepare("DELETE FROM taikhoan WHERE idtk = ?");
        $stmt->execute([$idtk]);
        
        // Xóa khách hàng
        $stmt = $conn->prepare("DELETE FROM khachhang WHERE idkh = ?");
        $stmt->execute([$idkh]);
        
        $conn->commit();
        
        echo "<script>alert('Xóa tài khoản thành công!'); window.location.href='?page=taikhoan';</script>";
    } catch(Exception $e) {
        $conn->rollBack();
        echo "<script>alert('Lỗi: " . $e->getMessage() . "');</script>";
    }
}
?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Quản lý Tài khoản</h2>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Thông tin đăng nhập</th>
                            <th>Mật khẩu</th>
                            <th>Họ tên</th>
                            <th>Ngày sinh</th>
                            <th>Địa chỉ</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($accounts as $account): ?>
                            <tr>
                                <td><?php echo $account['idtk']; ?></td>
                                <td><?php echo $account['login_info']; ?></td>
                                <td><?php echo $account['matkhau']; ?></td>
                                <td><?php echo $account['hovaten']; ?></td>
                                <td><?php echo date('d/m/Y', strtotime($account['ngaysinh'])); ?></td>
                                <td><?php echo $account['diachi']; ?></td>
                                <td>
                                    <?php if($account['oauth_provider'] == 'local'): ?>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="idtk" value="<?php echo $account['idtk']; ?>">
                                            <button type="submit" 
                                                    name="reset_password" 
                                                    class="btn btn-sm btn-info"
                                                    onclick="return confirm('Bạn có chắc muốn reset mật khẩu tài khoản này?')">
                                                Reset MK
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <button type="button" 
                                            class="btn btn-sm btn-primary view-orders"
                                            data-bs-toggle="modal"
                                            data-bs-target="#ordersModal"
                                            data-idkh="<?php echo $account['idkh']; ?>">
                                        Xem đơn hàng
                                    </button>

                                    <a href="?page=taikhoan_edit&id=<?php echo $account['idtk']; ?>" 
                                       class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> Sửa
                                    </a>

                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="idtk" value="<?php echo $account['idtk']; ?>">
                                        <input type="hidden" name="idkh" value="<?php echo $account['idkh']; ?>">
                                        <button type="submit" 
                                                name="delete_account" 
                                                class="btn btn-sm btn-danger"
                                                onclick="return confirm('Bạn có chắc muốn xóa tài khoản này? Hành động này không thể hoàn tác!')">
                                            <i class="fas fa-trash"></i> Xóa
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal xem đơn hàng -->
<div class="modal fade" id="ordersModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Lịch sử đơn hàng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="orderHistory">
                <!-- Nội dung sẽ được load bằng AJAX -->
            </div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.view-orders').forEach(button => {
    button.addEventListener('click', async function() {
        const idkh = this.dataset.idkh;
        try {
            const response = await fetch(`ajax/get_customer_orders.php?idkh=${idkh}`);
            const html = await response.text();
            document.getElementById('orderHistory').innerHTML = html;
        } catch(error) {
            console.error('Error:', error);
        }
    });
});
</script>