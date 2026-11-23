<?php
declare(strict_types=1);

/*
 * Bài CRUD cá nhân - Thành viên 5 (Nhóm 09)
 * Quản lý Điểm & Xếp loại (bảng diem): Xem/Tra cứu - Thêm - Sửa - Xóa (+ Khóa điểm).
 * Mọi truy vấn có dữ liệu nhập đều dùng PDO prepared statement; dữ liệu hiển thị
 * được bọc htmlspecialchars() qua hàm e(); Xóa/Khóa chỉ nhận form POST theo id.
 */

session_start();
require_once __DIR__ . '/functions.php';
require_once is_file(__DIR__ . '/config.php') ? __DIR__ . '/config.php' : __DIR__ . '/config.example.php';

/** @var PDO $pdo */

/* ---------------------------------------------------------------
 * Dữ liệu cho ô chọn
 * ------------------------------------------------------------- */
$dsSinhVien = $pdo->query(
    'SELECT sv.id, sv.ma_sv, sv.ho_ten, l.ma_lop
       FROM sinhvien sv LEFT JOIN lop l ON l.id = sv.lop_id
      ORDER BY sv.ma_sv'
)->fetchAll();
$dsHocPhan = $pdo->query('SELECT id, ma_hp, ten_hp, so_tin_chi FROM hocphan ORDER BY ma_hp')->fetchAll();

/** Lấy một dòng điểm (kèm thông tin SV, HP) theo id. */
function lay_diem(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare(
        'SELECT d.*, sv.ma_sv, sv.ho_ten, hp.ma_hp, hp.ten_hp
           FROM diem d
           JOIN sinhvien sv ON sv.id = d.sinhvien_id
           JOIN hocphan  hp ON hp.id = d.hocphan_id
          WHERE d.id = :id'
    );
    $stmt->execute(['id' => $id]);
    $row = $stmt->fetch();
    return $row === false ? null : $row;
}

$errors   = [];
$old      = ['sinhvien_id' => '', 'hocphan_id' => '', 'diem_qt' => '', 'diem_thi' => ''];
$editId   = null;
$editRow  = null;
$editOld  = ['diem_qt' => '', 'diem_thi' => ''];

