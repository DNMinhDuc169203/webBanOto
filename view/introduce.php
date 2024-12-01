<?php
session_start();
include '../layout/header.php';
?>

<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <h1 class="text-center mb-4">Giới thiệu về chúng tôi</h1>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-md-6">
            <img src="../image/gioithieu.png" alt="Về chúng tôi" class="img-fluid rounded shadow">
        </div>
        <div class="col-md-6">
            <h2 class="mb-3">Câu chuyện của chúng tôi</h2>
            <p>Được thành lập vào năm 2024, chúng tôi đã không ngừng phát triển và cam kết mang đến những sản phẩm chất lượng nhất cho khách hàng.</p>
            <p>Với đội ngũ nhân viên chuyên nghiệp và tận tâm, chúng tôi luôn đặt sự hài lòng của khách hàng lên hàng đầu.</p>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-12">
            <h2 class="text-center mb-4">Giá trị cốt lõi</h2>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-heart fa-3x text-primary mb-3"></i>
                    <h3 class="card-title">Chất lượng</h3>
                    <p class="card-text">Cam kết cung cấp sản phẩm chất lượng cao nhất đến tay khách hàng.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-handshake fa-3x text-primary mb-3"></i>
                    <h3 class="card-title">Uy tín</h3>
                    <p class="card-text">Xây dựng niềm tin với khách hàng thông qua sự minh bạch và trung thực.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-3x text-primary mb-3"></i>
                    <h3 class="card-title">Dịch vụ</h3>
                    <p class="card-text">Đặt khách hàng làm trọng tâm với dịch vụ chăm sóc tận tình.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../layout/footer.php'; ?> 