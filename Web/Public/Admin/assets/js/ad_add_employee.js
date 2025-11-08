document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("formAddEmployee");
  const notifyBox = document.getElementById("notifyOverlay");
  const notifyMessage = document.getElementById("notifyMessage");
  const closeNotify = document.getElementById("closeNotify");
  const storeSelect = document.getElementById("store");

  function showNotify(msg, success = true) {
    notifyMessage.innerHTML = `<p style="color:${success ? 'green' : 'red'};">${msg}</p>`;
    notifyBox.style.display = "flex";
  }

  closeNotify.addEventListener("click", () => (notifyBox.style.display = "none"));

  function removeVietnameseTones(str) {
    return str
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/đ/g, 'd')
        .replace(/Đ/g, 'D')
        .replace(/\s+/g, '')               
    }

  async function loadStores() {
    try {
      const res = await fetch("../../API/admin/stores_api.php?action=getAll");
      const result = await res.json();

      if (result.status === "success" && result.data.length > 0) {
        storeSelect.innerHTML = `<option value="">-- Chọn cửa hàng --</option>`;
        result.data.forEach(store => {
          const opt = document.createElement("option");
          opt.value = store.MaCH;
          opt.textContent = `${store.MaCH} - ${store.TenCH}`;
          storeSelect.appendChild(opt);
        });
      } else {
        storeSelect.innerHTML = `<option value="">(Không có cửa hàng nào)</option>`;
      }
    } catch (error) {
      console.error("Lỗi khi load cửa hàng:", error);
      storeSelect.innerHTML = `<option value="">(Không tải được danh sách)</option>`;
    }
  }

  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const data = {
      TenNV: document.getElementById("name").value.trim(),
      GioiTinh: document.getElementById("gender").value.trim(),
      NgaySinh: document.getElementById("birth").value.trim(),
      SoDienThoai: document.getElementById("phone").value.trim(),
      MaCH: document.getElementById("store").value.trim()
    };

    if (!data.TenNV || !data.MaCH) {
      showNotify("⚠️ Vui lòng nhập đầy đủ thông tin!", false);
      return;
    }

    try {
      const res = await fetch("../../API/admin/employee_api.php?action=add", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data)
      });
      const result = await res.json();

      if (result.status === "success" && result.data) {
        const emp = result.data;
        const firstName = emp.TenNV.split(" ").pop().trim();
        const username = `${removeVietnameseTones(firstName)}_${emp.MaNV}`;
        const password = "123456";
        const role = document.getElementById("role").value;

        const accRes = await fetch("../../API/admin/auth_api.php?action=register", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            username,
            password,
            MaNV: emp.MaNV,
            role
          })
        });

        const accResult = await accRes.json();
        if (accResult.status === "success") {
          showNotify(`Đã thêm nhân viên ${emp.TenNV} và tạo tài khoản: ${username}`);
          form.reset();
        } else {
          showNotify(`Nhân viên thêm thành công nhưng lỗi khi tạo tài khoản: ${accResult.message}`, false);
        }
      } else {
        showNotify(`Thêm nhân viên thất bại: ${result.message}`, false);
      }
    } catch (err) {
      console.error(err);
      showNotify("Lỗi hệ thống khi thêm nhân viên!", false);
    }
  });

  loadStores();
});