/* ---------------------------------------------------------------
 * Xử lý POST: create | update | lock | delete
 * ------------------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string)($_POST['action'] ?? '');

    if (!csrf_ok()) {
        flash_set('error', 'Phiên làm việc không hợp lệ, vui lòng thử lại.');
        header('Location: index.php');
        exit;
    }

    /* ---- THÊM ---- */
    if ($action === 'create') {
        $old = [
            'sinhvien_id' => trim((string)($_POST['sinhvien_id'] ?? '')),
            'hocphan_id'  => trim((string)($_POST['hocphan_id'] ?? '')),
            'diem_qt'     => trim((string)($_POST['diem_qt'] ?? '')),
            'diem_thi'    => trim((string)($_POST['diem_thi'] ?? '')),
        ];
        $svId = doc_id($old['sinhvien_id']);
        $hpId = doc_id($old['hocphan_id']);
        if ($svId === null) $errors[] = 'Vui lòng chọn sinh viên.';
        if ($hpId === null) $errors[] = 'Vui lòng chọn học phần.';

        [$qt, $loiQt]   = parse_diem($old['diem_qt'], 'Điểm quá trình');
        [$thi, $loiThi] = parse_diem($old['diem_thi'], 'Điểm thi');
        if ($loiQt)  $errors[] = $loiQt;
        if ($loiThi) $errors[] = $loiThi;
        if (!$loiQt && !$loiThi && (($qt === null) !== ($thi === null))) {
            $errors[] = 'Hãy nhập đủ cả điểm quá trình và điểm thi, hoặc để trống cả hai (trạng thái Chưa nhập).';
        }

        if (!$errors) {
            $c = $pdo->prepare('SELECT COUNT(*) FROM sinhvien WHERE id = :id');
            $c->execute(['id' => $svId]);
            if ((int)$c->fetchColumn() === 0) $errors[] = 'Sinh viên được chọn không tồn tại.';
            $c = $pdo->prepare('SELECT COUNT(*) FROM hocphan WHERE id = :id');
            $c->execute(['id' => $hpId]);
            if ((int)$c->fetchColumn() === 0) $errors[] = 'Học phần được chọn không tồn tại.';
        }
        if (!$errors) {
            $c = $pdo->prepare('SELECT COUNT(*) FROM diem WHERE sinhvien_id = :sv AND hocphan_id = :hp');
            $c->execute(['sv' => $svId, 'hp' => $hpId]);
            if ((int)$c->fetchColumn() > 0) {
                $errors[] = 'Sinh viên này đã có bản ghi điểm cho học phần đã chọn. Hãy dùng nút Sửa/Nhập điểm.';
            }
        }

        if (!$errors) {
            if ($qt === null) {              // cả hai trống -> Chưa nhập
                $tk = null; $xl = null; $tt = 'Chua_nhap';
            } else {                          // đủ hai điểm -> Đã nhập, tính tổng kết + xếp loại
                $tk = tinh_diem_tk($qt, $thi);
                $xl = xep_loai($tk);
                $tt = 'Da_nhap';
            }
            try {
                $stmt = $pdo->prepare(
                    'INSERT INTO diem (sinhvien_id, hocphan_id, diem_qt, diem_thi, diem_tk, xep_loai, trang_thai)
                     VALUES (:sv, :hp, :qt, :thi, :tk, :xl, :tt)'
                );
                $stmt->execute(['sv' => $svId, 'hp' => $hpId, 'qt' => $qt, 'thi' => $thi,
                                'tk' => $tk, 'xl' => $xl, 'tt' => $tt]);
                flash_set('ok', $tt === 'Da_nhap'
                    ? "Đã lưu điểm. Tổng kết " . hien_diem($tk) . ", xếp loại $xl."
                    : 'Đã tạo bản ghi điểm ở trạng thái Chưa nhập.');
                header('Location: index.php');
                exit;
            } catch (PDOException $ex) {
                $errors[] = 'Không lưu được do lỗi cơ sở dữ liệu (có thể trùng bản ghi hoặc vi phạm ràng buộc).';
            }
        }
    }

    /* ---- SỬA / NHẬP ĐIỂM ---- */
    elseif ($action === 'update') {
        $editId = doc_id($_POST['id'] ?? null);
        $editOld = [
            'diem_qt'  => trim((string)($_POST['diem_qt'] ?? '')),
            'diem_thi' => trim((string)($_POST['diem_thi'] ?? '')),
        ];
        $editRow = $editId === null ? null : lay_diem($pdo, $editId);

        if ($editRow === null) {
            flash_set('error', 'Không tìm thấy bản ghi điểm cần sửa (ID không tồn tại hoặc không hợp lệ).');
            header('Location: index.php');
            exit;
        }
        if ($editRow['trang_thai'] === 'Da_khoa') {
            flash_set('error', 'Điểm đã khóa, không được sửa.');
            header('Location: index.php');
            exit;
        }

        [$qt, $loiQt]   = parse_diem($editOld['diem_qt'], 'Điểm quá trình');
        [$thi, $loiThi] = parse_diem($editOld['diem_thi'], 'Điểm thi');
        if ($loiQt)  $errors[] = $loiQt;
        if ($loiThi) $errors[] = $loiThi;
        if (!$loiQt && $qt === null)   $errors[] = 'Vui lòng nhập điểm quá trình.';
        if (!$loiThi && $thi === null) $errors[] = 'Vui lòng nhập điểm thi.';

        if (!$errors) {
            $tk = tinh_diem_tk($qt, $thi);
            $xl = xep_loai($tk);
            try {
                // Điều kiện trang_thai <> 'Da_khoa' chặn sửa điểm đã khóa ngay trong SQL.
                $stmt = $pdo->prepare(
                    "UPDATE diem
                        SET diem_qt = :qt, diem_thi = :thi, diem_tk = :tk,
                            xep_loai = :xl, trang_thai = 'Da_nhap'
                      WHERE id = :id AND trang_thai <> 'Da_khoa'"
                );
                $stmt->execute(['qt' => $qt, 'thi' => $thi, 'tk' => $tk, 'xl' => $xl, 'id' => $editId]);
                if ($stmt->rowCount() === 0) {
                    flash_set('error', 'Không cập nhật được (bản ghi không còn tồn tại hoặc đã bị khóa).');
                } else {
                    flash_set('ok', "Đã cập nhật điểm. Tổng kết " . hien_diem($tk) . ", xếp loại $xl.");
                }
                header('Location: index.php');
                exit;
            } catch (PDOException $ex) {
                $errors[] = 'Không cập nhật được do lỗi cơ sở dữ liệu.';
            }
        }
    }

    /* ---- KHÓA ĐIỂM (Đã nhập -> Đã khóa) ---- */
    elseif ($action === 'lock') {
        $id = doc_id($_POST['id'] ?? null);
        if ($id === null) {
            flash_set('error', 'ID không hợp lệ.');
        } else {
            $stmt = $pdo->prepare("UPDATE diem SET trang_thai = 'Da_khoa' WHERE id = :id AND trang_thai = 'Da_nhap'");
            $stmt->execute(['id' => $id]);
            flash_set($stmt->rowCount() === 1 ? 'ok' : 'error',
                $stmt->rowCount() === 1
                    ? 'Đã khóa điểm.'
                    : 'Không khóa được: chỉ khóa được điểm ở trạng thái Đã nhập (hoặc bản ghi không tồn tại).');
        }
        header('Location: index.php');
        exit;
    }

    /* ---- XÓA (chỉ form POST theo id) ---- */
    elseif ($action === 'delete') {
        $id = doc_id($_POST['id'] ?? null);
        if ($id === null) {
            flash_set('error', 'ID không hợp lệ.');
        } else {
            $stmt = $pdo->prepare("DELETE FROM diem WHERE id = :id AND trang_thai <> 'Da_khoa'");
            $stmt->execute(['id' => $id]);
            if ($stmt->rowCount() === 1) {
                flash_set('ok', 'Đã xóa bản ghi điểm.');
            } else {
                flash_set('error', 'Không xóa được: bản ghi không tồn tại hoặc điểm đã khóa.');
            }
        }
        header('Location: index.php');
        exit;
    }
}

