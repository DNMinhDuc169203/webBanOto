<?php
// Get summary counts from database
$stmt = $conn->prepare("SELECT 
    (SELECT COUNT(*) FROM oto) as total_cars,
    (SELECT COUNT(*) FROM khachhang) as total_customers,
    (SELECT COUNT(*) FROM phieudathang) as total_orders,
    (SELECT COUNT(*) FROM thuonghieu) as total_brands
");
$stmt->execute();
$stats = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<div class="container mt-4">
    <h2>Dashboard</h2>
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5>Tổng số xe</h5>
                    <h3><?php echo $stats['total_cars']; ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5>Khách hàng</h5>
                    <h3><?php echo $stats['total_customers']; ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5>Đơn hàng</h5>
                    <h3><?php echo $stats['total_orders']; ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5>Thương hiệu</h5>
                    <h3><?php echo $stats['total_brands']; ?></h3>
                </div>
            </div>
        </div>
    </div>
</div> 