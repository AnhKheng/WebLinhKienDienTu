

const API_CATEGORY = "../../API/admin/category_api.php?action=view";
const API_PRODUCT = "../../API/admin/product_api.php?action=getAll";
const API_INVOICE = "../../API/admin/invoice_api.php?action=add";

let productList = [];
let allProducts = [];
let storeProducts = [];
let invoiceDetails = [];
let selectedStore = "";



document.addEventListener("DOMContentLoaded", () => {
  // 1. Mặc định ngày hôm nay
  const today = new Date().toISOString().split("T")[0];
  document.getElementById("ngayBan").value = today;

  // 2. Load danh sách khách hàng + cửa hàng
  loadCustomers();
  loadStores();
  loadNewInvoiceCode();   // 🔹 Lấy mã hóa đơn tự động
  loadCurrentEmployee();  // 🔹 Lấy mã nhân viên tự động
});

async function searchProduct() {
  const keyword = document.getElementById("searchBox").value.trim();
  const MaCH = selectedStore || ""; // nếu chưa chọn cửa hàng thì tìm tất cả

  const url = `../../API/admin/product_api.php?action=search&keyword=${encodeURIComponent(keyword)}&MaCH=${MaCH}`;

  try {
    const res = await fetch(url);
    const data = await res.json();

    if (data.status === "success" && Array.isArray(data.data)) {
      renderProducts(data.data);
    } else {
      renderProducts([]);
    }
  } catch (err) {
    console.error("❌ Lỗi khi tìm kiếm sản phẩm:", err);
  }
}



// 🔹 Lấy mã hóa đơn tự động
async function loadNewInvoiceCode() {
  try {
    const res = await fetch("../../API/admin/invoice_api.php?action=getNewCode");
    const data = await res.json();
    if (data.status === "success") {
      document.getElementById("maHD").value = data.newCode;
      document.getElementById("maHD").readOnly = true;
    } else {
      console.error("Không lấy được mã hóa đơn mới");
    }
  } catch (err) {
    console.error("Lỗi loadNewInvoiceCode:", err);
  }
}

// 🔹 Lấy mã nhân viên tự động
async function loadCurrentEmployee() {
  try {
    const res = await fetch("../../API/admin/invoice_api.php?action=getCurrentNV");
    const data = await res.json();
    if (data.status === "success") {
      document.getElementById("maNV").value = data.MaNV;
      document.getElementById("maNV").readOnly = true;
    } else {
      console.warn("Chưa đăng nhập hoặc chưa có mã NV trong session");
    }
  } catch (err) {
    console.error("Lỗi loadCurrentEmployee:", err);
  }
}


// ======== LOAD KHÁCH HÀNG ========

async function loadCustomers() {
  try {
    const res = await fetch("../../API/admin/customer_api.php?action=getAll");
    const data = await res.json();

    if (data.status === "success" && Array.isArray(data.data)) {
      const select = document.getElementById("maKH");
      select.innerHTML = `<option value="">-- Chọn khách hàng --</option>`;
      data.data.forEach(kh => {
        select.innerHTML += `<option value="${kh.MaKH}">${kh.TenKH} (${kh.MaKH})</option>`;
      });
    } else {
      console.error("Không tải được danh sách khách hàng");
    }
  } catch (error) {
    console.error("Lỗi loadCustomers:", error);
  }
}


// ======== LOAD CỬA HÀNG ========

async function loadStores() {
  try {
    const res = await fetch("../../API/admin/stores_api.php?action=getAll");
    const data = await res.json();

    if (data.status === "success" && Array.isArray(data.data)) {
      const select = document.getElementById("maCH");
      select.innerHTML = `<option value="">-- Chọn cửa hàng --</option>`;
      data.data.forEach(ch => {
        select.innerHTML += `<option value="${ch.MaCH}">${ch.TenCH} (${ch.MaCH})</option>`;
      });
    } else {
      console.error("Không tải được danh sách cửa hàng");
    }
  } catch (error) {
    console.error("Lỗi loadStores:", error);
  }
}

