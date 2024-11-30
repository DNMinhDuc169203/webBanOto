<?php
// Lấy danh sách đơn hàng kèm thông tin khách hàng
$stmt = $conn->prepare("
    SELECT pdh.*, kh.hovaten, hd.trangthai as thanhtoan
    FROM phieudathang pdh
    JOIN khachhang kh ON pdh.idkh = kh.idkh
    LEFT JOIN hoadon hd ON pdh.idpdh = hd.idpdh
    ORDER BY pdh.idpdh DESC
");
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Xử lý cập nhật trạng thái đơn hàng
if(isset($_POST['update_status'])) {
    try {
        $conn->beginTransaction();
        
        $idpdh = $_POST['idpdh'];
        $trangthai = $_POST['trangthai'];
        
        // Cập nhật trạng thái đơn hàng
        $stmt = $conn->prepare("UPDATE phieudathang SET trangthai = ? WHERE idpdh = ?");
        $stmt->execute([$trangthai, $idpdh]);

        // Nếu trạng thái là "Đã xác nhận" (1) hoặc "Đã hủy" (3)
        if($trangthai == 1 || $trangthai == 3) {
            // Xóa chi tiết phiếu đặt hàng
            $stmt = $conn->prepare("DELETE FROM chitietphieudathang WHERE idpdh = ?");
            $stmt->execute([$idpdh]);
        }
        
        $conn->commit();
        echo "<script>alert('Cập nhật trạng thái thành công!'); window.location.href='?page=donhang';</script>";
    } catch(Exception $e) {
        $conn->rollBack();
        echo "<script>alert('Lỗi: " . $e->getMessage() . "'); window.location.href='?page=donhang';</script>";
    }
}

// Xử lý xác nhận thanh toán
if(isset($_POST['confirm_payment'])) {
    try {
        $conn->beginTransaction();
        
        $idpdh = $_POST['idpdh'];
        
        // Kiểm tra xem đã có hóa đơn chưa
        $check = $conn->prepare("SELECT COUNT(*) FROM hoadon WHERE idpdh = ?");
        $check->execute([$idpdh]);
        if($check->fetchColumn() > 0) {
            throw new Exception("Đơn hàng này đã có hóa đơn!");
        }
        
        // Lấy tổng tiền từ phiếu đặt hàng
        $stmt = $conn->prepare("SELECT tongtien FROM phieudathang WHERE idpdh = ?");
        $stmt->execute([$idpdh]);
        $tongtien = $stmt->fetchColumn();
        
        // Tạo hóa đơn mới
        $stmt = $conn->prepare("INSERT INTO hoadon (idpdh, ngayxuathoadon, tongtien, trangthai) VALUES (?, CURDATE(), ?, 1)");
        $stmt->execute([$idpdh, $tongtien]);
        
        $conn->commit();
        echo "<script>alert('Xác nhận thanh toán thành công!'); window.location.href='?page=donhang';</script>";
    } catch(Exception $e) {
        $conn->rollBack();
        echo "<script>alert('Lỗi: " . $e->getMessage() . "'); window.location.href='?page=donhang';</script>";
    }
}
?>

<div class="container mt-4">
    <h2>Quản lý Đơn hàng</h2>

    <!-- Bảng danh sách đơn hàng -->
    <table class="table table-striped mt-4">
        <thead>
            <tr>
                <th>Mã đơn</th>
                <th>Khách hàng</th>
                <th>Ngày đặt</th>
                <th>Tổng tiền</th>
                <th>Trạng thái</th>
                <th>Thanh toán</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($orders as $order): ?>
            <tr>
                <td><?php echo $order['idpdh']; ?></td>
                <td><?php echo $order['hovaten']; ?></td>
                <td><?php echo date('d/m/Y', strtotime($order['ngaylaphoadon'])); ?></td>
                <td><?php echo number_format($order['tongtien']); ?> VNĐ</td>
                <td>
                    <?php
                    $status_class = '';
                    $status_text = '';
                    switch($order['trangthai']) {
                        case 0:
                            $status_class = 'badge bg-warning';
                            $status_text = 'Chờ xử lý';
                            break;
                        case 1:
                            $status_class = 'badge bg-info';
                            $status_text = 'Đã xác nhận';
                            break;
                        case 2:
                            $status_class = 'badge bg-success';
                            $status_text = 'Đã giao';
                            break;
                        case 3:
                            $status_class = 'badge bg-danger';
                            $status_text = 'Đã hủy';
                            break;
                    }
                    ?>
                    <span class="<?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                </td>
                <td>
                    <?php if($order['thanhtoan']): ?>
                        <span class="badge bg-success">Đã thanh toán</span>
                    <?php else: ?>
                        <span class="badge bg-danger">Chưa thanh toán</span>
                    <?php endif; ?>
                </td>
                <td>
                    <button class="btn btn-sm btn-info view-order" 
                            data-id="<?php echo $order['idpdh']; ?>"
                            data-bs-toggle="modal" 
                            data-bs-target="#viewOrderModal">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-warning update-status" 
                            data-id="<?php echo $order['idpdh']; ?>"
                            data-status="<?php echo $order['trangthai']; ?>"
                            data-bs-toggle="modal" 
                            data-bs-target="#updateStatusModal">
                        <i class="fas fa-edit"></i>
                    </button>
                    <?php if(!$order['thanhtoan']): ?>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="idpdh" value="<?php echo $order['idpdh']; ?>">
                        <button type="submit" name="confirm_payment" 
                                class="btn btn-sm btn-success"
                                onclick="return confirm('Xác nhận thanh toán đơn hàng này?')">
                            <i class="fas fa-check"></i>
                        </button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Modal xem chi tiết đơn hàng -->
    <div class="modal fade" id="viewOrderModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chi tiết đơn hàng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="orderDetails">
                    <!-- Nội dung chi tiết đơn hàng sẽ được load bằng AJAX -->
                </div>
            </div>
        </div>
    </div>

    <!-- Modal cập nhật trạng thái -->
    <div class="modal fade" id="updateStatusModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cập nhật trạng thái đơn hàng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="idpdh" id="update_idpdh">
                        <div class="mb-3">
                            <label class="form-label">Trạng thái</label>
                            <select class="form-select" name="trangthai" id="update_trangthai">
                                <option value="0">Chờ xử lý</option>
                                <option value="1">Đã xác nhận</option>
                                <option value="2">Đã giao</option>
                                <option value="3">Đã hủy</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" name="update_status" class="btn btn-primary">Cập nhật</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Xử lý sự kiện khi nhấn nút cập nhật trạng thái
document.querySelectorAll('.update-status').forEach(button => {
    button.addEventListener('click', function() {
        const id = this.dataset.id;
        const status = this.dataset.status;
        
        document.getElementById('update_idpdh').value = id;
        document.getElementById('update_trangthai').value = status;
    });
});

// Xử lý sự kiện khi nhấn nút xem chi tiết
document.querySelectorAll('.view-order').forEach(button => {
    button.addEventListener('click', async function() {
        const id = this.dataset.id;
        try {
            const response = await fetch(`ajax/get_order_details.php?id=${id}`);
            const html = await response.text();
            document.getElementById('orderDetails').innerHTML = html;
        } catch(error) {
            console.error('Error:', error);
        }
    });
});
</script> 