// Xử lý Google Sign-In
function handleCredentialResponse(response) {
    console.log('Google Sign-In response:', response);
    
    // Sửa đường dẫn đến google_login.php
    fetch('../auth/google_login.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            token: response.credential
        })
    })
    .then(response => {
        console.log('Raw response:', response);
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            console.log('Login successful, redirecting...');
            window.location.href = '../index.php';
        } else {
            console.error('Login failed:', data.error);
            showError(data.error || 'Đăng nhập thất bại');
        }
    })
    .catch(error => {
        console.error('Error during login:', error);
        showError('Có lỗi xảy ra khi đăng nhập');
    });
}

// Xử lý Facebook Login
function checkLoginState() {
    // Hiển thị loading
    showLoading();
    
    FB.getLoginStatus(function(response) {
        if (response.status === 'connected') {
            FB.api('/me', {fields: 'name,email'}, function(response) {
                fetch('facebook_login.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        name: response.name,
                        email: response.email,
                        facebook_id: response.id
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = 'index.php';
                    } else {
                        showError(data.error || 'Đăng nhập thất bại');
                    }
                })
                .catch(error => {
                    showError('Có lỗi xảy ra khi đăng nhập');
                    console.error('Error:', error);
                })
                .finally(() => {
                    hideLoading();
                });
            });
        } else {
            hideLoading();
            showError('Không thể kết nối với Facebook');
        }
    });
}

// Utility functions
function showLoading() {
    // Thêm loading spinner hoặc disable nút đăng nhập
    document.querySelectorAll('button[type="submit"]').forEach(button => {
        button.disabled = true;
    });
}

function hideLoading() {
    // Ẩn loading spinner hoặc enable nút đăng nhập
    document.querySelectorAll('button[type="submit"]').forEach(button => {
        button.disabled = false;
    });
}

function showError(message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'alert alert-danger';
    errorDiv.textContent = message;
    
    const form = document.querySelector('form');
    form.insertBefore(errorDiv, form.firstChild);
    
    // Tự động ẩn sau 5 giây
    setTimeout(() => {
        errorDiv.remove();
    }, 5000);
} 