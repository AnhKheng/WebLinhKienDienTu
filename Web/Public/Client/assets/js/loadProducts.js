let currentPages = {}; // Lưu trang hiện tại cho mỗi category
let currentSearchPage = 1; // Biến mới: lưu trang tìm kiếm

/**
 * Helper: Định dạng tiền tệ
 */
function formatCurrency(number) {
    if (isNaN(number)) return '0₫';
    return new Intl.NumberFormat('vi-VN').format(number) + '₫';
}

/**
 * Helper: Tạo HTML cho một thẻ sản phẩm (Dùng thẻ <a>)
 */
function createProductCardHTML(product) {
    const hinh = product.HinhAnh 
        ? `../img/${product.HinhAnh}` 
        : '../img/default_product.png';
        
    return `
    <div class="product-card">
        <img src="${hinh}" 
             alt="${product.TenSP}" 
             loading="lazy"
             onerror="this.src='../img/default_product.png'">
        <h3>${product.TenSP}</h3>
        <p class="price">${formatCurrency(product.DonGia)}</p>
        <a href="Index.php?do=Details&id=${product.MaSP}" class="btn-detail">
            Chi tiết sản phẩm
        </a>
    </div>`;
}

// Load sản phẩm nổi bật
function loadFeaturedProducts(category, page = 1) {
    const slider = document.querySelector(`.product-slider[data-category="${category}"]`);
    const pagination = document.querySelector(`.pagination-mini[data-category="${category}"]`);
    if (!slider || !pagination) return;

    // Thay đổi: Thêm page vào API
    const url = `../../API/client/Product/get_products.php?category=${category}&page=${page}&featured=1`;

    fetch(url)
        .then(r => r.json())
        .then(data => {
            if (data.error) {
                slider.innerHTML = '<p style="color:red;">Lỗi tải</p>';
                return;
            }
            
            if (data.products && data.products.length > 0) {
                let html = '<div class="product-slider">';
                data.products.forEach(product => {
                    html += createProductCardHTML(product);
                });
                html += '</div>';
                slider.innerHTML = html;
            } else {
                slider.innerHTML = '<p style="color:red;">Không có dữ liệu sản phẩm.</p>';
            }

            currentPages[category] = data.currentPage;
            // Sửa: Truyền container là pagination-mini
            renderPagination(pagination, category, data.currentPage, data.totalPages, 'mini');
        })
        .catch(() => {
            slider.innerHTML = '<p style="color:red;">Lỗi tải</p>';
        });
}

/**
 * HÀM SỬA ĐỔI: renderPagination
 * Thêm tham số 'type' ('mini' hoặc 'main')
 */
function renderPagination(container, category, currentPage, totalPages, type = 'mini') {
    container.innerHTML = '';
    if (totalPages <= 1) return; // Không hiển thị nếu chỉ có 1 trang

    // Nút Prev (Dùng mũi tên)
    const prevBtn = document.createElement('button');
    prevBtn.className = 'page-btn prev';
    prevBtn.innerHTML = '←'; // Dùng mũi tên
    prevBtn.dataset.category = category;
    prevBtn.dataset.type = type;
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
        pageNum.dataset.type = type;
        if (i === currentPage) pageNum.classList.add('active');
        pageNumbers.appendChild(pageNum);
    }
    container.appendChild(pageNumbers);

    // Nút Next (Dùng mũi tên)
    const nextBtn = document.createElement('button');
    nextBtn.className = 'page-btn next';
    nextBtn.innerHTML = '→'; // Dùng mũi tên
    nextBtn.dataset.category = category;
    nextBtn.dataset.type = type;
    nextBtn.disabled = currentPage >= totalPages;
    container.appendChild(nextBtn);
}

/**
 * HÀM SỬA ĐỔI: Xử lý click phân trang
 * Xử lý cả 'mini' và 'main'
 */
document.addEventListener('click', function(e) {
    const target = e.target.closest('.page-num, .page-btn');
    if (!target) return;

    const cat = target.dataset.category; // 'DM01' (mini) hoặc 'search' (main)
    const type = target.dataset.type; // 'mini' hoặc 'main'

    if (type === 'mini') {
        // Xử lý pagination mini (Nổi bật)
        let page = parseInt(target.dataset.page) || currentPages[cat] || 1;
        if (target.classList.contains('prev')) page = Math.max(1, page - 1);
        if (target.classList.contains('next')) page = page + 1;
        loadFeaturedProducts(cat, page);

    } else if (type === 'main') {
        // Xử lý pagination main (Tìm kiếm)
        let page = parseInt(target.dataset.page) || currentSearchPage || 1;
        if (target.classList.contains('prev')) page = Math.max(1, page - 1);
        if (target.classList.contains('next')) page = page + 1;
        
        // Chỉ cần fetch lại kết quả, không cần reload trang
        const params = getUrlParams();
        const categoryValue = params.category || '';
        const searchValue = params.search || '';
        displaySearchResults(categoryValue, searchValue, page);
        
        // Cuộn lên đầu #searchResults
        const searchResults = document.getElementById('searchResults');
        if (searchResults) {
            searchResults.scrollIntoView({ behavior: 'smooth' });
        }
    }
});

