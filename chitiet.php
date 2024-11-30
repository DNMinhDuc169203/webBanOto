<?php
session_start();
require_once 'config.php';
require_once 'layout/header.php';

$id = isset($_GET['id']) ? $_GET['id'] : 0;

try {
    // Lấy thông tin xe
    $stmt = $conn->prepare("
        SELECT o.*, th.tenthuonghieu, nsx.tennsx, nsx.quocgia, ct.*
        FROM oto o
        LEFT JOIN thuonghieu th ON o.idth = th.idth
        LEFT JOIN nhasanxuat nsx ON th.idnsx = nsx.idnsx
        LEFT JOIN chitietxeoto ct ON o.idoto = ct.idoto
        WHERE o.idoto = ?
    ");
    $stmt->execute([$id]);
    $car = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$car) {
        throw new Exception("Không tìm thấy xe");
    }
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-6">
            <img src="image/<?php echo $car['img']; ?>" class="img-fluid" alt="<?php echo $car['tenxe']; ?>">
        </div>
        <div class="col-md-6">
            <h1><?php echo $car['tenxe']; ?></h1>
            <h3 class="text-danger"><?php echo number_format($car['giaxe'], 0, ',', '.'); ?> VNĐ</h3>
            
            <div class="specs mt-4">
                <h4>Thông số cơ bản</h4>
                <table class="table">
                    <tr>
                        <td>Thương hiệu:</td>
                        <td><?php echo $car['tenthuonghieu']; ?></td>
                    </tr>
                    <tr>
                        <td>Nhà sản xuất:</td>
                        <td><?php echo $car['tennsx']; ?> (<?php echo $car['quocgia']; ?>)</td>
                    </tr>
                    <tr>
                        <td>Màu sắc:</td>
                        <td><?php echo $car['mausac']; ?></td>
                    </tr>
                    <tr>
                        <td>Dòng xe:</td>
                        <td><?php echo $car['dongxe']; ?></td>
                    </tr>
                    <tr>
                        <td>Năm sản xuất:</td>
                        <td><?php echo date('Y', strtotime($car['ngaysanxuat'])); ?></td>
                    </tr>
                </table>

                <?php if ($car['idctxeoto']): ?>
                <h4 class="mt-4">Thông số kỹ thuật</h4>
                <table class="table">
                    <tr>
                        <td>Động cơ:</td>
                        <td><?php echo $car['dongco']; ?></td>
                    </tr>
                    <tr>
                        <td>Hộp số:</td>
                        <td><?php echo $car['hopso']; ?></td>
                    </tr>
                    <tr>
                        <td>Nhiên liệu:</td>
                        <td><?php echo $car['nhienlieu']; ?></td>
                    </tr>
                    <tr>
                        <td>Số chỗ ngồi:</td>
                        <td><?php echo $car['chongoi']; ?></td>
                    </tr>
                    <tr>
                        <td>Xuất xứ:</td>
                        <td><?php echo $car['xuatxu']; ?></td>
                    </tr>
                </table>

                <?php if ($car['mota']): ?>
                <h4 class="mt-4">Mô tả</h4>
                <p><?php echo nl2br($car['mota']); ?></p>
                <?php endif; ?>
                <?php endif; ?>

                <div class="mt-4">
                    <?php if (isset($_SESSION['user'])): ?>
                        <form action="cart_process.php" method="POST">
                            <input type="hidden" name="idoto" value="<?php echo $car['idoto']; ?>">
                            <input type="hidden" name="tenxe" value="<?php echo $car['tenxe']; ?>">
                            <input type="hidden" name="giaxe" value="<?php echo $car['giaxe']; ?>">
                            <input type="hidden" name="img" value="<?php echo $car['img']; ?>">
                            <button type="submit" name="add_to_cart" class="btn btn-primary btn-lg">Thêm vào giỏ hàng</button>
                            <a href="index.php" class="btn btn-secondary btn-lg ms-2">Quay lại</a>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            Vui lòng <a href="login.php">đăng nhập</a> để thêm sản phẩm vào giỏ hàng
                        </div>
                        <a href="index.php" class="btn btn-secondary btn-lg">Quay lại</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
} catch(Exception $e) {
    echo '<div class="container mt-5"><div class="alert alert-danger">' . $e->getMessage() . '</div></div>';
}

require_once 'layout/footer.php';
?> 