document.addEventListener("DOMContentLoaded", () => {
  // ======= BIẾN CỐ ĐỊNH =======
  const notifyOverlay = document.getElementById("notifyOverlay");
  const notifyMessage = document.getElementById("notifyMessage");
  const closeNotify = document.getElementById("closeNotify");
  const filterSelect = document.getElementById("inventoryFilter");
  const searchInput = document.getElementById("inventorySearch");
  let allInventory = [];
  let currentPage = 1;
  const rowsPerPage = 20;

  if (closeNotify)
    closeNotify.onclick = () => (notifyOverlay.style.display = "none");

  function showNotify(message) {
    notifyMessage.textContent = message ?? "";
    notifyOverlay.style.display = "flex";
  }

  window.onclick = (e) => {
    if (e.target === notifyOverlay) notifyOverlay.style.display = "none";
  };

function renderInventoryTable(list) {
  const tbody = document.querySelector("#InventoryTable tbody");
  const pagination = document.getElementById("pagination");
  tbody.innerHTML = "";

  if (!list || list.length === 0) {
    tbody.innerHTML = `<tr><td colspan="4" style="text-align:center;">Không có dữ liệu tồn kho</td></tr>`;
    if (pagination) pagination.innerHTML = "";
    return;
  }

  allInventory = list;
  setupPagination(list);

  const start = (currentPage - 1) * rowsPerPage;
  const end = start + rowsPerPage;
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

  async function loadStoreList() {
    try {
      const res = await fetch("../../API/admin/stores_api.php?action=getAll");
      const data = await res.json();

      if (data.status === "success" && data.data.length > 0) {
        filterSelect.innerHTML = data.data
          .map(
            (ch, i) =>
              `<option value="${ch.MaCH}" ${
                i === 0 ? "selected" : ""
              }>${ch.TenCH}</option>`
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
  function setupPagination(list) {
    const pagination = document.getElementById("pagination");
    if (!pagination) return;

    const totalPages = Math.ceil(list.length / rowsPerPage);
    pagination.innerHTML = "";

    if (totalPages <= 1) return;

    const makeButton = (text, disabled, handler) => {
      const btn = document.createElement("button");
      btn.textContent = text;
      btn.disabled = disabled;
      btn.addEventListener("click", handler);
      pagination.appendChild(btn);
    };

    makeButton("« Trang đầu", currentPage === 1, () => {
      currentPage = 1;
      renderInventoryTable(allInventory);
    });

    makeButton("‹ Trước", currentPage === 1, () => {
      if (currentPage > 1) currentPage--;
      renderInventoryTable(allInventory);
    });

    const maxButtons = 5;
    let startPage = Math.max(1, currentPage - Math.floor(maxButtons / 2));
    let endPage = Math.min(totalPages, startPage + maxButtons - 1);

    if (endPage - startPage < maxButtons - 1)
      startPage = Math.max(1, endPage - maxButtons + 1);

    for (let i = startPage; i <= endPage; i++) {
      const btn = document.createElement("button");
      btn.textContent = i;
      if (i === currentPage) btn.classList.add("active");
      btn.addEventListener("click", () => {
        currentPage = i;
        renderInventoryTable(allInventory);
      });
      pagination.appendChild(btn);
    }

    makeButton("Sau ›", currentPage === totalPages, () => {
      if (currentPage < totalPages) currentPage++;
      renderInventoryTable(allInventory);
    });

    makeButton("Trang cuối »", currentPage === totalPages, () => {
      currentPage = totalPages;
      renderInventoryTable(allInventory);
    });
  }

  window.applyFilter = function () {
    const MaCH = filterSelect.value;
    if (!MaCH) return;
    currentPage = 1;
    loadInventoryByStore(MaCH);
  };

  searchInput.addEventListener("input", () => applySearch());
  window.applySearch = function () {
    const keyword = searchInput.value.trim().toLowerCase();

    if (keyword === "") {
      renderInventoryTable(allInventory);
      return;
    }

    const filtered = allInventory.filter(item =>
      item.TenSP.toLowerCase().includes(keyword) ||
      item.MaSP.toLowerCase().includes(keyword)
    );

    currentPage = 1;
    renderInventoryTable(filtered);
  };
  btnRefresh.addEventListener("click", () => {
    const MaCH = filterSelect.value;
    searchInput.value = "";
    currentPage = 1;
    loadInventoryByStore(MaCH); 
  });


  filterSelect.addEventListener("change", () => {
    const MaCH = filterSelect.value;
    searchInput.value = "";
    currentPage = 1;
    loadInventoryByStore(MaCH);
  });
  // ======= KHỞI CHẠY =======
  loadStoreList();
});
