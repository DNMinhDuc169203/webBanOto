<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user']['id'];

try {
    // Lấy danh sách đơn hàng của khách hàng
    $stmt = $conn->prepare("
        SELECT pdh.*, 
            CASE 
                WHEN pdh.trangthai = 0 THEN 'Chờ xác nhận'
                WHEN pdh.trangthai = 1 THEN 'Đã xác nhận'
                WHEN pdh.trangthai = 2 THEN 'Đã giao'
                WHEN pdh.trangthai = 3 THEN 'Đã hủy'
            END as trangthai_text
        FROM phieudathang pdh
        WHERE pdh.idkh = ?
        ORDER BY pdh.ngaylaphoadon DESC
    ");
    $stmt->execute([$user_id]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "Lỗi: " . $e->getMessage();
}

require_once 'layout/header.php';
?>

<div class="container mt-5">
    <h2>Đơn hàng của tôi</h2>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if (empty($orders)): ?>
        <div class="alert alert-info">Bạn chưa có đơn hàng nào.</div>
        <a href="index.php" class="btn btn-primary">Tiếp tục mua sắm</a>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Ngày đặt</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Chi tiết</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>#<?php echo $order['idpdh']; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($order['ngaylaphoadon'])); ?></td>
                            <td><?php echo number_format($order['tongtien'], 0, ',', '.'); ?> VNĐ</td>
                            <td>
                                <?php
                                $status_class = '';
                                switch($order['trangthai']) {
                                    case 0: $status_class = 'warning'; break;
                                    case 1: $status_class = 'info'; break;
                                    case 2: $status_class = 'success'; break;
                                    case 3: $status_class = 'danger'; break;
                                }
                                ?>
                                <span class="badge bg-<?php echo $status_class; ?>">
                                    <?php echo $order['trangthai_text']; ?>
                                </span>
                            </td>
                            <td>
                                <button type="button" 
                                        class="btn btn-sm btn-primary view-order" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#orderModal"
                                        data-id="<?php echo $order['idpdh']; ?>">
                                    Xem chi tiết
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Modal chi tiết đơn hàng -->
        <div class="modal fade" id="orderModal" tabindex="-1">
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
    <?php endif; ?>
</div>

<script>
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

<?php require_once 'layout/footer.php'; ?> 