function initDetailPage(id) {
    
    const container = document.getElementById('product-detail-container');
    const productNameEl = document.getElementById('product-name');
    const productPriceEl = document.getElementById('product-price');
    const productImageEl = document.getElementById('product-image');
    const productDescEl = document.getElementById('product-description');
    
    const btnAddToCart = document.getElementById('btn-add-to-cart');
    const btnBuyNow = document.getElementById('btn-buy-now');

    if (!container) return;

    if (!id) {
        container.innerHTML = '<p style="color:red; text-align:center;">Không tìm thấy mã sản phẩm.</p>';
        return;
    }
    fetch(`../../API/client/Product/Detail.php?id=${encodeURIComponent(id)}`)
        .then(r => r.json())
        .then(data => {
            if (data.error || !data.product) {
                container.innerHTML = '<p style="color:red;">' + (data.error || 'Lỗi tải sản phẩm') + '</p>';
                return;
            }

            const product = data.product;
            const price = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(product.DonGia);

            if (productNameEl) productNameEl.textContent = product.TenSP;
            if (productPriceEl) productPriceEl.textContent = price;
            if (productDescEl) productDescEl.innerHTML = product.MoTa;
            
            if (productImageEl) {
                productImageEl.src = product.HinhAnh ? `../img/${product.HinhAnh}` : '../img/placeholder.png';
                productImageEl.alt = product.TenSP;
                productImageEl.onerror = () => { productImageEl.src = '../img/placeholder.png'; };
            }
            

            // ĐÃ XÓA LOGIC .classList
            if (btnBuyNow) {
                btnBuyNow.addEventListener('click', function() {
                    alert('Chức năng "Mua ngay" sẽ được triển khai sau. Sản phẩm: ' + product.TenSP);
                    window.location.href = `Index.php?do=CartForm&action=mua&MaSP=${encodeURIComponent(id)}`;
                });
            }
            if(btnAddToCart)
            {
                btnAddToCart.addEventListener('click', function() {
                    alert('Đã thêm sản phẩm: ' + product.TenSP + ' vào giỏ hàng');
                    window.location.href = `Index.php?do=CartForm&action=them&MaSP=${encodeURIComponent(id)}`;
                });
            }
        })
        .catch(err => {
            container.innerHTML = '<p style="color:red;">Lỗi kết nối khi tải chi tiết.</p>';
            console.error(err);
        });
}