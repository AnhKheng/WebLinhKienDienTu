// === MỞ & ĐÓNG POPUP ===
const addModal = document.getElementById("categoryAddModal");
const editModal = document.getElementById("categoryEditModal");

const btnOpenAdd = document.getElementById("btnOpenForm");
const btnCloseAdd = document.getElementById("closeAddModal");
const btnCloseEdit = document.getElementById("closeEditModal");

// === SỰ KIỆN MỞ/ĐÓNG POPUP THÊM ===
btnOpenAdd.onclick = () => addModal.style.display = "flex";
btnCloseAdd.onclick = () => addModal.style.display = "none";

// === ĐÓNG POPUP KHI CLICK NỀN ===
window.onclick = e => {
  if (e.target === addModal) addModal.style.display = "none";
  if (e.target === editModal) editModal.style.display = "none";
};

// === ĐÓNG POPUP SỬA ===
btnCloseEdit.onclick = () => editModal.style.display = "none";

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
    alert(data.message);
    if (data.status === "success") {
      addModal.style.display = "none"; // Đóng popup
      this.reset();                    // Xóa form
      loadCategories();                // 🔁 Tải lại bảng
    }
  })
  .catch(err => console.error(err));
});


// === MỞ POPUP SỬA ===
async function editCategory(maDM) {
  const res = await fetch(`../../API/admin/Category/Detail.php?MaDM=${maDM}`);
  const data = await res.json();

  if (data.status === "success") {
    const c = data.data;
    document.getElementById("edit_idDM_old").value = c.MaDM;
    document.getElementById("edit_idDM").value = c.MaDM;
    document.getElementById("edit_nameDM").value = c.TenDM;
    editModal.style.display = "flex";
  } else {
    alert(data.message || "Không tìm thấy danh mục.");
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

    alert(data.message);
    if (data.status === "success") {
      editModal.style.display = "none";
      loadCategories();
    }
  } catch (err) {
    console.error(err);
    alert("Không thể cập nhật danh mục.");
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


// ==== HÀM LOAD DANH MỤC TỪ API ====
async function loadCategories() {
  try {
    const response = await fetch("../../API/admin/Category/View.php");
    const result = await response.json();
    if (result.status === "success") {
      renderCategoryTable(result.data);
    } else {
      alert(result.message || "Không thể tải danh mục.");
    }
  } catch (error) {
    console.error("Lỗi khi tải danh mục:", error);
    alert("Không thể kết nối đến API.");
  }
}


// ==== GỌI KHI TRANG LOAD ====
window.addEventListener("load", loadCategories);
