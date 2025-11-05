    <!DOCTYPE html>
    <html lang="vi">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title> Admin AguTech</title>
        <link href="assets/css/styleAdmin.css" rel="stylesheet" text="text/css"/>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        
    </head>
    <body>
        <div class="container">
            <div class="sidebar">
                <?php
                    include("Includes/sidebar.php");
                ?>
            </div>

            <div class="main-wrapper">
                <div class="header">
                    <?php
                    include("Includes/header.php");
                    ?>
                </div>
                <div class="main-content">
                    <?php
                    include("Includes/main.php");
                    ?>
                </div>
            </div>
        </div>
        <div class="footer">
            <?php
                include("Includes/footer.php");
            ?>
        </div>
        <script src="assets/js/sidebar_toggle.js"></script>
        <script src="assets/js/admin.js"></script>
        <!-- <script>src="assets/js/product.js"</script> -->
    </body>
    </html>