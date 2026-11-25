<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($tieu_de ?? 'Quản lý điểm học phần') ?></title>
  <style>
    body { font-family: Arial, sans-serif; max-width: 960px; margin: 1.5rem auto; padding: 0 1rem; }
    nav a { margin-right: 1rem; }
    table { border-collapse: collapse; width: 100%; margin-top: 1rem; }
    th, td { border: 1px solid #999; padding: .4rem .6rem; text-align: left; }
    th { background: #f0f0f0; }
    label { display: block; margin-top: .8rem; }
    input[type=text], select { width: 100%; padding: .4rem; box-sizing: border-box; }
    button { padding: .35rem .8rem; }
    .loi { color: #b00020; }
    .ok  { color: #1b5e20; }
    .thongbao { padding: .5rem .8rem; background: #eef6ff; border: 1px solid #9cc3ee; }
    form.inline { display: inline; }
  </style>
</head>
<body>
  <nav>
    <a href="index.php">Danh sách điểm</a>
    <a href="them.php">Nhập điểm mới</a>
  </nav>
