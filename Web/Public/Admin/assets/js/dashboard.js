// ====== ĐƯỜNG DẪN API ======
const API_URL = "../../API/admin/dashboard_api.php?action=get_dashboard";

// ====== HÀM GỌI API LẤY DỮ LIỆU DASHBOARD ======
async function loadDashboard() {
  try {
    const response = await fetch(API_URL, { cache: "no-store" });
    const data = await response.json();

    if (!data || data.success !== true) {
      console.error("API trả về lỗi:", data ? data.message : "Không nhận được JSON");
      // show a friendly message on UI (optional)
      document.getElementById("BieuDoDanhThuThang").innerText = "Không lấy được dữ liệu dashboard.";
      return;
    }

    const d = data.data || {};
    animateNumber("countProduct", Number(d.countProduct) || 0);
    animateNumber("countCustomer", Number(d.countCustomer) || 0);
    animateNumber("countInvoice", Number(d.countInvoice) || 0);
    animateNumber("totalToday", Number(d.totalToday) || 0, true);

    if (Array.isArray(d.revenueByMonth) && d.revenueByMonth.length > 0) {
      renderRevenueChart(d.revenueByMonth);
    } else {
      document.getElementById("BieuDoDanhThuThang").innerText = "Không có dữ liệu doanh thu.";
    }

  } catch (err) {
    console.error("Lỗi khi gọi API:", err);
    document.getElementById("BieuDoDanhThuThang").innerText = "Lỗi khi tải dữ liệu.";
  }
}

// ====== HÀM HIỆU ỨNG ĐẾM SỐ ======
function animateNumber(elementId, value, isCurrency = false) {
  const el = document.getElementById(elementId);
  if (!el) return;

  const duration = 1000; // thời gian chạy (ms)
  const frameRate = 30; // số khung hình/giây
  const totalFrames = Math.round((duration / 1000) * frameRate);
  let frame = 0;
  const start = 0;
  value = Number(value) || 0;

  const counter = setInterval(() => {
    frame++;
    const progress = frame / totalFrames;
    const current = Math.round(start + (value - start) * progress);
    el.textContent = isCurrency ? formatCurrency(current) : current.toLocaleString('vi-VN');

    if (frame >= totalFrames) {
      clearInterval(counter);
      // ensure final value is exact
      el.textContent = isCurrency ? formatCurrency(value) : value.toLocaleString('vi-VN');
    }
  }, 1000 / frameRate);
}

// ====== HÀM ĐỊNH DẠNG TIỀN ======
function formatCurrency(num) {
  // đảm bảo num là number
  const n = Number(num) || 0;
  return n.toLocaleString("vi-VN", { style: "currency", currency: "VND" });
}

// ====== HÀM VẼ BIỂU ĐỒ DOANH THU ======
function renderRevenueChart(data) {
  const container = document.getElementById("BieuDoDanhThuThang");
  container.innerHTML = '<canvas id="revenueChart"></canvas>';

  // ensure Chart.js is loaded
  if (typeof Chart === "undefined") {
    container.innerText = "Chart.js chưa được nạp. Vui lòng thêm <script src=\"https://cdn.jsdelivr.net/npm/chart.js\"></script> vào trang.";
    return;
  }

  const ctx = document.getElementById("revenueChart").getContext("2d");

  const labels = data.map((item) => `${item.month}/${item.year}`);
  const values = data.map((item) => Number(item.total) || 0);

  new Chart(ctx, {
    type: "bar",
    data: {
      labels: labels,
      datasets: [
        {
          label: "Doanh thu (VNĐ)",
          data: values,
          // nếu muốn custom màu: đặt ở đây; giữ mặc định nếu cần
          backgroundColor: "rgba(52, 152, 219, 0.7)",
          borderColor: "rgba(41, 128, 185, 1)",
          borderWidth: 1,
        },
      ],
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: function (value) {
              return value.toLocaleString("vi-VN") + " ₫";
            },
          },
        },
      },
    },
  });
}

// ====== CHẠY SAU KHI TẢI TRANG ======
document.addEventListener("DOMContentLoaded", loadDashboard);
