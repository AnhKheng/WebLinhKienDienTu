document.addEventListener("DOMContentLoaded", () => {
  // === MỞ & ĐÓNG POPUP ===
  const notifyOverlay = document.getElementById("notifyOverlay");
  const notifyMessage = document.getElementById("notifyMessage");
  const btnCloseNotify = document.getElementById("closeNotify");
  const addModal = document.getElementById("categoryAddModal");
  const editModal = document.getElementById("categoryEditModal");
  const btnOpenAdd = document.getElementById("btnOpenForm");
  const btnCloseAdd = document.getElementById("closeAddModal");
  const btnCloseEdit = document.getElementById("closeEditModal");

  // === NÚT ĐÓNG POPUP THÔNG BÁO ===
  if (btnCloseNotify) {
    btnCloseNotify.onclick = () => notifyOverlay.style.display = "none";
  }

  // === SỰ KIỆN MỞ/ĐÓNG POPUP THÊM ===
  btnOpenAdd.onclick = () => addModal.style.display = "flex";
  btnCloseAdd.onclick = () => addModal.style.display = "none";

  // === ĐÓNG POPUP KHI CLICK NỀN ===
  window.onclick = e => {
    if (e.target === addModal) addModal.style.display = "none";
    if (e.target === editModal) editModal.style.display = "none";
    if (e.target === notifyOverlay) notifyOverlay.style.display = "none";
  };

  // === ĐÓNG POPUP SỬA ===
  btnCloseEdit.onclick = () => editModal.style.display = "none";

  // === HIỂN THỊ THÔNG BÁO ===
  function showNotify(message) {
    if (!notifyOverlay || !notifyMessage) {
      console.warn("notifyOverlay hoặc notifyMessage không tồn tại trong DOM!");
      return;
    }
    notifyMessage.textContent = message ?? "";
    notifyOverlay.style.display = "flex";
  }

  // ==== GỬI DỮ LIỆU THÊM DANH MỤC ====
  document.getElementById("formAddCategory").addEventListener("submit", function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch("../../API/admin/Category/Add.php", {
      method: "POST",
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      showNotify(data.message);
      if (data.status === "success") {
        addModal.style.display = "none";
        this.reset();
        loadCategories();
      }
    })
    .catch(err => {
      console.error(err);
      showNotify("Đã xảy ra lỗi khi thêm danh mục.");
    });
  });

  // === MỞ POPUP SỬA ===
  window.editCategory = async function(maDM) {
    try {
      const res = await fetch(`../../API/admin/Category/Detail.php?MaDM=${maDM}`);
      const data = await res.json();

      if (data.status === "success") {
        const c = data.data;
        document.getElementById("edit_idDM_old").value = c.MaDM;
        document.getElementById("edit_idDM").value = c.MaDM;
        document.getElementById("edit_nameDM").value = c.TenDM;
        editModal.style.display = "flex";
      } else {
        showNotify(data.message || "Không thể tải thông tin danh mục.");
      }
    } catch (error) {
      console.error(error);
      showNotify("Lỗi khi kết nối đến máy chủ.");
    }
  }

  // ==== GỬI DỮ LIỆU CẬP NHẬT DANH MỤC ====
  document.getElementById("formEditCategory").addEventListener("submit", async function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    try {
      const res = await fetch("../../API/admin/Category/Edit.php", {
        method: "POST",
        body: formData
      });
      const data = await res.json();

      showNotify(data.message);
      if (data.status === "success") {
        editModal.style.display = "none";
        loadCategories();
      }
    } catch (err) {
      console.error(err);
      showNotify("Đã xảy ra lỗi khi cập nhật danh mục.");
    }
  });

  // ==== HÀM HIỂN THỊ DANH MỤC RA BẢNG ====
  function renderCategoryTable(categories) {
    const tbody = document.querySelector("#categoryTable tbody");
    tbody.innerHTML = "";
    categories.forEach(c => {
      const row = `
        <tr>
          <td>${c.MaDM}</td>
          <td>${c.TenDM}</td>
          <td>
            <button class="btn-edit" onclick="editCategory('${c.MaDM}')">Sửa</button>
            <button class="btn-delete" onclick="deleteCategory('${c.MaDM}')">Xóa</button>
          </td>
        </tr>`;
      tbody.insertAdjacentHTML("beforeend", row);
    });
  }

  // ==== HÀM LOAD DANH MỤC ====
  async function loadCategories() {
    try {
      const response = await fetch("../../API/admin/Category/View.php");
      const result = await response.json();
      if (result.status === "success") renderCategoryTable(result.data);
      else showNotify(result.message || "Không thể tải danh mục.");
    } catch (error) {
      console.error(error);
      showNotify("Không thể kết nối đến máy chủ.");
    }
  }

  // === POPUP XÁC NHẬN XÓA ===
const confirmOverlay = document.getElementById("confirmOverlay");
const confirmYes = document.getElementById("confirmYes");
const confirmNo = document.getElementById("confirmNo");
const closeConfirm = document.getElementById("closeConfirm");

// Hàm mở popup xác nhận (trả về Promise để dễ await)
function showConfirm(message = "Bạn có chắc chắn muốn xóa mục này không?") {
  return new Promise(resolve => {
    document.getElementById("confirmMessage").textContent = message;
    confirmOverlay.style.display = "flex";

    // Gỡ mọi listener cũ
    confirmYes.onclick = null;
    confirmNo.onclick = null;
    closeConfirm.onclick = null;

    // Gán sự kiện
    confirmYes.onclick = () => {
      confirmOverlay.style.display = "none";
      resolve(true);
    };
    confirmNo.onclick = closeConfirm.onclick = () => {
      confirmOverlay.style.display = "none";
      resolve(false);
    };
    // Click ra ngoài để đóng
    confirmOverlay.onclick = (e) => {
      if (e.target === confirmOverlay) {
        confirmOverlay.style.display = "none";
        resolve(false);
      }
    };
  });
}

// === CẬP NHẬT HÀM XÓA DANH MỤC ===
window.deleteCategory = async function(maDM) {
  const isConfirmed = await showConfirm("Bạn có chắc chắn muốn xóa danh mục này không?");
  if (!isConfirmed) return;

  try {
    const formData = new FormData();
    formData.append("idDM", maDM);

    const res = await fetch("../../API/admin/Category/Delete.php", {
      method: "POST",
      body: formData
    });
    const data = await res.json();

    showNotify(data.message);
    if (data.status === "success") loadCategories();
  } catch (err) {
    console.error(err);
    showNotify("Đã xảy ra lỗi khi xóa danh mục.");
  }
};

  // ==== GỌI KHI TRANG LOAD ====
  loadCategories();
});
