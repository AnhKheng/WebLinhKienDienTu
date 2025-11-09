let allEmployees = [];
let filteredEmployees = [];

let currentPage = 1;
const rowsPerPage = 10;
let employeeToDelete = null;
let employeeToReset = null;

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
document.getElementById("closeNotify").addEventListener("click", hideNotify);

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

// ===== LOAD DANH SÁCH TÀI KHOẢN =====
async function loadEmployees() {
  try {
    const res = await fetch(`../../API/admin/auth_api.php?action=get_all`);
    const data = await res.json();
    if (data.status === "success" && Array.isArray(data.data)) {
      allEmployees = data.data;
      filteredEmployees = [...allEmployees];
      renderTable();
      renderStoreOptions();
    } else {
      showNotify(data.message || "Không thể tải danh sách tài khoản.");
    }
  } catch (err) {
    console.error(err);
    showNotify("Lỗi kết nối API.");
  }
}

// ===== RENDER DANH SÁCH CỬA HÀNG CHO SELECT =====
function renderStoreOptions() {
  const select = document.getElementById("edit_MaCH");
  select.innerHTML = "";
  const stores = [...new Set(allEmployees.map(e => e.MaCH))];
  stores.forEach(store => {
    const opt = document.createElement("option");
    opt.value = store;
    opt.textContent = store;
    select.appendChild(opt);
  });
}

// ===== RENDER BẢNG TÀI KHOẢN =====
function renderTable() {
  const tbody = document.querySelector("#employeeTable tbody");
  tbody.innerHTML = "";

  if (!filteredEmployees.length) {
    tbody.innerHTML = `<tr><td colspan="6" style="text-align:center;">Không có dữ liệu.</td></tr>`;
    return;
  }

  filteredEmployees.forEach(e => {
    const row = `
      <tr>
        <td>${e.MaNV}</td>
        <td>${e.TenNV}</td>
        <td>${e.TenDangNhap}</td>
        <td>${e.VaiTro}</td>
        <td>${e.MaCH}</td>
        <td>
          <button class="btn-edit" onclick="editEmployee('${e.MaNV}')">Sửa</button>
          <button class="btn-delete" onclick="deleteEmployee('${e.MaNV}')">Xóa</button>
          <button class="btn-detail" onclick="resetPassword('${e.MaNV}')">Reset</button>
        </td>
      </tr>
    `;
    tbody.insertAdjacentHTML("beforeend", row);
  });
}

// ===== LỌC DANH SÁCH =====
function applyFilter() {
  const searchValue = document.getElementById("employeeSearch").value.trim().toLowerCase();
  filteredEmployees = allEmployees.filter(e => 
    e.MaNV.toLowerCase().includes(searchValue) || e.TenNV.toLowerCase().includes(searchValue)
  );
  renderTable();
}
document.getElementById("employeeSearch").addEventListener("input", applyFilter);

// ===== SỬA TÀI KHOẢN =====
async function editEmployee(id) {
  try {
    const res = await fetch(`../../API/admin/auth_api.php?action=get_one&MaNV=${id}`);
    const data = await res.json();
    if (data.status === "success") {
      const e = data.data;
      document.getElementById("edit_MaNV").value = e.MaNV;
      document.getElementById("edit_TenNV").value = e.TenNV;
      document.getElementById("edit_TenDangNhap").value = e.TenDangNhap;
      document.getElementById("edit_VaiTro").value = e.VaiTro;
      renderStoreOptions();

      const vaiTroSelect = document.getElementById("edit_VaiTro");
      if (vaiTroSelect) {
        // Nếu giá trị trả về từ API không có trong 2 lựa chọn, thêm tạm
        if (![...vaiTroSelect.options].some(opt => opt.value === e.VaiTro)) {
          const opt = document.createElement("option");
          opt.value = e.VaiTro;
          opt.textContent = e.VaiTro;
          vaiTroSelect.appendChild(opt);
        }
        vaiTroSelect.value = e.VaiTro;
      }
      document.getElementById("edit_MaCH").value = e.MaCH;
      openModal("accountEditModal");
    } else {
      showNotify("Không thể tải thông tin để sửa.");
    }
  } catch (err) {
    console.error(err);
    showNotify("Lỗi kết nối API.");
  }
}

