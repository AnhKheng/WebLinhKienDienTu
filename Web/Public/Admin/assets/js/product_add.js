async function loadCategories() {
  try {
    const response = await fetch("../../API/admin/category_api.php?action=view");
    const result = await response.json();

    if (result.status === "success" && result.data.length > 0) {
      const select = document.getElementById("add_category");
      select.innerHTML = '<option value="">-- Chọn danh mục sản phẩm --</option>';

      result.data.forEach(cat => {
        const option = document.createElement("option");
        option.value = cat.MaDM;
        option.textContent = cat.TenDM;
        select.appendChild(option);
      });
    } else {
      console.warn("Không có danh mục nào được trả về từ API.");
    }
  } catch (error) {
    console.error("Lỗi khi tải danh mục:", error);
  }
}

function showNotify(message) {
  const notifyOverlay = document.getElementById("notifyOverlay");
  const notifyMessage = document.getElementById("notifyMessage");
  if (!notifyOverlay || !notifyMessage) return;
  notifyMessage.textContent = message;
  notifyOverlay.style.display = "flex";
}

function hideNotify() {
  const notifyOverlay = document.getElementById("notifyOverlay");
  if (notifyOverlay) notifyOverlay.style.display = "none";
}

document.addEventListener("DOMContentLoaded", () => {
  const closeNotify = document.getElementById("closeNotify");
  if (closeNotify) closeNotify.addEventListener("click", hideNotify);
});

async function handleAddProduct(e) {
  e.preventDefault(); 
  const form = e.target;
  const formData = new FormData(form);
  const name = formData.get("nameSP")?.trim();
  const category = formData.get("category");
  const price = formData.get("price");
  if (!name || !category || category === "notchoose" || !price) {
    showNotify("Vui lòng nhập đầy đủ thông tin sản phẩm.");
    return;
  }

  try {
    const response = await fetch("../../API/admin/product_api.php?action=add", {
      method: "POST",
      body: formData
    });
    const result = await response.json();

    if (result.status === "success") {
      showNotify("Thêm sản phẩm thành công!");
      form.reset();
    } else {
      showNotify(`${result.message || "Không thể thêm sản phẩm."}`);
    }
  } catch (error) {
    console.error("Lỗi khi thêm sản phẩm:", error);
    showNotify("Không thể kết nối đến API.");
  }
}

document.addEventListener("DOMContentLoaded", () => {
  loadCategories();

  const form = document.getElementById("formAddProduct");
  if (form) {
    form.addEventListener("submit", handleAddProduct);
  }

  const closeButtons = document.querySelectorAll(".modal-close");
  closeButtons.forEach(btn => {
    btn.addEventListener("click", () => {
      const overlay = btn.closest(".modal-overlay");
      if (overlay) overlay.style.display = "none";
    });
  });
});
