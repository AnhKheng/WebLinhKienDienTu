<?php
class CuaHang {
    private $connect;

    public function __construct($db) {
        $this->connect = $db;
    }


    // ============== LẤY DOANH THU THEO THÁNG ==============
    public function getStatistical($maCH, $tungay = null, $denngay = null) {
        $where = "WHERE MaCH = ?";
        $params = [$maCH];
        $types = "s"; 

        if (!empty($tungay) && !empty($denngay)) {
            $where .= " AND DATE(NgayBan) BETWEEN ? AND ?";
            $params[] = $tungay;
            $params[] = $denngay;
            $types .= "ss"; 
        }

        $sql = "
            SELECT 
                MONTH(NgayBan) AS Thang,
                SUM(TongTien) AS TongDoanhThu,
                COUNT(MaHD) AS SoHoaDon
            FROM tbl_hoadonban
            $where
            GROUP BY MONTH(NgayBan)
            ORDER BY Thang ASC
        ";

        $stmt = $this->connect->prepare($sql);
        if (!$stmt) {
            return ["status" => "error", "message" => "Lỗi prepare(): " . $this->connect->error];
        }

        $stmt->bind_param($types, ...$params);
        if (!$stmt->execute()) {
            return ["status" => "error", "message" => "Lỗi execute(): " . $stmt->error];
        }

        $result = $stmt->get_result();
        $data = [];

        while ($row = $result->fetch_assoc()) {
            $thang = (int)$row['Thang'];
            $tongDoanhThu = (float)$row['TongDoanhThu'];
            $soHD = (int)$row['SoHoaDon'];

            $loinhuan = $tongDoanhThu * 0.2;

            $data[] = [
                "Thang" => $thang,
                "TongDoanhThu" => $tongDoanhThu,
                "LoiNhuan" => $loinhuan,
                "SoHoaDon" => $soHD
            ];
        }

        return ["status" => "success", "data" => $data];
    }

    // ============== BÁO CÁO TỒN KHO ==============
    public function getInventory($maCH) {
        $sql = "
            SELECT 
                sp.MaSP,
                sp.TenSP,
                dm.TenDM AS TenDanhMuc,
                sp.DonGia,
                k.SoLuongTon
            FROM tbl_kho k
            INNER JOIN tbl_sanpham sp ON k.MaSP = sp.MaSP
            LEFT JOIN tbl_danhmuc dm ON sp.MaDM = dm.MaDM
            WHERE k.MaCH = ?
            ORDER BY k.SoLuongTon DESC
        ";

        $stmt = $this->connect->prepare($sql);
        if (!$stmt) {
            return ["status" => "error", "message" => "Lỗi prepare(): " . $this->connect->error];
        }

        $stmt->bind_param("s", $maCH);
        if (!$stmt->execute()) {
            return ["status" => "error", "message" => "Lỗi execute(): " . $stmt->error];
        }

        $result = $stmt->get_result();
        $data = [];

        while ($row = $result->fetch_assoc()) {
            $data[] = [
                "sku" => $row["MaSP"],
                "name" => $row["TenSP"],
                "category" => $row["TenDanhMuc"] ?? "Không rõ",
                "cost" => (float)$row["DonGia"],
                "qty" => (int)$row["SoLuongTon"]
            ];
        }

        return ["status" => "success", "data" => $data];
    }

}
?>
