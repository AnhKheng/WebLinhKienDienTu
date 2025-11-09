

async function loadStores() {
  try {
    const res = await fetch("../../API/admin/stores_api.php?action=getAll");
    const data = await res.json();

    if (data.status === "success" && Array.isArray(data.data)) {
      const select = document.getElementById("cuahangFilter");
      select.innerHTML = `<option value="">-- Chọn cửa hàng --</option>`;
      data.data.forEach(kh => {
        select.innerHTML += `<option value="${kh.MaCH}">${kh.TenCH} (${kh.MaCH})</option>`;
      });
    } else {
      console.error("Không tải được danh sách cửa hàng");
    }
  } catch (error) {
    console.error("Lỗi loadStores:", error);
  }
}


// 🔹 Lọc hóa đơn theo cửa hàng
async function applyFilter() {
  const selected = document.getElementById("cuahangFilter").value;
  let tu = document.getElementById("tungay").value;
  let den = document.getElementById("denngay").value;

  if (!tu || !den) {
    tu = "2025-01-01";
    den = "2025-12-31";
  }

  if (selected === "all" || selected === "") {
    alert("Vui lòng chọn cửa hàng");
    return;
  }

  try {
    const url = `../../API/admin/statistical_api.php?action=getStatistical&MaCH=${selected}&tungay=${tu}&denngay=${den}`;
    const res = await fetch(url);
    const data = await res.json();

    console.log("Dữ liệu trả về từ API:", data);

    if (data.status === "success" && Array.isArray(data.data)) {
      renderTable(data.data);
      renderChart(data.data);
    } else {
      alert("Không có dữ liệu thống kê phù hợp.");
    }
  } catch (err) {
    console.error("Lỗi khi load thống kê:", err);
  }
}


function renderTable(data) {
  const tbody = document.querySelector("table tbody");
  tbody.innerHTML = "";

  if (!Array.isArray(data)) return;

  data.forEach(row => {
    const tr = document.createElement("tr");
    tr.innerHTML = `
      <td>${row.Thang}</td>
      <td>${Number(row.TongDoanhThu).toLocaleString()}₫</td>
      <td>${Number(row.LoiNhuan).toLocaleString()}₫</td>
      <td>${row.SoHoaDon}</td>
    `;
    tbody.appendChild(tr);
  });
}

let chart;
function renderChart(data) {
  const ctx = document.getElementById("chartDoanhThu").getContext("2d");
  const labels = data.map(d => `Tháng ${d.Thang}`);
  const doanhThu = data.map(d => d.TongDoanhThu);
  const loiNhuan = data.map(d => d.LoiNhuan);

  if (chart) chart.destroy();

  chart = new Chart(ctx, {
    type: "bar",
    data: {
      labels,
      datasets: [
        {
          label: "Tổng Doanh Thu",
          data: doanhThu,
          backgroundColor: "rgba(54, 162, 235, 0.7)"
        },
        {
          label: "Lợi Nhuận",
          data: loiNhuan,
          backgroundColor: "rgba(255, 159, 64, 0.7)"
        }
      ]
    },
    options: {
      responsive: true,
      scales: {
        y: { beginAtZero: true }
      }
    }
  });
}

document.addEventListener("DOMContentLoaded", () => {
  loadStores();
  document.querySelector("button.btn-primary").addEventListener("click", applyFilter);
});
