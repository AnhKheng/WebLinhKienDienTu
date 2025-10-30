<div class="clear">
</div>
<div class="main-content">
  <?php
    $temp = isset($_GET['action']) ? $_GET['action'] : $temp=" ";

    switch ($temp) {
      case 'Products':
        include "modules/Products/Products.html";
        break;
      case 'Add-Product':
        include "modules/Products/Add.html";
        break;
      case 'Category':
        include "modules/Category/View.html";
        break;
      case 'Invoice':
        include "modules/invoice/invoice.html";
        break;
      default:
        include "modules/dashboard.php";
        break;
    }

  ?>
</div>
