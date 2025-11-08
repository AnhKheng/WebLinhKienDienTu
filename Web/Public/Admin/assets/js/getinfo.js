document.addEventListener("DOMContentLoaded", () => {
  const params = new URLSearchParams(window.location.search);
  const id = params.get("id"); // lấy MaNV từ URL

  if (!id) {
    alert("Không tìm thấy mã nhân viên!");
    return;
  }

  // ===== Lấy các element =====
  const form = document.getElementById("formEditEmployee");
  const MaNV = document.getElementById("MaNV");
  const TenNV = document.getElementById("TenNV");
  const GioiTinh = document.getElementById("GioiTinh");
  const NgaySinh = document.getElementById("NgaySinh");
  const SoDienThoai = document.getElementById("SoDienThoai");
  const MaCH = document.getElementById("MaCH");

  const notifyOverlay = document.getElementById("notifyOverlay");
  const notifyMessage = document.getElementById("notifyMessage");
  const closeNotify = document.getElementById("closeNotify");

  // ===== LẤY THÔNG TIN NHÂN VIÊN =====
  fetch(`../../API/admin/employee_api.php?action=getOne&MaNV=${id}`)
    .then(res => res.json())
    .then(data => {
      if (data.status === "success" && data.data) {
        const emp = data.data;
        MaNV.value = emp.MaNV || "";
        TenNV.value = emp.TenNV || "";
        GioiTinh.value = emp.GioiTinh || "Nam";
        NgaySinh.value = emp.NgaySinh || "";
        SoDienThoai.value = emp.SoDienThoai || "";
        MaCH.value = emp.MaCH || "";
      } else {
        alert("Không tìm thấy nhân viên!");
      }
    })
    .catch(err => {
      console.error("Lỗi khi tải dữ liệu:", err);
      alert("Lỗi kết nối đến server!");
    });

  // ===== GỬI CẬP NHẬT =====
  form.addEventListener("submit", e => {
    e.preventDefault();

    // Tạo form data
    const formData = new FormData();
    formData.append("action", "update");
    formData.append("MaNV", MaNV.value);
    formData.append("TenNV", TenNV.value);
    formData.append("GioiTinh", GioiTinh.value);
    formData.append("NgaySinh", NgaySinh.value);
    formData.append("SoDienThoai", SoDienThoai.value);
    formData.append("MaCH", MaCH.value);

    fetch("../../API/admin/employee_api.php", {
      method: "POST",
      body: formData
    })
      .then(res => res.json())
      .then(data => {
        notifyMessage.textContent = data.message || "Không có phản hồi từ server!";
        notifyMessage.style.color = data.status === "success" ? "green" : "red";
        notifyOverlay.style.display = "flex";
      })
      .catch(err => {
        console.error("Lỗi khi cập nhật:", err);
        notifyMessage.textContent = "Có lỗi xảy ra khi gửi yêu cầu!";
        notifyMessage.style.color = "red";
        notifyOverlay.style.display = "flex";
      });
  });

  // ===== ĐÓNG THÔNG BÁO =====
  closeNotify.addEventListener("click", () => {
    notifyOverlay.style.display = "none";
  });
});
