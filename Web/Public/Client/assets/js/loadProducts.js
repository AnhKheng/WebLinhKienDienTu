function createProductCardHTML(product) {
    const price = new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(product.DonGia);

    const imageUrl = product.HinhAnh ? `../img/${product.HinhAnh}` : '../img/placeholder.png';

    return `
    <div class="product-card">
        <a href="Index.php?do=Details&id=${product.MaSP}">
            <img src="${imageUrl}" alt="${product.TenSP}" onerror="this.src='../img/placeholder.png';">
        </a>
        <a href="Index.php?do=Details&id=${product.MaSP}">
            <h3>${product.TenSP}</h3>
        </a>
        <p class="price">${price}</p>
        <a href="Index.php?do=Details&id=${product.MaSP}" class="btn-detail">Xem chi tiết</a>
    </div>
    `;
}

function renderPagination(container, category, currentPage, totalPages, type = 'mini') {
    container.innerHTML = '';
    if (totalPages <= 1) return;

    let startPage = Math.max(1, currentPage - 2);
    let endPage = Math.min(totalPages, currentPage + 2);

    if (currentPage <= 3) {
        endPage = Math.min(totalPages, 5);
    }
    if (currentPage > totalPages - 3) {
        startPage = Math.max(1, totalPages - 4);
    }

    const prevBtn = document.createElement('button');
    prevBtn.className = 'page-btn prev';
    prevBtn.innerHTML = '←';
    prevBtn.dataset.category = category;
    prevBtn.dataset.type = type;
    prevBtn.disabled = currentPage <= 1;
    container.appendChild(prevBtn);

    const pageNumbers = document.createElement('span');
    pageNumbers.className = 'page-numbers';

    if (startPage > 1) {
        pageNumbers.appendChild(createPageNum(1, category, type, '1', false));
        if (startPage > 2) {
            pageNumbers.appendChild(createPageEllipsis());
        }
    }

    for (let i = startPage; i <= endPage; i++) {
        pageNumbers.appendChild(createPageNum(i, category, type, i.toString(), i === currentPage));
    }

    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            pageNumbers.appendChild(createPageEllipsis());
        }
        pageNumbers.appendChild(createPageNum(totalPages, category, type, totalPages.toString(), false));
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

function createPageNum(page, category, type, text, isActive) {
    const pageNum = document.createElement('span');
    pageNum.className = 'page-num';
    pageNum.textContent = text;
    pageNum.dataset.category = category;
    pageNum.dataset.page = page;
    pageNum.dataset.type = type;
    if (isActive) pageNum.classList.add('active');
    return pageNum;
}

function createPageEllipsis() {
    const ellipsis = document.createElement('span');
    ellipsis.className = 'page-num-ellipsis';
    ellipsis.innerHTML = '...';
    return ellipsis;
}

let currentPages = {};
let currentSearchPage = 1;

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

