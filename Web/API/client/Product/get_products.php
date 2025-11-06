<?php
include_once '../../Config/db_config.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($connect) || $connect->connect_error) {
    echo json_encode(['error' => 'Lỗi hệ thống']);
    exit;
}

// === LẤY CÁC THAM SỐ ===
$category = $_GET['category'] ?? '';
$search = $_GET['search'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
$featured = isset($_GET['featured']) && $_GET['featured'] == '1';
$price_filter = $_GET['price'] ?? '';


// --- LOGIC PHÂN TRANG ---
if ($featured && in_array($category, ['DM01', 'DM05', 'DM12', 'DM03']) && empty($search)) {
    $limit_per_page = 4;
} else {
    $limit_per_page = 10;
}
$offset = ($page - 1) * $limit_per_page;


// === XÂY DỰNG CÂU TRUY VẤN (SQL) ===

$where_clause = " WHERE 1=1";
$params = [];
$types = '';

if (!empty($category)) {
    $where_clause .= " AND MaDM = ?";
    $params[] = $category;
    $types .= 's';
}

if (!empty($search)) {
    $where_clause .= " AND (TenSP LIKE ? OR MoTa LIKE ?)";
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
    $types .= 'ss';
}

if (!empty($price_filter)) {
    switch ($price_filter) {
        case '0-500k':
            $where_clause .= " AND DonGia < 500000";
            break;
        case '500k-1m':
            $where_clause .= " AND DonGia BETWEEN 500000 AND 1000000";
            break;
        case '1m-2m':
            $where_clause .= " AND DonGia BETWEEN 1000000 AND 2000000";
            break;
        case '2m-5m':
            $where_clause .= " AND DonGia BETWEEN 2000000 AND 5000000";
            break;
        case 'over-5m':
            $where_clause .= " AND DonGia > 5000000";
            break;
    }
}

// === MAP LOGIC TÌM KIẾM CHO TẤT CẢ FILTER KEYS ===
// *** ĐÃ SỬA LOGIC: Tìm ở cả TenSP và MoTa ***
$feature_map = [
    // DM01: Chuột
    'ketnoi' => [
        'Wireless' => "((TenSP LIKE '%Wireless%' OR TenSP LIKE '%Không dây%' OR TenSP LIKE '%Bluetooth%') OR (MoTa LIKE '%Wireless%' OR MoTa LIKE '%Không dây%' OR MoTa LIKE '%Bluetooth%'))",
        'CoDay' => "((TenSP NOT LIKE '%Wireless%' AND TenSP NOT LIKE '%Không dây%' AND TenSP NOT LIKE '%Bluetooth%') AND (MoTa NOT LIKE '%Wireless%' AND MoTa NOT LIKE '%Không dây%' AND MoTa NOT LIKE '%Bluetooth%'))"
    ],
    'tinhnang_chuot' => [
        'RGB' => "((TenSP LIKE '%RGB%') OR (MoTa LIKE '%RGB%'))",
        'Silent' => "((TenSP LIKE '%Silent%') OR (MoTa LIKE '%Silent%'))"
    ],
    // DM02: Bàn phím
    'switch' => [
        'Blue' => "((TenSP LIKE '%Blue%') OR (MoTa LIKE '%Blue%'))",
        'Red' => "((TenSP LIKE '%Red%') OR (MoTa LIKE '%Red%'))",
        'Brown' => "((TenSP LIKE '%Brown%') OR (MoTa LIKE '%Brown%'))"
    ],
    'tinhnang_bp' => [
        'Wireless' => "((TenSP LIKE '%Wireless%' OR TenSP LIKE '%Không dây%') OR (MoTa LIKE '%Wireless%' OR MoTa LIKE '%Không dây%'))",
        'RGB' => "((TenSP LIKE '%RGB%') OR (MoTa LIKE '%RGB%'))",
        'Hot-swap' => "((TenSP LIKE '%Hot-swap%') OR (MoTa LIKE '%Hot-swap%'))"
    ],
    // DM03: Tai nghe
    // 'ketnoi' (Dùng chung DM01)
    'tinhnang_tn' => [
        'ANC' => "((TenSP LIKE '%ANC%') OR (MoTa LIKE '%ANC%'))",
        '7.1' => "((TenSP LIKE '%7.1%') OR (MoTa LIKE '%7.1%'))"
    ],
    // DM04: Màn hình
    'tamnen' => [
        'IPS' => "((TenSP LIKE '%IPS%') OR (MoTa LIKE '%IPS%'))",
        'VA' => "((TenSP LIKE '%VA%') OR (MoTa LIKE '%VA%'))"
    ],
    'tinhnang_mh' => [
        'Cong' => "((TenSP LIKE '%Cong%') OR (MoTa LIKE '%Cong%') OR (MoTa LIKE '%1500R%'))",
        'Scan144' => "((TenSP LIKE '%144Hz%') OR (TenSP LIKE '%165Hz%') OR (MoTa LIKE '%144Hz%') OR (MoTa LIKE '%165Hz%'))"
    ],
    // DM05: Laptop
    'cpu' => [
        'i3' => "((TenSP LIKE '%i3-%') OR (MoTa LIKE '%i3-%'))",
        'i5' => "((TenSP LIKE '%i5-%') OR (MoTa LIKE '%i5-%'))",
        'i7' => "((TenSP LIKE '%i7-%') OR (MoTa LIKE '%i7-%'))"
    ],
    'hang_lap' => [
        'Dell' => "((TenSP LIKE '%Dell%') OR (MoTa LIKE '%Dell%'))",
        'HP' => "((TenSP LIKE '%HP%') OR (MoTa LIKE '%HP%'))",
        'Lenovo' => "((TenSP LIKE '%Lenovo%') OR (MoTa LIKE '%Lenovo%'))"
    ],
    // DM06: Laptop Gaming
    'vga_lap' => [
        'RTX 40' => "((TenSP LIKE '%RTX 40%') OR (MoTa LIKE '%RTX 40%'))",
        'RTX 3070' => "((TenSP LIKE '%RTX 3070%') OR (MoTa LIKE '%RTX 3070%'))",
        'RTX 3060' => "((TenSP LIKE '%RTX 3060%') OR (MoTa LIKE '%RTX 3060%'))",
        'RTX 3050' => "((TenSP LIKE '%RTX 3050%') OR (MoTa LIKE '%RTX 3050%'))"
    ],
    'hang_lapg' => [
        'ASUS' => "((TenSP LIKE '%ASUS%') OR (MoTa LIKE '%ASUS%'))",
        'MSI' => "((TenSP LIKE '%MSI%') OR (MoTa LIKE '%MSI%'))",
        'Acer' => "((TenSP LIKE '%Acer%') OR (MoTa LIKE '%Acer%'))",
        'Legion' => "((TenSP LIKE '%Legion%') OR (MoTa LIKE '%Legion%'))"
    ],
    // DM07: PC GVN
    'cpu_pc' => [
        'i5' => "((TenSP LIKE '%i5-%') OR (MoTa LIKE '%i5-%'))",
        'i7' => "((TenSP LIKE '%i7-%') OR (MoTa LIKE '%i7-%'))",
        'i9' => "((TenSP LIKE '%i9-%') OR (MoTa LIKE '%i9-%'))",
        'Ryzen 5' => "((TenSP LIKE '%Ryzen 5%') OR (TenSP LIKE '%5600X%') OR (MoTa LIKE '%Ryzen 5%') OR (MoTa LIKE '%5600X%'))",
        'Ryzen 7' => "((TenSP LIKE '%Ryzen 7%') OR (TenSP LIKE '%5800X%') OR (MoTa LIKE '%Ryzen 7%') OR (MoTa LIKE '%5800X%'))"
    ],
    'vga_pc' => [
        'RTX 40' => "((TenSP LIKE '%RTX 40%') OR (MoTa LIKE '%RTX 40%'))",
        'RTX 30' => "((TenSP LIKE '%RTX 30%') OR (MoTa LIKE '%RTX 30%'))",
        'RX' => "((TenSP LIKE '%RX 6%') OR (MoTa LIKE '%RX 6%'))"
    ],
    // DM08: Main, CPU, VGA
    'loai_comp' => [
        'Mainboard' => "((TenSP LIKE '%Mainboard%') OR (TenSP LIKE '%Main%') OR (MoTa LIKE '%Mainboard%'))",
        'CPU' => "((TenSP LIKE '%CPU%') OR (TenSP LIKE '%i5-%') OR (TenSP LIKE '%Ryzen%'))",
        'VGA' => "((TenSP LIKE '%VGA%') OR (TenSP LIKE '%RTX%') OR (MoTa LIKE '%VGA%'))"
    ],
    // DM09: Case, Nguồn, Tản
    'loai_case' => [
        'Case' => "((TenSP LIKE '%Case%') OR (MoTa LIKE '%Case%'))",
        'Nguồn' => "((TenSP LIKE '%Nguồn%') OR (MoTa LIKE '%Nguồn%'))",
        'Tản' => "((TenSP LIKE '%Tản%') OR (MoTa LIKE '%Tản%'))"
    ],
    // DM10: Ổ cứng, RAM, Thẻ nhớ
    'loai_mem' => [
        'SSD' => "((TenSP LIKE '%SSD%') OR (TenSP LIKE '%Ổ cứng%') OR (MoTa LIKE '%SSD%'))",
        'RAM' => "((TenSP LIKE '%RAM%') OR (MoTa LIKE '%RAM%'))",
        'Thẻ nhớ' => "((TenSP LIKE '%Thẻ nhớ%') OR (MoTa LIKE '%Thẻ nhớ%'))"
    ],
    'loai_ssd' => [
        'NVMe' => "((TenSP LIKE '%NVMe%') OR (MoTa LIKE '%NVMe%') OR (MoTa LIKE '%Gen4%'))",
        'SATA' => "((TenSP LIKE '%SATA%') OR (MoTa LIKE '%SATA%'))"
    ],
    // DM11: Loa, Micro, Webcam
    'loai_audio' => [
        'Loa' => "((TenSP LIKE '%Loa%') OR (TenSP LIKE '%Soundbar%') OR (MoTa LIKE '%Loa%'))",
        'Micro' => "((TenSP LIKE '%Micro%') OR (MoTa LIKE '%Micro%'))",
        'Webcam' => "((TenSP LIKE '%Webcam%') OR (MoTa LIKE '%Webcam%'))"
    ],
    // DM12: Lót chuột
    'tinhnang_lot' => [
        'RGB' => "((TenSP LIKE '%RGB%') OR (MoTa LIKE '%RGB%'))",
        'XL' => "((TenSP LIKE '%XL%') OR (TenSP LIKE '%800x300%') OR (TenSP LIKE '%900x400%') OR (MoTa LIKE '%XL%'))",
        'Chống mỏi' => "((TenSP LIKE '%Chống mỏi%') OR (MoTa LIKE '%Gel wrist%'))"
    ],
    // DM13: Bàn ghế
    'loai_ghe' => [
        'Gaming' => "((TenSP LIKE '%DXRacer%') OR (TenSP LIKE '%Secretlab%') OR (TenSP LIKE '%Anda%') OR (TenSP LIKE '%gaming%') OR (MoTa LIKE '%Gaming%'))",
        'Ergo' => "((TenSP LIKE '%Ergo%') OR (TenSP LIKE '%công thái học%') OR (TenSP LIKE '%văn phòng%') OR (MoTa LIKE '%Lưới%'))"
    ],
    'chatlieu_ghe' => [
        'Da' => "((TenSP LIKE '%Da%') OR (MoTa LIKE '%Da %'))",
        'Lưới' => "((TenSP LIKE '%Lưới%') OR (MoTa LIKE '%Lưới%'))"
    ],
    // DM14: Handheld, Console
    'loai_may' => [
        'Handheld' => "((TenSP LIKE '%Handheld%') OR (TenSP LIKE '%Steam Deck%') OR (MoTa LIKE '%Handheld%'))",
        'Console' => "((TenSP LIKE '%Console%') OR (TenSP LIKE '%Nintendo%') OR (TenSP LIKE '%GameBox%') OR (TenSP LIKE '%MiniStation%') OR (MoTa LIKE '%Console%'))",
        'Retro' => "((TenSP LIKE '%Retro%') OR (MoTa LIKE '%Retro%') OR (MoTa LIKE '%3000 game%'))"
    ],
    // DM15: Phụ kiện
    'loai_pk' => [
        'Hub' => "((TenSP LIKE '%Hub%') OR (MoTa LIKE '%Hub%'))",
        'Sạc' => "((TenSP LIKE '%Sạc%') OR (MoTa LIKE '%Sạc%'))",
        'Cáp' => "((TenSP LIKE '%Cáp%') OR (MoTa LIKE '%Cáp%'))"
    ]
];

// Lặp qua tất cả các tham số GET để tìm các key có trong $feature_map
foreach ($_GET as $key => $values) {
    if (isset($feature_map[$key])) {
        $values_array = (array)$values; 
        $or_conditions = [];

        foreach ($values_array as $value) {
            if (isset($feature_map[$key][$value])) {
                $or_conditions[] = $feature_map[$key][$value];
            }
        }
        
        if (!empty($or_conditions)) {
            $where_clause .= " AND (" . implode(' OR ', $or_conditions) . ")";
        }
    }
}


// === 1. ĐẾM TỔNG SẢN PHẨM (ĐỂ PHÂN TRANG) ===
$count_sql = "SELECT COUNT(*) as total FROM tbl_sanpham" . $where_clause;
$count_stmt = $connect->prepare($count_sql);

if ($count_stmt && !empty($types)) {
    $count_stmt->bind_param($types, ...$params);
}
if ($count_stmt) $count_stmt->execute();
$count_result = $count_stmt ? $count_stmt->get_result() : null;
$total_products = $count_result ? $count_result->fetch_assoc()['total'] : 0;

$total_pages = ceil($total_products / $limit_per_page);
$total_pages = max(1, $total_pages);


// === 2. LẤY SẢN PHẨM (CHO TRANG HIỆN TẠI) ===
$sql = "SELECT MaSP, TenSP, DonGia, HinhAnh FROM tbl_sanpham" . $where_clause;

$sql .= " LIMIT ? OFFSET ?";
$params[] = $limit_per_page;
$params[] = $offset;
$types .= 'ii';

$stmt = $connect->prepare($sql);
if ($stmt === false) {
    echo json_encode(['error' => 'Lỗi truy vấn']);
    exit;
}

$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// === TẠO MẢNG DỮ LIỆU ===
$products = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

// === TRẢ VỀ JSON ===
echo json_encode([
    'products' => $products,
    'currentPage' => $page,
    'totalPages' => $total_pages,
    'totalProducts' => $total_products
]);

$stmt->close();
if (isset($count_stmt)) $count_stmt->close();
$connect->close();
?>