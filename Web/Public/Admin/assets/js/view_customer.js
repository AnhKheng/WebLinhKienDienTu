let allCustomers = [];
let filteredCustomers = [];

let currentPage = 1;
const rowsPerPage = 10;
let customerToDelete = null;

// ===== POPUP THÔNG BÁO =====
function showNotify(message, success = true) {
  const overlay = document.getElementById("notifyOverlay");
  const msg = document.getElementById("notifyMessage");
  msg.innerHTML = `<p style="color:${success ? 'green' : 'red'};">${message}</p>`;
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
window.addEventListener("click", e => {
  document.querySelectorAll(".modal-overlay").forEach(m => {
    if (e.target === m) closeModal(m.id);
  });
});
document.querySelectorAll(".modal-close").forEach(btn => {
  btn.addEventListener("click", () => {
    const modal = btn.closest(".modal-overlay");
    if (modal) closeModal(modal.id);
  });
});

// ===== LOAD DANH SÁCH KHÁCH HÀNG =====
async function loadCustomers(page = 1) {
  try {
    const res = await fetch(`../../API/admin/customer_api.php?action=getAllPaged&page=${page}&limit=${rowsPerPage}`);
    const data = await res.json();

    if (data.status === "success") {
      allCustomers = data.data || [];
      filteredCustomers = [...allCustomers];
      renderTable(filteredCustomers, data.totalPages);
    } else {
      showNotify(data.message || "Không thể tải danh sách khách hàng.", false);
    }
  } catch (err) {
    console.error(err);
    showNotify("Lỗi kết nối API.", false);
  }
}

// ===== RENDER BẢNG KHÁCH HÀNG =====
function renderTable(list, totalPages = 1) {
  const tbody = document.querySelector("#CustomerTable tbody");
  const pagination = document.getElementById("pagination");
  tbody.innerHTML = "";

  if (!list.length) {
    tbody.innerHTML = `<tr><td colspan="5" style="text-align:center;">Không có dữ liệu.</td></tr>`;
    pagination.innerHTML = "";
    return;
  }

  setupPagination(totalPages);

    const pageList = list;

  pageList.forEach(c => {
    const row = `
      <tr>
        <td>${c.MaKH}</td>
        <td>${c.TenKH}</td>
        <td>${c.SoDienThoai}</td>
        <td>${c.DiaChi}</td>
        <td>
            <a class="btn-detail" href="index.php?module=Customer&page=Detail&id=${c.MaKH}">Xem</a>
            <button class="btn-edit" onclick="editCustomer('${c.MaKH}')">Sửa</button>
            <button class="btn-delete" onclick="deleteCustomer('${c.MaKH}')">Xóa</button>
        </td>
      </tr>
    `;
    tbody.insertAdjacentHTML("beforeend", row);
  });
}

// ===== PHÂN TRANG =====
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

  makeBtn("«", currentPage === 1, () => { currentPage = 1; loadCustomers(currentPage); });
  for (let i = 1; i <= totalPages; i++) {
    const btn = document.createElement("button");
    btn.textContent = i;
    if (i === currentPage) btn.classList.add("active");
    btn.addEventListener("click", () => { currentPage = i; loadCustomers(currentPage); });
    pagination.appendChild(btn);
  }
  makeBtn("»", currentPage === totalPages, () => { currentPage = totalPages; loadCustomers(currentPage); });
}

// ===== TÌM KIẾM =====
document.getElementById("customerSearch").addEventListener("input", applyFilter);
function applyFilter() {
  const searchValue = document.getElementById("customerSearch").value.trim().toLowerCase();
  filteredCustomers = allCustomers.filter(c =>
    c.MaKH.toLowerCase().includes(searchValue) ||
    c.TenKH.toLowerCase().includes(searchValue)
  );
  currentPage = 1;
  renderTable(filteredCustomers);
}

// ===== XÓA KHÁCH HÀNG =====
function deleteCustomer(id) {
  customerToDelete = id;
  document.getElementById("confirmMessage").textContent = `Bạn có chắc chắn muốn xóa khách hàng [${id}] không?`;
  openModal("confirmOverlay");
}

document.getElementById("confirmYes").addEventListener("click", async () => {
  if (!customerToDelete) return;

  try {
    const res = await fetch(`../../API/admin/customer_api.php?action=delete`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ MaKH: customerToDelete })
    });
    const data = await res.json();
    if (data.status === "success") {
      showNotify("Xóa khách hàng thành công!");
      loadCustomers(currentPage);
    } else {
      showNotify(data.message || "Không thể xóa khách hàng!", false);
    }
  } catch (err) {
    console.error(err);
    showNotify("Lỗi kết nối API.", false);
  }

  closeModal("confirmOverlay");
  customerToDelete = null;
});

document.getElementById("confirmNo").addEventListener("click", () => {
  closeModal("confirmOverlay");
  customerToDelete = null;
});

document.getElementById("closeConfirm").addEventListener("click", () => {
  closeModal("confirmOverlay");
  customerToDelete = null;
});

// ===== CHỈNH SỬA KHÁCH HÀNG =====
async function editCustomer(id) {
  try {
    const res = await fetch(`../../API/admin/customer_api.php?action=get_by_id&MaKH=${id}`);
    const data = await res.json();
    if (data.status === "success" && data.data) {
      const c = data.data;
      document.getElementById("edit_MaKH").value = c.MaKH;
      document.getElementById("edit_TenKh").value = c.TenKH;
      document.getElementById("edit_SoDienThoai").value = c.SoDienThoai;
      document.getElementById("edit_DiaChi").value = c.DiaChi;
      openModal("EditModal");
    } else {
      showNotify(data.message || "Không thể tải thông tin khách hàng.", false);
    }
  } catch (err) {
    console.error(err);
    showNotify("Lỗi kết nối API.", false);
  }
}

document.getElementById("formEdit").addEventListener("submit", async (e) => {
  e.preventDefault();
  const MaKH = document.getElementById("edit_MaKH").value;
  const TenKH = document.getElementById("edit_TenKh").value.trim();
  const SoDienThoai = document.getElementById("edit_SoDienThoai").value.trim();
  const DiaChi = document.getElementById("edit_DiaChi").value.trim();

  if (!TenKH) {
    showNotify("Tên khách hàng không được để trống!", false);
    return;
  }

  try {
    const res = await fetch("../../API/admin/customer_api.php?action=update", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ MaKH, TenKH, SoDienThoai, DiaChi })
    });
    const data = await res.json();
    if (data.status === "success") {
      showNotify("Cập nhật khách hàng thành công!");
      closeModal("EditModal");
      loadCustomers(currentPage);
    } else {
      showNotify(data.message || "Không thể cập nhật khách hàng!", false);
    }
  } catch (err) {
    console.error(err);
    showNotify("Lỗi kết nối API.", false);
  }
});

// ===== KHỞI TẠO =====
document.addEventListener("DOMContentLoaded", () => loadCustomers());
