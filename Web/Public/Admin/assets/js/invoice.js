
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
      renderCuaHangOptions(allHoaDon);
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
        <td>${hd.MaNV}</td>
        <td>${hd.MaKH}</td>
        <td>${hd.MaCH}</td>
        <td>${Number(hd.TongTien).toLocaleString("vi-VN")} ₫</td>
        <td>${hd.MaNV == null ? "Đang xử lý" : "Đã xử lý"}</td>
        <td>
          <button type="button" class="btn-detail" onclick="viewDetail('${hd.MaHD}')">Chi tiết</button>
          <button type="button" class="btn-edit" onclick="openUpdateModal('${hd.MaHD}')">Cập nhật</button>        
          <button type="button" class="btn-delete" onclick="deleteHoaDon('${hd.MaHD}')">Xóa</button>
          <button type="button" class="btn-detail" onclick="exportInvoiceExcel('${hd.MaHD}')">In HĐ</button>
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
    renderTable(allHoaDon);
  } else {
    const filtered = allHoaDon.filter(
      (hd) => (hd.TenCH || hd.MaCH) === selected
    );
    renderTable(filtered);
  }
}


//  ----------------------Xem chi tiết hóa đơn --------------
async function viewDetail(maHD) {
  try {
    const response = await fetch(`../../API/admin/invoice_api.php?action=viewDetail&MaHD=${maHD}`);
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

//-----------------------------------update------------------------
// 🟢 Mở popup cập nhật và điền thông tin
function openUpdateModal(maHD) {
  const hd = allHoaDon.find(item => item.MaHD === maHD);
  if (!hd) return alert("Không tìm thấy hóa đơn!");
  if (hd.NgayBan) {
    const d = new Date(hd.NgayBan);
    const local = d.toISOString().slice(0, 16); // YYYY-MM-DDTHH:mm
    document.getElementById("txtNgayBan").value = local;
  } else {
    document.getElementById("txtNgayBan").value = "";
  }

  document.getElementById("txtMaHD").value = hd.MaHD;
  document.getElementById("txtNgayBan").value = hd.NgayBan ? hd.NgayBan.replace(" ", "T") : "";
  document.getElementById("txtMaNV").value = hd.MaNV;
  document.getElementById("txtMaKH").value = hd.MaKH;
  document.getElementById("txtMaCH").value = hd.MaCH;
  document.getElementById("txtTongTien").value = hd.TongTien;

  document.getElementById("updateModal").style.display = "flex";
}

// 🟢 Đóng popup
function closeUpdateModal() {
  document.getElementById("updateModal").style.display = "none";
}

// 🟢 Lưu cập nhật
async function saveUpdate() {
  const maHD = document.getElementById("txtMaHD").value;
  const rawNgayBan = document.getElementById("txtNgayBan").value;
  const ngayBan = rawNgayBan ? rawNgayBan.replace("T", " ") + ":00" : null;
  const maNV = document.getElementById("txtMaNV").value;
  const maKH = document.getElementById("txtMaKH").value;
  let maCH = document.getElementById("txtMaCH").value; // 👈 dùng let
  const tongTien = parseFloat(document.getElementById("txtTongTien").value);

  // ✅ Nếu MaCH rỗng, lấy lại từ dữ liệu cũ
  if (!maCH) {
    const oldInvoice = allHoaDon.find(item => item.MaHD === maHD);
    if (oldInvoice) maCH = oldInvoice.MaCH;
  }

  const data = { MaHD: maHD, NgayBan: ngayBan, MaNV: maNV, MaKH: maKH, MaCH: maCH, TongTien: tongTien };

  try {
    const res = await fetch("../../API/admin/invoice_api.php?action=update", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(data)
    });
    const result = await res.json();

    if (result.status === "success") {
      alert("✅ Cập nhật hóa đơn thành công!");
      closeUpdateModal();
      loadHoaDon();
    } else {
      alert("❌ Cập nhật thất bại: " + result.message);
    }
  } catch (err) {
    alert("⚠️ Lỗi khi cập nhật: " + err.message);
  }
}

//---------------------------add---------------------------


function openAddModal() {
  // Chuyển trang bằng đường dẫn tương đối
  window.location.href = 'index.php?module=invoice&page=invoice_add';
}

// 🟢 Xuất 1 hóa đơn ra file Excel
async function exportInvoiceExcel(maHD) {
  try {
    const res = await fetch(`../../API/admin/excel.php?MaHD=${maHD}`);
    
    if (!res.ok) throw new Error("Không thể tạo file Excel.");

    // Nhận dữ liệu dạng blob (file)
    const blob = await res.blob();
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = `HoaDon_${maHD}.xlsx`;
    document.body.appendChild(a);
    a.click();
    a.remove();
  } catch (err) {
    alert("Lỗi khi xuất hóa đơn: " + err.message);
  }
}


// Khi trang load
window.addEventListener("DOMContentLoaded", loadHoaDon);
