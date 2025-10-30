<div class="main-content">
  <?php
    $module = $_GET['module'] ?? 'dashboard'; // Lấy module từ URL
    $page   = $_GET['page'] ?? 'index';       // Lấy page từ URL

    // Tạo đường dẫn gốc
    $basePath = "modules/" . ucfirst($module) . "/" . ucfirst($page);

    // Ưu tiên include file PHP → nếu không có thì thử HTML
    if (file_exists($basePath . ".php")) {
      include $basePath . ".php";
    } elseif (file_exists($basePath . ".html")) {
      include $basePath . ".html";
    } else {
      echo "<h3>Trang không tồn tại!</h3>";
    }
  ?>
</div>
