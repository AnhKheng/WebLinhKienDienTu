let allEmployees = [];
let filteredEmployees = [];

let currentPage = 1;
const rowsPerPage = 10;
let employeeToDelete = null;

// ===== POPUP THÔNG BÁO =====
function showNotify(message) {
  const overlay = document.getElementById("notifyOverlay");
  const msg = document.getElementById("notifyMessage");
  msg.textContent = message;
  overlay.style.display = "flex";
}
function hideNotify() {
  document.getElementById("notifyOverlay").style.display = "none";
}

// ===== MỞ / ĐÓNG MODAL =====
function openModal(id) {
  const modal = document.getElementById(id);
  modal.style.display = "flex";
  modal.setAttribute("aria-hidden", "false");
}
function closeModal(id) {
  const modal = document.getElementById(id);
  modal.style.display = "none";
  modal.setAttribute("aria-hidden", "true");
}
window.addEventListener("click", (e) => {
  document.querySelectorAll(".modal-overlay").forEach((m) => {
    if (e.target === m) closeModal(m.id);
  });
});
document.getElementById("closeNotify").addEventListener("click", hideNotify);

// ===== LOAD DANH SÁCH NHÂN VIÊN =====
async function loadEmployees(page = 1) {
  try {
    const res = await fetch(`../../API/admin/employee_api.php?action=view&page=${page}&limit=${rowsPerPage}`);
    const data = await res.json();

    if (data.status === "success") {
      allEmployees = data.data || [];
      filteredEmployees = [...allEmployees];
      renderStoreOptions();
      renderTable(filteredEmployees, data.total_pages);
    } else {
      showNotify(data.message || "Không thể tải danh sách nhân viên.");
    }
  } catch (err) {
    console.error(err);
    showNotify("Lỗi kết nối API.");
  }
}

// ====== TẢI DANH SÁCH CỬA HÀNG CHO SELECT ======
async function loadStores() {
  try {
    const res = await fetch("../../API/admin/stores_api.php?action=getAll");
    const result = await res.json();

    if (result.status === "success" && Array.isArray(result.data)) {
      const select = document.getElementById("edit_idStore");
      select.innerHTML = "";

      result.data.forEach(store => {
        const opt = document.createElement("option");
        opt.value = store.MaCH;
        opt.textContent = store.TenCH;
        select.appendChild(opt);
      });
    } else {
      console.warn("Không tải được danh sách cửa hàng:", result.message);
    }
  } catch (err) {
    console.error("Lỗi khi tải danh sách cửa hàng:", err);
  }
}

// ====== RENDER BẢNG NHÂN VIÊN ======
function renderTable(list, totalPages = 1) {
  const tbody = document.querySelector("#employeeTable tbody");
  const pagination = document.getElementById("pagination");
  tbody.innerHTML = "";

  if (!list.length) {
    tbody.innerHTML = `<tr><td colspan="7" style="text-align:center;">Không có dữ liệu.</td></tr>`;
    pagination.innerHTML = "";
    return;
  }

  setupPagination(totalPages);

  const start = (currentPage - 1) * rowsPerPage;
  const end = start + rowsPerPage;
  const pageList = list.slice(start, end);

  pageList.forEach(e => {
    const row = `
      <tr>
        <td>${e.MaNV}</td>
        <td>${e.TenNV}</td>
        <td>${e.GioiTinh}</td>
        <td>${e.NgaySinh}</td>
        <td>${e.SoDienThoai}</td>
        <td>${e.MaCH}</td>
        <td>
          <button class="btn-detail" onclick="viewEmployee('${e.MaNV}')">Xem</button>
          <button class="btn-edit" onclick="editEmployee('${e.MaNV}')">Sửa</button>
          <button class="btn-delete" onclick="deleteEmployee('${e.MaNV}')">Xóa</button>
        </td>
      </tr>
    `;
    tbody.insertAdjacentHTML("beforeend", row);
  });
}

function renderStoreOptions() {
  const select = document.getElementById("employeeFilter");
  select.innerHTML = '<option value="all">Tất cả</option>';
  const stores = [...new Set(allEmployees.map(e => e.MaCH))];
  stores.forEach(store => {
    const opt = document.createElement("option");
    opt.value = store;
    opt.textContent = store;
    select.appendChild(opt);
  });
}

