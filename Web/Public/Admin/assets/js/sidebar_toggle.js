document.addEventListener("DOMContentLoaded", () => {
  // ==== Toggle menu user ====
  const btnUser = document.getElementById("btn-user");
  const menu = document.getElementById("profile-menu");
  const logoutBtn = document.getElementById("logoutBtn");

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

  // ==== Đăng xuất ====
  if (logoutBtn) {
    logoutBtn.addEventListener("click", async () => {
      if (!confirm("Bạn có chắc muốn đăng xuất?")) return;

      try {
        const res = await fetch("../../API/admin/auth_api.php?action=logout", { method: "POST" });
        const data = await res.json();

        if (data.status === "success") {
          sessionStorage.clear();
          localStorage.clear();

          alert("Đăng xuất thành công!");
          window.location.href = "modules/Auth/login.html";
        } else {
          alert(data.message || "Không thể đăng xuất!");
        }
      } catch (error) {
        console.error("Lỗi khi đăng xuất:", error);
        alert("Lỗi kết nối đến máy chủ!");
      }
    });
  }

  // ==== Mở/đóng submenu (đóng menu khác khi mở menu mới) ====
  const menuToggles = document.querySelectorAll(".menu-toggle");

  menuToggles.forEach((btn) => {
    btn.addEventListener("click", () => {
      const item = btn.closest(".menu-item");
      if (!item) return;

      // Đóng tất cả menu khác
      document.querySelectorAll(".menu-item.active").forEach((other) => {
        if (other !== item) {
          other.classList.remove("active");
        }
      });

      // Toggle menu hiện tại
      item.classList.toggle("active");
    });
  });
});
