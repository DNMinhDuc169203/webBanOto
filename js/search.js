document.addEventListener('DOMContentLoaded', function() {
    const searchForm = document.querySelector('.search-box form');
    const searchInput = searchForm.querySelector('input[name="keyword"]');
    
    searchForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        fetch(`search.php?keyword=${encodeURIComponent(searchInput.value)}`)
            .then(response => response.json())
            .then(data => {
                const carContainer = document.querySelector('.row.row-cols-1.row-cols-md-3.g-4');
                carContainer.innerHTML = ''; // Xóa nội dung hiện tại
                
                data.forEach(car => {
                    const carHTML = `
                        <div class="col">
                            <div class="card h-100">
                                <img src="image/${car.img}" class="card-img-top" alt="${car.tenxe}">
                                <div class="card-body">
                                    <h5 class="card-title">${car.tenxe}</h5>
                                    <ul class="list-unstyled">
                                        <li><i class="fas fa-palette"></i> Màu sắc: ${car.mausac}</li>
                                        <li><i class="fas fa-car"></i> Dòng xe: ${car.dongxe}</li>
                                        <li><i class="fas fa-calendar"></i> Ngày sản xuất: ${new Date(car.ngaysanxuat).toLocaleDateString('vi-VN')}</li>
                                        <li class="price-tag"><i class="fas fa-tag"></i> Giá: ${new Intl.NumberFormat('vi-VN').format(car.giaxe)} VNĐ</li>
                                    </ul>
                                    <div class="text-center mt-3">
                                        <a href="chitiet.php?id=${car.idoto}" class="btn btn-primary">Xem chi tiết</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    carContainer.innerHTML += carHTML;
                });
                
                if (data.length === 0) {
                    carContainer.innerHTML = '<div class="col-12 text-center"><p>Không tìm thấy kết quả nào.</p></div>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    });
}); 