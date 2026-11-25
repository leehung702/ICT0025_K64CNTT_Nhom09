<?php
// Form dùng chung cho them.php và sua.php.
// Biến cần có: $values, $loi, $ds_sv, $ds_hp, $action, $nut
?>
<?php if (!empty($loi)): ?>
  <ul class="loi">
    <?php foreach ($loi as $m): ?>
      <li><?= e($m) ?></li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>

<form method="post" action="<?= e($action) ?>">
  <label>Sinh viên (bắt buộc)
    <select name="sinhvien_id">
      <option value="">-- Chọn sinh viên --</option>
      <?php foreach ($ds_sv as $sv): ?>
        <option value="<?= (int)$sv['id'] ?>"
          <?= (string)$values['sinhvien_id'] === (string)$sv['id'] ? 'selected' : '' ?>>
          <?= e($sv['ma_sv'] . ' - ' . $sv['ho_ten']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </label>

  <label>Học phần (bắt buộc)
    <select name="hocphan_id">
      <option value="">-- Chọn học phần --</option>
      <?php foreach ($ds_hp as $hp): ?>
        <option value="<?= (int)$hp['id'] ?>"
          <?= (string)$values['hocphan_id'] === (string)$hp['id'] ? 'selected' : '' ?>>
          <?= e($hp['ma_hp'] . ' - ' . $hp['ten_hp']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </label>

  <label>Điểm quá trình (0 đến 10)
    <input type="text" name="diem_qt" inputmode="decimal" value="<?= e($values['diem_qt']) ?>">
  </label>

  <label>Điểm thi (0 đến 10)
    <input type="text" name="diem_thi" inputmode="decimal" value="<?= e($values['diem_thi']) ?>">
  </label>

  <label>
    <input type="checkbox" name="khoa" value="1" <?= $values['khoa'] === '1' ? 'checked' : '' ?>>
    Khóa điểm sau khi lưu (khóa rồi thì không sửa, không xóa được)
  </label>

  <p><button type="submit"><?= e($nut) ?></button> <a href="index.php">Hủy</a></p>
</form>
