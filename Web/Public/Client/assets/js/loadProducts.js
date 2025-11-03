let currentPages = {};
let currentSearchPage = 1;

function formatCurrency(number) {
    if (isNaN(number)) return '0₫';
    return new Intl.NumberFormat('vi-VN').format(number) + '₫';
}


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
            
            if (data.products && data.products.length > 0) {
                let html = '';
                data.products.forEach(product => {
                    html += createProductCardHTML(product);
                });
                slider.innerHTML = html;
            } else {
                slider.innerHTML = '<p style="color:red;">Không có dữ liệu sản phẩm.</p>';
            }

            currentPages[category] = data.currentPage;
            renderPagination(pagination, category, data.currentPage, data.totalPages, 'mini');
        })
        .catch(() => {
            slider.innerHTML = '<p style="color:red;">Lỗi tải</p>';
        });
}

function renderPagination(container, category, currentPage, totalPages, type = 'mini') {
    container.innerHTML = '';
    if (totalPages <= 1) return;

    const prevBtn = document.createElement('button');
    prevBtn.className = 'page-btn prev';
    prevBtn.innerHTML = '←';
    prevBtn.dataset.category = category;
    prevBtn.dataset.type = type;
    prevBtn.disabled = currentPage <= 1;
    container.appendChild(prevBtn);

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

    const nextBtn = document.createElement('button');
    nextBtn.className = 'page-btn next';
    nextBtn.innerHTML = '→';
    nextBtn.dataset.category = category;
    nextBtn.dataset.type = type;
    nextBtn.disabled = currentPage >= totalPages;
    container.appendChild(nextBtn);
}

document.addEventListener('click', function(e) {
    const target = e.target.closest('.page-num, .page-btn');
    if (!target) return;

    const cat = target.dataset.category;
    const type = target.dataset.type;

    if (type === 'mini') {
        let page = parseInt(target.dataset.page) || currentPages[cat] || 1;
        if (target.classList.contains('prev')) page = Math.max(1, page - 1);
        if (target.classList.contains('next')) page = page + 1;
        loadFeaturedProducts(cat, page);

    } else if (type === 'main') {
        let page = parseInt(target.dataset.page) || currentSearchPage || 1;
        if (target.classList.contains('prev')) page = Math.max(1, page - 1);
        if (target.classList.contains('next')) page = page + 1;
        
        const params = getUrlParams();
        const categoryValue = params.category || '';
        const searchValue = params.search || '';
        displaySearchResults(categoryValue, searchValue, page);
        
        const searchResults = document.getElementById('searchResults');
        if (searchResults) {
            searchResults.scrollIntoView({ behavior: 'smooth' });
        }
    }
});

function getUrlParams() {
    const params = new URLSearchParams(window.location.search);
    return {
        category: params.get('category'), 
        search: params.get('search'),
        page: parseInt(params.get('page')) || 1,
        hasCategory: params.has('category'),
        hasSearch: params.has('search')
    };
}


function displaySearchResults(category, search, page = 1) {
    currentSearchPage = page;

    const results = document.getElementById('searchResults');
    const featured = document.querySelector('.featured-section-v3');

    if (featured) featured.style.display = 'none';
    if (results) results.style.display = 'block';

    const url = `../../API/client/Product/get_products.php?category=${encodeURIComponent(category)}&search=${encodeURIComponent(search)}&page=${page}`;
    
    fetch(url)
        .then(r => r.json())
        .then(data => {
            const container = document.getElementById('productContainer');
            const paginationContainer = document.getElementById('paginationContainer');
            if (!container || !paginationContainer) return;

            if (data.products && data.products.length > 0) {
                let html = '';
                data.products.forEach(product => {
                    html += createProductCardHTML(product);
                });
                container.innerHTML = html;
            } else {
                container.innerHTML = '<p style="grid-column: 1/-1; text-align:center; color:#999; padding:20px;">Không tìm thấy sản phẩm.</p>';
            }
            
            renderPagination(paginationContainer, 'search', data.currentPage, data.totalPages, 'main');
        });
}

function initialPageLoad() {
    const params = getUrlParams();
    const isSearching = params.hasCategory || params.hasSearch;

    const categoryValue = params.category || '';
    const searchValue = params.search || '';
    const pageValue = params.page;

    if (isSearching) {
        document.getElementById('categorySelect').value = categoryValue;
        document.getElementById('searchInput').value = searchValue;
        
        displaySearchResults(categoryValue, searchValue, pageValue);

    } else {
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

document.addEventListener('DOMContentLoaded', function() {
    
    const searchBtn = document.getElementById('searchBtn');
    const categorySelect = document.getElementById('categorySelect');
    const searchInput = document.getElementById('searchInput');

    if (searchBtn && categorySelect && searchInput) {
        
        function standardSearch() {
            let category = categorySelect.value;
            const search = searchInput.value.trim();
            if (category === 'featured') {
                category = ''; 
            }
            window.location.href = `Index.php?do=Home&category=${encodeURIComponent(category)}&search=${encodeURIComponent(search)}&page=1`;
        }

        searchBtn.addEventListener('click', standardSearch);
        searchInput.addEventListener('keypress', e => {
            if (e.key === 'Enter') standardSearch();
        });

        categorySelect.addEventListener('change', function() {
            const category = categorySelect.value;
            searchInput.value = ''; 
            const search = '';

            if (category === 'featured') {
                window.location.href = `Index.php?do=Home`;
            } else {
                window.location.href = `Index.php?do=Home&category=${encodeURIComponent(category)}&search=${encodeURIComponent(search)}&page=1`;
            }
        });
    }

    if (document.querySelector('.featured-section-v3')) {
        initialPageLoad();
    }
});