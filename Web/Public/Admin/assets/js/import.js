const API_CATEGORY = "../../API/admin/category_api.php?action=view";
let productList = [];  
let allProducts = []; 
let importDetails = [];

//------------- Tải danh sách sản phẩm

async function loadProducts() {
  try {

    const link = `../../API/admin/product_api.php?action=getAll`;
    const res = await fetch(link);
    const data = await res.json();

    if (data.status === "success" && Array.isArray(data.data)) {
      productList = data.data;
      allProducts = data.data;
      renderProducts(productList);
    } else {
      console.error("Không có dữ liệu sản phẩm.");
    }
  } catch (error) {
    console.error("Lỗi loadProducts:", error);
  }
}
//----------------- Hiển thị danh sách sản phẩm

function renderProducts(list) {
  const tbody = document.getElementById("productList");
  tbody.innerHTML = "";

  list.forEach(sp => {
    tbody.innerHTML += `
      <tr>
        <td><img src="../img/${sp.HinhAnh || 'no_image.png'}" class="thumb"></td>
        <td>${sp.MaSP}</td>
        <td>${sp.TenSP}</td>
        <td>${sp.TenDM}</td>
        <td>${(sp.DonGia*0.8).toLocaleString()}₫</td>
        
        <td><input type="number" id="qty_${sp.MaSP}" min="1" value="1" class="qty-input qty-col"></td>
        <td><button class="btn-add-row" onclick="addToImport('${sp.MaSP}')">+</button></td>
      </tr>
    `;
  });
}
//------------ hiện select danh mục
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

// ------------ Lọc sản phẩm theo danh mục

function filterByCategory() {
  const select = document.getElementById("categoryFilter");
  const storeSelect = document.getElementById("maCH").value;
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

// ------------tìm sp--------------------
async function searchProduct() {
  const keyword = document.getElementById("searchBox").value.trim(); 
  const url = `../../API/admin/product_api.php?action=search&keyword=${encodeURIComponent(keyword)}`;

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
//---------------nhấn vào nút thêm +   --------
function addToImport(maSP) {
  const product = productList.find(p => p.MaSP === maSP);
  const qty = parseInt(document.getElementById(`qty_${maSP}`).value);

  if (!product || qty <= 0) return;

  // Nếu đã có sản phẩm trong phiếu nhập → cộng dồn số lượng
  const existing = importDetails.find(item => item.MaSP === maSP);
  if (existing) {
    existing.SoLuong += qty;
  } else {
    importDetails.push({
      MaSP: product.MaSP,
      TenSP: product.TenSP,
      SoLuong: qty,
      DonGiaNhap: product.DonGia * 0.8
    });
  }

  renderImportDetail();
}

// ----Hiển thị bảng chi tiết phiếu nhập-------

function renderImportDetail() {
  const tbody = document.querySelector("#importDetail tbody");
  tbody.innerHTML = "";

  importDetails.forEach((item, index) => {
    const thanhTien = item.SoLuong * item.DonGiaNhap;
    tbody.innerHTML += `
      <tr>
        <td>${item.MaSP}</td>
        <td>${item.SoLuong}</td>
        <td>${item.DonGiaNhap.toLocaleString()}₫</td>
        <td>${thanhTien.toLocaleString()}₫</td>
        <td><button class="btn-delete" onclick="removeItem(${index})">✖</button></td>
      </tr>
    `;
  });

  updateTotal();
}
// xóa khỏi chi tiết phiếu nhập
function removeItem(index) {
  importDetails.splice(index, 1);
  renderImportDetail();
}


// Cập nhật tổng tiền
function updateTotal() {
  const total = importDetails.reduce(
    (sum, item) => sum + item.SoLuong * item.DonGiaNhap,
    0
  );
  document.getElementById("tongTienNhap").value = total;
}

//---------load trang---------
document.addEventListener("DOMContentLoaded", async () => {
  try {
    await loadProducts();
    await loadCategories();

  } catch (err) {
    console.error("❌ Lỗi khi khởi tạo trang:", err);
  }
});