// loadDetails.js
(function() {

    // ĐÃ XÓA hàm formatCurrency()
    // ĐÃ XÓA hàm createDetailHTML()

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
                
                // Hàm createDetailHTML() này bây giờ là hàm global
                // do file Details.php cung cấp
                container.innerHTML = createDetailHTML(data.product, data.services); 

                // ==============================================
                // == THÊM MỚI: XỬ LÝ CLICK CHO NÚT MUA HÀNG ==
                // ==============================================
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