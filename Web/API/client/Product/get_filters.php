<?php
// API/client/Product/get_filters.php

header('Content-Type: application/json; charset=utf-8');

$category = $_GET['category'] ?? '';
$filter_groups = [];

if (empty($category)) {
    echo json_encode(['success' => false, 'error' => 'No category']);
    exit;
}

// --- ĐỊNH NGHĨA CÁC BỘ LỌC TÙY CHỈNH CHO TẤT CẢ 15 DANH MỤC ---
switch ($category) {
    
    // DM01: Chuột máy tính
    case 'DM01':
        $filter_groups = [
            [
                'groupName' => 'Kết nối',
                'filterKey' => 'ketnoi',
                'options' => [
                    ['label' => 'Không dây (Wireless)', 'value' => 'Wireless'],
                    ['label' => 'Có dây', 'value' => 'CoDay']
                ]
            ],
            [
                'groupName' => 'Tính năng',
                'filterKey' => 'tinhnang_chuot',
                'options' => [
                    ['label' => 'LED / RGB', 'value' => 'RGB'],
                    ['label' => 'Silent Click (Im lặng)', 'value' => 'Silent']
                ]
            ]
        ];
        break;

    // DM02: Bàn phím
    case 'DM02':
        $filter_groups = [
            [
                'groupName' => 'Loại Switch',
                'filterKey' => 'switch',
                'options' => [
                    ['label' => 'Blue Switch', 'value' => 'Blue'],
                    ['label' => 'Red Switch', 'value' => 'Red'],
                    ['label' => 'Brown Switch', 'value' => 'Brown']
                ]
            ],
            [
                'groupName' => 'Tính năng',
                'filterKey' => 'tinhnang_bp',
                'options' => [
                    ['label' => 'Không dây', 'value' => 'Wireless'],
                    ['label' => 'LED / RGB', 'value' => 'RGB'],
                    ['label' => 'Hot-swap', 'value' => 'Hot-swap']
                ]
            ]
        ];
        break;

    // DM03: Tai nghe
    case 'DM03':
        $filter_groups = [
            [
                'groupName' => 'Kết nối',
                'filterKey' => 'ketnoi',
                'options' => [
                    ['label' => 'Không dây (Bluetooth)', 'value' => 'Wireless'],
                    ['label' => 'Có dây (Jack 3.5mm/USB)', 'value' => 'CoDay']
                ]
            ],
            [
                'groupName' => 'Tính năng',
                'filterKey' => 'tinhnang_tn',
                'options' => [
                    ['label' => 'Chống ồn (ANC)', 'value' => 'ANC'],
                    ['label' => 'Âm thanh 7.1', 'value' => '7.1']
                ]
            ]
        ];
        break;

    // DM04: Màn hình
    case 'DM04':
        $filter_groups = [
            [
                'groupName' => 'Tấm nền',
                'filterKey' => 'tamnen',
                'options' => [
                    ['label' => 'IPS', 'value' => 'IPS'],
                    ['label' => 'VA', 'value' => 'VA']
                ]
            ],
            [
                'groupName' => 'Tính năng',
                'filterKey' => 'tinhnang_mh',
                'options' => [
                    ['label' => 'Màn hình cong', 'value' => 'Cong'],
                    ['label' => 'Tần số quét cao (>=144Hz)', 'value' => 'Scan144']
                ]
            ]
        ];
        break;

    // DM05: Laptop
    case 'DM05':
        $filter_groups = [
            [
                'groupName' => 'CPU',
                'filterKey' => 'cpu',
                'options' => [
                    ['label' => 'Intel Core i3', 'value' => 'i3'],
                    ['label' => 'Intel Core i5', 'value' => 'i5'],
                    ['label' => 'Intel Core i7', 'value' => 'i7']
                ]
            ],
            [
                'groupName' => 'Hãng',
                'filterKey' => 'hang_lap',
                'options' => [
                    ['label' => 'Dell', 'value' => 'Dell'],
                    ['label' => 'HP', 'value' => 'HP'],
                    ['label' => 'Lenovo', 'value' => 'Lenovo']
                ]
            ]
        ];
        break;

    // DM06: Laptop Gaming
    case 'DM06':
        $filter_groups = [
            [
                'groupName' => 'Card đồ họa (VGA)',
                'filterKey' => 'vga_lap',
                'options' => [
                    ['label' => 'RTX 40-Series', 'value' => 'RTX 40'],
                    ['label' => 'RTX 3070', 'value' => 'RTX 3070'],
                    ['label' => 'RTX 3060', 'value' => 'RTX 3060'],
                    ['label' => 'RTX 3050 / 3050Ti', 'value' => 'RTX 3050']
                ]
            ],
            [
                'groupName' => 'Hãng',
                'filterKey' => 'hang_lapg',
                'options' => [
                    ['label' => 'ASUS', 'value' => 'ASUS'],
                    ['label' => 'MSI', 'value' => 'MSI'],
                    ['label' => 'Acer', 'value' => 'Acer'],
                    ['label' => 'Legion (Lenovo)', 'value' => 'Legion']
                ]
            ]
        ];
        break;
        
    // DM07: PC GVN
    case 'DM07':
        $filter_groups = [
            [
                'groupName' => 'Dòng CPU',
                'filterKey' => 'cpu_pc',
                'options' => [
                    ['label' => 'Intel Core i5', 'value' => 'i5'],
                    ['label' => 'Intel Core i7', 'value' => 'i7'],
                    ['label' => 'Intel Core i9', 'value' => 'i9'],
                    ['label' => 'AMD Ryzen 5', 'value' => 'Ryzen 5'],
                    ['label' => 'AMD Ryzen 7', 'value' => 'Ryzen 7']
                ]
            ],
            [
                'groupName' => 'Dòng VGA',
                'filterKey' => 'vga_pc',
                'options' => [
                    ['label' => 'RTX 40-Series', 'value' => 'RTX 40'],
                    ['label' => 'RTX 30-Series', 'value' => 'RTX 30'],
                    ['label' => 'AMD RX', 'value' => 'RX']
                ]
            ]
        ];
        break;
        
    // DM08: Main, CPU, VGA
    case 'DM08':
        $filter_groups = [
            [
                'groupName' => 'Loại linh kiện',
                'filterKey' => 'loai_comp',
                'options' => [
                    ['label' => 'Mainboard (Bo mạch chủ)', 'value' => 'Mainboard'],
                    ['label' => 'CPU (Vi xử lý)', 'value' => 'CPU'],
                    ['label' => 'VGA (Card đồ họa)', 'value' => 'VGA']
                ]
            ]
        ];
        break;

    // DM09: Case, Nguồn, Tản
    case 'DM09':
        $filter_groups = [
            [
                'groupName' => 'Loại linh kiện',
                'filterKey' => 'loai_case',
                'options' => [
                    ['label' => 'Case (Vỏ máy tính)', 'value' => 'Case'],
                    ['label' => 'Nguồn (PSU)', 'value' => 'Nguồn'],
                    ['label' => 'Tản nhiệt', 'value' => 'Tản']
                ]
            ]
        ];
        break;

    // DM10: Ổ cứng, RAM, Thẻ nhớ
    case 'DM10':
        $filter_groups = [
            [
                'groupName' => 'Loại linh kiện',
                'filterKey' => 'loai_mem',
                'options' => [
                    ['label' => 'Ổ cứng SSD', 'value' => 'SSD'],
                    ['label' => 'RAM', 'value' => 'RAM'],
                    ['label' => 'Thẻ nhớ', 'value' => 'Thẻ nhớ']
                ]
            ],
            [
                'groupName' => 'Loại SSD (nếu chọn)',
                'filterKey' => 'loai_ssd',
                'options' => [
                    ['label' => 'NVMe (M.2)', 'value' => 'NVMe'],
                    ['label' => 'SATA', 'value' => 'SATA']
                ]
            ]
        ];
        break;

    // DM11: Loa, Micro, Webcam
    case 'DM11':
        $filter_groups = [
            [
                'groupName' => 'Loại thiết bị',
                'filterKey' => 'loai_audio',
                'options' => [
                    ['label' => 'Loa', 'value' => 'Loa'],
                    ['label' => 'Micro', 'value' => 'Micro'],
                    ['label' => 'Webcam', 'value' => 'Webcam']
                ]
            ]
        ];
        break;

    // DM12: Lót chuột
    case 'DM12':
        $filter_groups = [
            [
                'groupName' => 'Tính năng',
                'filterKey' => 'tinhnang_lot',
                'options' => [
                    ['label' => 'LED / RGB', 'value' => 'RGB'],
                    ['label' => 'Cỡ lớn (XL)', 'value' => 'XL'],
                    ['label' => 'Chống mỏi (Có đệm)', 'value' => 'Chống mỏi']
                ]
            ]
        ];
        break;

    // DM13: Bàn ghế
    case 'DM13':
        $filter_groups = [
            [
                'groupName' => 'Loại ghế',
                'filterKey' => 'loai_ghe',
                'options' => [
                    ['label' => 'Ghế Gaming', 'value' => 'Gaming'],
                    ['label' => 'Ghế công thái học', 'value' => 'Ergo']
                ]
            ],
            [
                'groupName' => 'Chất liệu',
                'filterKey' => 'chatlieu_ghe',
                'options' => [
                    ['label' => 'Da (PU, PVC, Nappa)', 'value' => 'Da'],
                    ['label' => 'Lưới', 'value' => 'Lưới']
                ]
            ]
        ];
        break;

    // DM14: Handheld, Console
    case 'DM14':
        $filter_groups = [
            [
                'groupName' => 'Loại máy',
                'filterKey' => 'loai_may',
                'options' => [
                    ['label' => 'Handheld (Cầm tay)', 'value' => 'Handheld'],
                    ['label' => 'Console (Để bàn)', 'value' => 'Console'],
                    ['label' => 'Máy chơi game Retro', 'value' => 'Retro']
                ]
            ]
        ];
        break;

    // DM15: Phụ kiện (Hub, Sạc, Cáp,...)
    case 'DM15':
        $filter_groups = [
            [
                'groupName' => 'Loại phụ kiện',
                'filterKey' => 'loai_pk',
                'options' => [
                    ['label' => 'Hub (Bộ chia USB/Type-C)', 'value' => 'Hub'],
                    ['label' => 'Sạc (Củ sạc, không dây)', 'value' => 'Sạc'],
                    ['label' => 'Cáp (HDMI, Sạc,...)', 'value' => 'Cáp']
                ]
            ]
        ];
        break;
}

if (empty($filter_groups)) {
    echo json_encode(['success' => false, 'error' => 'No filters for this category']);
} else {
    echo json_encode(['success' => true, 'filterGroups' => $filter_groups]);
}

?>