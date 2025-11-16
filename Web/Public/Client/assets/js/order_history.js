// JS cho hiệu ứng bật/tắt (accordion) của trang Lịch Sử Đơn Hàng

function toggleOrderDetails(detailsId, headerElement) {
    var details = document.getElementById(detailsId);
    var icon = headerElement.querySelector('.toggle-icon');
    
    if (details.style.display === 'none' || details.style.display === '') {
        details.style.display = 'block';
        if (icon) icon.textContent = '[-]'; // Đổi icon thành [-]
        headerElement.style.borderRadius = '8px 8px 0 0'; // Bo góc khi mở
    } else {
        details.style.display = 'none';
        if (icon) icon.textContent = '[+]'; // Đổi icon thành [+]
        headerElement.style.borderRadius = '8px'; // Trả lại bo góc
    }
}