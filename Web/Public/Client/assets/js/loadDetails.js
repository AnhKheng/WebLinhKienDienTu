(function() {

    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('product-detail-container');
        
        if (!container) {
            return; 
        }

        container.addEventListener('click', function(e) {
            const serviceItem = e.target.closest('.service-item');
            if (!serviceItem) {
                return;
            }
            serviceItem.classList.toggle('selected');
        });

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

                const buyNowBtn = container.querySelector('.buy-now-btn');
                const addToCartBtn = container.querySelector('.add-to-cart-btn');

                if (buyNowBtn) {
                    buyNowBtn.addEventListener('click', function() {
                        alert('Chức năng "Mua ngay" sẽ được triển khai sau. Sản phẩm: ' + data.product.TenSP);
                    });
                }

                if (addToCartBtn) {
                    addToCartBtn.addEventListener('click', function() {
                        alert('Sản phẩm "' + data.product.TenSP + '" đã được thêm vào giỏ hàng.');
                    });
                }
            })
            .catch(err => {
                container.innerHTML = '<p style="color:red;">Lỗi kết nối khi tải chi tiết.</p>';
                console.error(err);
            });
    });

})();