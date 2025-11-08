document.addEventListener("DOMContentLoaded", () => {
    const params = new URLSearchParams(window.location.search);
    const id = params.get('id'); // lấy MaKH từ URL

    if (!id) {
        alert("Không tìm thấy ID khách hàng!");
        return;
    }

    const notifyOverlay = document.getElementById("notifyOverlay");
    const notifyMessage = document.getElementById("notifyMessage");
    const closeNotify = document.getElementById("closeNotify");

    function showNotify(msg) {
        notifyMessage.textContent = msg;
        notifyOverlay.style.display = "flex";
    }

    function hideNotify() {
        notifyOverlay.style.display = "none";
    }

    closeNotify.addEventListener("click", hideNotify);

    // ================== 1. Lấy thông tin khách hàng ==================
    fetch(`../../API/admin/customer_api.php?action=get_by_id&MaKH=${id}`)
        .then(res => res.json())
        .then(data => {
            if(data.status === "success") {
                const c = data.data;
                document.querySelector(".MaKH").textContent = c.MaKH;
                document.querySelector(".TenKH").textContent = c.TenKH;
                document.querySelector(".SoDienThoai").textContent = c.SoDienThoai;
                document.querySelector(".DiaChi").textContent = c.DiaChi;

                // Sau khi load thông tin khách hàng, load lịch sử mua hàng
                loadPurchaseHistory(c.MaKH);
            } else {
                showNotify(data.message);
            }
        })
        .catch(err => {
            console.error(err);
            showNotify("Lỗi kết nối server!");
        });

    // ================== 2. Lấy lịch sử mua hàng ==================
    function loadPurchaseHistory(MaKH) {
        fetch(`../../API/admin/PuschaseHistory_api.php?action=getHistory&MaKH=${MaKH}`)
            .then(res => res.json())
            .then(res => {
                const tbody = document.querySelector("#HistoryTable tbody");
                tbody.innerHTML = ""; // Xóa dữ liệu cũ

                if (res.status === "success") {
                    res.data.forEach(item => {
                        const tr = document.createElement("tr");
                        tr.innerHTML = `
                            <td>${item.MaHD}</td>
                            <td>${item.NgayBan}</td>
                            <td>${item.MaSP}</td>
                            <td>${item.TenSP}</td>
                            <td>${item.SoLuong}</td>
                            <td>${item.DonGia.toLocaleString()} VNĐ</td>
                            <td>${item.ThanhTien.toLocaleString()} VNĐ</td>
                        `;
                        tbody.appendChild(tr);
                    });

                    if (res.count === 0) {
                        tbody.innerHTML = `<tr><td colspan="4" style="text-align:center;">Chưa có lịch sử mua hàng</td></tr>`;
                    }

                } else {
                    tbody.innerHTML = `<tr><td colspan="4" style="text-align:center;">${res.message}</td></tr>`;
                }
            })
            .catch(err => {
                console.error(err);
                showNotify("Lỗi khi lấy lịch sử mua hàng!");
            });
    }
});
