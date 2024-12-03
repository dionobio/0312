<?php
class DonHangModel
{
    public $conn;

    // Kết nối CSDL
    public function __construct()
    {
        $this->conn = connectDB();
    }
    // Lấy danh sách đơn hàng theo tài khoản
    public function getOrdersByUser($tai_khoan_id)
    {
        $sql = "SELECT * FROM don_hangs WHERE tai_khoan_id = :tai_khoan_id ORDER BY ngay_dat DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['tai_khoan_id' => $tai_khoan_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getTrangThaiDonHang($trang_thai_id)
    {
        $sql = "SELECT ten_trang_thai FROM trang_thai_don_hangs WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $trang_thai_id]);
        return $stmt->fetchColumn(); // Lấy giá trị cột đầu tiên
    }
    public function getPhuongThucThanhToan($phuong_thuc_id)
    {
        $sql = "SELECT ten_phuong_thuc FROM phuong_thuc_thanh_toans WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $phuong_thuc_id]);
        return $stmt->fetchColumn(); // Lấy giá trị cột đầu tiên
    }

    // Lấy chi tiết đơn hàng
    public function getDonHangByMaDonHang($maDonHang)
    {
        $sql = "SELECT * FROM don_hangs WHERE ma_don_hang = :ma_don_hang";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['ma_don_hang' => $maDonHang]);
        return $stmt->fetch(PDO::FETCH_ASSOC); // Trả về chi tiết đơn hàng
    }

    public function getSanPhamByMaDonHang($maDonHang)
{
    $sql = "SELECT sp.ten_san_pham, sp.gia_ban, sp.hinh_anh, ctdh.so_luong 
            FROM chi_tiet_don_hangs ctdh 
            JOIN san_phams sp ON ctdh.san_pham_id = sp.id 
            WHERE ctdh.ma_don_hang = :ma_don_hang";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['ma_don_hang' => $maDonHang]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


    // Hủy đơn hàng
    public function huyDonHang($ma_don_hang)
    {
        $sql = "UPDATE don_hangs SET trang_thai_id = 7 WHERE ma_don_hang = :ma_don_hang AND trang_thai_id = 1";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(['ma_don_hang' => $ma_don_hang]);
    }

    public function capNhatTrangThaiDonHang($ma_don_hang, $trang_thai_id) {
        $sql = "UPDATE don_hangs SET trang_thai_id = :trang_thai_id WHERE ma_don_hang = :ma_don_hang";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':trang_thai_id', $trang_thai_id, PDO::PARAM_INT);
        $stmt->bindParam(':ma_don_hang', $ma_don_hang, PDO::PARAM_STR);
        $stmt->execute();
    }
    
}