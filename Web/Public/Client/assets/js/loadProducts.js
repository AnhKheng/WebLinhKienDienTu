function loadProducts() {
    var category = document.getElementById('categorySelect').value;
    var search = document.getElementById('searchInput').value;
    
    var url = '../../API/client/Product/get_products.php?category=' + encodeURIComponent(category) + '&search=' + encodeURIComponent(search);  // Cập nhật đường dẫn
    
    console.log('AJAX URL:', url);  // Debug: Xem URL trong Console
    
    var xhr = new XMLHttpRequest();
    xhr.open('GET', url, true);
    xhr.onload = function() {
        console.log('AJAX Status:', xhr.status);  // Debug: Xem status
        console.log('AJAX Response:', xhr.responseText);  // Debug: Xem response
        if (xhr.status === 200) {
            document.getElementById('productContainer').innerHTML = xhr.responseText;
        } else {
            document.getElementById('productContainer').innerHTML = '<p>Lỗi tải sản phẩm. Status: ' + xhr.status + '</p>';
        }
    };
    xhr.onerror = function() {
        console.log('AJAX Error occurred');  // Debug: Nếu network error
        document.getElementById('productContainer').innerHTML = '<p>Lỗi mạng khi tải sản phẩm.</p>';
    };
    xhr.send();
}

// Load sản phẩm ban đầu khi trang load
window.onload = function() {
    loadProducts();
};

// Gán sự kiện cho nút tìm kiếm
document.getElementById('searchBtn').addEventListener('click', loadProducts);