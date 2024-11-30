<link rel="stylesheet" href="css/body.css">
<main class="container">
    <!-- Hero Section -->
    <section class="hero-section">
        <!-- Carousel -->
        <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active"></button>
                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1"></button>
                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2"></button>
            </div>
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="image/banner/banner1oto.jpg" class="d-block w-100" alt="Banner 1">
                </div>
                <div class="carousel-item">
                    <img src="image/banner/banner2oto.jpg" class="d-block w-100" alt="Banner 2">
                </div>
                <div class="carousel-item">
                    <img src="image/banner/banner3oto.jpg" class="d-block w-100" alt="Banner 3">
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </section>

    <!-- Welcome Section -->
    <section class="welcome-section">
        <div class="welcome-content text-center mt-5">
            <h1 class="welcome-title">Showroom Xe Hơi Cao Cấp</h1>
            <p class="welcome-description">
                Chào mừng quý khách đến với showroom của chúng tôi - nơi quy tụ những dòng xe sang trọng và đẳng cấp nhất. 
                Với đội ngũ tư vấn chuyên nghiệp, chúng tôi cam kết mang đến trải nghiệm mua sắm xe hơi tuyệt vời nhất.
            </p>
            
            </div>
            <div class="welcome-features mt-4">
                <div class="row justify-content-center">
                    <div class="col-md-3">
                        <i class="fas fa-shield-alt"></i>
                        <h5>Bảo Hành Chính Hãng</h5>
                    </div>
                    <div class="col-md-3">
                        <i class="fas fa-money-check-alt"></i>
                        <h5>Hỗ Trợ Trả Góp</h5>
                    </div>
                    <div class="col-md-3">
                        <i class="fas fa-tools"></i>
                        <h5>Dịch Vụ Bảo Dưỡng</h5>
                    </div>
                </div>
                <div class="search-box text-center mt-4">
                    <form action="search.php" method="GET" class="d-inline-block">
                        <input type="text" name="keyword" placeholder="Tìm kiếm sản phẩm...">
                        <button type="submit">
                            <i class="fa fa-search"></i> Tìm kiếm
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
  