function displaySearchResults(params, page = 1) {
    currentSearchPage = page;

    const results = document.getElementById('searchResults');
    const featured = document.querySelector('.featured-section-v3');

    if (featured) featured.style.display = 'none';
    if (results) results.style.display = 'block';

    params.set('page', page);
    params.delete('featured'); 
    
    const url = `../../API/client/Product/get_products.php?${params.toString()}`;
    
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

function loadDynamicFilters(category) {
    const filterBox = document.getElementById('dynamicFilterBox');
    const filterForm = document.getElementById('dynamicFilterForm');
    
    if (!filterBox || !filterForm) return;

    if (!category) {
        filterBox.style.display = 'none';
        filterForm.innerHTML = '';
        return;
    }

    fetch(`../../API/client/Product/get_filters.php?category=${encodeURIComponent(category)}`)
        .then(r => r.json())
        .then(data => {
            if (data.success && data.filterGroups) {
                let html = '';
                data.filterGroups.forEach(group => {
                    html += `<h4 style="padding: 5px 15px 0; margin: 8px 0 0; color: #333;">${group.groupName}</h4>`;
                    
                    group.options.forEach(option => {
                        html += `
                        <label class="menu-item">
                            <input type="checkbox" name="${group.filterKey}" value="${option.value}">
                            ${option.label}
                        </label>
                        `;
                    });
                });

                filterForm.innerHTML = html;
                filterBox.style.display = 'block';

                restoreFilterState();
            } else {
                filterBox.style.display = 'none';
                filterForm.innerHTML = '';
            }
        })
        .catch(() => {
            filterForm.innerHTML = '<p style="padding: 10px 15px; color: red; margin: 0;">Lỗi tải bộ lọc</p>';
            filterBox.style.display = 'block';
        });
}

function restoreFilterState() {
    const params = new URLSearchParams(window.location.search);
    
    const price = params.get('price');
    if (price) {
        const priceInput = document.querySelector(`#priceFilterForm input[name="price"][value="${price}"]`);
        if (priceInput) {
            priceInput.checked = true;
        }
    }

    const filterForm = document.getElementById('dynamicFilterForm');
    if (!filterForm) return;

    params.forEach((value, key) => {
        const inputs = filterForm.querySelectorAll(`input[name="${key}"][value="${value}"]`);
        inputs.forEach(input => {
            if (input.type === 'checkbox') {
                input.checked = true;
            }
        });
    });
}

function initHomePage() {
    const params = new URLSearchParams(window.location.search);
    const categoryValue = params.get('category') || '';
    const searchValue = params.get('search') || '';
    const pageValue = parseInt(params.get('page')) || 1;
    const isSearching = params.has('category') || params.has('search') || params.has('price') || params.size > 2;

    if (isSearching) {
        document.getElementById('categorySelect').value = categoryValue;
        document.getElementById('searchInput').value = searchValue;
        
        loadDynamicFilters(categoryValue);
        
        displaySearchResults(params, pageValue);

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
        
        const filterBox = document.getElementById('dynamicFilterBox');
        if(filterBox) filterBox.style.display = 'none';
    }
}

function addListeners() {
    
    document.addEventListener('click', function(e) {
        const target = e.target.closest('.page-num, .page-btn');
        if (!target) return;

        e.preventDefault();
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
            
            const params = new URLSearchParams(window.location.search);
            params.set('page', page);
            
            const newUrl = `Index.php?${params.toString()}`;
            window.history.pushState({path: newUrl}, '', newUrl);

            displaySearchResults(params, page);
            
            const searchResults = document.getElementById('searchResults');
            if (searchResults) {
                searchResults.scrollIntoView({ behavior: 'smooth' });
            }
        }
    });

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
    
    const applyFiltersBtn = document.getElementById('applyFiltersBtn');
    if (applyFiltersBtn) {
        applyFiltersBtn.addEventListener('click', function() {
            const params = new URLSearchParams(window.location.search);
            let category = params.get('category') || document.getElementById('categorySelect').value;
            let search = params.get('search') || document.getElementById('searchInput').value.trim();
            
            if (category === 'featured') category = '';

            const newParams = new URLSearchParams();
            newParams.set('do', 'Home');
            if (category) newParams.set('category', category);
            if (search) newParams.set('search', search);
            newParams.set('page', '1'); // Luôn reset về trang 1

            const priceForm = document.getElementById('priceFilterForm');
            if (priceForm) {
                const selectedPrice = priceForm.querySelector('input[name="price"]:checked');
                if (selectedPrice) {
                    newParams.set('price', selectedPrice.value);
                }
            }
            const featureForm = document.getElementById('dynamicFilterForm');
            if (featureForm) {
                const checkedFeatures = featureForm.querySelectorAll('input[type="checkbox"]:checked');
                checkedFeatures.forEach(cb => {
                    newParams.append(cb.name, cb.value); 
                });
            }
            window.location.href = `Index.php?${newParams.toString()}`;
        });
    }
}

document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('searchResults')) {
        initHomePage();
    }
    addListeners();
});