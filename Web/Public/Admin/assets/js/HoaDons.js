let allHoaDon = []; // Lưu toàn bộ dữ liệu hóa đơn để lọc

// 🧾 Tải danh sách hóa đơn từ API
async function loadHoaDon() {
  try {
    const response = await fetch("../../API/admin/HoaDon/ViewHoaDon.php");
    const result = await response.json();

    if (result.status === "success") {
      allHoaDon = result.data;
      renderNhanVienOptions(allHoaDon);
      renderTable(allHoaDon);
    } else {
      alert(result.message || "Không thể tải danh sách hóa đơn");
    }
  } catch (error) {
    console.error("Lỗi khi tải hóa đơn:", error);
    alert("Không thể kết nối đến API.");
  }
}

// 🧩 Hiển thị dữ liệu ra bảng
function renderTable(hoadons) {
  const tbody = document.querySelector("#hoadonTable tbody");
  tbody.innerHTML = "";

  hoadons.forEach((hd) => {
    const date = hd.NgayBan
      ? new Date(hd.NgayBan).toLocaleString("vi-VN")
      : "—";

    const row = `
      <tr>
        <td>${hd.MaHD}</td>
        <td>${date}</td>
        <td>${hd.MaNV} - ${hd.TenNV || "—"}</td>
        <td>${hd.MaKH} - ${hd.TenKH || "—"}</td>
        <td>${hd.MaCH} - ${hd.TenCH || "—"}</td>
        <td>${Number(hd.TongTien).toLocaleString("vi-VN")} ₫</td>
        <td>
          <button class="btn-detail" onclick="viewDetail('${hd.MaHD}')">Chi tiết</button>
          <button class="btn-delete" onclick="deleteHoaDon('${hd.MaHD}')">Xóa</button>
        </td>
      </tr>
    `;
    tbody.insertAdjacentHTML("beforeend", row);
  });
}

// 🧩 Sinh danh sách nhân viên để lọc
function renderNhanVienOptions(hoadons) {
  const select = document.getElementById("nhanvienFilter");
  select.innerHTML = '<option value="all">Tất cả nhân viên</option>';

  const nhanviens = [...new Set(hoadons.map((hd) => hd.TenNV || hd.MaNV))];

  nhanviens.forEach((nv) => {
    const option = document.createElement("option");
    option.value = nv;
    option.textContent = nv;
    select.appendChild(option);
  });
}

// 🧩 Lọc hóa đơn theo nhân viên
function applyFilter() {
  const selected = document.getElementById("nhanvienFilter").value;

  if (selected === "all") {
    renderTable(allHoaDon);
  } else {
    const filtered = allHoaDon.filter(
      (hd) => (hd.TenNV || hd.MaNV) === selected
    );
    renderTable(filtered);
  }
}

// 🧩 Xem chi tiết hóa đơn (demo)
function viewDetail(id) {
  alert(`Xem chi tiết hóa đơn: ${id}`);
  // 👉 Sau này có thể mở modal hoặc redirect sang trang chi tiết
}

// 🧩 Xóa hóa đơn (demo)
function deleteHoaDon(id) {
  if (confirm(`Bạn có chắc muốn xóa hóa đơn ${id}?`)) {
    alert(`Đã xóa hóa đơn: ${id}`);
    // 👉 Sau này bạn có thể gọi API Delete tại đây
  }
}

// Khi trang load
window.addEventListener("DOMContentLoaded", loadHoaDon);
