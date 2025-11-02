let allProducts = [];
let filteredProducts = [];

// 🧩 Tải dữ liệu
async function loadProducts() {
  try {
    const response = await fetch("../../API/admin/product_api.php?action=getAll");
    const result = await response.json();

    if (result.status === "success") {
      allProducts = result.data.map(p => ({
        ...p,
        DonGia: Number(p.DonGia),
        TrangThai: p.TrangThai === "Hoạt động" ? 1 : 0
      }));

      filteredProducts = [...allProducts];
      renderCategoryOptions(allProducts);
      renderTable(filteredProducts);
    } else {
      showNotify(result.message || "Không thể tải danh sách sản phẩm");
    }
  } catch (error) {
    console.error("Lỗi khi tải sản phẩm:", error);
    showNotify("Không thể kết nối đến API.");
  }
}

// 🧩 Hiển thị bảng
function renderTable(products) {
  const tbody = document.querySelector("#productTable tbody");
  tbody.innerHTML = "";

  if (!products.length) {
    tbody.innerHTML = `<tr><td colspan="8" style="text-align:center;">Không có sản phẩm phù hợp.</td></tr>`;
    return;
  }

  products.forEach(p => {
    const isActive = p.TrangThai == 1;
    const statusText = isActive ? "Còn hàng" : "Hết hàng";
    const statusClass = isActive ? "status-active" : "status-inactive";
    const imgPath = p.HinhAnh ? `../img/${p.HinhAnh}` : "../img/no-image.png";

    const row = `
      <tr>
        <td>${p.MaSP}</td>
        <td>${p.TenSP}</td>
        <td>${p.TenDM || p.MaDM}</td>
        <td>${p.DonGia.toLocaleString("vi-VN")} ₫</td>
        <td>${p.MoTa || ""}</td>
        <td><span class="${statusClass}">${statusText}</span></td>
        <td><img class="img-thumbnail rounded-circle" src="${imgPath}" alt="${p.TenSP}" width="60" height="60"></td>
        <td>
          <button class="btn-edit" onclick="editProduct('${p.MaSP}')">Sửa</button>
          <button class="btn-delete" onclick="deleteProduct('${p.MaSP}')">Xóa</button>
        </td>
      </tr>
    `;
    tbody.insertAdjacentHTML("beforeend", row);
  });
}

// 🧩 Sinh danh mục
function renderCategoryOptions(products) {
  const select = document.getElementById("categoryFilter");
  select.innerHTML = '<option value="all">Tất cả</option>';
  const categories = [...new Set(products.map(p => p.TenDM || p.MaDM))];
  categories.forEach(cat => {
    const option = document.createElement("option");
    option.value = cat;
    option.textContent = cat;
    select.appendChild(option);
  });
}

// 🧩 Khi người dùng chọn loại khoảng giá
function onPriceRangeChange() {
  const value = document.getElementById("priceRange").value;
  const customInputs = document.getElementById("customPriceInputs");
  if (value === "custom") {
    customInputs.style.display = "inline";
  } else {
    customInputs.style.display = "none";
    applyFilter(); // lọc ngay khi chọn khoảng giá có sẵn
  }
}

// 🧩 Lọc theo danh mục + khoảng giá
function applyFilter() {
  const selectedCat = document.getElementById("categoryFilter").value;
  const priceRange = document.getElementById("priceRange").value;

  let min = 0, max = Infinity;
  switch (priceRange) {
    case "1": min = 0; max = 50000; break;
    case "2": min = 50000; max = 100000; break;
    case "3": min = 100000; max = 200000; break;
    case "4": min = 200000; max = 500000; break;
    case "5": min = 500000; max = 1000000; break;
    case "6": min = 1000000; max = 2000000; break;
    case "7": min = 2000000; max = Infinity; break;
  }

  filteredProducts = allProducts.filter(p => {
    const matchCat = selectedCat === "all" || (p.TenDM || p.MaDM) === selectedCat;
    const matchPrice = p.DonGia >= min && p.DonGia <= max;
    return matchCat && matchPrice;
  });

  renderTable(filteredProducts);
}

// 🧩 Lọc tùy chỉnh (từ input)
function filterByPrice() {
  const min = parseFloat(document.getElementById("minPrice").value) || 0;
  const max = parseFloat(document.getElementById("maxPrice").value) || Infinity;
  const selectedCat = document.getElementById("categoryFilter").value;

  filteredProducts = allProducts.filter(p => {
    const matchCat = selectedCat === "all" || (p.TenDM || p.MaDM) === selectedCat;
    const matchPrice = p.DonGia >= min && p.DonGia <= max;
    return matchCat && matchPrice;
  });

  renderTable(filteredProducts);
}

// 🧩 Sắp xếp theo giá (trên danh sách đang lọc)
function sortByPrice(order) {
  const sorted = [...filteredProducts].sort((a, b) =>
    order === "asc" ? a.DonGia - b.DonGia : b.DonGia - a.DonGia
  );
  renderTable(sorted);
}

// 🧩 Thông báo popup
function showNotify(message) {
  const notifyOverlay = document.getElementById("notifyOverlay");
  const notifyMessage = document.getElementById("notifyMessage");
  if (!notifyOverlay || !notifyMessage) return;
  notifyMessage.textContent = message;
  notifyOverlay.style.display = "flex";
}

// 🧩 Load khi khởi động
document.addEventListener("DOMContentLoaded", () => {
  loadProducts();
});
