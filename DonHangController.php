
    <?php
    class DonHangController {
        public $model;

        public function __construct() {
            $this->model = new DonHangModel();
        }
    // Hiển thị danh sách đơn hàng
    public function LichSuMuaHang()
{
    if (isset($_SESSION['nguoidungs_client'])) {
        $tai_khoan_id = $_SESSION['nguoidungs_client']['id'];
        $diachi = $_SESSION['nguoidungs_client']['dia_chi'];


        // Lấy lại danh sách đơn hàng từ cơ sở dữ liệu mỗi lần truy cập trang
        $orders = $this->model->getOrdersByUser($tai_khoan_id);

        // Cập nhật lại session với danh sách đơn hàng mới
        $_SESSION['orders'] = $orders;

        // Thêm tên trạng thái và phương thức thanh toán
        foreach (  $orders as &$order) {
            $order['ten_trang_thai'] = $this->model->getTrangThaiDonHang($order['trang_thai_id']);
            $order['ten_phuong_thuc'] = $this->model->getPhuongThucThanhToan($order['phuong_thuc_thanh_toan_id']);
        }

        // Render view
        require_once './views/donhang/danhsachdonhang.php';
    } else {
        header("Location: index.php?act=login");
        exit();
    }
}



    // Hiển thị chi tiết đơn hàng
    // Hiển thị chi tiết đơn hàng
public function ChiTietMuaHang()
{
    if (isset($_GET['ma_don_hang'])) {
        $ma_don_hang = $_GET['ma_don_hang'];

        // Lấy thông tin chi tiết đơn hàng
        $donHang = $this->model->getDonHangByMaDonHang($ma_don_hang);

        if (empty($donHang)) {
            $_SESSION['error'] = 'Đơn hàng không tồn tại.';
            header('Location: index.php?act=lich-su-mua-hang');
            exit();
        }

        // Lấy danh sách sản phẩm trong đơn hàng
        $sanPhamTrongDon = $this->model->getSanPhamByMaDonHang($ma_don_hang);

        // Render view chi tiết đơn hàng
        require_once './views/donhang/chitietdonhang.php';
    } else {
        $_SESSION['error'] = 'Mã đơn hàng không hợp lệ.';
        header('Location: index.php?act=lich-su-mua-hang');
        exit();
    }
}

    
    // Hủy đơn hàng
    public function HuyDonHang(){
        if (isset($_GET['ma_don_hang'])) {
            $ma_don_hang = $_GET['ma_don_hang'];
    
            // Kiểm tra trạng thái của đơn hàng có phải là "Chờ Xác Nhận" (id = 1) hay không
            $order = $this->model->getDonHangByMaDonHang($ma_don_hang);
            
            if (empty($order)) {
                // Nếu không có đơn hàng với mã đó
                $_SESSION['error'] = 'Đơn hàng không tồn tại.';
                header('Location: index.php?act=lich-su-mua-hang');
                exit();
            }
    
            // Kiểm tra trạng thái đơn hàng có phải là "Chờ Xác Nhận" (id = 1)
            if ($order[0]['trang_thai_id'] != 0) {
                // Nếu không phải trạng thái "Chờ Xác Nhận", không thể hủy đơn
                $_SESSION['error'] = 'Không thể hủy đơn hàng này, chỉ đơn hàng có trạng thái "Chờ Xác Nhận" mới có thể hủy.';
                header('Location: index.php?act=lich-su-mua-hang');
                exit();
            }
    
            // Tiến hành hủy đơn hàng
            $this->model->huyDonHang($ma_don_hang);
    
            // Lưu thông báo thành công
            $_SESSION['success'] = 'Đơn hàng đã được hủy thành công.';
            
            // Chuyển hướng về trang lịch sử đơn hàng
            header('Location: index.php?act=lich-su-mua-hang');
            exit();
        } else {
            // Nếu không có ma_don_hang thì trả về lỗi hoặc thông báo
            $_SESSION['error'] = 'Mã đơn hàng không hợp lệ.';
            header('Location: index.php?act=lich-su-mua-hang');
            exit();
        }
    }

    public function XacNhanDonHang() {
        if (isset($_GET['ma_don_hang'])) {
            $ma_don_hang = $_GET['ma_don_hang'];
    
            // Lấy thông tin đơn hàng
            $donHang = $this->model->getDonHangByMaDonHang($ma_don_hang);
    
            if (empty($donHang)) {
                $_SESSION['error'] = 'Đơn hàng không tồn tại.';
                header('Location: index.php?act=lich-su-mua-hang');
                exit();
            }
    
            // Kiểm tra trạng thái đơn hàng là "Đã Giao" (id = 4)
            if ($donHang['trang_thai_id'] != 4) {
                $_SESSION['error'] = 'Chỉ những đơn hàng ở trạng thái "Đã Giao" mới có thể xác nhận.';
                header('Location: index.php?act=lich-su-mua-hang');
                exit();
            }
    
            // Chuyển trạng thái đơn hàng sang "Thành Công" (id = 5)
            $this->model->capNhatTrangThaiDonHang($ma_don_hang, 5);
    
            $_SESSION['success'] = 'Đơn hàng đã được xác nhận thành công.';
            header('Location: index.php?act=lich-su-mua-hang');
            exit();
        } else {
            $_SESSION['error'] = 'Mã đơn hàng không hợp lệ.';
            header('Location: index.php?act=lich-su-mua-hang');
            exit();
        }
    }
    
    
    
}
