<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<aside class="sidebar" id="sidebar">
    <div class="sidebar-avatar">
      <img src="assets/img/logo.png" alt="Admin Avatar">
    </div>

    <ul class="menu">
      <li>
        <a href="#" class="menu-link">
          <i class="fas fa-tachometer-alt"></i>
          <span>Bảng điều khiển</span>
        </a>
      </li>

      <li class="menu-item has-sub">
        <button class="menu-toggle">
          <i class="fas fa-box"></i>
          <span>Sản phẩm</span>
        </button>
        <ul class="submenu">
          <li><a href="index.php?module=Products&page=View"><i class="fas fa-list"></i> Danh sách sản phẩm</a></li>
          <li><a href="index.php?module=Products&page=Add"><i class="fas fa-plus"></i> Thêm sản phẩm</a></li>
          <li><a href="index.php?module=Category&page=View"><i class="fas fa-tags"></i> Quản lý danh mục</a></li>
        </ul>
      </li>

      <li class="menu-item has-sub">
        <button class="menu-toggle">
          <i class="fas fa-file-invoice"></i>
          <span>Hóa đơn</span>
        </button>
        <ul class="submenu">
          <li><a href="index.php?module=invoice&page=invoice"><i class="fas fa-list"></i> Danh sách hóa đơn</a></li>
          <li><a href="index.php?module=invoice&page=invoice_add"><i class="fas fa-plus"></i> Thêm hóa đơn</a></li>
        </ul>
      </li>
      <li class="menu-item has-sub">
        <button class="menu-toggle">
          <i class="fas fa-file-invoice-dollar"></i>  
          <span>Nhập Hàng</span>
        </button>
        <ul class="submenu">
          <li><a href="index.php?module=Supplier&page=View"><i class="fa-solid fa-building"></i> Nhà cung cấp</a></li>
          <li><a href="index.php?module=Import&page=View"><i class="fa-solid fa-boxes-packing"></i> Hóa đơn nhập</a></li>
          <li><a href="index.php?module=Import&page=Add"><i class="fa-solid fa-file-pen"></i> Thêm phiếu nhập</a></li>
        </ul>
      </li>
      <li class="menu-item has-sub">
        <button class="menu-toggle">
          <i class="fas fa-file-invoice-dollar"></i>  
          <span>Kho hàng</span>
        </button>
        <ul class="submenu">
          <li><a href="index.php?module=Inventory&page=ViewAll"><i class="fa-solid fa-building"></i> Kho hàng tổng</a></li>
          <li><a href="index.php?module=Inventory&page=ViewOne"><i class="fa-solid fa-boxes-packing"></i> Kho hàng</a></li>
        </ul>
      </li>
      <li class="menu-item has-sub">
        <button class="menu-toggle">
          <i class="fas fa-file-invoice-dollar"></i>  
          <span>Nhân viên</span>
        </button>
        <ul class="submenu">
          <li><a href="index.php?module=Employee&page=ViewAll"><i class="fa-solid fa-building"></i> Quản lý nhân viên</a></li>
          <li><a href="index.php?module=Employee&page=AddAdmin"><i class="fa-solid fa-building"></i> Thêm nhân viên</a></li>
          <li><a href="index.php?module=Employee&page=ViewOne"><i class="fa-solid fa-boxes-packing"></i> Quản lý nhân viên</a></li>
          <li><a href="index.php?module=Employee&page=Add"><i class="fa-solid fa-building"></i> Thêm nhân viên</a></li>
        </ul>
      </li>

      <li>
        <a href="#" class="menu-link">
          <i class="fas fa-user"></i>
          <span>Tài khoản</span>
        </a>
      </li>
    </ul>
  </aside>