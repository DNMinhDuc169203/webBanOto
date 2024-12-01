<?php
session_start();
include '../layout/header.php';
?>

<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <h1 class="text-center mb-4">Liên hệ với chúng tôi</h1>
        </div>
    </div>

    <div class="row">
        <!-- Thông tin liên hệ -->
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h3 class="card-title mb-4">Thông tin liên hệ</h3>
                    <div class="mb-3">
                        <i class="fas fa-map-marker-alt text-primary me-2"></i>
                        <span>125A/ Au Duong Lan,Phuong 1, Quan 8, TP.HCM</span>
                    </div>
                    <div class="mb-3">
                        <i class="fas fa-phone text-primary me-2"></i>
                        <span>(84) 824 390 594</span>
                    </div>
                    <div class="mb-3">
                        <i class="fas fa-envelope text-primary me-2"></i>
                        <span>dh@student.stu.edu.vn</span>
                    </div>
                    <div class="mb-3">
                        <i class="fas fa-clock text-primary me-2"></i>
                        <span>Thứ 2 - Thứ 7: 8:00 - 17:00</span>
                    </div>
                    
                    <!-- Mạng xã hội -->
                    <div class="mt-4">
                        <h4 class="h5 mb-3">Kết nối với chúng tôi</h4>
                        <a href="#" class="text-primary me-3"><i class="fab fa-facebook fa-2x"></i></a>
                        <a href="#" class="text-info me-3"><i class="fab fa-twitter fa-2x"></i></a>
                        <a href="#" class="text-danger"><i class="fab fa-instagram fa-2x"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form liên hệ -->
        <div class="col-md-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h3 class="card-title mb-4">Gửi tin nhắn cho chúng tôi</h3>
                    <form action="process_contact.php" method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Họ và tên</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Số điện thoại</label>
                            <input type="tel" class="form-control" id="phone" name="phone">
                        </div>
                        <div class="mb-3">
                            <label for="subject" class="form-label">Chủ đề</label>
                            <input type="text" class="form-control" id="subject" name="subject" required>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Nội dung tin nhắn</label>
                            <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Gửi tin nhắn</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bản đồ -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h3 class="card-title mb-4">Vị trí của chúng tôi</h3>
                    <div class="ratio ratio-16x9">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d1165.395634914771!2d106.68725022364993!3d10.741570067737335!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2zMTDCsDQ0JzI5LjciTiAxMDbCsDQxJzEzLjMiRQ!5e0!3m2!1svi!2s!4v1733034271233!5m2!1svi!2s" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                                style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../layout/footer.php'; ?> 