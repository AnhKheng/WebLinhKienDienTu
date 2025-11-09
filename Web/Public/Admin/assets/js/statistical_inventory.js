
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

let pieChart;

function renderInventory(data) {
  const tbody = document.getElementById('inventoryBody');
  tbody.innerHTML = '';

  let totalItems = data.length;
  let totalQty = 0;
  let totalValue = 0;

  data.forEach(item => {
    const value = item.cost * item.qty;
    totalQty += item.qty;
    totalValue += value;

    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${item.sku}</td>
      <td>${item.name}</td>
      <td>${item.category}</td>
      <td>${Number(item.cost).toLocaleString()}₫</td>
      <td>${item.qty}</td>
      <td>${Number(value).toLocaleString()}₫</td>
    `;
    tbody.appendChild(tr);
  });

  document.getElementById('totalItems').textContent = totalItems;
  document.getElementById('totalQty').textContent = totalQty;
  document.getElementById('totalValue').textContent = totalValue.toLocaleString() + '₫';

  renderPie(data);
}

function renderPie(data) {
  // Tính giá trị theo sản phẩm, lấy top 8, còn lại gộp 'Khác'
  const byProduct = data.map(it => ({ label: it.name, value: it.cost * it.qty }));
  byProduct.sort((a,b) => b.value - a.value);

  const top = byProduct.slice(0,8);
  const others = byProduct.slice(8);
  const othersValue = others.reduce((s,c) => s + c.value, 0);
  if (othersValue > 0) top.push({ label: 'Khác', value: othersValue });

  const labels = top.map(i => i.label);
  const values = top.map(i => i.value);

  const ctx = document.getElementById('inventoryPie').getContext('2d');
  if (pieChart) pieChart.destroy();

  pieChart = new Chart(ctx, {
    type: 'pie',
    data: {
      labels: labels,
      datasets: [{
        data: values,
        // Chart.js will pick default colors if not specified
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { position: 'right' },
        tooltip: {
          callbacks: {
            label: function(context) {
              const v = context.raw;
              return context.label + ': ' + v.toLocaleString() + '₫';
            }
          }
        }
      }
    }
  });
}
async function loadInventory() {
  const selected = document.getElementById("cuahangFilter").value;
  if (!selected) {
    alert("Vui lòng chọn cửa hàng để xem tồn kho");
    return;
  }

  try {
    const res = await fetch(`../../API/admin/statistical_api.php?action=getInventory&MaCH=${selected}`);
    const data = await res.json();

    if (data.status === "success" && Array.isArray(data.data)) {
      renderInventory(data.data);
    } else {
      alert("Không có dữ liệu tồn kho cho cửa hàng này.");
    }
  } catch (error) {
    console.error("Lỗi khi tải tồn kho:", error);
  }
}



// --- On load ---
document.addEventListener("DOMContentLoaded", () => {
  loadStores();
 
});