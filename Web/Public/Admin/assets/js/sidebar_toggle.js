document.addEventListener("DOMContentLoaded", () => {
  // ==== Toggle menu user ====
  const btnUser = document.getElementById("btn-user");
  const menu = document.getElementById("profile-menu");

  if (btnUser && menu) {
    btnUser.addEventListener("click", (e) => {
      e.stopPropagation();
      menu.classList.toggle("show");
    });

    // Click ngoài vùng menu -> ẩn menu
    document.addEventListener("click", (e) => {
      if (!menu.contains(e.target) && !btnUser.contains(e.target)) {
        menu.classList.remove("show");
      }
    });
  }
logoutBtn.addEventListener("click", async () => {
    if (!confirm("Bạn có chắc muốn đăng xuất?")) return;

    try {
      const res = await fetch("../../API/admin/auth_api.php?action=logout", { method: "POST" });
      const data = await res.json();

      if (data.status === "success") {
        // Xóa thông tin người dùng lưu tạm (nếu có)
        sessionStorage.clear();
        localStorage.clear();

        alert("Đăng xuất thành công!");
        window.location.href = "modules/Auth/login.html"; // 🔁 Chuyển về trang login
      } else {
        alert(data.message || "Không thể đăng xuất!");
      }
    } catch (error) {
      console.error("Lỗi khi đăng xuất:", error);
      alert("Lỗi kết nối đến máy chủ!");
    }
  });
  // ==== Mở/đóng submenu ====
  document.querySelectorAll(".menu-toggle").forEach((btn) => {
    btn.addEventListener("click", () => {
      const item = btn.closest(".menu-item");
      if (!item) return;
      item.classList.toggle("active");
    });
  });
});
