<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user']['id'];

// Kiểm tra giỏ hàng có trống không
if (!isset($_SESSION['carts'][$user_id]) || empty($_SESSION['carts'][$user_id])) {
    header('Location: cart.php');
    exit();
}

// Xử lý đặt hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conn->beginTransaction();
        
        // Tính tổng tiền
        $total = 0;
        foreach ($_SESSION['carts'][$user_id] as $item) {
            $total += $item['giaxe'] * $item['soluong'];
        }

        // Tạo phiếu đặt hàng với trạng thái chờ xác nhận (0)
        $stmt = $conn->prepare("INSERT INTO phieudathang (idkh, ngaylaphoadon, tongtien, trangthai) VALUES (?, CURDATE(), ?, 0)");
        $stmt->execute([$user_id, $total]);
        $order_id = $conn->lastInsertId();

        // Thêm chi tiết phiếu đặt hàng
        $stmt = $conn->prepare("INSERT INTO chitietphieudathang (idpdh, idoto, soluong, gia, thanhtien) VALUES (?, ?, ?, ?, ?)");
        foreach ($_SESSION['carts'][$user_id] as $item) {
            $subtotal = $item['giaxe'] * $item['soluong'];
            $stmt->execute([
                $order_id,
                $item['idoto'],
                $item['soluong'],
                $item['giaxe'],
                $subtotal
            ]);
        }

        // Xóa giỏ hàng sau khi đặt hàng thành công
        $stmt = $conn->prepare("DELETE FROM giohang WHERE idkh = ?");
        $stmt->execute([$user_id]);
        unset($_SESSION['carts'][$user_id]);

        $conn->commit();
        
        header('Location: thank_you.php?order=' . $order_id);
        exit();
        
    } catch (Exception $e) {
        $conn->rollBack();
        $error = "Có lỗi xảy ra khi đặt hàng. Vui lòng thử lại!";
    }
}

require_once '../layout/header.php';
?>

<div class="container mt-5">
    <h2>Thanh toán</h2>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h4>Thông tin đơn hàng</h4>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Số lượng</th>
                                <th>Đơn giá</th>
                                <th>Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $total = 0;
                            foreach ($_SESSION['carts'][$user_id] as $item): 
                                $subtotal = $item['giaxe'] * $item['soluong'];
                                $total += $subtotal;
                            ?>
                                <tr>
                                    <td>
                                        <img src="../image/<?php echo $item['img']; ?>" alt="<?php echo $item['tenxe']; ?>" style="max-width: 50px;">
                                        <?php echo $item['tenxe']; ?>
                                    </td>
                                    <td><?php echo $item['soluong']; ?></td>
                                    <td><?php echo number_format($item['giaxe'], 0, ',', '.'); ?> VNĐ</td>
                                    <td><?php echo number_format($subtotal, 0, ',', '.'); ?> VNĐ</td>
                                </tr>
                            <?php endforeach; ?>
                            <tr>
                                <td colspan="3" class="text-end"><strong>Tổng cộng:</strong></td>
                                <td><strong><?php echo number_format($total, 0, ',', '.'); ?> VNĐ</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4>Thông tin khách hàng</h4>
                </div>
                <div class="card-body">
                    <?php
                    // Lấy thông tin khách hàng
                    $stmt = $conn->prepare("SELECT * FROM khachhang WHERE idkh = ?");
                    $stmt->execute([$user_id]);
                    $customer = $stmt->fetch(PDO::FETCH_ASSOC);
                    ?>
                    <p><strong>Họ tên:</strong> <?php echo $customer['hovaten']; ?></p>
                    <p><strong>Địa chỉ:</strong> <?php echo $customer['diachi']; ?></p>

                    <form method="POST" class="mt-4">
                        <div class="mb-3">
                            <label class="form-label">Ghi chú đơn hàng (không bắt buộc)</label>
                            <textarea class="form-control" name="note" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Đặt hàng</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../layout/footer.php'; ?> 