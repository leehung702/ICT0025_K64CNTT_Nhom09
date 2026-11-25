
<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Danh sách học phần';

// Nhận từ khóa tìm kiếm
$keyword = trim($_GET['q'] ?? '');

// Xử lý tìm kiếm
if ($keyword !== '') {

    $stmt = $conn->prepare(
        'SELECT * FROM hocphan
         WHERE ma_hp LIKE :q1
         OR ten_hp LIKE :q2
         ORDER BY id DESC'
    );

    $search = "%{$keyword}%";

    $stmt->execute([
        'q1' => $search,
        'q2' => $search
    ]);

} else {

    $stmt = $conn->query(
        'SELECT * FROM hocphan ORDER BY id DESC'
    );
}

$hocphans = $stmt->fetchAll();

// Thông báo
$message = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);

require __DIR__ . '/../includes/header.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <div class="text-primary fw-semibold small text-uppercase">
            Danh mục dữ liệu
        </div>

        <h1 class="h3 fw-bold mb-1">Danh sách học phần</h1>

        <div class="text-secondary">
            Quản lý mã học phần, tên học phần và số tín chỉ.
        </div>
    </div>

    <a class="btn btn-primary px-4" href="add.php">
        ＋ Thêm học phần
    </a>
</div>

<?php if ($message): ?>
    <div class="alert alert-info">
        <?= e($message) ?>
    </div>
<?php endif; ?>

<div class="card page-card">
    <div class="card-body p-4">

        <!-- FORM TÌM KIẾM -->
        <form class="row g-2 mb-3" method="get">

            <div class="col-md-6">
                <input
                    class="form-control"
                    type="text"
                    name="q"
                    value="<?= e($keyword) ?>"
                    placeholder="Tìm theo mã hoặc tên học phần..."
                >
            </div>

            <div class="col-auto">
                <button type="submit" class="btn btn-outline-primary">
                    Tìm kiếm
                </button>
            </div>

            <div class="col-auto">
                <a class="btn btn-outline-secondary" href="index.php">
                    Đặt lại
                </a>
            </div>

        </form>

        <!-- BẢNG DANH SÁCH -->
        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead class="table-light">
                    <tr>
                        <th>STT</th>
                        <th>Mã học phần</th>
                        <th>Tên học phần</th>
                        <th class="text-center">Số tín chỉ</th>
                        <th class="text-end">Thao tác</th>
                    </tr>
                </thead>

                <tbody>

                <?php if (!$hocphans): ?>

                    <tr>
                        <td colspan="5" class="text-center text-secondary py-4">
                            Chưa có học phần phù hợp.
                        </td>
                    </tr>

                <?php else: ?>

                    <?php foreach ($hocphans as $i => $hp): ?>

                        <tr>

                            <td><?= $i + 1 ?></td>

                            <td>
                                <span class="badge text-bg-light border">
                                    <?= e($hp['ma_hp']) ?>
                                </span>
                            </td>

                            <td class="fw-semibold">
                                <?= e($hp['ten_hp']) ?>
                            </td>

                            <td class="text-center">
                                <?= e($hp['so_tin_chi']) ?>
                            </td>

                            <td class="text-end text-nowrap">

                                <a
                                    class="btn btn-sm btn-outline-primary"
                                    href="edit.php?id=<?= (int)$hp['id'] ?>"
                                >
                                    Sửa
                                </a>

                                <form
                                    class="d-inline"
                                    method="post"
                                    action="delete.php"
                                    onsubmit="return confirm('Bạn chắc chắn muốn xóa học phần này?');"
                                >

                                    <input
                                        type="hidden"
                                        name="csrf_token"
                                        value="<?= e(csrf_token()) ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= (int)$hp['id'] ?>"
                                    >

                                    <button
                                        type="submit"
                                        class="btn btn-sm btn-outline-danger"
                                    >
                                        Xóa
                                    </button>

                                </form>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

        <div class="small text-secondary">
            Tổng số: <?= count($hocphans) ?> học phần
        </div>

    </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
