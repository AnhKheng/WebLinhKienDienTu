// loadDetails.js
(function() {

    /**
     * Helper: Định dạng tiền tệ
     */
    function formatCurrency(number) {
        if (isNaN(number) || number === null) return '0₫';
        return new Intl.NumberFormat('vi-VN').format(number) + '₫';
    }

    /**
     * Helper: Tạo HTML cho trang chi tiết
     */
    function createDetailHTML(product, services) {
        const hinh = product.HinhAnh 
            ? `../img/${product.HinhAnh}` 
            : '../img/default_product.png';
        const ten = product.TenSP;
        const gia = formatCurrency(product.DonGia); 
        const mota = product.MoTa || '<em>Chưa có mô tả cho sản phẩm này.</em>';

        // --- TẠO HTML CHO DỊCH VỤ ---
        let servicesHTML = '';
        if (services && services.length > 0) {
            servicesHTML += '<div class="related-services">';
            servicesHTML += '<h3>Dịch vụ đi kèm</h3>';
            
            services.forEach(service => {
                const serviceImg = service.HinhAnh
                    ? `../img/${service.HinhAnh}`
                    : '../img/default_product.png';

                servicesHTML += `
                    <div class="service-item">
                        <img src="${serviceImg}" alt="${service.TenSP}" class="service-img">
                        <div class="service-info">
                            <h4>${service.TenSP}</h4>
                            <span class="price">${formatCurrency(service.DonGia)}</span>
                        </div>
                    </div>
                `;
            });

            servicesHTML += '</div>';
        }
        // --- KẾT THÚC TẠO HTML DỊCH VỤ ---

        return `
        <div class="product-detail">
            <h1 style="text-align: center; color: #d10024; margin-bottom: 25px;">CHI TIẾT SẢN PHẨM</h1> 
            <div style="display:flex;gap:20px;align-items:flex-start;flex-wrap:wrap;">
                <div style="flex:0 0 360px;max-width:360px;background:#fff;padding:12px;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,0.06);">
                    <img src="${hinh}" alt="${ten}" style="width:100%;height:auto;object-fit:contain;" onerror="this.src='../img/default_product.png'">
                    
                    <div class="product-actions">
                        <button class="buy-now-btn">
                            <i class="fa-solid fa-bolt-lightning"></i> Mua ngay
                        </button>
                        <button class="add-to-cart-btn">
                            <i class="fa-solid fa-cart-shopping"></i> Thêm vào giỏ
                        </button>
                    </div>
                    </div>
                <div style="flex:1;min-width:280px;">
                    <h2 style="margin-top:0;">${ten}</h2>
                    <p class="price" style="font-size:20px;color:#d10024;font-weight:700;">${gia}</p>
                    <div class="description" style="margin-top:12px;padding:12px;background:#fff;border-radius:8px;box-shadow:0 2px 6px rgba(0,0,0,0.04);">
                        ${mota}
                    </div>
                    ${servicesHTML}
                </div>
            </div>
        </div>`;
    }

    // Chỉ chạy khi trang được tải xong
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('product-detail-container');
        
        if (!container) {
            return; 
        }

        // ==============================================
        // == XỬ LÝ CLICK VÀO GÓI DỊCH VỤ ==
        // ==============================================
        container.addEventListener('click', function(e) {
            const serviceItem = e.target.closest('.service-item');
            if (!serviceItem) {
                return;
            }
            serviceItem.classList.toggle('selected');
        });
        // ==============================================
        // == KẾT THÚC PHẦN DỊCH VỤ ==
        // ==============================================


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
                
                container.innerHTML = createDetailHTML(data.product, data.services);

                // ==============================================
                // == THÊM MỚI: XỬ LÝ CLICK CHO NÚT MUA HÀNG ==
                // ==============================================
                // Vì các nút được thêm DYNAMICALLY, ta cần lắng nghe SAU KHI HTML đã được chèn.
                const buyNowBtn = container.querySelector('.buy-now-btn');
                const addToCartBtn = container.querySelector('.add-to-cart-btn');

                if (buyNowBtn) {
                    buyNowBtn.addEventListener('click', function() {
                        alert('Chức năng "Mua ngay" sẽ được triển khai sau. Sản phẩm: ' + data.product.TenSP);
                        // TODO: Thêm logic mua hàng thực tế ở đây
                    });
                }

                if (addToCartBtn) {
                    addToCartBtn.addEventListener('click', function() {
                        alert('Sản phẩm "' + data.product.TenSP + '" đã được thêm vào giỏ hàng.');
                        // TODO: Thêm logic thêm vào giỏ hàng thực tế ở đây
                    });
                }
                // ==============================================
                // == KẾT THÚC PHẦN THÊM MỚI ==
                // ==============================================

            })
            .catch(err => {
                container.innerHTML = '<p style="color:red;">Lỗi kết nối khi tải chi tiết.</p>';
                console.error(err);
            });
    });

})(); // Kết thúc IIFE