
let allHoaDon = []; // Lưu toàn bộ dữ liệu hóa đơn để lọc

// Tải danh sách hóa đơn từ API
async function loadHoaDon() {
  try {
    console.log("🏁 DOMContentLoaded triggered");
    const tbody = document.querySelector("#hoadonTable tbody");
    console.log("tbody:", tbody);
    const response = await fetch("../../API/admin/invoice_api.php");

    // ✅ Kiểm tra HTTP status trước
    if (!response.ok) {
      throw new Error(`HTTP error! Status: ${response.status}`);
    }

    //  Kiểm tra nội dung có phải JSON không
    const text = await response.text();
    let result;
    try {
      result = JSON.parse(text);
    } catch (err) {
      console.error("Phản hồi API không phải JSON:", text);
      throw new Error("API không trả về dữ liệu JSON hợp lệ.");
    }

    // Nếu JSON hợp lệ, xử lý dữ liệu
    if (result.status === "success") {
      allHoaDon = result.data;
      renderNhanVienOptions(allHoaDon);
      renderTable(allHoaDon);
    } else {
      alert(result.message || "Không thể tải danh sách hóa đơn");
    }

  } catch (error) {
    console.error("Lỗi khi tải hóa đơn:", error);
    alert("Không thể kết nối đến API. Chi tiết: " + error.message);
  }
}

// Hiển thị dữ liệu ra bảng
function renderTable(hoadons) {
  const tbody = document.querySelector("#hoadonTable tbody");
  tbody.innerHTML = "";

  hoadons.forEach((hd) => {
    const date = hd.NgayBan
      ? new Date(hd.NgayBan).toLocaleString("vi-VN")
      : "—";

    const row = `
     
      <tr data-mahd="${hd.MaHD}">
        <td>${hd.MaHD}</td>
        <td>${date}</td>
        <td>${hd.MaNV} - ${hd.TenNV || "—"}</td>
        <td>${hd.MaKH} - ${hd.TenKH || "—"}</td>
        <td>${hd.MaCH} - ${hd.TenCH || "—"}</td>
        <td>${Number(hd.TongTien).toLocaleString("vi-VN")} ₫</td>
        <td>
          <button class="btn-detail" onclick="viewDetail('${hd.MaHD}')">Chi tiết</button>
          <button type="button" class="btn-delete" onclick="deleteHoaDon('${hd.MaHD}')">Xóa</button>
      </tr>
    `;
    tbody.insertAdjacentHTML("beforeend", row);
  });
}

//  Sinh danh sách nhân viên để lọc
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

//  Lọc hóa đơn theo nhân viên
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

//  Xem chi tiết hóa đơn (demo)
async function viewDetail(maHD) {
  try {
    const response = await fetch(`../../API/admin/invoice_api.php?action=viewDetail&MaHD=${maHD}`);
    const result = await response.json();

    if (result.status === "success") {
      const details = result.data;

      if (details.length === 0) {
        alert("Không có chi tiết nào cho hóa đơn này.");
        return;
      }

      let html = "";
      details.forEach((hd, index) => {
        html += `
          <div style="border-bottom: 1px solid #ccc; padding: 5px 0;">
            <p><strong>#${index + 1}</strong></p>
            <p><strong>Mã HĐ:</strong> ${hd.MaHD}</p>
            <p><strong>Mã SP:</strong> ${hd.MaSP}</p>
            <p><strong>Số lượng:</strong> ${hd.SoLuong}</p>
            <p><strong>Đơn giá:</strong> ${Number(hd.DonGia).toLocaleString("vi-VN")} ₫</p>
          </div>
        `;
      });

      document.getElementById("detailContent").innerHTML = html;
      document.getElementById("detailModal").style.display = "block";
    } else {
      alert(result.message || "Không tìm thấy chi tiết hóa đơn.");
    }
  } catch (err) {
    alert("Lỗi khi tải chi tiết hóa đơn: " + err.message);
  }
}


function closeModal() {
  document.getElementById("detailModal").style.display = "none";
}


//  Xóa hóa đơn (demo)
async function deleteHoaDon(maHD) {
  if (!confirm(`Bạn có chắc muốn xóa hóa đơn ${maHD}?`)) return;

  try {
    const response = await fetch(`../../API/admin/invoice_api.php?action=delete&MaHD=${maHD}`);
    const result = await response.json();

    if (result.status === "success") {
      alert(result.message);
      // Xóa dòng khỏi bảng HTML mà không cần load lại trang
      const row = document.querySelector(`tr[data-mahd='${maHD}']`);
      if (row) row.remove();
    } else {
      alert(result.message || "Không thể xóa hóa đơn.");
    }
  } catch (err) {
    alert("Lỗi khi xóa hóa đơn: " + err.message);
  }
}

// Khi trang load
window.addEventListener("DOMContentLoaded", loadHoaDon);
