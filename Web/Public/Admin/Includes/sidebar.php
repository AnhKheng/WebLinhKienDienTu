<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

<aside class="sidebar" id="sidebar">
  <div class="sidebar-avatar">
    <img src="assets/img/logo.png" alt="Admin Avatar">
  </div>

  <ul class="menu">
    <!-- Bảng điều khiển -->
    <li>
      <a href="#" class="menu-link">
        <i class="fa-solid fa-gauge-high"></i>
        <span>Bảng điều khiển</span>
      </a>
    </li>

    <!-- Sản phẩm -->
    <li class="menu-item has-sub">
      <button class="menu-toggle">
        <i class="fa-solid fa-box-open"></i>
        <span>Sản phẩm</span>
      </button>
      <ul class="submenu">
        <li><a href="index.php?module=Products&page=View"><i class="fa-solid fa-list"></i> Danh sách sản phẩm</a></li>
        <li><a href="index.php?module=Products&page=Add"><i class="fa-solid fa-square-plus"></i> Thêm sản phẩm</a></li>
        <li><a href="index.php?module=Category&page=View"><i class="fa-solid fa-tags"></i> Quản lý danh mục</a></li>
      </ul>
    </li>

    <!-- Hóa đơn -->
    <li class="menu-item has-sub">
      <button class="menu-toggle">
        <i class="fa-solid fa-file-invoice"></i>
        <span>Hóa đơn</span>
      </button>
      <ul class="submenu">
        <li><a href="index.php?module=invoice&page=invoice"><i class="fa-solid fa-clipboard-list"></i> Danh sách hóa đơn</a></li>
        <li><a href="index.php?module=invoice&page=invoice_add"><i class="fa-solid fa-circle-plus"></i> Thêm hóa đơn</a></li>
      </ul>
    </li>

    <!-- Nhập hàng -->
    <li class="menu-item has-sub">
      <button class="menu-toggle">
        <i class="fa-solid fa-truck-arrow-right"></i>
        <span>Nhập hàng</span>
      </button>
      <ul class="submenu">
        <li><a href="index.php?module=Supplier&page=View"><i class="fa-solid fa-industry"></i> Nhà cung cấp</a></li>
        <li><a href="index.php?module=Import&page=View"><i class="fa-solid fa-file-invoice"></i> Hóa đơn nhập</a></li>
        <li><a href="index.php?module=Import&page=Add"><i class="fa-solid fa-circle-plus"></i> Thêm phiếu nhập</a></li>
      </ul>
    </li>

    <!-- Kho hàng -->
    <li class="menu-item has-sub">
      <button class="menu-toggle">
        <i class="fa-solid fa-warehouse"></i>
        <span>Kho hàng</span>
      </button>
      <ul class="submenu">
        <li><a href="index.php?module=Inventory&page=ViewAll"><i class="fa-solid fa-boxes-stacked"></i> Kho hàng tổng</a></li>
        <li><a href="index.php?module=Inventory&page=ViewOne"><i class="fa-solid fa-box"></i> Kho hàng</a></li>
      </ul>
    </li>

    <!-- Nhân viên -->
    <li class="menu-item has-sub">
      <button class="menu-toggle">
        <i class="fa-solid fa-user-tie"></i>
        <span>Nhân viên</span>
      </button>
      <ul class="submenu">
        <li><a href="index.php?module=Employee&page=ViewAll"><i class="fa-solid fa-users"></i> Quản lý nhân viên</a></li>
        <li><a href="index.php?module=Employee&page=AddAdmin"><i class="fa-solid fa-user-shield"></i> Thêm quản trị viên</a></li>
        <li><a href="index.php?module=Employee&page=ViewOne"><i class="fa-solid fa-id-card-clip"></i> Chi tiết nhân viên</a></li>
        <li><a href="index.php?module=Employee&page=Add"><i class="fa-solid fa-user-plus"></i> Thêm nhân viên</a></li>
      </ul>
    </li>

    <!-- Khách hàng -->
    <li class="menu-item has-sub">
      <button class="menu-toggle">
        <i class="fa-solid fa-user-group"></i>
        <span>Khách hàng</span>
      </button>
      <ul class="submenu">
        <li><a href="index.php?module=Customer&page=View"><i class="fa-solid fa-address-book"></i> Quản lý khách hàng</a></li>
        <li><a href="index.php?module=Customer&page=Add"><i class="fa-solid fa-user-plus"></i> Thêm khách hàng</a></li>
      </ul>
    </li>

    <!-- Tài khoản -->
    <li class="menu-item has-sub">
      <button class="menu-toggle">
        <i class="fa-solid fa-shield-halved"></i>
        <span>Tài khoản</span>
      </button>
      <ul class="submenu">
        <li><a href="index.php?module=User&page=View"><i class="fa-solid fa-users-gear"></i> Quản lý tài khoản</a></li>
      </ul>
    </li>

    <!-- Báo cáo thống kê -->
    <li class="menu-item has-sub">
      <button class="menu-toggle">
        <i class="fa-solid fa-chart-pie"></i>
        <span>Báo cáo thống kê</span>
      </button>
      <ul class="submenu">
        <li><a href="index.php?module=Statistical&page=Statistical"><i class="fa-solid fa-chart-line"></i> Báo cáo doanh thu</a></li>
        <li><a href="index.php?module=Statistical&page=Inventory_report"><i class="fa-solid fa-box-archive"></i> Báo cáo tồn kho</a></li>
      </ul>
    </li>

    <!-- Quản lý cửa hàng -->
    <li class="menu-item has-sub">
      <button class="menu-toggle">
        <i class="fa-solid fa-store"></i>
        <span>Quản lý cửa hàng</span>
      </button>
      <ul class="submenu">
        <li><a href="index.php?module=Store&page=Store"><i class="fa-solid fa-building"></i> Cửa hàng</a></li>
      </ul>
    </li>
  </ul>
</aside>
