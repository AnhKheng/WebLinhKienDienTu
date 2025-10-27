    <!DOCTYPE html>
    <html lang="vi">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title> Admin AguTech</title>
        <link href="assets/css/styleAdmin.css" rel="stylesheet" text="text/css"/>
        
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
                <dev class="main-content">
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
    </body>
    </html>