/* ---------------------------------------------------------------
 * Chế độ sửa (GET ?edit=ID)
 * ------------------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['edit'])) {
    $editId = doc_id($_GET['edit']);
    $editRow = $editId === null ? null : lay_diem($pdo, $editId);
    if ($editRow === null) {
        flash_set('error', 'Không tìm thấy bản ghi điểm cần sửa.');
        header('Location: index.php');
        exit;
    }
    if ($editRow['trang_thai'] === 'Da_khoa') {
        flash_set('error', 'Điểm đã khóa, không được sửa.');
        header('Location: index.php');
        exit;
    }
    $editOld = [
        'diem_qt'  => $editRow['diem_qt'] === null ? '' : hien_diem($editRow['diem_qt']),
        'diem_thi' => $editRow['diem_thi'] === null ? '' : hien_diem($editRow['diem_thi']),
    ];
}

/* ---------------------------------------------------------------
 * XEM / TRA CỨU: lọc theo từ khóa SV, học phần, xếp loại, trạng thái
 * ------------------------------------------------------------- */
$q  = trim((string)($_GET['q'] ?? ''));
$fHp = doc_id($_GET['hp'] ?? null);
$fXl = (string)($_GET['xl'] ?? '');
$fTt = (string)($_GET['tt'] ?? '');
if (!in_array($fXl, XEP_LOAI, true) && $fXl !== 'CHUA') $fXl = '';
if (!array_key_exists($fTt, TRANG_THAI)) $fTt = '';

