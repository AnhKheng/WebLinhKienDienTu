<div class="clear">
</div>
<div class="main-content">
  <?php
    $temp = isset($_GET['action']) ? $_GET['action'] : $temp=" ";

    switch ($temp) {
      case 'Products':
        include "modules/Products/Products.html";
        break;
      
      default:
        include "modules/dashboard.php";
        break;
    }

  ?>
</div>