// --- PHẦN TÌM KIẾM (ĐÃ CẬP NHẬT LOGIC) ---

/**
 * HÀM SỬA ĐỔI: Lấy tham số từ URL
 * Thêm tham số 'page'
 */
function getUrlParams() {
    const params = new URLSearchParams(window.location.search);
    return {
        category: params.get('category'), 
        search: params.get('search'),
        page: parseInt(params.get('page')) || 1, // Lấy trang từ URL
        hasCategory: params.has('category'),
        hasSearch: params.has('search')
    };
}

/**
 * HÀM SỬA ĐỔI: Hiển thị kết quả TÌM KIẾM
 * Thêm tham số 'page'
 */
function displaySearchResults(category, search, page = 1) {
    currentSearchPage = page; // Cập nhật trang tìm kiếm hiện tại

    const results = document.getElementById('searchResults');
    const featured = document.querySelector('.featured-section-v3');

    if (featured) featured.style.display = 'none';
    if (results) results.style.display = 'block';

    // Thêm page vào API
    const url = `../../API/client/Product/get_products.php?category=${encodeURIComponent(category)}&search=${encodeURIComponent(search)}&page=${page}`;
    
    fetch(url)
        .then(r => r.json())
        .then(data => {
            const container = document.getElementById('productContainer');
            const paginationContainer = document.getElementById('paginationContainer');
            if (!container || !paginationContainer) return;

            // 1. Render sản phẩm
            if (data.products && data.products.length > 0) {
                let html = '<div class="product-grid">';
                data.products.forEach(product => {
                    html += createProductCardHTML(product);
                });
                html += '</div>';
                container.innerHTML = html;
            } else {
                container.innerHTML = '<div class="product-grid"><p style="grid-column: 1/-1; text-align:center; color:#999; padding:20px;">Không tìm thấy sản phẩm.</p></div>';
            }
            
            // 2. Render thanh phân trang CHÍNH
            renderPagination(paginationContainer, 'search', data.currentPage, data.totalPages, 'main');
        });
}

/**
 * HÀM SỬA ĐỔI: Quyết định tải trang
 * Lấy 'page' từ URL
 */
function initialPageLoad() {
    const params = getUrlParams();
    const isSearching = params.hasCategory || params.hasSearch;

    const categoryValue = params.category || '';
    const searchValue = params.search || '';
    const pageValue = params.page; // Lấy trang

    if (isSearching) {
        document.getElementById('categorySelect').value = categoryValue;
        document.getElementById('searchInput').value = searchValue;
        
        // Hiển thị kết quả tìm kiếm DỰA TRÊN TRANG TỪ URL
        displaySearchResults(categoryValue, searchValue, pageValue);

    } else {
        // Trang Nổi bật (mặc định)
        document.getElementById('categorySelect').value = 'featured'; 
        document.getElementById('searchInput').value = '';

        const featured = document.querySelector('.featured-section-v3');
        const results = document.getElementById('searchResults');
        if (featured) featured.style.display = 'block';
        if (results) results.style.display = 'none';

        ['DM01', 'DM05', 'DM12', 'DM03'].forEach(cat => {
            currentPages[cat] = 1;
            loadFeaturedProducts(cat, 1);
        });
    }
}

// Khởi động
document.addEventListener('DOMContentLoaded', function() {
    
    // 1. Gắn sự kiện cho các nút tìm kiếm
    const searchBtn = document.getElementById('searchBtn');
    const categorySelect = document.getElementById('categorySelect');
    const searchInput = document.getElementById('searchInput');

    if (searchBtn && categorySelect && searchInput) {
        
        // HÀNH ĐỘNG 1: Bấm nút Search hoặc Enter
        function standardSearch() {
            let category = categorySelect.value;
            const search = searchInput.value.trim();
            if (category === 'featured') {
                category = ''; 
            }
            // Luôn reset về trang 1 khi tìm kiếm mới
            window.location.href = `Index.php?do=Home&category=${encodeURIComponent(category)}&search=${encodeURIComponent(search)}&page=1`;
        }

        searchBtn.addEventListener('click', standardSearch);
        searchInput.addEventListener('keypress', e => {
            if (e.key === 'Enter') standardSearch();
        });

        // HÀNH ĐỘNG 2: Thay đổi danh mục (dropdown)
        categorySelect.addEventListener('change', function() {
            const category = categorySelect.value;
            searchInput.value = ''; 
            const search = '';

            if (category === 'featured') {
                window.location.href = `Index.php?do=Home`;
            } else {
                // Luôn reset về trang 1 khi đổi danh mục
                window.location.href = `Index.php?do=Home&category=${encodeURIComponent(category)}&search=${encodeURIComponent(search)}&page=1`;
            }
        });
    }

    // 2. Quyết định tải trang Home
    if (document.querySelector('.featured-section-v3')) {
        initialPageLoad();
    }
});