// 1️⃣ Tải danh mục sản phẩm

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

    if (data.status === "success" && Array.isArray(data.data)) {
      productList = data.data;
      allProducts = data.data; // ✅ thêm dòng này để lọc danh mục hoạt động
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
        <td><img src="../img/${sp.HinhAnh || 'no_image.png'}" class="thumb"></td>
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
  const select = document.getElementById("categoryFilter");
  const storeSelect = document.getElementById("maCH");
  if (!select) {
    console.warn("Không tìm thấy #categoryFilter");
    return;
  }

  const selectedCategory = select.value;                      // ví dụ "DM01" hoặc ""
  const selectedStore = storeSelect ? storeSelect.value : ""; // ví dụ "CH01" hoặc ""

  // chọn nguồn để lọc: nếu đã chọn cửa hàng dùng storeProducts (chứa TonKho), ngược lại dùng allProducts (hoặc productList)
  const sourceList = (selectedStore && storeProducts && storeProducts.length>0) ? storeProducts : (allProducts && allProducts.length>0 ? allProducts : productList);

  if (!selectedCategory || selectedCategory === "all") {
    renderProducts(sourceList);
    return;
  }

  // so khớp linh hoạt: so sánh string, đồng thời thử so với TenDM nếu MaDM không khớp
  const filtered = sourceList.filter(sp => {
    const maMatch = String(sp.MaDM).trim() === String(selectedCategory).trim();
    const tenMatch = String(sp.TenDM || "").trim().toLowerCase() === String(select.options[select.selectedIndex].text || "").trim().toLowerCase();
    return maMatch || tenMatch;
  });

  // DEBUG: kết quả lọc
  console.log("filtered.length:", filtered.length);
  console.table(filtered.slice(0,12).map(p=>({MaSP:p.MaSP,MaDM:p.MaDM,TenDM:p.TenDM,TonKho:p.TonKho})));

  renderProducts(filtered);

  console.log("📦 sourceList sample:", sourceList.map(sp => ({
  MaSP: sp.MaSP,
  MaDM: sp.MaDM,
  TenDM: sp.TenDM
})));
console.log("🎯 selectedCategory:", selectedCategory);

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

// 10 ======== LỌC SẢN PHẨM THEO CỬA HÀNG ========

async function filterByStore() {
  const maCH = document.getElementById("maCH").value;
  selectedStore = maCH;
  if (!maCH) {
    storeProducts = [];
    renderProducts(allProducts);
    return;
  }

  try {
    const res = await fetch(`../../API/admin/product_api.php?action=getByStore&MaCH=${maCH}`);
    const text = await res.text(); // 👈 đọc thô để debug lỗi PHP
    let data;

    try {
      data = JSON.parse(text);
    } catch (e) {
      console.error("❌ Lỗi JSON parse, server trả về:", text);
      alert("⚠️ Server trả về HTML (có thể PHP bị lỗi). Kiểm tra lại file product_api.php!");
      return;
    }

    if (data.status === "success" && Array.isArray(data.data)) {
      storeProducts = data.data;
      console.log(`✅ Đã tải ${storeProducts.length} sản phẩm của cửa hàng ${maCH}`);
      renderProducts(storeProducts);

      // ✅ Nếu người dùng đã chọn danh mục, lọc lại ngay
      const selectedCategory = document.getElementById("categoryFilter").value;
      if (selectedCategory) filterByCategory();

    } else {
      console.warn("⚠️ API trả về rỗng hoặc sai định dạng", data);
      storeProducts = [];
      renderProducts([]);
    }

  } catch (err) {
    console.error("❌ Lỗi khi gọi filterByStore:", err);
  }
}



//Gọi khi trang load

window.onload = function () {
  loadCategories();
  loadProducts();
};
