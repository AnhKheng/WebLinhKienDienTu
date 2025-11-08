document.addEventListener("DOMContentLoaded", async () => {
  const formAdd = document.getElementById("formAddEmployee");
  const notifyOverlay = document.getElementById("notifyOverlay");
  const notifyMessage = document.getElementById("notifyMessage");
  const closeNotify = document.getElementById("closeNotify");
  const submitBtn = formAdd.querySelector("button[type='submit']");

  let MaCH = null;

  // --- Hàm loại bỏ dấu tiếng Việt ---
  function removeVietnameseTones(str) {
    return str
      .normalize("NFD")
      .replace(/[\u0300-\u036f]/g, "")
      .replace(/đ/g, "d")
      .replace(/Đ/g, "D")
      .replace(/\s+/g, "")
      .trim();
  }

  // --- Hiển thị popup ---
  function showNotify(msg) {
    notifyMessage.textContent = msg;
    notifyOverlay.style.display = "flex";
  }

  function hideNotify() {
    notifyOverlay.style.display = "none";
  }

  // --- Gắn sự kiện đóng popup ---
  if (closeNotify) closeNotify.addEventListener("click", hideNotify);
  notifyOverlay.addEventListener("click", (e) => {
    if (e.target === notifyOverlay) hideNotify();
  });

  // --- Load MaCH từ server ---
  async function loadStoreId() {
    try {
      const res = await fetch("../../API/admin/employee_api.php?action=getMaCH");
      const data = await res.json();
      if (data.status === "success" && data.MaCH) {
        MaCH = data.MaCH;
        submitBtn.disabled = false; // cho phép submit
      } else {
        console.warn("Không tìm thấy mã cửa hàng!");
        submitBtn.disabled = true;
      }
    } catch (err) {
      console.error(err);
      console.warn("Lỗi khi lấy mã cửa hàng!");
      submitBtn.disabled = true;
    }
  }

  // --- Xử lý submit form ---
  if (formAdd) {
    // disable nút submit cho tới khi load MaCH
    submitBtn.disabled = true;

    formAdd.addEventListener("submit", async (e) => {
      e.preventDefault();

      if (!MaCH) return showNotify("Không xác định được cửa hàng!");

      const TenNV = document.getElementById("name").value.trim();
      const GioiTinh = document.getElementById("gender").value;
      const NgaySinh = document.getElementById("birth").value;
      const SoDienThoai = document.getElementById("phone").value.trim();

      if (!TenNV || !NgaySinh || !SoDienThoai) {
        return showNotify("Vui lòng nhập đầy đủ thông tin!");
      }

      try {
        // --- Thêm nhân viên ---
        const res = await fetch("../../API/admin/employee_api.php?action=add", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ TenNV, GioiTinh, NgaySinh, SoDienThoai, MaCH }),
        });

        const result = await res.json();
        console.log("KQ thêm nhân viên:", result);

        if (result.status === "success" && result.data) {
          const emp = result.data;
          const firstName = emp.TenNV.split(" ").pop();
          const username = `${removeVietnameseTones(firstName)}_${emp.MaNV}`;
          const password = "123456";

          // --- Tạo tài khoản ---
          const accRes = await fetch("../../API/admin/auth_api.php?action=register", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ username, password, MaNV: emp.MaNV, role: "nhanvien" }),
          });

          const accData = await accRes.json();
          console.log("KQ tạo tài khoản:", accData);

          if (accData.status === "success") {
            showNotify(`✅ Đã thêm nhân viên "${emp.TenNV}" và tạo tài khoản "${username}"`);
            formAdd.reset();
          } else {
            showNotify(`⚠️ Nhân viên đã thêm, nhưng lỗi tạo tài khoản: ${accData.message || "Không xác định"}`);
          }
        } else {
          showNotify(result.message || "Không thể thêm nhân viên!");
        }
      } catch (err) {
        console.error(err);
        showNotify("Lỗi hệ thống khi thêm nhân viên!");
      }
    });
  }

  // --- Gọi load MaCH ngay khi DOM sẵn sàng ---
  await loadStoreId();
});
