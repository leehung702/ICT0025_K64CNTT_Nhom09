<?php
// READ: xem danh sách điểm (nối 3 bảng để hiện tên thay vì chỉ hiện id).
require 'db.php';
require 'helpers.php';

$sql = "SELECT d.id, sv.ma_sv, sv.ho_ten, hp.ma_hp, hp.ten_hp,
               d.diem_qt, d.diem_thi, d.diem_tk, d.xep_loai, d.trang_thai
        FROM diem AS d
        INNER JOIN sinhvien AS sv ON d.sinhvien_id = sv.id
        INNER JOIN hocphan  AS hp ON d.hocphan_id  = hp.id
        ORDER BY sv.ma_sv, hp.ma_hp";
$rows = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);

$msg = thong_bao($_GET['msg'] ?? '');

ve_dau_trang('Quản lý điểm học phần');
?>
<?php if ($msg !== ''): ?>
    <p class="ok"><?= h($msg) ?></p>
<?php endif; ?>

<p><a href="them.php">+ Thêm điểm</a></p>

<?php if (count($rows) === 0): ?>
    <p>Chưa có bản ghi điểm nào.</p>
<?php else: ?>
<table>
    <tr>
        <th>STT</th><th>Mã SV</th><th>Họ tên</th><th>Học phần</th>
        <th>Quá trình</th><th>Thi</th><th>Tổng kết</th><th>Xếp loại</th><th>Trạng thái</th><th>Thao tác</th>
    </tr>
    <?php foreach ($rows as $i => $r): ?>
    <tr>
        <td><?= $i + 1 ?></td>
        <td><?= h($r['ma_sv']) ?></td>
        <td><?= h($r['ho_ten']) ?></td>
        <td><?= h($r['ma_hp']) ?> - <?= h($r['ten_hp']) ?></td>
        <td><?= hien_diem($r['diem_qt']) ?></td>
        <td><?= hien_diem($r['diem_thi']) ?></td>
        <td><?= hien_diem($r['diem_tk']) ?></td>
        <td><?= h($r['xep_loai'] ?? '—') ?></td>
        <td><?= h($r['trang_thai']) ?></td>
        <td>
        <?php if ($r['trang_thai'] !== 'Da_khoa'): ?>
            <a href="sua.php?id=<?= (int)$r['id'] ?>">Sửa</a>
            <?php if ($r['trang_thai'] === 'Da_nhap'): ?>
            <form class="inline" method="POST" action="khoa.php">
                <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                <button type="submit">Khóa</button>
            </form>
            <?php endif; ?>
            <!-- DELETE bắt buộc dùng form POST theo id, có hỏi xác nhận -->
            <form class="inline" method="POST" action="xoa.php"
                  onsubmit="return confirm('Xóa bản ghi điểm này?');">
                <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                <button type="submit">Xóa</button>
            </form>
        <?php else: ?>
            Đã khóa
        <?php endif; ?>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<?php endif; ?>
<?php ve_cuoi_trang(); ?>
