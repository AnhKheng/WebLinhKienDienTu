ecommerce-php/
│
├── api/                               # Lớp Controller (Xử lý logic và RESTful API)
│   ├── client/
│   │   ├── cart/
│   │   │   ├── add.php
│   │   │   ├── view.php
│   │   │   └── delete.php
│   │   ├── order/
│   │   │   ├── create.php
│   │   │   ├── cancel.php
│   │   │   └── status.php
│   │   ├── product/
│   │   │   ├── list.php
│   │   │   └── detail.php
│   │   ├── user/
│   │   │   ├── login.php
│   │   │   ├── register.php
│   │   │   └── profile.php
│   │   ├── checkout/
│   │   │   ├── cod.php
│   │   │   └── online.php
│   │   └── contact/send.php
│   │
│   ├── admin/
│   │   ├── dashboard/stats.php
│   │   ├── product/
│   │   │   ├── add.php
│   │   │   ├── update.php
│   │   │   └── delete.php
│   │   ├── order/manage.php
│   │   ├── customer/manage.php
│   │   ├── staff/manage.php
│   │   ├── supplier/manage.php
│   │   └── warehouse/manage.php
│   │
│   └── config.php                     # File cấu hình API (database, JWT, headers, ...)
│
│
├── includes/                          # Lớp Model (Nghiệp vụ, truy vấn DB)
│   ├── db.php                         # Kết nối Database
│   ├── Product.php
│   ├── Order.php
│   ├── User.php
│   ├── Cart.php
│   ├── Supplier.php
│   ├── Warehouse.php
│   └── Report.php
│
│
├── public/                            # Lớp View (Giao diện hiển thị)
│   ├── assets/
│   │   ├── css/
│   │   │   ├── client/
│   │   │   │   ├── style.css
│   │   │   │   ├── product.css
│   │   │   │   ├── cart.css
│   │   │   │   └── responsive.css
│   │   │   │
│   │   │   └── admin/
│   │   │       ├── dashboard.css
│   │   │       ├── sidebar.css
│   │   │       ├── forms.css
│   │   │       └── charts.css
│   │   │
│   │   ├── js/
│   │   │   ├── client/
│   │   │   │   ├── main.js
│   │   │   │   ├── cart.js
│   │   │   │   └── product.js
│   │   │   │
│   │   │   └── admin/
│   │   │       ├── dashboard.js
│   │   │       ├── charts.js
│   │   │       └── validation.js
│   │   │
│   │   └── images/
│   │       ├── client/
│   │       └── admin/
│   │
│   │
│   ├── client/                        # Giao diện người dùng
│   │   ├── includes/
│   │   │   ├── header.html
│   │   │   ├── footer.html
│   │   │   └── navbar.html
│   │   ├── index.html
│   │   ├── product.html
│   │   ├── cart.html
│   │   ├── checkout.html
│   │   ├── login.html
│   │   └── profile.html
│   │
│   └── admin/                         # Giao diện quản trị
│       ├── includes/
│       │   ├── header.html
│       │   ├── sidebar.html
│       │   └── footer.html
│       ├── dashboard.html
│       ├── products.html
│       ├── orders.html
│       ├── customers.html
│       ├── staff.html
│       ├── suppliers.html
│       └── reports.html
│
│
└── README.md

