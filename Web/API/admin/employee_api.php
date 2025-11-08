    <?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");

    require_once "../Config/db_config.php";
    require_once "../../Includes/Employee.php";

    $employee = new Employee($connect);

    // Nhận action từ GET hoặc POST
    $action = $_GET['action'] ?? $_POST['action'] ?? '';

    switch ($action) {

        // ==== XEM DANH SÁCH NHÂN VIÊN (PHÂN TRANG + TÌM KIẾM) ====
        case 'view':
            $keyword = trim($_GET['keyword'] ?? '');
            $MaCH = trim($_GET['MaCH'] ?? '');
            $GioiTinh = trim($_GET['GioiTinh'] ?? '');
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
            $offset = ($page - 1) * $limit;

            // Nếu có từ khóa tìm kiếm
            if (!empty($keyword)) {
                $data = $employee->searchByCode($keyword);
                $total = count($data);
            }
            // Nếu có lọc theo cửa hàng hoặc giới tính
            elseif (!empty($MaCH) || !empty($GioiTinh)) {
                $data = $employee->filter($MaCH, $GioiTinh);
                $total = count($data);
            }
            // Mặc định: phân trang toàn bộ
            else {
                $data = $employee->paginate($limit, $offset);
                $total = $employee->count();
            }

            $totalPages = ceil($total / $limit);
            echo json_encode([
                "status" => "success",
                "page" => $page,
                "limit" => $limit,
                "total" => $total,
                "total_pages" => $totalPages,
                "data" => $data
            ]);
            break;

        // ==== LẤY 1 NHÂN VIÊN ====
        case 'getOne':
            $MaNV = trim($_GET['MaNV'] ?? '');
            if (empty($MaNV)) {
                echo json_encode(["status" => "error", "message" => "Thiếu mã nhân viên!"]);
                exit;
            }

            $result = $employee->getOne($MaNV);
            echo json_encode([
                "status" => $result ? "success" : "error",
                "data" => $result ?: null,
                "message" => $result ? "" : "Không tìm thấy nhân viên."
            ]);
            break;

        // ==== THÊM NHÂN VIÊN ====
        case 'add':
            $data = json_decode(file_get_contents("php://input"), true);
            if (!is_array($data)) {
            echo json_encode(['status' => 'error', 'message' => 'Dữ liệu không hợp lệ']);
            exit;
            }

            $TenNV = trim($data['TenNV'] ?? '');
            $GioiTinh = trim($data['GioiTinh'] ?? '');
            $NgaySinh = trim($data['NgaySinh'] ?? '');
            $SoDienThoai = trim($data['SoDienThoai'] ?? '');
            $MaCH = trim($data['MaCH'] ?? '');

            if ($TenNV === '' || $MaCH === '') {
            echo json_encode(['status' => 'error', 'message' => 'Thiếu thông tin bắt buộc']);
            exit;
            }

            $MaNV = $employee->add($TenNV, $GioiTinh, $NgaySinh, $SoDienThoai, $MaCH);
            if ($MaNV) {
            echo json_encode([
                'status' => 'success',
                'data' => [
                'MaNV' => $MaNV,
                'TenNV' => $TenNV,
                'GioiTinh' => $GioiTinh,
                'NgaySinh' => $NgaySinh,
                'SoDienThoai' => $SoDienThoai,
                'MaCH' => $MaCH
                ]
            ]);
            } else {
            echo json_encode(['status' => 'error', 'message' => 'Không thể thêm nhân viên']);
            }
            break;

        // ==== CẬP NHẬT NHÂN VIÊN ====
        case 'update':
            $MaNV = trim($_POST['MaNV'] ?? '');
            $TenNV = trim($_POST['TenNV'] ?? '');
            $GioiTinh = trim($_POST['GioiTinh'] ?? '');
            $NgaySinh = trim($_POST['NgaySinh'] ?? '');
            $SoDienThoai = trim($_POST['SoDienThoai'] ?? '');
            $MaCH = trim($_POST['MaCH'] ?? '');

            if (empty($MaNV) || empty($TenNV) || empty($GioiTinh) || empty($NgaySinh) || empty($SoDienThoai) || empty($MaCH)) {
                echo json_encode(["status" => "error", "message" => "Thiếu dữ liệu đầu vào!"]);
                exit;
            }

            $result = $employee->update($MaNV, $TenNV, $GioiTinh, $NgaySinh, $SoDienThoai, $MaCH);
            echo json_encode([
                "status" => $result ? "success" : "error",
                "message" => $result ? "Cập nhật nhân viên thành công!" : "Không thể cập nhật nhân viên."
            ]);
            break;

        // ==== XÓA NHÂN VIÊN ====
        case 'delete':
            $data = json_decode(file_get_contents("php://input"), true);
            $MaNV = trim($data['MaNV'] ?? '');
            if (empty($MaNV)) {
                echo json_encode(["status" => "error", "message" => "Thiếu mã nhân viên cần xóa!"]);
                exit;
            }

            if (!$employee->exists($MaNV)) {
                echo json_encode(["status" => "error", "message" => "Nhân viên không tồn tại!"]);
                exit;
            }

            $result = $employee->delete($MaNV);
            echo json_encode([
                "status" => $result ? "success" : "error",
                "message" => $result ? "Đã xóa nhân viên thành công!" : "Không thể xóa nhân viên."
            ]);
            break;

        // ==== LẤY THEO CỬA HÀNG (lọc riêng) ====
        case 'getByStore':
            $MaCH = trim($_GET['MaCH'] ?? '');
            if (empty($MaCH)) {
                echo json_encode(["status" => "error", "message" => "Thiếu mã cửa hàng!"]);
                exit;
            }

            $data = $employee->filter($MaCH);
            echo json_encode([
                "status" => "success",
                "count" => count($data),
                "data" => $data
            ]);
            break;

        // ==== LẤY MÃ CỬA HÀNG TỪ SESSION ====
        case 'getMaCH':
            session_start();
            if (isset($_SESSION['ma_ch'])) {
                echo json_encode(["status" => "success", "MaCH" => $_SESSION['ma_ch']]);
            } else {
                echo json_encode(["status" => "error", "message" => "Chưa đăng nhập hoặc chưa có session ma_ch!"]);
            }
            break;

        // ==== MẶC ĐỊNH ====
        default:
            echo json_encode([
                "status" => "error",
                "message" => "Action không hợp lệ hoặc chưa được gửi!"
            ]);
            break;
    }
    ?>
