<?php
include_once '../../API/Config/db_config.php';

$featured_categories = [
    'DM01' => 'Chuột',
    'DM05' => 'Lap',
    'DM12' => 'Lót chuột',
    'DM03' => 'Tai nghe'
];
?>

<div class="featured-section-v3">
    <h2>Danh mục nổi bật</h2>
    <div class="featured-list">
        <?php foreach ($featured_categories as $maDM => $tenDM): ?>
            <div class="featured-row">
                <div class="category-label">
                    <span><?= htmlspecialchars($tenDM) ?></span>
                </div>
                <div class="product-row">
                    <div class="product-slider" data-category="<?= $maDM ?>">
                        </div>
                    <div class="pagination-mini" data-category="<?= $maDM ?>">
    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="product-section" id="searchResults" style="display: none;">
    <h2>Danh sách sản phẩm</h2>
    <div id="productContainer" class="product-grid"></div>
    <div id="paginationContainer" class="pagination-main"></div>
</div>

<script>
    /**
     * Helper: Định dạng tiền tệ (Cần cho cả 2 hàm)
     */
    function formatCurrency(number) {
        if (isNaN(number) || number === null) return '0₫';
        return new Intl.NumberFormat('vi-VN').format(number) + '₫';
    }

    /**
     * Hàm này do Home.php cung cấp,
     * để loadProducts.js sử dụng
     */
    function createProductCardHTML(product) {
        const hinh = product.HinhAnh 
            ? `../img/${product.HinhAnh}` 
            : '../img/default_product.png';
            
        return `
        <div class="product-card">
            <img src="${hinh}" 
                 alt="${product.TenSP}" 
                 loading="lazy"
                 onerror="this.src='../img/default_product.png'">
            <h3>${product.TenSP}</h3>
            <p class="price">${formatCurrency(product.DonGia)}</p>
            <a href="Index.php?do=Details&id=${product.MaSP}" class="btn-detail">
                Chi tiết sản phẩm
            </a>
        </div>`;
    }

    /**
     * Hàm này cũng phải được cung cấp,
     * vì loadDetails.js cũng chạy trên trang Home
     */
    function createDetailHTML(product, services) {
        const hinh = product.HinhAnh 
            ? `../img/${product.HinhAnh}` 
            : '../img/default_product.png';
        const ten = product.TenSP;
        const gia = formatCurrency(product.DonGia); 
        const mota = product.MoTa || '<em>Chưa có mô tả cho sản phẩm này.</em>';

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
</script>