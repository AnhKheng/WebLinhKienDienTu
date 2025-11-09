let allCuaHang = []; // Lưu toàn bộ dữ liệu cửa hàng
let isEditMode = false;

// 🔹 Tải danh sách cửa hàng từ API
async function loadCuaHang() {
  try {
    console.log("🏁 DOMContentLoaded triggered");
    const tbody = document.querySelector("#cuahangTable tbody");
    console.log("tbody:", tbody);

    const response = await fetch("../../API/admin/stores_api.php?action=getAll");
    if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);

    const text = await response.text();
    let result;
    try {
      result = JSON.parse(text);
    } catch (err) {
      console.error("Phản hồi API không phải JSON:", text);
      throw new Error("API không trả về dữ liệu JSON hợp lệ.");
    }

    if (result.status === "success") {
      allCuaHang = result.data;
      renderTable(allCuaHang);
    } else {
      alert(result.message || "Không thể tải danh sách cửa hàng");
    }

  } catch (error) {
    console.error("Lỗi khi tải cửa hàng:", error);
    alert("Không thể kết nối đến API. Chi tiết: " + error.message);
  }
}

// 🔹 Hiển thị dữ liệu ra bảng
function renderTable(cuahangs) {
  const tbody = document.querySelector("#cuahangTable tbody");
  tbody.innerHTML = "";

  cuahangs.forEach((ch) => {
    const row = `
      <tr data-mach="${ch.MaCH}">
        <td>${ch.MaCH}</td>
        <td>${ch.TenCH}</td>
        <td>${ch.DiaChi || "—"}</td>
        <td>${ch.SoDienThoai || "—"}</td>
        <td>
          <button type="button" class="btn-edit" onclick="openUpdateModal('${ch.MaCH}')">Sửa</button>
          <button type="button" class="btn-delete" onclick="deleteCuaHang('${ch.MaCH}')">Xóa</button>
        </td>
      </tr>
    `;
    tbody.insertAdjacentHTML("beforeend", row);
  });
}

// 🔹 Thêm cửa hàng
function openAddModal() {
  document.getElementById("txtTenCH_Add").value = "";
  document.getElementById("txtDiaChi_Add").value = "";
  document.getElementById("txtSDT_Add").value = "";
  document.getElementById("addModal").style.display = "flex";
}

function closeAddModal() {
  document.getElementById("addModal").style.display = "none";
}

async function saveAdd() {
  const tenCH = document.getElementById("txtTenCH_Add").value;
  const diaChi = document.getElementById("txtDiaChi_Add").value;
  const sdt = document.getElementById("txtSDT_Add").value;

  if (!tenCH) return alert("Tên cửa hàng không được để trống!");

  const data = { TenCH: tenCH, DiaChi: diaChi, SoDienThoai: sdt };

  try {
    const res = await fetch("../../API/admin/stores_api.php?action=add", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(data)
    });
    const result = await res.json();
    if (result.status === "success") {
      alert(result.message || "Thêm thành công! Mã cửa hàng: " + result.MaCH);
      closeAddModal();
      loadCuaHang();
    } else {
      alert(result.message || "Thêm thất bại!");
    }
  } catch (err) {
    alert("Lỗi khi thêm cửa hàng: " + err.message);
  }
}

// 🔹 Sửa cửa hàng
function openUpdateModal(maCH) {
  const ch = allCuaHang.find(x => x.MaCH === maCH);
  if (!ch) return alert("Không tìm thấy cửa hàng!");

  document.getElementById("txtMaCH").value = ch.MaCH;
  document.getElementById("txtTenCH").value = ch.TenCH;
  document.getElementById("txtDiaChi").value = ch.DiaChi;
  document.getElementById("txtSDT").value = ch.SoDienThoai;

  document.getElementById("updateModal").style.display = "flex";
}

function closeUpdateModal() {
  document.getElementById("updateModal").style.display = "none";
}

async function saveUpdate() {
  const maCH = document.getElementById("txtMaCH").value;
  const tenCH = document.getElementById("txtTenCH").value;
  const diaChi = document.getElementById("txtDiaChi").value;
  const sdt = document.getElementById("txtSDT").value;

  if (!tenCH) return alert("Tên cửa hàng không được để trống!");

  const data = { MaCH: maCH, TenCH: tenCH, DiaChi: diaChi, SoDienThoai: sdt };

  try {
    const res = await fetch("../../API/admin/stores_api.php?action=update", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(data)
    });
    const result = await res.json();
    if (result.status === "success") {
      alert(result.message || "Cập nhật thành công!");
      closeUpdateModal();
      loadCuaHang();
    } else {
      alert(result.message || "Cập nhật thất bại!");
    }
  } catch (err) {
    alert("Lỗi khi cập nhật cửa hàng: " + err.message);
  }
}

// 🔹 Xóa cửa hàng
async function deleteCuaHang(maCH) {
  if (!confirm(`Bạn có chắc muốn xóa cửa hàng ${maCH}?`)) return;

  try {
    const res = await fetch(`../../API/admin/stores_api.php?action=delete&MaCH=${maCH}`);
    const result = await res.json();

    if (result.status === "success") {
      alert("Xóa thành công!");
      const row = document.querySelector(`tr[data-mach='${maCH}']`);
      if (row) row.remove();
      allCuaHang = allCuaHang.filter(c => c.MaCH !== maCH);
    } else {
      alert(result.message || "Không thể xóa cửa hàng.");
    }
  } catch (err) {
    alert("Lỗi khi xóa cửa hàng: " + err.message);
  }
}

// 🔹 Khi trang load
window.addEventListener("DOMContentLoaded", loadCuaHang);