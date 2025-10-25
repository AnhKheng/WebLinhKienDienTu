ecommerce-php/
│
├── api/                             # Lớp Controller (API)
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
│   └── config.php
│
├── includes/                        # Lớp Model (business & data)
│   ├── db.php
│   ├── Product.php
│   ├── Order.php
│   ├── User.php
│   ├── Cart.php
│   ├── Supplier.php
│   ├── Warehouse.php
│   └── Report.php
│
├── public/                          # Lớp View (frontend HTML/JS)
│   ├── index.html                   # Home page
│   ├── product.html
│   ├── cart.html
│   ├── checkout.html
│   ├── login.html
│   ├── profile.html
│   ├── admin/
│   │   ├── dashboard.html
│   │   ├── products.html
│   │   ├── orders.html
│   │   ├── customers.html
│   │   └── reports.html
│   └── assets/
│       ├── css/
│       ├── js/
│       └── images/
│
└── README.md
