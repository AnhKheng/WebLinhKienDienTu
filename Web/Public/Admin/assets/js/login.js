document.addEventListener("DOMContentLoaded", () => {
  const form = document.querySelector(".login_form");

  form.addEventListener("submit", async (e) => {
    e.preventDefault(); // Ngăn reload mặc định

    const username = document.getElementById("username").value.trim();
    const password = document.getElementById("password").value.trim();

    if (!username || !password) {
      alert("Vui lòng nhập đầy đủ thông tin!");
      return;
    }

    try {
      // Chuẩn bị FormData
      const formData = new FormData(form);
      formData.append("action", "login");

      // Gửi POST request (ẩn URL)
      const res = await fetch("../../../../API/admin/auth_api.php", {
        method: "POST",
        headers: { "Accept": "application/json" },
        body: formData
      });

      if (!res.ok) throw new Error(`Lỗi HTTP ${res.status}`);

      const data = await res.json();

      if (data.status === "success") {
        // Lưu thông tin người dùng vào localStorage
        localStorage.setItem("MaNV", data.data.id);
        localStorage.setItem("TenNV", data.data.username);
        localStorage.setItem("VaiTro", data.data.role);

        // Điều hướng sang trang chính
        window.location.href = "../../../Admin/index.php";
      } else {
        alert(data.message || "Sai tài khoản hoặc mật khẩu!");
      }

    } catch (error) {
      console.error("Lỗi đăng nhập:", error);
      alert("Không thể kết nối đến máy chủ. Vui lòng thử lại sau.");
    }
  });
});
