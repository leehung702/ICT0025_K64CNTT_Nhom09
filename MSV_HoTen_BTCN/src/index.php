<?php
// READ: danh sách điểm học phần.
require 'db_connect.php';
require 'functions.php';

$thongbao = [
    'them_ok'    => 'Đã thêm điểm thành công.',
    'sua_ok'     => 'Đã cập nhật điểm thành công.',
    'xoa_ok'     => 'Đã xóa điểm.',
    'sua_khoa'   => 'Điểm đã khóa, không thể sửa.',
    'xoa_khoa'   => 'Điểm đã khóa, không thể xóa.',
    'khong_thay' => 'Không tìm thấy bản ghi điểm.',
    'loi'        => 'Có lỗi xảy ra, vui lòng thử lại.',
];
$ma = isset($_GET['msg']) && is_string($_GET['msg']) ? $_GET['msg'] : '';
$noi_dung = $thongbao[$ma] ?? '';

$rows = lay_danh_sach($conn,
    "SELECT d.id, s.ma_sv, s.ho_ten, h.ma_hp, h.ten_hp,
            d.diem_qt, d.diem_thi, d.diem_tk, d.xep_loai, d.trang_thai
     FROM diem d
     JOIN sinhvien s ON s.id = d.sinhvien_id
     JOIN hocphan  h ON h.id = d.hocphan_id
     ORDER BY d.id DESC");

$tieu_de = 'Danh sách điểm học phần';
require 'header.php';
?>
<h1>Danh sách điểm học phần</h1>

<?php if ($noi_dung !== ''): ?>
  <p class="thongbao"><?= e($noi_dung) ?></p>
<?php endif; ?>

<?php if (count($rows) === 0): ?>
  <p>Chưa có điểm nào. <a href="them.php">Nhập điểm mới</a></p>
<?php else: ?>
  <table>
    <tr>
      <th>STT</th><th>Sinh viên</th><th>Học phần</th>
      <th>Điểm QT</th><th>Điểm thi</th><th>Điểm TK</th>
      <th>Xếp loại</th><th>Trạng thái</th><th>Thao tác</th>
    </tr>
    <?php $stt = 1; foreach ($rows as $r): ?>
      <tr>
        <td><?= $stt++ ?></td>
        <td><?= e($r['ma_sv'] . ' - ' . $r['ho_ten']) ?></td>
        <td><?= e($r['ma_hp'] . ' - ' . $r['ten_hp']) ?></td>
        <td><?= $r['diem_qt']  === null ? '—' : e($r['diem_qt'])  ?></td>
        <td><?= $r['diem_thi'] === null ? '—' : e($r['diem_thi']) ?></td>
        <td><?= $r['diem_tk']  === null ? '—' : e($r['diem_tk'])  ?></td>
        <td><?= $r['xep_loai'] === null ? '—' : e($r['xep_loai']) ?></td>
        <td><?= e(nhan_trang_thai($r['trang_thai'])) ?></td>
        <td>
          <?php if ($r['trang_thai'] === 'Da_khoa'): ?>
            Đã khóa
          <?php else: ?>
            <a href="sua.php?id=<?= (int)$r['id'] ?>">Sửa</a>
            <form class="inline" method="post" action="xoa.php"
                  onsubmit="return confirm('Bạn chắc chắn muốn xóa điểm này?');">
              <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
              <button type="submit">Xóa</button>
            </form>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>
<?php endif; ?>
<?php require 'footer.php'; ?>