<section class="container mt-2">
    <div class="row">
        <!-- Sidebar bên trái -->
        <div class="col-md-3">
            <div class="sidebar-brands services-section">
                <h3 class="sidebar-title">Thương hiệu xe</h3>
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <a href="?">Tất cả xe</a>
                    </li>
                    <?php
                    require_once 'config.php';
                    try {
                        $stmt = $conn->query("SELECT * FROM thuonghieu");
                        $brands = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        foreach($brands as $brand) {
                            echo '<li class="list-group-item d-flex justify-content-between align-items-center">';
                            echo '<a href="?brand=' . $brand['idth'] . '">' . $brand['tenthuonghieu'] . '</a>';
                            
                            echo '</li>';
                        }
                    } catch(PDOException $e) {
                        echo "Lỗi: " . $e->getMessage();
                    }
                    ?>
                </ul>
            </div>
        </div>

        <!-- Content bên phải -->
        <div class="col-md-9">
            <!-- Services Section -->
            <section class="services-section">
                <h2 class="section-title text-center mb-4">Các Mẫu Xe Của Chúng Tôi</h2>
                <div class="row row-cols-1 row-cols-md-3 g-4">
                    <?php
                    try {
                        // Số sản phẩm trên mỗi trang
                        $items_per_page = 3;
                        
                        // Lấy trang hiện tại
                        $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                        $offset = ($current_page - 1) * $items_per_page;
                        
                        // Thêm điều kiện lọc theo thương hiệu nếu có
                        $brand_filter = isset($_GET['brand']) ? "WHERE idth = " . $_GET['brand'] : "";
                        
                        // Đếm tổng số sản phẩm
                        $count_stmt = $conn->query("SELECT COUNT(*) FROM oto $brand_filter");
                        $total_items = $count_stmt->fetchColumn();
                        $total_pages = ceil($total_items / $items_per_page);
                        
                        // Query với LIMIT và OFFSET cho phân trang
                        $stmt = $conn->query("SELECT * FROM oto $brand_filter LIMIT $items_per_page OFFSET $offset");
                        $cars = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        foreach($cars as $car) {
                            echo '<div class="col">';
                            echo '<div class="card h-100">';
                            echo '<img src="image/' . $car['img'] . '" class="card-img-top" alt="' . $car['tenxe'] . '">';
                            echo '<div class="card-body">';
                            echo '<h5 class="card-title">' . $car['tenxe'] . '</h5>';
                            echo '<ul class="list-unstyled">';
                            echo '<li><i class="fas fa-palette"></i> Màu sắc: ' . $car['mausac'] . '</li>';
                            echo '<li><i class="fas fa-car"></i> Dòng xe: ' . $car['dongxe'] . '</li>';
                            echo '<li><i class="fas fa-calendar"></i> Ngày sản xuất: ' . date('d/m/Y', strtotime($car['ngaysanxuat'])) . '</li>';
                            echo '<li class="price-tag"><i class="fas fa-tag"></i> Giá: ' . number_format($car['giaxe'], 0, ',', '.') . ' VNĐ</li>';
                            echo '</ul>';
                            echo '<div class="text-center mt-3">';
                            echo '<a href="chitiet.php?id=' . $car['idoto'] . '" class="btn btn-primary">Xem chi tiết</a>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                        }
                    } catch(PDOException $e) {
                        echo "Lỗi: " . $e->getMessage();
                    }
                    ?>
                </div>
                
                <!-- Pagination -->
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php
                        // Previous button
                        $prev_page = $current_page - 1;
                        $prev_link = $prev_page > 0 ? "?page=$prev_page" . (isset($_GET['brand']) ? "&brand=".$_GET['brand'] : "") : "#";
                        $prev_disabled = $prev_page <= 0 ? "disabled" : "";
                        
                        echo "<li class='page-item $prev_disabled'>";
                        echo "<a class='page-link' href='$prev_link'>Previous</a>";
                        echo "</li>";
                        
                        // Page numbers
                        for($i = 1; $i <= $total_pages; $i++) {
                            $active = $i == $current_page ? "active" : "";
                            $link = "?page=$i" . (isset($_GET['brand']) ? "&brand=".$_GET['brand'] : "");
                            echo "<li class='page-item $active'>";
                            echo "<a class='page-link' href='$link'>$i</a>";
                            echo "</li>";
                        }
                        
                        // Next button
                        $next_page = $current_page + 1;
                        $next_link = $next_page <= $total_pages ? "?page=$next_page" . (isset($_GET['brand']) ? "&brand=".$_GET['brand'] : "") : "#";
                        $next_disabled = $next_page > $total_pages ? "disabled" : "";
                        
                        echo "<li class='page-item $next_disabled'>";
                        echo "<a class='page-link' href='$next_link'>Next</a>";
                        echo "</li>";
                        ?>
                    </ul>
                </nav>
            </section>
        </div>
    </div>
    </section>


<section class="additional-services-section container">
                <h2 class="section-title text-center mb-4">Dịch Vụ Của Chúng Tôi</h2>
                <div class="row row-cols-1 row-cols-md-3 g-4">
                    <?php
                    try {
                        $stmt = $conn->query("SELECT * FROM dichvu");
                        $services = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        foreach($services as $service) {
                            echo '<div class="col">';
                            echo '<div class="card h-100">';
                            echo '<img src="image/dichvu/' . $service['hinhanh'] . '" class="card-img-top" alt="' . $service['tendv'] . '">';
                            echo '<div class="card-body">';
                            echo '<h5 class="card-title">' . $service['tendv'] . '</h5>';
                            echo '<p class="card-text">' . $service['mota'] . '</p>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                        }
                    } catch(PDOException $e) {
                        echo "Lỗi: " . $e->getMessage();
                    }
                    ?>
                </div>
</section>
            <div class="container mb-3 mt-3 ">
                <div class="row gx-5">
                    <div class="col">
                         <div class="p-3 border bg-light">
                            <img src="image/anhduoicung.png" class="img-fluid" width="100%" alt="Banner cuối">
                         </div>
                    </div>
                    <div class="col  d-flex flex-column justify-content-center align-items-center">
                        <div class="p-3 border bg-light">
                            <h1 class="mb-3">Mercedes-Maybach</h1>
                            <p>Kế thừa di sản trăm năm từ thương hiệu xe lừng danh, Mercedes-Maybach tái định nghĩa chuẩn mực thượng lưu: sự tự do bất tận. Tất cả những gì bạn cần làm là bước lên xe và tận hưởng không gian nội thất sang trọng bậc nhất, trải nghiệm sự kết hợp tuyệt vời giữa cải tiến công nghệ đỉnh cao và từng chi tiết được hoàn thiện tỉ mỉ.</p>
                        </div>
                    </div>
                </div>
            </div> 

</main>
<script src="js/search.js"></script>