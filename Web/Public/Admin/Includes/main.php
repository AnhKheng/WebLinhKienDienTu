<div class="clear">
</div>
<div class="main-content">
  <div> Welcome to Admin Page </div>
  <?php
    $temp = isset($_GET['action']) ? $_GET['action'] : $temp=" ";

    switch ($temp) {
      case 'ShowProducts':
        include "modules/ShowProducts.php";
        break;
      
      default:
        include "modules/dashboard.php";
        break;
    }

  ?>
</div>
