let isSearching = false;

// Load 4 sản phẩm cho 1 danh mục + trang
function loadFeaturedProducts(category, page = 1) {
    const slider = document.querySelector(`.product-slider[data-category="${category}"]`);
    if (!slider) return;

    const url = `../../API/client/Product/get_products.php?category=${category}&page=${page}&limit=4`;
    
    fetch(url)
        .then(r => r.text())
        .then(html => {
            slider.innerHTML = html;
        })
        .catch(() => {
            slider.innerHTML = '<p style="color:red;">Lỗi tải</p>';
        });
}

// Cập nhật trang active
function updateActivePage(category, page) {
    document.querySelectorAll(`.page-num[data-category="${category}"]`).forEach(el => {
        el.classList.toggle('active', parseInt(el.dataset.page) === page);
    });
}

// Xử lý click phân trang
document.addEventListener('click', function(e) {
    const target = e.target;

    // Click số trang
    if (target.classList.contains('page-num') && target.dataset.category) {
        const cat = target.dataset.category;
        const page = parseInt(target.dataset.page);
        loadFeaturedProducts(cat, page);
        updateActivePage(cat, page);
    }

    // Click mũi tên
    if (target.classList.contains('page-btn') && target.dataset.category) {
        e.preventDefault();
        const cat = target.dataset.category;
        const current = document.querySelector(`.page-num[data-category="${cat}"].active`);
        let page = current ? parseInt(current.dataset.page) : 1;

        if (target.classList.contains('prev')) page = Math.max(1, page - 1);
        if (target.classList.contains('next')) page = Math.min(5, page + 1);

        if (page >= 1 && page <= 5) {
            loadFeaturedProducts(cat, page);
            updateActivePage(cat, page);
        }
    }
});

// Tìm kiếm
function loadProducts() {
    const category = document.getElementById('categorySelect').value;
    const search = document.getElementById('searchInput').value.trim();
    isSearching = !!(category || search);

    const results = document.getElementById('searchResults');
    const featured = document.querySelector('.featured-section-v3');

    if (isSearching) {
        featured.style.display = 'none';
        results.style.display = 'block';
        const url = `../../API/client/Product/get_products.php?category=${encodeURIComponent(category)}&search=${encodeURIComponent(search)}`;
        fetch(url).then(r => r.text()).then(html => {
            document.getElementById('productContainer').innerHTML = html;
        });
    } else {
        featured.style.display = 'block';
        results.style.display = 'none';
        // Load trang 1 cho tất cả danh mục
        ['DM01', 'DM05', 'DM12', 'DM03'].forEach(cat => loadFeaturedProducts(cat, 1));
    }
}

// Khởi động
document.addEventListener('DOMContentLoaded', function() {
    // Load trang 1 cho tất cả
    ['DM01', 'DM05', 'DM12', 'DM03'].forEach(cat => loadFeaturedProducts(cat, 1));

    // Sự kiện tìm kiếm
    document.getElementById('searchBtn').addEventListener('click', loadProducts);
    document.getElementById('categorySelect').addEventListener('change', loadProducts);
    document.getElementById('searchInput').addEventListener('keypress', e => {
        if (e.key === 'Enter') loadProducts();
    });
});