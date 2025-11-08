
let allHoaDonNhapNhap = []; // Lưu toàn bộ dữ liệu hóa đơn để lọc

// Tải danh sách hóa đơn từ API
async function loadImport() {
  try {
    console.log("🏁 DOMContentLoaded triggered");
    const tbody = document.querySelector("#hoadonTable tbody");
    console.log("tbody:", tbody);
    const response = await fetch("../../API/admin/import_api.php");

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
      allHoaDonNhap = result.data;
      renderCuaHangOptions(allHoaDonNhap);
      renderTable(allHoaDonNhap);
    } else {
      alert(result.message || "Không thể tải danh sách hóa đơn nhập");
    }

  } catch (error) {
    console.error("Lỗi khi tải hóa đơn:", error);
    alert("Không thể kết nối đến API. Chi tiết: " + error.message);
  }
}

// Hiển thị dữ liệu ra bảng
function renderTable(hoadonnhap) {
  const tbody = document.querySelector("#hoadonTable tbody");
  tbody.innerHTML = "";

  hoadonnhap.forEach((hd) => {
    const date = hd.NgayNhap
      ? new Date(hd.NgayNhap).toLocaleString("vi-VN")
      : "—";

    const row = `
     
      <tr data-maPN="${hd.MaPN}">
        <td>${hd.MaPN}</td>
        <td>${date}</td>
        <td>${hd.MaNCC}${hd.TenNCC || ""}</td>
        <td>${hd.MaNV}${hd.TenNV || ""}</td>
        
        <td>${hd.MaCH}${hd.TenCH || ""}</td>
        <td>${Number(hd.TongTien).toLocaleString("vi-VN")} ₫</td>
        <td>
          <button type="button" class="btn-detail" onclick="viewDetail('${hd.MaPN}')">Chi tiết</button>    
          <button type="button" class="btn-delete" onclick="deleteHoaDon('${hd.MaPN}')">Xóa</button>
          <button type="button" class="btn-detail" onclick="exportInvoiceExcel('${hd.MaPN}')">In HĐ</button>
      </tr>
    `;
    tbody.insertAdjacentHTML("beforeend", row);
  });
}

// 🔹 Sinh danh sách cửa hàng để lọc
function renderCuaHangOptions(hoadons) {
  const select = document.getElementById("cuahangFilter");
  select.innerHTML = '<option value="all">Tất cả</option>';

  // ✅ Lấy danh sách mã cửa hàng (hoặc tên nếu có)
  const cuahangs = [...new Set(hoadons.map((hd) => hd.TenCH || hd.MaCH))];

  cuahangs.forEach((ch) => {
    const option = document.createElement("option");
    option.value = ch;
    option.textContent = ch;
    select.appendChild(option);
  });
}

// 🔹 Lọc hóa đơn theo cửa hàng
function applyFilter() {
  const selected = document.getElementById("cuahangFilter").value;

  if (selected === "all") {
    renderTable(allHoaDonNhap);
  } else {
    const filtered = allHoaDonNhap.filter(
      (hd) => (hd.TenCH || hd.MaCH) === selected
    );
    renderTable(filtered);
  }
}


//  ----------------------Xem chi tiết hóa đơn --------------
async function viewDetail(maPN) {
  try {
    
    const response = await fetch(`../../API/admin/import_api.php?action=viewDetail&maPN=${maPN}`);
    const result = await response.json();

    const modal = document.getElementById("detailModal");
    const content = document.getElementById("detailContent");

    if (result.status === "success" && result.data.length > 0) {
      let html = "";
      result.data.forEach((item, i) => {
        html += `
          <div class="detail-item">
            <p><strong>#${i + 1}</strong></p>
            <p><strong>Mã SP:</strong> ${item.MaSP}</p>
            <p><strong>Tên SP:</strong> ${item.TenSP || "—"}</p>
            <p><strong>Số lượng:</strong> ${item.SoLuong}</p>
            <p><strong>Đơn giá:</strong> ${Number(item.DonGia).toLocaleString("vi-VN")} ₫</p>
            <p><strong>Thành tiền:</strong> ${Number(item.ThanhTien).toLocaleString("vi-VN")} ₫</p>
          </div>
        `;
      });
      content.innerHTML = html;
    } else {
      content.innerHTML = `<p style="text-align:center;">Không có chi tiết cho hóa đơn này.</p>`;
    }

    // ✅ Hiển thị popup
    modal.style.display = "flex";

  } catch (err) {
    alert("Lỗi khi tải chi tiết hóa đơn: " + err.message);
  }
}


function closeModal() {
  document.getElementById("detailModal").style.display = "none";
}


// ---------------------------- Xóa hóa đơn---------------
async function deleteHoaDon(maPN) {
  if (!confirm(`Bạn có chắc muốn xóa hóa đơn ${maPN}?`)) return;

  try {
    const response = await fetch(`../../API/admin/import_api.php?action=delete&maPN=${maPN}`);
    const result = await response.json();

    if (result.status === "success") {
      alert(result.message);
      // Xóa dòng khỏi bảng HTML mà không cần load lại trang
      const row = document.querySelector(`tr[data-maPN='${maPN}']`);
      if (row) row.remove();
    } else {
      alert(result.message || "Không thể xóa hóa đơn.");
    }
  } catch (err) {
    alert("Lỗi khi xóa hóa đơn: " + err.message);
  }
}

//---------------------------add---------------------------

function openAddModal() {
  // Chuyển trang bằng đường dẫn tương đối
  window.location.href = 'index.php?module=import&page=Add';
}

// 🟢 Xuất 1 hóa đơn ra file Excel
async function exportInvoiceExcel(maPN) {
  try {
    const res = await fetch(`../../API/admin/excel_pn.php?MaPN=${maPN}`);
    
    if (!res.ok) throw new Error("Không thể tạo file Excel.");

    // Nhận dữ liệu dạng blob (file)
    const blob = await res.blob();
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = `HoaDon_${maPN}.xlsx`;
    document.body.appendChild(a);
    a.click();
    a.remove();
  } catch (err) {
    alert("Lỗi khi xuất hóa đơn: " + err.message);
  }
}


// Khi trang load
window.addEventListener("DOMContentLoaded", loadImport);
