<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Bảng điều khiển</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link rel="stylesheet" href="assets/css/style_dashboard.css">
</head>
<body>
  <main class="dashboard-container">
    <h1 class="dashboard-title"><i class="fa-solid fa-gauge-high"></i> Bảng điều khiển</h1>

    <!-- Khu vực thống kê nhanh -->
    <section class="stats-cards">
      <div class="card">
        <div class="card-icon bg-blue"><i class="fa-solid fa-box-open"></i></div>
        <div class="card-info">
          <h3>Sản phẩm</h3>
          <p id="countProduct"></p>
        </div>
      </div>

      <div class="card">
        <div class="card-icon bg-green"><i class="fa-solid fa-users"></i></div>
        <div class="card-info">
          <h3>Khách hàng</h3>
          <p id="countCustomer"></p>
        </div>
      </div>

      <div class="card">
        <div class="card-icon bg-orange"><i class="fa-solid fa-file-invoice"></i></div>
        <div class="card-info">
          <h3>Hóa đơn</h3>
          <p id="countInvoice"></p>
        </div>
      </div>

      <div class="card">
        <div class="card-icon bg-red"><i class="fa-solid fa-dollar-sign"></i></div>
        <div class="card-info">
          <h3>Doanh thu hôm nay</h3>
          <p id="totalToday"></p>
        </div>
      </div>
    </section>

    <!-- Khu vực biểu đồ hoặc nội dung chi tiết -->
    <section class="chart-section">
      <h2><i class="fa-solid fa-chart-line"></i> Thống kê doanh thu</h2>
      <div class="chart-placeholder">
        <p id="BieuDoDanhThuThang"></p>
      </div>
    </section>
  </main>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="assets/js/dashboard.js"></script>


</body>
</html>