$sql = 'SELECT d.id, d.diem_qt, d.diem_thi, d.diem_tk, d.xep_loai, d.trang_thai,
               sv.ma_sv, sv.ho_ten, l.ma_lop, hp.ma_hp, hp.ten_hp, hp.so_tin_chi
          FROM diem d
          JOIN sinhvien sv ON sv.id = d.sinhvien_id
          JOIN hocphan  hp ON hp.id = d.hocphan_id
          LEFT JOIN lop l  ON l.id = sv.lop_id
         WHERE 1 = 1';
$params = [];
if ($q !== '') {
    $sql .= " AND (sv.ma_sv LIKE :q1 ESCAPE '!' OR sv.ho_ten LIKE :q2 ESCAPE '!')";
    $params['q1'] = $params['q2'] = '%' . like_escape($q) . '%';
}
if ($fHp !== null) { $sql .= ' AND d.hocphan_id = :hp'; $params['hp'] = $fHp; }
if ($fXl === 'CHUA') { $sql .= ' AND d.xep_loai IS NULL'; }
elseif ($fXl !== '') { $sql .= ' AND d.xep_loai = :xl'; $params['xl'] = $fXl; }
if ($fTt !== '') { $sql .= ' AND d.trang_thai = :tt'; $params['tt'] = $fTt; }
$sql .= ' ORDER BY sv.ma_sv, hp.ma_hp';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

// Thống kê trên kết quả đang lọc
$demXl = array_fill_keys(XEP_LOAI, 0);
$tong = 0.0; $soCoDiem = 0; $tongTc = 0; $tongTcDiem = 0.0; $svSet = [];
foreach ($rows as $r) {
    $svSet[$r['ma_sv']] = true;
    if ($r['xep_loai'] !== null) $demXl[$r['xep_loai']]++;
    if ($r['diem_tk'] !== null) {
        $tong += (float)$r['diem_tk'];
        $soCoDiem++;
        $tongTc += (int)$r['so_tin_chi'];
        $tongTcDiem += (float)$r['diem_tk'] * (int)$r['so_tin_chi'];
    }
}
$dtbChung = $soCoDiem ? $tong / $soCoDiem : null;
$dtbTinChi = (count($svSet) === 1 && $tongTc > 0) ? $tongTcDiem / $tongTc : null;