document.getElementById("formEditaccount").addEventListener("submit", async (e) => {
  e.preventDefault();
  const MaNV = document.getElementById("edit_MaNV").value;
  const TenNV = document.getElementById("edit_TenNV").value.trim();
  const TenDangNhap = document.getElementById("edit_TenDangNhap").value.trim();
  const VaiTro = document.getElementById("edit_VaiTro").value.trim();
  const MaCH = document.getElementById("edit_MaCH").value;

  try {
    const res = await fetch("../../API/admin/auth_api.php?action=update_account", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ MaNV, TenDangNhap, VaiTro, MaCH })
    });
    const data = await res.json();
    if (data.status === "success") {
      showNotify("Cập nhật thành công!");
      closeModal("accountEditModal");
      loadEmployees();
    } else {
      showNotify(data.message || "Không thể cập nhật tài khoản.");
    }
  } catch (err) {
    console.error(err);
    showNotify("Lỗi kết nối API.");
  }
});

// ===== XÓA TÀI KHOẢN =====
function deleteEmployee(id) {
  employeeToDelete = id;
  document.getElementById("confirmMessage").textContent = `Bạn có chắc chắn muốn xóa tài khoản [${id}] không?`;
  openModal("confirmOverlay");
}

document.getElementById("confirmYes").addEventListener("click", async () => {
  if (!employeeToDelete) return;
  try {
    const res = await fetch(`../../API/admin/auth_api.php?action=delete_account`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ MaNV: employeeToDelete })
    });
    const data = await res.json();
    if (data.status === "success") {
      showNotify("✅ Xóa tài khoản thành công!");
      loadEmployees();
    } else {
      showNotify(data.message || "Không thể xóa tài khoản.");
    }
  } catch (err) {
    console.error(err);
    showNotify("Lỗi kết nối API.");
  }
  closeModal("confirmOverlay");
  employeeToDelete = null;
});
function resetPassword(MaNV) {
  employeeToReset = MaNV;
  // Ghi thông điệp xác nhận vào modal có sẵn
  document.getElementById("confirmMessage").textContent =
    `Bạn có chắc chắn muốn đặt lại mật khẩu cho tài khoản [${MaNV}] không?`;
  
  // Hiển thị nút xác nhận
  openModal("confirmOverlay");

  // Tạm gán chức năng “Yes” thành reset (tách biệt với xóa)
  const confirmYes = document.getElementById("confirmYes");
  confirmYes.textContent = "Đặt lại"; // đổi chữ cho rõ
  confirmYes.onclick = async () => {
    try {
      const res = await fetch(`../../API/admin/auth_api.php?action=reset_password`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ MaNV: employeeToReset })
      });
      const data = await res.json();
      if (data.status === "success") {
        showNotify(`✅ ${data.message || "Đặt lại mật khẩu thành công!"}`);
      } else {
        showNotify(data.message || "Không thể đặt lại mật khẩu.");
      }
    } catch (err) {
      console.error(err);
      showNotify("Lỗi kết nối API khi đặt lại mật khẩu.");
    }
    closeModal("confirmOverlay");
    employeeToReset = null;
    confirmYes.textContent = "Đồng ý"; // phục hồi nút về trạng thái mặc định
    confirmYes.onclick = null; // xoá handler tạm
  };
}

document.getElementById("confirmNo").addEventListener("click", () => {
  closeModal("confirmOverlay");
  employeeToDelete = null;
});
document.getElementById("closeConfirm").addEventListener("click", () => {
  closeModal("confirmOverlay");
  employeeToDelete = null;
});
document.querySelectorAll(".modal-close").forEach(btn => {
  btn.addEventListener("click", () => {
    const modal = btn.closest(".modal-overlay");
    if (modal) closeModal(modal.id);
  });
});
// ===== CHẠY KHI LOAD =====
document.addEventListener("DOMContentLoaded", () => {
  loadEmployees();
});
