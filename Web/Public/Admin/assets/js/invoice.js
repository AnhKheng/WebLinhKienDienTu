
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
        <td>${hd.MaNV}${hd.TenNV || ""}</td>
        <td>${hd.MaKH}${hd.TenKH || ""}</td>
        <td>${hd.MaCH}${hd.TenCH || ""}</td>
        <td>${Number(hd.TongTien).toLocaleString("vi-VN")} ₫</td>
        <td>
          <button class="btn-detail" onclick="viewDetail('${hd.MaHD}')">Chi tiết</button>
          <button type="button" class="btn-edit" onclick="openUpdateModal('${hd.MaHD}')">Cập nhật</button>        
          <button type="button" class="btn-delete" onclick="deleteHoaDon('${hd.MaHD}')">Xóa</button>

      </tr>
    `;
    tbody.insertAdjacentHTML("beforeend", row);
  });
}

// 🔹 Sinh danh sách cửa hàng để lọc
function renderCuaHangOptions(hoadons) {
  const select = document.getElementById("cuahangFilter");
  select.innerHTML = '<option value="all">Tất cả cửa hàng</option>';

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

  // ✅ Chuyển định dạng datetime-local -> MySQL
  const rawNgayBan = document.getElementById("txtNgayBan").value;
  const ngayBan = rawNgayBan ? rawNgayBan.replace("T", " ") + ":00" : null;

  const maNV = document.getElementById("txtMaNV").value;
  const maKH = document.getElementById("txtMaKH").value;
  const maCH = document.getElementById("txtMaCH").value;
  const tongTien = parseFloat(document.getElementById("txtTongTien").value);

  const data = {
    MaHD: maHD,
    NgayBan: ngayBan,  // ✅ định dạng MySQL hợp lệ
    MaNV: maNV,
    MaKH: maKH,
    MaCH: maCH,
    TongTien: tongTien
  };

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

// // 👉 Khi DOM tải xong
// document.addEventListener("DOMContentLoaded", async () => {
//   // Chỉ chạy JS này nếu đang ở trang thêm hóa đơn
//   if (window.location.pathname.includes("invoice_add.html")) {
//     await loadCategories();
//     await loadProducts();

//     // Lắng nghe sự kiện tìm kiếm / lọc
//     document.getElementById("searchInput").addEventListener("input", filterProducts);
//     document.getElementById("categorySelect").addEventListener("change", filterProducts);
//   }
// });

// function renderCuaHangOptions(hoadons) {
//   const select = document.getElementById("cuahangFilter");
//   if (!select) return; // 🧩 Không có phần tử này trên trang thì bỏ qua

//   select.innerHTML = '<option value="all">Tất cả cửa hàng</option>';

//   const cuahangs = [...new Set(hoadons.map((hd) => hd.TenCH || hd.MaCH))];
//   cuahangs.forEach((ch) => {
//     const option = document.createElement("option");
//     option.value = ch;
//     option.textContent = ch;
//     select.appendChild(option);
//   });
// }

// // 🟢 Lấy danh mục
// async function loadCategories() {
//   try {
//     const res = await fetch("../../API/admin/category.php?action=view");
//     const data = await res.json();

//     if (data.status === "success") {
//       const select = document.getElementById("categorySelect");
//       select.innerHTML = `<option value="">Tất cả danh mục</option>`;
//       data.data.forEach(dm => {
//         select.innerHTML += `<option value="${dm.MaDM}">${dm.TenDM}</option>`;
//       });
//     } else {
//       console.warn("⚠ Không có danh mục nào!");
//     }
//   } catch (err) {
//     console.error("❌ Lỗi loadCategories:", err);
//   }
// }

// // 🟢 Lấy sản phẩm
// let allProducts = [];

// async function loadProducts() {
//   try {
//     const res = await fetch("../../API/admin/product.php?action=getAll");
//     const data = await res.json();

//     if (data.status === "success") {
//       allProducts = data.data;
//       renderProducts(allProducts);
//     } else {
//       console.warn("⚠ Không có sản phẩm nào!");
//     }
//   } catch (err) {
//     console.error("❌ Lỗi loadProducts:", err);
//   }
// }

// // 🟢 Hiển thị sản phẩm
// function renderProducts(list) {
//   const tbody = document.getElementById("productTableBody");
//   if (!tbody) return;

//   tbody.innerHTML = "";
//   list.forEach(sp => {
//     tbody.innerHTML += `
//       <tr>
//         <td>${sp.MaSP}</td>
//         <td>${sp.TenSP}</td>
//         <td>${parseFloat(sp.DonGia).toLocaleString()}đ</td>
//         <td>${sp.MaDM}</td>
//         <td><button class="btn-add-item" onclick="addToInvoice('${sp.MaSP}', '${sp.TenSP}', ${sp.DonGia})">Thêm</button></td>
//       </tr>`;
//   });
// }

// // 🟢 Lọc theo danh mục / từ khóa
// function filterProducts() {
//   const keyword = document.getElementById("searchInput").value.toLowerCase();
//   const maDM = document.getElementById("categorySelect").value;

//   const filtered = allProducts.filter(sp => {
//     const matchName = sp.TenSP.toLowerCase().includes(keyword);
//     const matchCategory = maDM === "" || sp.MaDM === maDM;
//     return matchName && matchCategory;
//   });

//   renderProducts(filtered);
// }

// // 🟢 Thêm sản phẩm vào danh sách hóa đơn
// function addToInvoice(maSP, tenSP, donGia) {
//   const tbody = document.getElementById("invoiceItems");
//   if (!tbody) return;

//   const tr = document.createElement("tr");
//   tr.innerHTML = `
//     <td>${maSP}</td>
//     <td>${tenSP}</td>
//     <td><input type="number" min="1" value="1" onchange="updateTotal()" /></td>
//     <td>${donGia}</td>
//     <td class="thanhTien">${donGia}</td>
//     <td><button class="btn-remove" onclick="this.closest('tr').remove(); updateTotal()">Xóa</button></td>
//   `;
//   tbody.appendChild(tr);
//   updateTotal();
// }

// // 🟢 Cập nhật tổng tiền
// function updateTotal() {
//   let total = 0;
//   document.querySelectorAll("#invoiceItems tr").forEach(row => {
//     const qty = parseInt(row.querySelector("input").value) || 0;
//     const price = parseFloat(row.children[3].textContent) || 0;
//     const thanhTien = qty * price;
//     row.querySelector(".thanhTien").textContent = thanhTien.toLocaleString();
//     total += thanhTien;
//   });

//   const totalField = document.getElementById("tongTien");
//   if (totalField) totalField.value = total;
// }

// function filterByCategory() {
//   const category = document.getElementById("categoryFilter").value;
//   const rows = document.querySelectorAll("#productList tr");

//   rows.forEach(row => {
//     const categoryCell = row.querySelector("td:nth-child(3)"); // Cột "Danh mục"
//     if (!category || categoryCell.textContent === category) {
//       row.style.display = "";
//     } else {
//       row.style.display = "none";
//     }
//   });
// }
// window.addEventListener("DOMContentLoaded", () => {
//   const path = window.location.pathname;

//   if (path.includes("invoice_add.html")) {
//     // Trang thêm hóa đơn
//     loadCategories();
//     loadProducts();
//   } else if (path.includes("invoice.html")) {
//     // Trang danh sách hóa đơn
//     loadHoaDon();
//   }
// });


// Khi trang load
window.addEventListener("DOMContentLoaded", loadHoaDon);
