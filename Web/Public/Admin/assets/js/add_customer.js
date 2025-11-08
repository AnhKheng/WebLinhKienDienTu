document.addEventListener("DOMContentLoaded", () => {
  const formAdd = document.getElementById("formAddCustomer");
  const notifyOverlay = document.getElementById("notifyOverlay");
  const notifyMessage = document.getElementById("notifyMessage");
  const closeNotify = document.getElementById("closeNotify");

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

  // --- Xử lý submit form ---
  if (formAdd) {
    formAdd.addEventListener("submit", async (e) => {
      e.preventDefault();

      const TenKH = document.getElementById("TenKH").value.trim();
      const SoDienThoai = document.getElementById("SoDienThoai").value.trim();
      const DiaChi = document.getElementById("DiaChi").value.trim();

      if (!TenKH || !SoDienThoai || !DiaChi) {
        return showNotify("⚠️ Vui lòng nhập đầy đủ thông tin!");
      }

      try {
        const res = await fetch("../../API/admin/customer_api.php?action=add", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ TenKH, SoDienThoai, DiaChi }),
        });

        const result = await res.json();
        console.log("KQ thêm khách hàng:", result);

        if (result.status === "success") {
          showNotify(`✅ Đã thêm khách hàng "${TenKH}" thành công!`);
          formAdd.reset();
        } else {
          showNotify(`${result.message || "Không thể thêm khách hàng!"}`);
        }
      } catch (err) {
        console.error(err);
        showNotify(" Lỗi kết nối Server!");
      }
    });
  }
});
