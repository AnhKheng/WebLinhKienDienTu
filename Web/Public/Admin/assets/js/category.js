// ==== MỞ & ĐÓNG POPUP ====
const popup = document.getElementById("popupContainer");
const btnOpen = document.getElementById("btnOpenForm");
const btnClose = document.getElementById("closePopup");

btnOpen.onclick = () => popup.style.display = "flex";
btnClose.onclick = () => popup.style.display = "none";
window.onclick = e => { if (e.target === popup) popup.style.display = "none"; };


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
      popup.style.display = "none"; // Đóng popup
      this.reset();                // Xóa form
      loadCategories();            // 🔁 Tải lại bảng
    }
  })
  .catch(err => console.error(err));
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
      </tr>`;
    tbody.insertAdjacentHTML("beforeend", row);
  });
}


// ==== HÀM LOAD DANH MỤC TỪ API ====
async function loadCategories() {
    try{
        const response = await fetch("../../API/admin/Category/View.php");
        const result = await response.json();
        if (result.status === "success") {
            renderCategoryTable(result.data);
        }else {
            alert(result.message || "Không thể tải danh mục");
        }
    } catch (error) {
        console.error("Lỗi khi tải danh mục:", error);
        alert("Không thể kết nối đến API.");
    }
}


// ==== GỌI KHI TRANG LOAD ====
window.addEventListener("load", loadCategories);
