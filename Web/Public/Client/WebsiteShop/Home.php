<?php
include_once '../../API/Config/db_config.php';

$featured_categories = [
    'DM01' => 'Chuột',
    'DM05' => 'Lap',
    'DM12' => 'Lót chuột',
    'DM03' => 'Tai nghe'
];
?>

<div class="featured-section-v3">
    <h2>Danh mục nổi bật</h2>
    <div class="featured-list">
        <?php foreach ($featured_categories as $maDM => $tenDM): ?>
            <div class="featured-row">
                <div class="category-label">
                    <span><?= htmlspecialchars($tenDM) ?></span>
                </div>
                <div class="product-row">
                    <div class="product-slider" data-category="<?= $maDM ?>">
                        <!-- 4 sản phẩm sẽ load bằng JS -->
                    </div>
                    <div class="pagination-mini" data-category="<?= $maDM ?>">
    <!-- JS sẽ tự sinh nội dung -->
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Kết quả tìm kiếm -->
<div class="product-section" id="searchResults" style="display: none;">
    <h2>Danh sách sản phẩm</h2>
    <div id="productContainer" class="product-grid"></div>
    <div id="paginationContainer" class="pagination-main"></div>
</div>