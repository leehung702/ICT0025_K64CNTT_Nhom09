<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <select name="nam_sinh"> 
    <option value="">-- Chọn năm sinh --</option> 
    <?php 
    $nam = date('Y'); 
    for ($nam = $nam_hien_tai - 18; $nam >= $nam - 60; $nam--) { 
        echo "<option value='$nam'>$nam</option>"; 
    } 
    ?> 
</select>
</body>
</html>