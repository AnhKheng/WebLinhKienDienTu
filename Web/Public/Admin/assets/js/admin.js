async function checkLogin() {
  try {
    const res = await fetch("../../API/admin/auth_api.php?action=get_user");
    if (!res.ok) throw new Error(`HTTP error! Status: ${res.status}`);

    const data = await res.json();

    if (data.status !== "success") {
      // Nếu chưa đăng nhập => quay về login
      window.location.href = "modules/Auth/login.html";
      return;
    }

    // Lưu thông tin từ API
    const id = data.data.MaNV;
    const username = data.data.TenNV;
    const role = data.data.VaiTro;

    // Gắn vào giao diện
    document.getElementById("userId").textContent = id;
    document.getElementById("userName").textContent = username;
    document.getElementById("userRole").textContent = role;

    // Ẩn menu admin nếu không phải quản trị
    if (role !== "QuanTri") {
      document.querySelectorAll(".admin-only").forEach(el => el.style.display = "none");
    }

  } catch (err) {
    console.error("Lỗi kiểm tra đăng nhập:", err);
    alert("Không thể kết nối đến máy chủ.");
  }
}

// Gọi khi load trang
document.addEventListener("DOMContentLoaded", checkLogin);
