<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

require_once "../Config/db_config.php";
require_once "../../Includes/Products.php";

$product = new Product($connect);

// 🧭 Lấy hành động từ query
$action = $_GET["action"] ?? "";

// 🧩 Xử lý các hành động
switch ($action) {

    // ===== 1️ Thêm sản phẩm =====
    case "add":
        if (isset($_POST["nameSP"], $_POST["category"], $_POST["price"], $_POST["description"])) {
            $TenSP = trim($_POST["nameSP"]);
            $MaDM = trim($_POST["category"]);
            $DonGia = floatval($_POST["price"]);
            $MoTa = trim($_POST["description"]);
            $TrangThai = 1; // Mặc định đang hoạt động

            $HinhAnh = '';
            if (isset($_FILES["image"]) && $_FILES["image"]["error"] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . "/../../Public/img/";
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                $fileName = uniqid() . "_" . basename($_FILES["image"]["name"]);
                $targetPath = $uploadDir . $fileName;
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetPath)) {
                    $HinhAnh = "img/" . $fileName; // đường dẫn client dùng hiển thị
                }
            }

            $MaSP = $product->add($TenSP, $MaDM, $DonGia, $MoTa, $TrangThai, $HinhAnh);
            if ($MaSP) {
                echo json_encode([
                    "status" => "success",
                    "message" => "Thêm sản phẩm thành công!",
                    "MaSP" => $MaSP
                ]);
            } else {
                echo json_encode([
                    "status" => "error",
                    "message" => "Không thể thêm sản phẩm.",
                    "sql_error" => "Xem log file PHP để biết chi tiết."
                ]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Thiếu dữ liệu đầu vào."]);
        }
        break;

    // ===== 2️ Cập nhật sản phẩm =====
    case "update":
    if (
        isset($_POST["idSP"], $_POST["nameSP"], $_POST["category"], $_POST["price"], $_POST["description"], $_POST["status"])
    ) {
        $MaSP = $_POST["idSP"];
        $TenSP = $_POST["nameSP"];
        $MaDM = $_POST["category"];
        $DonGia = floatval($_POST["price"]);
        $MoTa = $_POST["description"];
        $TrangThai = $_POST["status"];

        // Lấy ảnh cũ trước
        $oldData = $product->getOne($MaSP);
        $HinhAnh = $oldData["HinhAnh"] ?? null;

        // Nếu có upload ảnh mới thì cập nhật
        if (isset($_FILES["image"]) && $_FILES["image"]["error"] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . "/../../Public/img/";
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $fileName = uniqid() . "_" . basename($_FILES["image"]["name"]);
            $targetPath = $uploadDir . $fileName;
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetPath)) {
                $HinhAnh = $fileName; // đường dẫn client dùng hiển thị
            }
        }

        $result = $product->update($MaSP, $TenSP, $MaDM, $DonGia, $MoTa, $TrangThai, $HinhAnh);
        echo json_encode([
            "status" => $result ? "success" : "error",
            "message" => $result ? "Cập nhật sản phẩm thành công." : "Không thể cập nhật sản phẩm.",
            'error' => $result

        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Thiếu dữ liệu đầu vào."]);
    }
    break;

    // ===== 3️ Xóa sản phẩm =====
    case "delete":
        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data["MaSP"])) {
            echo json_encode(["status" => "error", "message" => "Thiếu mã sản phẩm cần xóa."]);
            exit;
        }

        $MaSP = $data["MaSP"];

        if (!$product->exists($MaSP)) {
            echo json_encode(["status" => "error", "message" => "Sản phẩm không tồn tại."]);
            exit;
        }

        $result = $product->delete($MaSP);
        echo json_encode([
            "status" => $result ? "success" : "error",
            "message" => $result ? "Đã xóa sản phẩm thành công." : "Không thể xóa sản phẩm."
        ]);
        break;

    // ===== 4️ Xem chi tiết 1 sản phẩm =====
    case "getOne":
        if (isset($_GET["MaSP"])) {
            $MaSP = $_GET["MaSP"];
            $data = $product->getOne($MaSP);
            echo json_encode($data ? ["status" => "success", "data" => $data] : ["status" => "error", "message" => "Không tìm thấy sản phẩm."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Thiếu mã sản phẩm."]);
        }
        break;

    // ===== 5️ Lấy tất cả sản phẩm =====
    case "getAll":
        $data = $product->getAll();
        if (!empty($data)) {
            foreach ($data as &$row) {
                $row["DonGia"] = floatval($row["DonGia"]);
                $row["TrangThai"] = $row["TrangThai"] == 1 ? "Hoạt động" : "Ngừng bán";
            }
            echo json_encode(["status" => "success", "data" => $data]);
        } else {
            echo json_encode(["status" => "error", "message" => "Không có sản phẩm nào."]);
        }
        break;

    // ===== 6️ Phân trang =====
    case "paginate":
        $limit = isset($_GET["limit"]) ? intval($_GET["limit"]) : 10;
        $page = isset($_GET["page"]) ? intval($_GET["page"]) : 1;
        $offset = ($page - 1) * $limit;

        $data = $product->paginate($limit, $offset);
        $total = $product->count();

        echo json_encode([
            "status" => "success",
            "total" => $total,
            "page" => $page,
            "limit" => $limit,
            "data" => $data
        ]);
        break;
    case "filter":
        $MaDM = $_GET["MaDM"] ?? null;
        $min  = $_GET["min"] ?? null;
        $max  = $_GET["max"] ?? null;

        $data = $product->filter($MaDM, $min, $max);
        echo json_encode([
            "status" => "success",
            "count"  => count($data),
            "data"   => $data
        ]);
        break;

    // ===== 8️ Sắp xếp theo giá =====
    case "sort":
        $order = $_GET["order"] ?? "asc"; // asc | desc
        $data = $product->sortByPrice($order);
        echo json_encode([
            "status" => "success",
            "order"  => $order,
            "data"   => $data
        ]);
        break;

    //=======9 Lấy theo mã CH=========
    case "getByStore":
    $MaCH = $_GET["MaCH"] ?? '';

    if (empty($MaCH)) {
        echo json_encode(["status" => "error", "message" => "Thiếu mã cửa hàng"]);
        exit;
    }

    // ✅ Dùng prepared statement đúng cách, KHÔNG có dấu ' ' bao quanh ?
    $sql = "SELECT sp.MaSP, sp.TenSP, sp.MaDM, dm.TenDM, sp.DonGia, sp.HinhAnh, 
                   k.SoLuongTon AS TonKho
            FROM tbl_sanpham sp
            JOIN tbl_danhmuc dm ON sp.MaDM = dm.MaDM
            JOIN tbl_kho k ON sp.MaSP = k.MaSP
            WHERE k.MaCH = ? AND k.SoLuongTon > 0";

    $stmt = $connect->prepare($sql);
    $stmt->bind_param("s", $MaCH);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $row["DonGia"] = floatval($row["DonGia"]);
            $row["TonKho"] = intval($row["TonKho"]);
            $data[] = $row;
        }
        echo json_encode(["status" => "success", "data" => $data]);
    } else {
        echo json_encode(["status" => "error", "message" => "Không có sản phẩm tồn trong cửa hàng này."]);
    }
    break;
    case "search":
        $keyword = $_GET["keyword"] ?? "";
        $MaCH = $_GET["MaCH"] ?? ""; // có thể rỗng nếu không chọn cửa hàng

        if (empty($keyword)) {
            echo json_encode(["status" => "error", "message" => "Thiếu từ khóa tìm kiếm"]);
            exit;
        }

        // Nếu có mã cửa hàng => chỉ lấy sản phẩm thuộc cửa hàng đó
        if (!empty($MaCH)) {
            $sql = "SELECT sp.MaSP, sp.TenSP, sp.MaDM, dm.TenDM, sp.DonGia, sp.HinhAnh,
                           k.SoLuongTon AS TonKho
                    FROM tbl_sanpham sp
                    JOIN tbl_danhmuc dm ON sp.MaDM = dm.MaDM
                    JOIN tbl_kho k ON sp.MaSP = k.MaSP
                    WHERE k.MaCH = ? AND sp.TenSP LIKE ? AND k.SoLuongTon > 0";
            $stmt = $connect->prepare($sql);
            $likeKeyword = "%$keyword%";
            $stmt->bind_param("ss", $MaCH, $likeKeyword);
        } else {
            // Không chọn cửa hàng => tìm trên toàn hệ thống
            $sql = "SELECT sp.MaSP, sp.TenSP, sp.MaDM, dm.TenDM, sp.DonGia, sp.HinhAnh
                    FROM tbl_sanpham sp
                    JOIN tbl_danhmuc dm ON sp.MaDM = dm.MaDM
                    WHERE sp.TenSP LIKE ?";
            $stmt = $connect->prepare($sql);
            $likeKeyword = "%$keyword%";
            $stmt->bind_param("s", $likeKeyword);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $row["DonGia"] = floatval($row["DonGia"]);
                if (isset($row["TonKho"])) $row["TonKho"] = intval($row["TonKho"]);
                $data[] = $row;
            }
            echo json_encode(["status" => "success", "count" => count($data), "data" => $data]);
        } else {
            echo json_encode(["status" => "error", "message" => "Không tìm thấy sản phẩm nào."]);
        }
        break;



    // ===== 0 Mặc định =====
    default:
        echo json_encode([
            "status" => "error",
            "message" => "Hành động không hợp lệ. Hãy dùng ?action=add|getOne|getAll|paginate|update|delete"
        ]);
        break;
}
?>
