// loadDetails.js
(function() {
    /**
     * Helper: Tạo HTML cho trang chi tiết
     */
    /**
     * Helper: Tạo HTML cho trang chi tiết
     */
    function createDetailHTML(product) {
        const hinh = product.HinhAnh 
            ? `../img/${product.HinhAnh}` 
            : '../img/default_product.png';
        const ten = product.TenSP;
        const gia = new Intl.NumberFormat('vi-VN').format(product.DonGia) + '₫';
        const mota = product.MoTa || '<em>Chưa có mô tả cho sản phẩm này.</em>';

        return `
        <div class="product-detail">
            <h1 style="text-align: center; color: #d10024; margin-bottom: 25px;">CHI TIẾT SẢN PHẨM</h1> 
            <div style="display:flex;gap:20px;align-items:flex-start;flex-wrap:wrap;">
                <div style="flex:0 0 360px;max-width:360px;background:#fff;padding:12px;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,0.06);">
                    <img src="${hinh}" alt="${ten}" style="width:100%;height:auto;object-fit:contain;" onerror="this.src='../img/default_product.png'">
                </div>
                <div style="flex:1;min-width:280px;">
                    <h2 style="margin-top:0;">${ten}</h2>
                    <p class="price" style="font-size:20px;color:#d10024;font-weight:700;"><?= $gia ?></p>
                    <div class="description" style="margin-top:12px;padding:12px;background:#fff;border-radius:8px;box-shadow:0 2px 6px rgba(0,0,0,0.04);">
                        ${mota}
                    </div>
                </div>
            </div>
        </div>`;
    }

    // Chỉ chạy khi trang được tải xong
    document.addEventListener('DOMContentLoaded', function() {
        // Tìm "khung chờ" mà chúng ta đã tạo trong Details.php
        const container = document.getElementById('product-detail-container');
        
        // Nếu không tìm thấy div này (nghĩa là chúng ta đang ở trang Home),
        // thì không làm gì cả.
        if (!container) {
            return; 
        }

        const id = container.dataset.id;
        if (!id) {
            container.innerHTML = '<p style="color:red;">Lỗi: Không có ID sản phẩm.</p>';
            return;
        }
        
        fetch(`../../API/client/Product/Detail.php?id=${encodeURIComponent(id)}`)
            .then(r => r.json())
            .then(data => {
                if (data.error || !data.product) {
                    container.innerHTML = '<p style="color:red;">' + (data.error || 'Lỗi tải sản phẩm') + '</p>';
                    return;
                }
                
                // Render HTML vào "khung chờ"
                container.innerHTML = createDetailHTML(data.product);
            })
            .catch(err => {
                container.innerHTML = '<p style="color:red;">Lỗi kết nối khi tải chi tiết.</p>';
                console.error(err);
            });
    });

})(); // Kết thúc IIFE