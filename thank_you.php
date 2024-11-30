<?php
session_start();
require_once 'config.php';

if (!isset($_GET['order'])) {
    header('Location: index.php');
    exit();
}

$order_id = $_GET['order'];

// Lấy thông tin đơn hàng
$stmt = $conn->prepare("
    SELECT pdh.*, kh.hovaten, kh.diachi 
    FROM phieudathang pdh
    JOIN khachhang kh ON pdh.idkh = kh.idkh
    WHERE pdh.idpdh = ?
");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header('Location: index.php');
    exit();
}

require_once 'layout/header.php';
?>

<div class="container mt-5">
    <div class="text-center">
        <i class="fas fa-check-circle text-success" style="font-size: 64px;"></i>
        <h2 class="mt-3">Cảm ơn bạn đã đặt hàng!</h2>
        <p class="lead">Đơn hàng của bạn đã được tiếp nhận và đang được xử lý.</p>
    </div>

    <div class="row justify-content-center mt-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Thông tin đơn hàng #<?php echo $order_id; ?></h4>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>Khách hàng:</strong> <?php echo $order['hovaten']; ?></p>
                            <p><strong>Địa chỉ:</strong> <?php echo $order['diachi']; ?></p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <p><strong>Ngày đặt:</strong> <?php echo date('d/m/Y', strtotime($order['ngaylaphoadon'])); ?></p>
                            <p><strong>Tổng tiền:</strong> <?php echo number_format($order['tongtien'], 0, ',', '.'); ?> VNĐ</p>
                        </div>
                    </div>

                    <hr>

                    <div class="text-center mt-4">
                        <a href="index.php" class="btn btn-primary">Tiếp tục mua sắm</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'layout/footer.php'; ?> 