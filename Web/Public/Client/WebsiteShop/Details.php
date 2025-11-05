<?php
$product_id = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : '';
?>

<div class="product-detail-layout" id="product-detail-container">
    
    <div class="product-gallery">
        <img id="product-image" src="../img/placeholder.png" alt="Đang tải..." />
    </div>

    <div class="product-info">
        
        <h2 id="product-name">Đang tải tên sản phẩm...</h2>
        
        <div class="price-area">
            <span id="product-price" class="price">...</span>
        </div>

        <div class="action-buttons">
            <button id="btn-add-to-cart" class="btn-primary" data-id="<?php echo htmlspecialchars($product_id); ?>">
                <i class="fa fa-shopping-cart"></i> Thêm vào giỏ
            </button>

            <button id="btn-buy-now" class="btn-secondary">
                <i class="fa fa-bolt"></i> Mua ngay
            </button>
        </div>
        
        <div class="description-area">
            <h4>Mô tả chi tiết sản phẩm</h4>
            <div id="product-description">
                <p>Đang tải mô tả...</p>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        const productId = '<?php echo $product_id; ?>';
        initDetailPage(productId);
    });
</script>