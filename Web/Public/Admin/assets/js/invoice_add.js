

const API_CATEGORY = "../../API/admin/category_api.php?action=view";
const API_PRODUCT = "../../API/admin/product_api.php?action=getAll";
const API_INVOICE = "../../API/admin/invoice_api.php?action=add";

let productList = [];
let invoiceDetails = [];

// ===============================
// 1️⃣ Tải danh mục sản phẩm
// ===============================
async function loadCategories() {
  try {
    const res = await fetch(API_CATEGORY);
    const data = await res.json();

    if (data.status === "success" && Array.isArray(data.data)) {
      const select = document.getElementById("categoryFilter");
      select.innerHTML = `<option value="">Tất cả</option>`;
      data.data.forEach(dm => {
        select.innerHTML += `<option value="${dm.MaDM}">${dm.TenDM}</option>`;
      });
    } else {
      console.error("Không tải được danh mục");
    }
  } catch (error) {
    console.error("Lỗi loadCategories:", error);
  }
}

// ===============================
// 2️⃣ Tải danh sách sản phẩm
// ===============================
async function loadProducts() {
  try {
    const res = await fetch(API_PRODUCT);
    const data = await res.json();

    // Kiểm tra đúng định dạng mà API trả về
    if (data.status === "success" && Array.isArray(data.data)) {
      productList = data.data;
      renderProducts(productList);
    } else {
      console.error("Không có dữ liệu sản phẩm.");
    }
  } catch (error) {
    console.error("Lỗi loadProducts:", error);
  }
}


// ===============================
// 3️⃣ Hiển thị danh sách sản phẩm
// ===============================
function renderProducts(list) {
  const tbody = document.getElementById("productList");
  tbody.innerHTML = "";

  list.forEach(sp => {
    tbody.innerHTML += `
      <tr>
        <td><img src="../../uploads/${sp.HinhAnh || 'no_image.png'}" class="thumb"></td>
        <td>${sp.TenSP}</td>
        <td>${sp.TenDM}</td>
        <td>${sp.DonGia.toLocaleString()}₫</td>
        <td>${sp.TonKho ?? '—'}</td>
        <td><input type="number" id="qty_${sp.MaSP}" min="1" value="1" class="qty-input qty-col"></td>
        <td><button class="btn-add-row" onclick="addToInvoice('${sp.MaSP}')">+</button></td>
      </tr>
    `;
  });
}


// 4 Lọc sản phẩm theo danh mục

function filterByCategory() {
  const selected = document.getElementById("categoryFilter").value;
  if (!selected) return renderProducts(productList);
  const filtered = productList.filter(sp => sp.MaDM === selected);
  renderProducts(filtered);
}


// 5 Thêm sản phẩm vào chi tiết hóa đơn

function addToInvoice(maSP) {
  const product = productList.find(p => p.MaSP === maSP);
  const qty = parseInt(document.getElementById(`qty_${maSP}`).value);

  if (!product || qty <= 0) return;

  const existing = invoiceDetails.find(item => item.MaSP === maSP);
  if (existing) {
    existing.SoLuong += qty;
  } else {
    invoiceDetails.push({
      MaSP: maSP,
      TenSP: product.TenSP,
      DonGia: product.DonGia,
      SoLuong: qty,
    });
  }

  renderInvoiceDetail();
}


// 6 Hiển thị bảng chi tiết hóa đơn

function renderInvoiceDetail() {
  const tbody = document.querySelector("#invoiceDetail tbody");
  tbody.innerHTML = "";

  invoiceDetails.forEach((item, index) => {
    const thanhTien = item.SoLuong * item.DonGia;
    tbody.innerHTML += `
      <tr>
        <td>${item.TenSP}</td>
        <td>${item.SoLuong}</td>
        <td>${item.DonGia.toLocaleString()}₫</td>
        <td>${thanhTien.toLocaleString()}₫</td>
        <td><button class="btn-delete" onclick="removeItem(${index})">✖</button></td>
      </tr>
    `;
  });

  updateTotal();
}

// 7️ Xóa sản phẩm khỏi chi tiết

function removeItem(index) {
  invoiceDetails.splice(index, 1);
  renderInvoiceDetail();
}

// 8 cập nhật
function updateTotal() {
  const total = invoiceDetails.reduce(
    (sum, item) => sum + item.SoLuong * item.DonGia,
    0
  );
  document.getElementById("tongTien").value = total;
}

// 9 luu hóa đơn
async function saveInvoice() {
  const maHD = document.getElementById("maHD").value.trim();
  const maNV = document.getElementById("maNV").value.trim();
  const maKH = document.getElementById("maKH").value.trim();
  const maCH = document.getElementById("maCH").value.trim();
  const ngayBan = document.getElementById("ngayBan").value;
  const tongTien = parseFloat(document.getElementById("tongTien").value);

  if (!maHD || !maNV || !maKH || !maCH || !ngayBan || invoiceDetails.length === 0) {
    alert("⚠️ Vui lòng nhập đầy đủ thông tin và chọn sản phẩm.");
    return;
  }

  const data = {
    MaHD: maHD,
    MaNV: maNV,
    MaKH: maKH,
    MaCH: maCH,
    NgayBan: ngayBan,
    TongTien: tongTien,
    ChiTiet: invoiceDetails,
  };

  try {
    const res = await fetch(API_INVOICE, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(data),
    });

    const result = await res.json();
    if (result.status === "success") {
      alert("✅ Thêm hóa đơn thành công!");
      window.location.href = "index.php?module=invoice&page=invoice";
    } else {
      
      alert("❌ Lỗi: " + result.message);
    }
  } catch (error) {
    console.error("Lỗi khi lưu hóa đơn:", error);
  }
}


//Gọi khi trang load

window.onload = function () {
  loadCategories();
  loadProducts();
};