function applyFilter() {
  const selectedStore = document.getElementById("employeeFilter").value;
  const searchValue = document.getElementById("employeeSearch").value.trim().toLowerCase();

  filteredEmployees = allEmployees.filter(e => {
    const matchStore = selectedStore === "all" || e.MaCH === selectedStore;
    const matchSearch =
      e.MaNV.toLowerCase().includes(searchValue) ||
      e.TenNV.toLowerCase().includes(searchValue);
    return matchStore && matchSearch;
  });

  currentPage = 1;
  renderTable(filteredEmployees);
}

// ====== PHÂN TRANG ======
function setupPagination(totalPages) {
  const pagination = document.getElementById("pagination");
  pagination.innerHTML = "";

  if (totalPages <= 1) return;

  const makeBtn = (text, disabled, handler) => {
    const btn = document.createElement("button");
    btn.textContent = text;
    btn.disabled = disabled;
    btn.addEventListener("click", handler);
    pagination.appendChild(btn);
  };

  makeBtn("«", currentPage === 1, () => {
    currentPage = 1;
    loadEmployees(currentPage);
  });

  for (let i = 1; i <= totalPages; i++) {
    const btn = document.createElement("button");
    btn.textContent = i;
    if (i === currentPage) btn.classList.add("active");
    btn.addEventListener("click", () => {
      currentPage = i;
      loadEmployees(currentPage);
    });
    pagination.appendChild(btn);
  }

  makeBtn("»", currentPage === totalPages, () => {
    currentPage = totalPages;
    loadEmployees(currentPage);
  });
}

// ====== XEM CHI TIẾT ======
async function viewEmployee(id) {
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
}

// ====== SỬA NHÂN VIÊN ======
async function editEmployee(id) {
  try {
    const res = await fetch(`../../API/admin/employee_api.php?action=getOne&MaNV=${id}`);
    const data = await res.json();
    if (data.status === "success" && data.data) {
      const e = data.data;
      await loadStores();
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
}

// ====== XÓA NHÂN VIÊN ======
function deleteEmployee(id) {
  employeeToDelete = id;
  document.getElementById("confirmMessage").textContent =
    `Bạn có chắc chắn muốn xóa nhân viên [${id}] không?`;
  document.getElementById("confirmOverlay").style.display = "flex";
}
document.getElementById("confirmYes").addEventListener("click", async () => {
  if (!employeeToDelete) return;

  try {
    const resAcc = await fetch(`../../API/admin/auth_api.php?action=delete_account`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ MaNV: employeeToDelete })
    });
    const accData = await resAcc.json();

    if (accData.status !== "success" && accData.status !== "warning") {
      showNotify(accData.message || "Không thể xóa tài khoản, hủy thao tác xóa nhân viên!");
      document.getElementById("confirmOverlay").style.display = "none";
      employeeToDelete = null;
      return;
    }

    const resEmp = await fetch(`../../API/admin/employee_api.php?action=delete`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ MaNV: employeeToDelete })
    });
    const empData = await resEmp.json();

    if (empData.status === "success") {
      showNotify("✅ Xóa nhân viên & tài khoản thành công!");
      loadEmployees(currentPage);
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

document.getElementById("confirmNo").addEventListener("click", () => {
  document.getElementById("confirmOverlay").style.display = "none";
  employeeToDelete = null;
});

document.getElementById("closeConfirm").addEventListener("click", () => {
  document.getElementById("confirmOverlay").style.display = "none";
  employeeToDelete = null;
});


// ====== CẬP NHẬT NHÂN VIÊN ======
document.getElementById("formEditEmployee").addEventListener("submit", async (e) => {
  e.preventDefault();
  const formData = new FormData(e.target);

  // Chuyển key để khớp với PHP
  const mapped = new FormData();
  mapped.append("MaNV", formData.get("id"));
  mapped.append("TenNV", formData.get("name"));
  mapped.append("GioiTinh", formData.get("address"));
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
      loadEmployees(currentPage);
    } else {
      showNotify(data.message || "Không thể cập nhật nhân viên.");
    }
  } catch (err) {
    console.error(err);
    showNotify("Lỗi kết nối API.");
  }
});

document.querySelectorAll(".modal-close").forEach(btn => {
  btn.addEventListener("click", () => {
    const modal = btn.closest(".modal-overlay");
    if (modal) closeModal(modal.id);
  });
});

document.getElementById("employeeSearch").addEventListener("input", applyFilter);

// ====== CHẠY KHI LOAD ======
document.addEventListener("DOMContentLoaded", () => loadEmployees());
