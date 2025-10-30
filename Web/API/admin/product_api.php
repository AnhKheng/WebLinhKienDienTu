<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

require_once "../../Config/db_config.php";
require_once "../../../Includes/Products.php";

$product = new Product($connect);

// 🧭 Lấy hành động từ query
$action = $_GET["action"] ?? "";

// 🧩 Xử lý các hành động
switch ($action) {

    // ===== 1️⃣ Thêm sản phẩm =====
    case "add":
        $data = json_decode(file_get_contents("php://input"), true);
        if (
            isset($data["MaSP"], $data["TenSP"], $data["MaDM"], $data["DonGia"], $data["MoTa"], $data["TrangThai"])
        ) {
            $MaSP = $data["MaSP"];
            $TenSP = $data["TenSP"];
            $MaDM = $data["MaDM"];
            $DonGia = floatval($data["DonGia"]);
            $MoTa = $data["MoTa"];
            $TrangThai = $data["TrangThai"];
            $HinhAnh = $data["HinhAnh"] ?? null;

            if ($product->exists($MaSP)) {
                echo json_encode(["status" => "error", "message" => "Mã sản phẩm đã tồn tại."]);
                exit;
            }

            $result = $product->add($MaSP, $TenSP, $MaDM, $DonGia, $MoTa, $TrangThai, $HinhAnh);
            echo json_encode([
                "status" => $result ? "success" : "error",
                "message" => $result ? "Thêm sản phẩm thành công." : "Không thể thêm sản phẩm."
            ]);
        } else {
            echo json_encode(["status" => "error", "message" => "Thiếu dữ liệu đầu vào."]);
        }
        break;

    // ===== 2️⃣ Cập nhật sản phẩm =====
    case "update":
        $data = json_decode(file_get_contents("php://input"), true);
        if (
            isset($data["MaSP"], $data["TenSP"], $data["MaDM"], $data["DonGia"], $data["MoTa"], $data["TrangThai"])
        ) {
            $MaSP = $data["MaSP"];
            $TenSP = $data["TenSP"];
            $MaDM = $data["MaDM"];
            $DonGia = floatval($data["DonGia"]);
            $MoTa = $data["MoTa"];
            $TrangThai = $data["TrangThai"];
            $HinhAnh = $data["HinhAnh"] ?? null;

            $result = $product->update($MaSP, $TenSP, $MaDM, $DonGia, $MoTa, $TrangThai, $HinhAnh);
            echo json_encode([
                "status" => $result ? "success" : "error",
                "message" => $result ? "Cập nhật sản phẩm thành công." : "Không thể cập nhật sản phẩm."
            ]);
        } else {
            echo json_encode(["status" => "error", "message" => "Thiếu dữ liệu đầu vào."]);
        }
        break;

    // ===== 3️⃣ Xóa sản phẩm =====
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

    // ===== 4️⃣ Xem chi tiết 1 sản phẩm =====
    case "getOne":
        if (isset($_GET["MaSP"])) {
            $MaSP = $_GET["MaSP"];
            $data = $product->getOne($MaSP);
            echo json_encode($data ? ["status" => "success", "data" => $data] : ["status" => "error", "message" => "Không tìm thấy sản phẩm."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Thiếu mã sản phẩm."]);
        }
        break;

    // ===== 5️⃣ Lấy tất cả sản phẩm =====
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

    // ===== 6️⃣ Phân trang =====
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

    // ===== 7️⃣ Mặc định =====
    default:
        echo json_encode([
            "status" => "error",
            "message" => "Hành động không hợp lệ. Hãy dùng ?action=add|getOne|getAll|paginate|update|delete"
        ]);
        break;
}
?>
