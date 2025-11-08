// ================== CÁC HÀM CHÍNH ==================

// Hiển thị thông báo popup
function showNotify(message) {
  const notifyOverlay = document.getElementById("notifyOverlay");
  const notifyMessage = document.getElementById("notifyMessage");
  if (!notifyOverlay || !notifyMessage) return;

  notifyMessage.textContent = message ?? "";
  notifyOverlay.style.display = "flex";
}

// Ẩn thông báo popup
function hideNotify() {
  const notifyOverlay = document.getElementById("notifyOverlay");
  if (notifyOverlay) notifyOverlay.style.display = "none";
}

// Render bảng tồn kho
function renderInventoryTable(list) {
  const tbody = document.querySelector("#InventoryTable tbody");
  const pagination = document.getElementById("pagination");
  if (!tbody) return;

  tbody.innerHTML = "";
  if (!list || list.length === 0) {
    tbody.innerHTML = `<tr><td colspan="4" style="text-align:center;">Không có dữ liệu tồn kho</td></tr>`;
    if (pagination) pagination.innerHTML = "";
    return;
  }

  window.allInventory = list; // Lưu toàn cục
  setupPagination(list);

  const start = (window.currentPage - 1) * window.rowsPerPage;
  const end = start + window.rowsPerPage;
  const pageItems = list.slice(start, end);

  pageItems.forEach((item) => {
    tbody.insertAdjacentHTML(
      "beforeend",
      `<tr>
         <td>${item.MaCH}</td>
         <td>${item.MaSP}</td>
         <td>${item.TenSP}</td>
         <td>${item.SoLuongTon}</td>
       </tr>`
    );
  });
}

// Phân trang
function setupPagination(list) {
  const pagination = document.getElementById("pagination");
  if (!pagination) return;

  const totalPages = Math.ceil(list.length / window.rowsPerPage);
  pagination.innerHTML = "";
  if (totalPages <= 1) return;

  const makeButton = (text, disabled, handler) => {
    const btn = document.createElement("button");
    btn.textContent = text;
    btn.disabled = disabled;
    btn.addEventListener("click", handler);
    pagination.appendChild(btn);
  };

  makeButton("« Trang đầu", window.currentPage === 1, () => {
    window.currentPage = 1;
    renderInventoryTable(window.allInventory);
  });

  makeButton("‹ Trước", window.currentPage === 1, () => {
    if (window.currentPage > 1) window.currentPage--;
    renderInventoryTable(window.allInventory);
  });

  const maxButtons = 5;
  let startPage = Math.max(1, window.currentPage - Math.floor(maxButtons / 2));
  let endPage = Math.min(totalPages, startPage + maxButtons - 1);

  if (endPage - startPage < maxButtons - 1)
    startPage = Math.max(1, endPage - maxButtons + 1);

  for (let i = startPage; i <= endPage; i++) {
    const btn = document.createElement("button");
    btn.textContent = i;
    if (i === window.currentPage) btn.classList.add("active");
    btn.addEventListener("click", () => {
      window.currentPage = i;
      renderInventoryTable(window.allInventory);
    });
    pagination.appendChild(btn);
  }

  makeButton("Sau ›", window.currentPage === totalPages, () => {
    if (window.currentPage < totalPages) window.currentPage++;
    renderInventoryTable(window.allInventory);
  });

  makeButton("Trang cuối »", window.currentPage === totalPages, () => {
    window.currentPage = totalPages;
    renderInventoryTable(window.allInventory);
  });
}

// Lấy danh sách cửa hàng
async function loadStoreList() {
  try {
    const filterSelect = document.getElementById("inventoryFilter");
    const res = await fetch("../../API/admin/stores_api.php?action=getAll");
    const data = await res.json();

    if (data.status === "success" && data.data.length > 0) {
      filterSelect.innerHTML = data.data
        .map(
          (ch, i) =>
            `<option value="${ch.MaCH}" ${i === 0 ? "selected" : ""}>${ch.TenCH}</option>`
        )
        .join("");

      const firstStore = data.data[0].MaCH;
      loadInventoryByStore(firstStore);
    } else {
      showNotify("Không lấy được danh sách cửa hàng.");
    }
  } catch (err) {
    console.error(err);
    showNotify("Không thể kết nối tới máy chủ để lấy danh sách cửa hàng.");
  }
}

// Lấy tồn kho theo cửa hàng
async function loadInventoryByStore(MaCH) {
  try {
    const res = await fetch(
      `../../API/admin/inventory_api.php?action=view&MaCH=${MaCH}`
    );
    const data = await res.json();

    if (data.status === "success") {
      renderInventoryTable(data.data);
    } else {
      renderInventoryTable([]);
      showNotify(data.message);
    }
  } catch (err) {
    console.error(err);
    showNotify("Không thể kết nối máy chủ để tải tồn kho.");
  }
}

// Lọc theo cửa hàng
function applyFilter() {
  const filterSelect = document.getElementById("inventoryFilter");
  const MaCH = filterSelect?.value;
  if (!MaCH) return;
  window.currentPage = 1;
  loadInventoryByStore(MaCH);
}

// Tìm kiếm
function applySearch() {
  const searchInput = document.getElementById("inventorySearch");
  const keyword = searchInput.value.trim().toLowerCase();

  if (keyword === "") {
    renderInventoryTable(window.allInventory);
    return;
  }

  const filtered = window.allInventory.filter(
    (item) =>
      item.TenSP.toLowerCase().includes(keyword) ||
      item.MaSP.toLowerCase().includes(keyword)
  );

  window.currentPage = 1;
  renderInventoryTable(filtered);
}

// Làm mới
function refreshInventory() {
  const filterSelect = document.getElementById("inventoryFilter");
  const searchInput = document.getElementById("inventorySearch");
  searchInput.value = "";
  window.currentPage = 1;
  loadInventoryByStore(filterSelect.value);
}

// ================== CHẠY KHI DOM SẴN SÀNG ==================

document.addEventListener("DOMContentLoaded", () => {
  window.allInventory = [];
  window.currentPage = 1;
  window.rowsPerPage = 20;

  const closeNotify = document.getElementById("closeNotify");
  const notifyOverlay = document.getElementById("notifyOverlay");
  const searchInput = document.getElementById("inventorySearch");
  const filterSelect = document.getElementById("inventoryFilter");
  const btnRefresh = document.getElementById("btnRefresh");

  if (closeNotify) closeNotify.onclick = hideNotify;
  window.onclick = (e) => {
    if (e.target === notifyOverlay) hideNotify();
  };

  searchInput.addEventListener("input", applySearch);
  filterSelect.addEventListener("change", () => {
    searchInput.value = "";
    applyFilter();
  });
  btnRefresh.addEventListener("click", refreshInventory);

  // === Bắt đầu tải dữ liệu ===
  loadStoreList();
});
