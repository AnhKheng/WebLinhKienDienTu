document.addEventListener("DOMContentLoaded", () => {
  // === POPUP THÔNG BÁO ===
  const notifyOverlay = document.getElementById("notifyOverlay");
  const notifyMessage = document.getElementById("notifyMessage");
  const closeNotify = document.getElementById("closeNotify");

  if (closeNotify) closeNotify.onclick = () => (notifyOverlay.style.display = "none");
  function showNotify(message) {
    notifyMessage.textContent = message ?? "";
    notifyOverlay.style.display = "flex";
  }

  // === POPUP THÊM & SỬA ===
  const addModal = document.getElementById("supplierAddModal");
  const editModal = document.getElementById("supplierEditModal");
  const viewModal = document.getElementById("supplierViewModal");

  const btnOpenAdd = document.getElementById("btnOpenForm");
  const btnCloseAdd = document.getElementById("closeAddModal");
  const btnCloseEdit = document.getElementById("closeEditModal");
  const btnCloseView = document.getElementById("closeViewModal");

  if (btnOpenAdd) btnOpenAdd.onclick = () => (addModal.style.display = "flex");
  if (btnCloseAdd) btnCloseAdd.onclick = () => (addModal.style.display = "none");
  if (btnCloseEdit) btnCloseEdit.onclick = () => (editModal.style.display = "none");
  if (btnCloseView) btnCloseView.onclick = () => (viewModal.style.display = "none");

  window.onclick = (e) => {
    if (e.target === addModal) addModal.style.display = "none";
    if (e.target === editModal) editModal.style.display = "none";
    if (e.target === viewModal) viewModal.style.display = "none";
    if (e.target === notifyOverlay) notifyOverlay.style.display = "none";
  };
  window.viewSupplier = async function (idSup) {
    try {
        const res = await fetch(`../../API/admin/supplier_api.php?action=view&MaNCC=${idSup}`);
        const data = await res.json();

        if (data.status === "success") {
        const s = data.data;
        document.getElementById("detail_idSup").value = s.MaNCC;
        document.getElementById("detail_nameSup").value = s.TenNCC;
        document.getElementById("detail_addressSup").value = s.DiaChi;
        document.getElementById("detail_phoneSup").value = s.SoDienThoai;
        document.getElementById("countImport").value = s.SoLuongNhap ?? 0;

        document.getElementById("supplierViewModal").style.display = "flex";
        } else {
        showNotify(data.message || "Không thể tải dữ liệu.");
        }
    } catch {
        showNotify("Lỗi khi kết nối đến máy chủ.");
    }
    };
  // === GỬI DỮ LIỆU THÊM ===
  const formAdd = document.getElementById("formAddSupplier");
  if (formAdd) {
    formAdd.addEventListener("submit", async function (e) {
      e.preventDefault();
      const formData = new FormData(this);

      try {
        const res = await fetch("../../API/admin/supplier_api.php?action=add", {
          method: "POST",
          body: formData,
        });
        const data = await res.json();
        showNotify(data.message);

        if (data.status === "success") {
          addModal.style.display = "none";
          this.reset();
          loadSuppliers();
        }
      } catch {
        showNotify("Lỗi khi thêm nhà cung cấp.");
      }
    });
  }

  // === HIỂN THỊ THÔNG TIN SỬA ===
  window.editSupplier = async function (idSup) {
    try {
      const res = await fetch(`../../API/admin/supplier_api.php?action=view&MaNCC=${idSup}`);
      const data = await res.json();

      if (data.status === "success") {
        const s = data.data;
        document.getElementById("edit_idSup").value = s.MaNCC;
        document.getElementById("edit_nameSup").value = s.TenNCC;
        document.getElementById("edit_addressSup").value = s.DiaChi;
        document.getElementById("edit_phoneSup").value = s.SoDienThoai;
        editModal.style.display = "flex";
      } else {
        showNotify(data.message);
      }
    } catch {
      showNotify("Lỗi khi tải dữ liệu.");
    }
  };

  // === CẬP NHẬT NHÀ CUNG CẤP ===
  const formEdit = document.getElementById("formEditSupplier");
  if (formEdit) {
    formEdit.addEventListener("submit", async function (e) {
      e.preventDefault();
      const formData = new FormData(this);

      try {
        const res = await fetch("../../API/admin/supplier_api.php?action=update", {
          method: "POST",
          body: formData,
        });
        const data = await res.json();
        showNotify(data.message);

        if (data.status === "success") {
          editModal.style.display = "none";
          loadSuppliers();
        }
      } catch {
        showNotify("Lỗi khi cập nhật.");
      }
    });
  }

  // === POPUP XÁC NHẬN XÓA ===
  const confirmOverlay = document.getElementById("confirmOverlay");
  const confirmYes = document.getElementById("confirmYes");
  const confirmNo = document.getElementById("confirmNo");
  const closeConfirm = document.getElementById("closeConfirm");

  function showConfirm(message = "Bạn có chắc muốn xóa?") {
    return new Promise((resolve) => {
      document.getElementById("confirmMessage").textContent = message;
      confirmOverlay.style.display = "flex";

      confirmYes.onclick = () => (confirmOverlay.style.display = "none", resolve(true));
      confirmNo.onclick = closeConfirm.onclick = () => (confirmOverlay.style.display = "none", resolve(false));
    });
  }

  // === XÓA NHÀ CUNG CẤP ===
  window.deleteSupplier = async function (idSup) {
    const ok = await showConfirm("Xóa nhà cung cấp này?");
    if (!ok) return;

    const formData = new FormData();
    formData.append("idSup", idSup);

    try {
      const res = await fetch("../../API/admin/supplier_api.php?action=delete", {
        method: "POST",
        body: formData,
      });
      const data = await res.json();
      showNotify(data.message);

      if (data.status === "success") loadSuppliers();
    } catch {
      showNotify("Lỗi khi xóa.");
    }
  };

  // === HIỂN THỊ DANH SÁCH ===
  function renderSupplierTable(list) {
    const tbody = document.querySelector("#supplierTable tbody");
    tbody.innerHTML = "";

    if (!list || list.length === 0) {
      tbody.innerHTML = `<tr><td colspan="5" style="text-align:center;">Không có dữ liệu</td></tr>`;
      return;
    }

    list.forEach((s) => {
      tbody.insertAdjacentHTML(
        "beforeend",
        `<tr>
          <td>${s.MaNCC}</td>
          <td>${s.TenNCC}</td>
          <td>${s.DiaChi}</td>
          <td>${s.SoDienThoai}</td>
          <td>
            <button class="btn-detail" onclick="viewSupplier('${s.MaNCC}')">Xem</button>
            <button class="btn-edit" onclick="editSupplier('${s.MaNCC}')">Sửa</button>
            <button class="btn-delete" onclick="deleteSupplier('${s.MaNCC}')">Xóa</button>
          </td>
        </tr>`
      );
    });
  }

  // === LOAD DANH SÁCH ===
  async function loadSuppliers() {
    try {
      const res = await fetch("../../API/admin/supplier_api.php?action=view");
      const data = await res.json();

      if (data.status === "success") renderSupplierTable(data.data);
      else showNotify(data.message);
    } catch {
      showNotify("Không thể kết nối máy chủ.");
    }
  }

  loadSuppliers();
});
