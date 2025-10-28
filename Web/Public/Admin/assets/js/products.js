let allProducts = []; // Lưu toàn bộ dữ liệu để lọc

// 🧩 Tải danh sách sản phẩm từ API
async function loadProducts() {
  try {
    const response = await fetch("../../API/admin/Products/View.php");
    const result = await response.json();

    if (result.status === "success") {
      allProducts = result.data;
      renderCategoryOptions(allProducts);
      renderTable(allProducts);
    } else {
      alert(result.message || "Không thể tải danh sách sản phẩm");
    }
  } catch (error) {
    console.error("Lỗi khi tải sản phẩm:", error);
    alert("Không thể kết nối đến API.");
  }
}

// 🧩 Hiển thị dữ liệu ra bảng
function renderTable(products) {
  const tbody = document.querySelector("#productTable tbody");
  tbody.innerHTML = "";

  products.forEach((p) => {
    const isActive = p.TrangThai == 1;
    const statusText = isActive ? "Còn hàng" : "Hết hàng";
    const statusClass = isActive ? "status-active" : "status-inactive";

    const imgPath = p.HinhAnh
      ? `../img/${p.HinhAnh}`
      : "../img/no-image.png";

    const row = `
      <tr>
        <td>${p.MaSP}</td>
        <td>${p.TenSP}</td>
        <td>${p.TenDM || p.MaDM}</td>
        <td>${Number(p.DonGia).toLocaleString("vi-VN")} ₫</td>
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

// 🧩 Sinh danh sách danh mục duy nhất
function renderCategoryOptions(products) {
  const select = document.getElementById("categoryFilter");
  select.innerHTML = '<option value="all">Tất cả</option>'; // reset trước

  const categories = [...new Set(products.map((p) => p.TenDM || p.MaDM))];

  categories.forEach((cat) => {
    const option = document.createElement("option");
    option.value = cat;
    option.textContent = cat;
    select.appendChild(option);
  });
}

// 🧩 Lọc sản phẩm theo danh mục
function applyFilter() {
  const selected = document.getElementById("categoryFilter").value;

  if (selected === "all") {
    renderTable(allProducts);
  } else {
    const filtered = allProducts.filter(
      (p) => (p.TenDM || p.MaDM) === selected
    );
    renderTable(filtered);
  }
}

// 🧩 Sửa sản phẩm (demo)
function editProduct(id) {
  alert(`Chỉnh sửa sản phẩm: ${id}`);
  // 👉 Sau này bạn có thể mở form hoặc redirect đến trang Edit
}

// 🧩 Xóa sản phẩm (demo)
function deleteProduct(id) {
  if (confirm(`Bạn có chắc muốn xóa sản phẩm ${id}?`)) {
    alert(`Đã xóa sản phẩm: ${id}`);
    // 👉 Sau này bạn có thể gọi API Delete tại đây
  }
}

// Khi trang load
window.addEventListener("DOMContentLoaded", loadProducts);
