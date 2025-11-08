document.addEventListener("DOMContentLoaded", async () => {

  const notifyOverlay = document.getElementById("notifyOverlay");
  const notifyMessage = document.getElementById("notifyMessage");
  const closeNotify = document.getElementById("closeNotify");
  const searchInput = document.getElementById("employeeSearch");
  const btnRefresh = document.getElementById("btnRefresh");
  const storeNameElement = document.querySelector(".NameStore");
  const employeeFilter = document.getElementById("employeeFilter");

  let allEmployees = [];
  let filteredEmployees = [];
  let currentPage = 1;
  const rowsPerPage = 10;
  let MaCH = null;
  let employeeToDelete = null;


  function showNotify(message) {
    notifyMessage.textContent = message ?? "";
    notifyOverlay.style.display = "flex";
  }
  function hideNotify() {
    notifyOverlay.style.display = "none";
  }
  if (closeNotify) closeNotify.onclick = hideNotify;
  window.onclick = (e) => {
    if (e.target === notifyOverlay) hideNotify();
  };


  function openModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
      modal.style.display = "flex";
      modal.setAttribute("aria-hidden", "false");
    }
  }

  function closeModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
      modal.style.display = "none";
      modal.setAttribute("aria-hidden", "true");
    }
  }

  document.querySelectorAll(".modal-close").forEach((btn) => {
    btn.addEventListener("click", () => {
      const modal = btn.closest(".modal-overlay");
      if (modal) closeModal(modal.id);
    });
  });

  async function loadStoreInfo() {
    try {
      const res = await fetch("../../API/admin/employee_api.php?action=getMaCH");
      const data = await res.json();

      if (data.status === "success" && data.MaCH) {
        MaCH = data.MaCH;
        await loadStoreName();
        await loadEmployees();
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
        storeNameElement.textContent = `${data.store}`;
      } else {
        storeNameElement.textContent = "Không tìm thấy cửa hàng.";
      }
    } catch (err) {
      console.error(err);
      storeNameElement.textContent = "Lỗi khi tải tên cửa hàng.";
    }
  }

  async function loadEmployees() {
    try {
      const res = await fetch(`../../API/admin/employee_api.php?action=getByStore&MaCH=${MaCH}`);
      const data = await res.json();

      if (data.status === "success") {
        allEmployees = data.data;
        filteredEmployees = [...allEmployees];
        renderTable(filteredEmployees);
      } else {
        allEmployees = [];
        renderTable([]);
        showNotify(data.message || "Không thể tải danh sách nhân viên.");
      }
    } catch (err) {
      console.error(err);
      showNotify("Lỗi kết nối API.");
    }
  }

  function renderTable(list) {
    const tbody = document.querySelector("#employeeTable tbody");
    const pagination = document.getElementById("pagination");
    tbody.innerHTML = "";

    if (!list || list.length === 0) {
      tbody.innerHTML = `<tr><td colspan="6" style="text-align:center;">Không có dữ liệu.</td></tr>`;
      pagination.innerHTML = "";
      return;
    }

    const start = (currentPage - 1) * rowsPerPage;
    const end = start + rowsPerPage;
    const pageItems = list.slice(start, end);

    pageItems.forEach((e) => {
      tbody.insertAdjacentHTML(
        "beforeend",
        `
          <tr>
            <td>${e.MaNV}</td>
            <td>${e.TenNV}</td>
            <td>${e.GioiTinh}</td>
            <td>${e.NgaySinh}</td>
            <td>${e.SoDienThoai}</td>
            <td>
              <button class="btn-detail" onclick="viewEmployee('${e.MaNV}')">Xem</button>
              <button class="btn-edit" onclick="editEmployee('${e.MaNV}')">Sửa</button>
              <button class="btn-delete" onclick="deleteEmployee('${e.MaNV}')">Xóa</button>
            </td>
          </tr>
        `
      );
    });

    setupPagination(list);
  }

  function setupPagination(list) {
    const pagination = document.getElementById("pagination");
    if (!pagination) return;
    pagination.innerHTML = "";

    const totalPages = Math.ceil(list.length / rowsPerPage);
    if (totalPages <= 1) return;

    const makeBtn = (text, disabled, handler) => {
      const btn = document.createElement("button");
      btn.textContent = text;
      btn.disabled = disabled;
      btn.addEventListener("click", handler);
      pagination.appendChild(btn);
    };

    makeBtn("« Trang đầu", currentPage === 1, () => {
      currentPage = 1;
      renderTable(filteredEmployees);
    });

    makeBtn("‹ Trước", currentPage === 1, () => {
      if (currentPage > 1) currentPage--;
      renderTable(filteredEmployees);
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
        renderTable(filteredEmployees);
      });
      pagination.appendChild(btn);
    }

    makeBtn("Sau ›", currentPage === totalPages, () => {
      if (currentPage < totalPages) currentPage++;
      renderTable(filteredEmployees);
    });

    makeBtn("Trang cuối »", currentPage === totalPages, () => {
      currentPage = totalPages;
      renderTable(filteredEmployees);
    });
  }

  function applyFilter() {
    const keyword = searchInput.value.trim().toLowerCase();
    const selectedStore = employeeFilter ? employeeFilter.value : "all";

    filteredEmployees = allEmployees.filter((e) => {
      const matchSearch =
        e.MaNV.toLowerCase().includes(keyword) ||
        e.TenNV.toLowerCase().includes(keyword);
      const matchStore = selectedStore === "all" || e.MaCH === selectedStore;
      return matchSearch && matchStore;
    });

    currentPage = 1;
    renderTable(filteredEmployees);
  }

  if (searchInput) searchInput.addEventListener("input", applyFilter);
  if (employeeFilter) employeeFilter.addEventListener("change", applyFilter);
  if (btnRefresh) btnRefresh.addEventListener("click", loadEmployees);

  window.viewEmployee = async (id) => {
    try {
      const res = await fetch(`../../API/admin/employee_api.php?action=getOne&MaNV=${id}`);
      const data = await res.json();

      if (data.status === "success" && data.data) {
        const e = data.data;
        document.getElementById("detail_id").value = e.MaNV;
        document.getElementById("detail_name").value = e.TenNV;
        document.getElementById("detail_gender").value = e.GioiTinh;
        document.getElementById("detail_birth").value = e.NgaySinh;
        document.getElementById("detail_phone").value = e.SoDienThoai;
        document.getElementById("detail_idStore").value = e.MaCH;
        openModal("employeeViewModal");
      } else {
        showNotify("Không thể tải chi tiết nhân viên.");
      }
    } catch (err) {
      console.error(err);
      showNotify("Lỗi khi tải chi tiết.");
    }
  };

  window.editEmployee = async (id) => {
    try {
      const res = await fetch(`../../API/admin/employee_api.php?action=getOne&MaNV=${id}`);
      const data = await res.json();

      if (data.status === "success" && data.data) {
        const e = data.data;
        document.getElementById("edit_id").value = e.MaNV;
        document.getElementById("edit_name").value = e.TenNV;
        document.getElementById("edit_gender").value = e.GioiTinh;
        document.getElementById("edit_birth").value = e.NgaySinh;
        document.getElementById("edit_phone").value = e.SoDienThoai;
        document.getElementById("edit_idStore").value = e.MaCH || "";
        openModal("employeeEditModal");
      } else {
        showNotify("Không thể tải thông tin để sửa.");
      }
    } catch (err) {
      console.error(err);
      showNotify("Lỗi kết nối API.");
    }
  };

  window.deleteEmployee = (id) => {
    employeeToDelete = id;
    const messageEl = document.getElementById("confirmMessage");
    messageEl.textContent = `Bạn có chắc chắn muốn xóa nhân viên [${id}] và tài khoản liên quan không?`;
    document.getElementById("confirmOverlay").style.display = "flex";
  };
  document.getElementById("confirmYes").addEventListener("click", async () => {
    if (!employeeToDelete) return;

    try {
      const resAcc = await fetch("../../API/admin/auth_api.php?action=delete_account", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ MaNV: employeeToDelete })
      });
      const accData = await resAcc.json();

      if (accData.status !== "success" && accData.status !== "warning") {
        alert(accData.message || "Không thể xóa tài khoản, hủy thao tác xóa nhân viên!");
        document.getElementById("confirmOverlay").style.display = "none";
        employeeToDelete = null;
        return;
      }

      const formEmp = new FormData();
      formEmp.append("MaNV", employeeToDelete);

      const resEmp = await fetch("../../API/admin/employee_api.php?action=delete", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ MaNV: employeeToDelete })
      });
      const empData = await resEmp.json();

      if (empData.status === "success") {
        showNotify("✅ Xóa nhân viên và tài khoản thành công!");
        await loadEmployees();
      } else {
        showNotify(empData.message || "Không thể xóa nhân viên!");
      }
    } catch (err) {
      console.error(err);
      showNotify("Lỗi kết nối API.");
    }

    document.getElementById("confirmOverlay").style.display = "none";
    employeeToDelete = null;
  });

  // Nút hủy hoặc đóng modal
  document.getElementById("confirmNo").addEventListener("click", () => {
    document.getElementById("confirmOverlay").style.display = "none";
    employeeToDelete = null;
  });
  document.getElementById("closeConfirm").addEventListener("click", () => {
    document.getElementById("confirmOverlay").style.display = "none";
    employeeToDelete = null;
  });
  const formEdit = document.getElementById("formEditEmployee");
  if (formEdit) {
    formEdit.addEventListener("submit", async (e) => {
      e.preventDefault();
      const formData = new FormData(formEdit);
      const mapped = new FormData();

      mapped.append("MaNV", formData.get("id"));
      mapped.append("TenNV", formData.get("name"));
      mapped.append("GioiTinh", formData.get("gender"));
      mapped.append("NgaySinh", formData.get("birth"));
      mapped.append("SoDienThoai", formData.get("phone"));
      mapped.append("MaCH", formData.get("id_CH"));

      try {
        const res = await fetch("../../API/admin/employee_api.php?action=update", {
          method: "POST",
          body: mapped,
        });
        const data = await res.json();

        if (data.status === "success") {
          showNotify("Cập nhật thành công!");
          closeModal("employeeEditModal");
          loadEmployees();
        } else {
          showNotify(data.message || "Không thể cập nhật nhân viên.");
        }
      } catch (err) {
        console.error(err);
        showNotify("Lỗi kết nối API.");
      }
    });
  }
  await loadStoreInfo();
});