$flash = flash_get();
$csrf = csrf_token();
$locBatKy = ($q !== '' || $fHp !== null || $fXl !== '' || $fTt !== '');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Quản lý Điểm &amp; Xếp loại</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<main class="wrap">
  <header class="top">
    <h1>Quản lý Điểm &amp; Xếp loại</h1>
    <p>Nhóm 09 · Thành viên 5 · Công thức: tổng kết = <?= e(TRONG_SO_QT) ?> × quá trình + <?= e(TRONG_SO_THI) ?> × thi ·
       A ≥ 8.5 · B ≥ 7.0 · C ≥ 5.5 · D ≥ 4.0 · F &lt; 4.0</p>
  </header>

  <?php if ($flash): ?>
    <div class="alert <?= $flash['loai'] === 'ok' ? 'ok' : 'err' ?>" role="status"><?= e($flash['noi_dung']) ?></div>
  <?php endif; ?>
  <?php if ($errors): ?>
    <div class="alert err" role="alert"><strong>Có lỗi:</strong>
      <ul><?php foreach ($errors as $m): ?><li><?= e($m) ?></li><?php endforeach; ?></ul>
    </div>
  <?php endif; ?>

  <?php if ($editRow): ?>
  <!-- ===== FORM SỬA / NHẬP ĐIỂM ===== -->
  <section class="card">
    <h2><?= $editRow['trang_thai'] === 'Chua_nhap' ? 'Nhập điểm' : 'Sửa điểm' ?></h2>
    <p class="muted"><?= e($editRow['ma_sv']) ?> – <?= e($editRow['ho_ten']) ?> · <?= e($editRow['ma_hp']) ?> – <?= e($editRow['ten_hp']) ?>
       · Trạng thái: <?= e(TRANG_THAI[$editRow['trang_thai']]) ?></p>
    <form method="post" action="index.php" class="grid">
      <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
      <input type="hidden" name="action" value="update">
      <input type="hidden" name="id" value="<?= e($editRow['id']) ?>">
      <label>Điểm quá trình (0–10)
        <input type="text" name="diem_qt" inputmode="decimal" value="<?= e($editOld['diem_qt']) ?>" required></label>
      <label>Điểm thi (0–10)
        <input type="text" name="diem_thi" inputmode="decimal" value="<?= e($editOld['diem_thi']) ?>" required></label>
      <div class="actions">
        <button type="submit" class="btn primary">Lưu thay đổi</button>
        <a class="btn" href="index.php">Hủy sửa</a>
      </div>
    </form>
  </section>
  <?php else: ?>
  <!-- ===== FORM THÊM ===== -->
  <section class="card">
    <h2>Thêm bản ghi điểm</h2>
    <form method="post" action="index.php" class="grid">
      <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
      <input type="hidden" name="action" value="create">
      <label>Sinh viên
        <select name="sinhvien_id" required>
          <option value="">— Chọn sinh viên —</option>
          <?php foreach ($dsSinhVien as $s): ?>
            <option value="<?= e($s['id']) ?>" <?= (string)$s['id'] === $old['sinhvien_id'] ? 'selected' : '' ?>>
              <?= e($s['ma_sv']) ?> – <?= e($s['ho_ten']) ?> (<?= e($s['ma_lop'] ?? '?') ?>)</option>
          <?php endforeach; ?>
        </select></label>
      <label>Học phần
        <select name="hocphan_id" required>
          <option value="">— Chọn học phần —</option>
          <?php foreach ($dsHocPhan as $h): ?>
            <option value="<?= e($h['id']) ?>" <?= (string)$h['id'] === $old['hocphan_id'] ? 'selected' : '' ?>>
              <?= e($h['ma_hp']) ?> – <?= e($h['ten_hp']) ?></option>
          <?php endforeach; ?>
        </select></label>
      <label>Điểm quá trình (0–10)
        <input type="text" name="diem_qt" inputmode="decimal" value="<?= e($old['diem_qt']) ?>" placeholder="để trống = chưa nhập"></label>
      <label>Điểm thi (0–10)
        <input type="text" name="diem_thi" inputmode="decimal" value="<?= e($old['diem_thi']) ?>" placeholder="để trống = chưa nhập"></label>
      <div class="actions"><button type="submit" class="btn primary">Lưu</button></div>
    </form>
  </section>
  <?php endif; ?>

  <!-- ===== TRA CỨU ===== -->
  <section class="card">
    <h2>Tra cứu điểm</h2>
    <form method="get" action="index.php" class="grid filters">
      <label>Mã SV / họ tên
        <input type="text" name="q" value="<?= e($q) ?>" placeholder="VD: SV001 hoặc Minh Anh"></label>
      <label>Học phần
        <select name="hp">
          <option value="">Tất cả</option>
          <?php foreach ($dsHocPhan as $h): ?>
            <option value="<?= e($h['id']) ?>" <?= $fHp === (int)$h['id'] ? 'selected' : '' ?>><?= e($h['ma_hp']) ?></option>
          <?php endforeach; ?>
        </select></label>
      <label>Xếp loại
        <select name="xl">
          <option value="">Tất cả</option>
          <?php foreach (XEP_LOAI as $x): ?>
            <option value="<?= e($x) ?>" <?= $fXl === $x ? 'selected' : '' ?>><?= e($x) ?></option>
          <?php endforeach; ?>
          <option value="CHUA" <?= $fXl === 'CHUA' ? 'selected' : '' ?>>Chưa xếp loại</option>
        </select></label>
      <label>Trạng thái
        <select name="tt">
          <option value="">Tất cả</option>
          <?php foreach (TRANG_THAI as $k => $v): ?>
            <option value="<?= e($k) ?>" <?= $fTt === $k ? 'selected' : '' ?>><?= e($v) ?></option>
          <?php endforeach; ?>
        </select></label>
      <div class="actions">
        <button type="submit" class="btn primary">Tìm</button>
        <?php if ($locBatKy): ?><a class="btn" href="index.php">Xóa bộ lọc</a><?php endif; ?>
      </div>
    </form>

    <p class="stats">
      <strong><?= count($rows) ?></strong> bản ghi
      <?php if ($dtbChung !== null): ?>· ĐTB tổng kết: <strong><?= e(number_format($dtbChung, 2, '.', '')) ?></strong><?php endif; ?>
      <?php if ($dtbTinChi !== null): ?>· ĐTB theo tín chỉ của sinh viên này: <strong><?= e(number_format($dtbTinChi, 2, '.', '')) ?></strong><?php endif; ?>
      <br>
      <?php foreach ($demXl as $x => $n): ?><span class="chip xl-<?= e($x) ?>"><?= e($x) ?>: <?= $n ?></span> <?php endforeach; ?>
    </p>

    <div class="table-wrap">
    <table>
      <thead>
        <tr><th>ID</th><th>Mã SV</th><th>Họ tên</th><th>Lớp</th><th>Học phần</th>
            <th>Quá trình</th><th>Thi</th><th>Tổng kết</th><th>Xếp loại</th><th>Trạng thái</th><th>Thao tác</th></tr>
      </thead>
      <tbody>
      <?php if (!$rows): ?>
        <tr><td colspan="11" class="empty"><?= $locBatKy ? 'Không có bản ghi nào khớp bộ lọc.' : 'Chưa có dữ liệu điểm.' ?></td></tr>
      <?php endif; ?>
      <?php foreach ($rows as $r): ?>
        <tr>
          <td><?= e($r['id']) ?></td>
          <td><?= e($r['ma_sv']) ?></td>
          <td><?= e($r['ho_ten']) ?></td>
          <td><?= e($r['ma_lop'] ?? '') ?></td>
          <td><?= e($r['ma_hp']) ?> – <?= e($r['ten_hp']) ?></td>
          <td class="num"><?= e(hien_diem($r['diem_qt'])) ?></td>
          <td class="num"><?= e(hien_diem($r['diem_thi'])) ?></td>
          <td class="num"><strong><?= e(hien_diem($r['diem_tk'])) ?></strong></td>
          <td><?php if ($r['xep_loai'] !== null): ?><span class="chip xl-<?= e($r['xep_loai']) ?>"><?= e($r['xep_loai']) ?></span><?php else: ?>—<?php endif; ?></td>
          <td><span class="st st-<?= e($r['trang_thai']) ?>"><?= e(TRANG_THAI[$r['trang_thai']] ?? $r['trang_thai']) ?></span></td>
          <td class="ops">
            <?php if ($r['trang_thai'] !== 'Da_khoa'): ?>
              <a class="btn sm" href="index.php?edit=<?= e($r['id']) ?>"><?= $r['trang_thai'] === 'Chua_nhap' ? 'Nhập điểm' : 'Sửa' ?></a>
            <?php endif; ?>
            <?php if ($r['trang_thai'] === 'Da_nhap'): ?>
              <form method="post" action="index.php" class="inline"
                    onsubmit="return confirm('Khóa điểm này? Sau khi khóa sẽ không thể sửa hoặc xóa.');">
                <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
                <input type="hidden" name="action" value="lock">
                <input type="hidden" name="id" value="<?= e($r['id']) ?>">
                <button type="submit" class="btn sm">Khóa</button>
              </form>
            <?php endif; ?>
            <?php if ($r['trang_thai'] !== 'Da_khoa'): ?>
              <form method="post" action="index.php" class="inline"
                    onsubmit="return confirm('Xóa bản ghi điểm này? Thao tác không thể hoàn tác.');">
                <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= e($r['id']) ?>">
                <button type="submit" class="btn sm danger">Xóa</button>
              </form>
            <?php else: ?>
              <span class="muted">🔒 Đã khóa</span>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    </div>
  </section>
</main>
</body>
</html>
