let currentPages = {}; // Lưu trang hiện tại cho mỗi category

// Load sản phẩm nổi bật
function loadFeaturedProducts(category, page = 1) {
    const slider = document.querySelector(`.product-slider[data-category="${category}"]`);
    const pagination = document.querySelector(`.pagination-mini[data-category="${category}"]`);
    if (!slider || !pagination) return;

    const url = `../../API/client/Product/get_products.php?category=${category}&page=${page}&featured=1`;

    fetch(url)
        .then(r => r.json())
        .then(data => {
            if (data.error) {
                slider.innerHTML = '<p style="color:red;">Lỗi tải</p>';
                return;
            }
            // Render đúng trường html, không render cả object JSON
            if (typeof data.html === "string") {
                slider.innerHTML = data.html;
            } else {
                slider.innerHTML = '<p style="color:red;">Không có dữ liệu sản phẩm.</p>';
            }
            currentPages[category] = data.currentPage;
            renderPagination(pagination, category, data.currentPage, data.totalPages);
        })
        .catch(() => {
            slider.innerHTML = '<p style="color:red;">Lỗi tải</p>';
        });
}

// Tạo HTML phân trang động
function renderPagination(container, category, currentPage, totalPages) {
    container.innerHTML = '';

    // Nút Prev
    const prevBtn = document.createElement('button');
    prevBtn.className = 'page-btn prev';
    prevBtn.textContent = '<';
    prevBtn.dataset.category = category;
    prevBtn.disabled = currentPage <= 1;
    container.appendChild(prevBtn);

    // Các số trang
    const pageNumbers = document.createElement('span');
    pageNumbers.className = 'page-numbers';

    for (let i = 1; i <= totalPages; i++) {
        const pageNum = document.createElement('span');
        pageNum.className = 'page-num';
        pageNum.textContent = i;
        pageNum.dataset.category = category;
        pageNum.dataset.page = i;
        if (i === currentPage) pageNum.classList.add('active');
        pageNumbers.appendChild(pageNum);
    }
    container.appendChild(pageNumbers);

    // Nút Next
    const nextBtn = document.createElement('button');
    nextBtn.className = 'page-btn next';
    nextBtn.textContent = '>';
    nextBtn.dataset.category = category;
    nextBtn.disabled = currentPage >= totalPages;
    container.appendChild(nextBtn);
}

// Xử lý click phân trang
document.addEventListener('click', function(e) {
    const target = e.target.closest('.page-num, .page-btn');
    if (!target || !target.dataset.category) return;

    const cat = target.dataset.category;
    let page = parseInt(target.dataset.page) || currentPages[cat] || 1;

    if (target.classList.contains('prev')) page = Math.max(1, page - 1);
    if (target.classList.contains('next')) page = page + 1;

    loadFeaturedProducts(cat, page);
});

// Tìm kiếm (giữ nguyên, nhưng dùng JSON)
function loadProducts() {
    const category = document.getElementById('categorySelect').value;
    const search = document.getElementById('searchInput').value.trim();
    const isSearching = !!(category || search);

    const results = document.getElementById('searchResults');
    const featured = document.querySelector('.featured-section-v3');

    if (isSearching) {
        featured.style.display = 'none';
        results.style.display = 'block';
        const url = `../../API/client/Product/get_products.php?category=${encodeURIComponent(category)}&search=${encodeURIComponent(search)}`;
        fetch(url)
            .then(r => r.json())
            .then(data => {
                if (typeof data.html === "string") {
                    document.getElementById('productContainer').innerHTML = data.html;
                } else {
                    document.getElementById('productContainer').innerHTML = '<p>Không có sản phẩm.</p>';
                }
            });
    } else {
        featured.style.display = 'block';
        results.style.display = 'none';
        ['DM01', 'DM05', 'DM12', 'DM03'].forEach(cat => {
            currentPages[cat] = 1;
            loadFeaturedProducts(cat, 1);
        });
    }
}

// Khởi động
document.addEventListener('DOMContentLoaded', function() {
    ['DM01', 'DM05', 'DM12', 'DM03'].forEach(cat => {
        currentPages[cat] = 1;
        loadFeaturedProducts(cat, 1);
    });

    document.getElementById('searchBtn').addEventListener('click', loadProducts);
    document.getElementById('categorySelect').addEventListener('change', loadProducts);
    document.getElementById('searchInput').addEventListener('keypress', e => {
        if (e.key === 'Enter') loadProducts();
    });
});