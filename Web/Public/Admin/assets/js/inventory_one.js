document.addEventListener("DOMContentLoaded", async () => {
  const notifyOverlay = document.getElementById("notifyOverlay");
  const notifyMessage = document.getElementById("notifyMessage");
  const closeNotify = document.getElementById("closeNotify");
  const searchInput = document.getElementById("inventorySearch");
  const btnRefresh = document.getElementById("btnRefresh");
  const storeNameElement = document.querySelector(".NameStore");

  let allInventory = [];
  let currentPage = 1;
  const rowsPerPage = 20;
  let MaCH = null; 

  if (closeNotify)
    closeNotify.onclick = () => (notifyOverlay.style.display = "none");

  function showNotify(message) {
    notifyMessage.textContent = message ?? "";
    notifyOverlay.style.display = "flex";
  }

  window.onclick = (e) => {
    if (e.target === notifyOverlay) notifyOverlay.style.display = "none";
  };

  async function loadStoreInfo() {
    try {
      const res = await fetch("../../API/admin/inventory_api.php?action=getMaCH");
      const data = await res.json();
      if (data.status === "success" && data.MaCH) {
        MaCH = data.MaCH;
        await loadStoreName(); 
        await loadInventoryByStore(); 
      } else {
        showNotify("Không tìm thấy mã cửa hàng!");
      }
    } catch (err) {
      console.error(err);
      showNotify("Lỗi khi lấy mã cửa hàng!");
    }
  }

  async function loadStoreName() {
    try {
      const res = await fetch(`../../API/admin/inventory_api.php?action=getStore&MaCH=${MaCH}`);
      const data = await res.json();
      if (data.status === "success" && data.store) {
        storeNameElement.textContent = "Cửa hàng: " + data.store;
      } else {
        storeNameElement.textContent = "Không tìm thấy cửa hàng.";
      }
    } catch (err) {
      console.error(err);
      storeNameElement.textContent = "Lỗi khi tải tên cửa hàng.";
    }
  }

  async function loadInventoryByStore() {
    if (!MaCH) return showNotify("Chưa có mã cửa hàng!");
    try {
      const res = await fetch(`../../API/admin/inventory_api.php?action=view&MaCH=${MaCH}`);
      const data = await res.json();
      if (data.status === "success") {
        allInventory = data.data;
        renderInventoryTable(allInventory);
      } else {
        renderInventoryTable([]);
        showNotify(data.message);
      }
    } catch (err) {
      console.error(err);
      showNotify("Không thể kết nối máy chủ để tải tồn kho.");
    }
  }

  function renderInventoryTable(list) {
    const tbody = document.querySelector("#InventoryTable tbody");
    const pagination = document.getElementById("pagination");
    tbody.innerHTML = "";

    if (!list || list.length === 0) {
      tbody.innerHTML = `<tr><td colspan="3" style="text-align:center;">Không có dữ liệu tồn kho</td></tr>`;
      if (pagination) pagination.innerHTML = "";
      return;
    }

    const start = (currentPage - 1) * rowsPerPage;
    const end = start + rowsPerPage;
    const pageItems = list.slice(start, end);

    pageItems.forEach((item) => {
      tbody.insertAdjacentHTML(
        "beforeend",
        `<tr>
          <td>${item.MaSP}</td>
          <td>${item.TenSP}</td>
          <td>${item.SoLuongTon}</td>
        </tr>`
      );
    });

    setupPagination(list);
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

  searchInput.addEventListener("input", () => {
    const keyword = searchInput.value.trim().toLowerCase();
    const filtered = allInventory.filter((item) =>
      item.TenSP.toLowerCase().includes(keyword) ||
      item.MaSP.toLowerCase().includes(keyword)
    );
    currentPage = 1;
    renderInventoryTable(filtered);
  });

  btnRefresh.addEventListener("click", () => {
    searchInput.value = "";
    currentPage = 1;
    loadInventoryByStore();
  });

  await loadStoreInfo();
});
