let allProducts = [];
let filteredProducts = [];

let currentPage = 1;
const rowsPerPage = 10;

// Hiển thị thông báo popup
function showNotify(message) {
  const notifyOverlay = document.getElementById("notifyOverlay");
  const notifyMessage = document.getElementById("notifyMessage");
  if (!notifyOverlay || !notifyMessage) return;
  notifyMessage.textContent = message;
  notifyOverlay.style.display = "flex";
}

// Ẩn popup thông báo
function hideNotify() {
  const notifyOverlay = document.getElementById("notifyOverlay");
  if (notifyOverlay) notifyOverlay.style.display = "none";
}

// ======== 🔹 HÀM DÙNG CHUNG MỞ/ĐÓNG POPUP (MODAL) =========
function openModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) {
    modal.style.display = "flex";
    modal.setAttribute("aria-hidden", "false");
  }
}

function closeModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) {
    modal.style.display = "none";
    modal.setAttribute("aria-hidden", "true");
  }
}

// Đóng popup khi click ra ngoài
window.addEventListener("click", (e) => {
  document.querySelectorAll(".modal-overlay").forEach((modal) => {
    if (e.target === modal) closeModal(modal.id);
  });
});


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


function renderTable(products) {
  const tbody = document.querySelector("#productTable tbody");
  const pagination = document.getElementById("pagination");
  tbody.innerHTML = "";

  if (!products.length) {
    tbody.innerHTML = `<tr><td colspan="8" style="text-align:center;">Không có sản phẩm phù hợp.</td></tr>`;
    if (pagination) pagination.innerHTML = "";
    return;
  }

  setupPagination(products);

  const start = (currentPage - 1) * rowsPerPage;
  const end = start + rowsPerPage;
  const pageProducts = products.slice(start, end);

  pageProducts.forEach(p => {
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
          <button class="btn-detail" onclick="viewProduct('${p.MaSP}')">Xem</button>
          <button class="btn-edit" onclick="editProduct('${p.MaSP}')">Sửa</button>
          <button class="btn-delete" onclick="deleteProduct('${p.MaSP}')">Xóa</button>
        </td>
      </tr>
    `;
    tbody.insertAdjacentHTML("beforeend", row);
  });
}


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

function onPriceRangeChange() {
  const value = document.getElementById("priceRange").value;
  const customInputs = document.getElementById("customPriceInputs");
  if (value === "custom") {
    customInputs.style.display = "inline";
  } else {
    customInputs.style.display = "none";
    applyFilter();
  }
}

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

  currentPage = 1;
  renderTable(filteredProducts);
}

function filterByPrice() {
  const min = parseFloat(document.getElementById("minPrice").value) || 0;
  const max = parseFloat(document.getElementById("maxPrice").value) || Infinity;
  const selectedCat = document.getElementById("categoryFilter").value;

  filteredProducts = allProducts.filter(p => {
    const matchCat = selectedCat === "all" || (p.TenDM || p.MaDM) === selectedCat;
    const matchPrice = p.DonGia >= min && p.DonGia <= max;
    return matchCat && matchPrice;
  });

  currentPage = 1;
  renderTable(filteredProducts);
}

function sortByPrice(order) {
  filteredProducts.sort((a, b) =>
    order === "asc" ? a.DonGia - b.DonGia : b.DonGia - a.DonGia
  );
  renderTable(filteredProducts);
}


function setupPagination(products) {
  const pagination = document.getElementById("pagination");
  if (!pagination) return;

  const totalPages = Math.ceil(products.length / rowsPerPage);
  pagination.innerHTML = "";

  if (totalPages <= 1) return;

  const maxButtons = 5;
  let startPage = Math.max(1, currentPage - Math.floor(maxButtons / 2));
  let endPage = startPage + maxButtons - 1;

  if (endPage > totalPages) {
    endPage = totalPages;
    startPage = Math.max(1, endPage - maxButtons + 1);
  }

  const makeButton = (text, disabled, handler) => {
    const btn = document.createElement("button");
    btn.textContent = text;
    btn.disabled = disabled;
    btn.addEventListener("click", handler);
    pagination.appendChild(btn);
  };

  makeButton("« Trang đầu", currentPage === 1, () => {
    currentPage = 1;
    renderTable(filteredProducts);
  });

  makeButton("« Trước", currentPage === 1, () => {
    if (currentPage > 1) currentPage--;
    renderTable(filteredProducts);
  });

  for (let i = startPage; i <= endPage; i++) {
    const btn = document.createElement("button");
    btn.textContent = i;
    if (i === currentPage) btn.classList.add("active");
    btn.addEventListener("click", () => {
      currentPage = i;
      renderTable(filteredProducts);
    });
    pagination.appendChild(btn);
  }

  makeButton("Sau »", currentPage === totalPages, () => {
    if (currentPage < totalPages) currentPage++;
    renderTable(filteredProducts);
  });

  makeButton("Trang cuối »", currentPage === totalPages, () => {
    currentPage = totalPages;
    renderTable(filteredProducts);
  });
}

async function viewProduct(maSP) {
  try {
    const response = await fetch(`../../API/admin/product_api.php?action=getOne&MaSP=${maSP}`);
    const result = await response.json();

    if (result.status === "success" && result.data) {
      const p = result.data;

      document.getElementById("detail_idSP").value = p.MaSP || "";
      document.getElementById("detail_nameSP").value = p.TenSP || "";
      document.getElementById("detail_category").value = p.TenDM || p.MaDM || "";
      document.getElementById("detail_price").value = `${Number(p.DonGia).toLocaleString("vi-VN")} ₫` || "";
      document.getElementById("detail_description").value = p.MoTa || "";
      document.getElementById("detail_status").value =
        p.TrangThai == 1 || p.TrangThai === "Hoạt động" ? "Còn hàng" : "Hết hàng";

      const img = document.getElementById("detail_image");
      img.src = p.HinhAnh ? `../img/${p.HinhAnh}` : "../img/no-image.png";
      img.alt = p.TenSP || "Hình sản phẩm";

      openModal("productDetailModal");
    } else {
      showNotify(result.message || "Không thể tải chi tiết sản phẩm.");
    }
  } catch (error) {
    console.error("Lỗi khi tải chi tiết sản phẩm:", error);
    showNotify("Không thể kết nối đến API.");
  }
}

async function editProduct(maSP) {
  try {
    const response = await fetch(`../../API/admin/product_api.php?action=getOne&MaSP=${maSP}`);
    const result = await response.json();

    if (result.status === "success" && result.data) {
      const p = result.data;
      document.getElementById("edit_idSP").value = p.MaSP || "";
      document.getElementById("edit_nameSP").value = p.TenSP || "";
      document.getElementById("edit_price").value = p.DonGia || "";
      document.getElementById("edit_description").value = p.MoTa || "";

      document.getElementById("edit_status").value =
        p.TrangThai == 1 || p.TrangThai === "Hoạt động" ? "1" : "0";

      const categorySelect = document.getElementById("edit_category");
      categorySelect.innerHTML = "";
      const categoryMap = new Map();
      allProducts.forEach(prod => {
        if (prod.MaDM && prod.TenDM) categoryMap.set(prod.MaDM, prod.TenDM);
      });

      for (const [ma, ten] of categoryMap.entries()) {
        const opt = document.createElement("option");
        opt.value = ma;      
        opt.textContent = ten; 
        if (ma === p.MaDM) opt.selected = true;
        categorySelect.appendChild(opt);
      }
      let imgPreview = document.getElementById("edit_preview");
      if (!imgPreview) {
        imgPreview = document.createElement("img");
        imgPreview.id = "edit_preview";
        imgPreview.style.width = "120px";
        imgPreview.style.marginTop = "10px";
        imgPreview.style.borderRadius = "8px";
        document.getElementById("edit_image").insertAdjacentElement("afterend", imgPreview);
      }
      imgPreview.src = p.HinhAnh ? `../img/${p.HinhAnh}` : "../img/no-image.png";
      imgPreview.alt = p.TenSP || "Hình sản phẩm";

      openModal("productEditModal");
    } else {
      showNotify(result.message || "Không thể tải thông tin sản phẩm.");
    }
  } catch (error) {
    console.error("Lỗi khi tải sản phẩm để sửa:", error);
    showNotify("Không thể kết nối đến API.");
  }
}
document.getElementById("edit_image").addEventListener("change", function(e) {
  const file = e.target.files[0];
  const preview = document.getElementById("edit_preview");
  if (file) {
    preview.src = URL.createObjectURL(file);
  }
});

function showConfirm(message = "Bạn có chắc chắn muốn xóa mục này không?") {
  return new Promise((resolve) => {
    const overlay = document.getElementById("confirmOverlay");
    const msg = document.getElementById("confirmMessage");
    const btnYes = document.getElementById("confirmYes");
    const btnNo = document.getElementById("confirmNo");
    const btnClose = document.getElementById("closeConfirm");

    msg.textContent = message;
    overlay.style.display = "flex";

    btnYes.onclick = btnNo.onclick = btnClose.onclick = null;
    btnYes.onclick = () => {
      overlay.style.display = "none";
      resolve(true);
    };
    const cancel = () => {
      overlay.style.display = "none";
      resolve(false);
    };
    btnNo.onclick = btnClose.onclick = cancel;
  });
}

async function deleteProduct(maSP) {
  const isConfirmed = await showConfirm(`Bạn có chắc chắn muốn xóa sản phẩm [${maSP}] này không?`);
  if (!isConfirmed) return;

  try {
    const response = await fetch("../../API/admin/product_api.php?action=delete", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ MaSP: maSP }),
    });

    const result = await response.json();

    if (result.status === "success") {
      showNotify("Đã xóa sản phẩm thành công!");
      loadProducts();
    } else {
      showNotify(result.message || "Không thể xóa sản phẩm.");
    }
  } catch (error) {
    console.error("Lỗi khi xóa sản phẩm:", error);
    showNotify("Không thể kết nối đến API.");
  }
}

const formEdit = document.getElementById("formEditProduct");
if (formEdit) {
  formEdit.addEventListener("submit", async (e) => {
    e.preventDefault();
    const formData = new FormData(formEdit);
    formData.append("action", "update");

    try {
      const response = await fetch("../../API/admin/product_api.php?action=update", {
        method: "POST",
        body: formData
      });
      const result = await response.json();

      if (result.status === "success") {
        showNotify("Cập nhật sản phẩm thành công!");
        closeModal("productEditModal");
        loadProducts();
      } else {
        showNotify(result.message || "Không thể cập nhật sản phẩm.");
      }
    } catch (error) {
      console.error("Lỗi khi cập nhật sản phẩm:", error);
      showNotify("Không thể kết nối đến API.");
    }
  });
}
document.querySelectorAll(".modal-close").forEach(btn => {
  btn.addEventListener("click", () => {
    const modalId = btn.dataset.closeModal; 
    if (modalId) closeModal(modalId);
    else btn.closest(".modal-overlay").style.display = "none";
  });
});

const closeNotify = document.getElementById("closeNotify");
if (closeNotify) {
  closeNotify.addEventListener("click", hideNotify);
}

document.addEventListener("DOMContentLoaded", () => {
  loadProducts();
});
