<?php
require_once 'functions.php';

$id = (int)($_GET['id'] ?? 0);R
$book = getBookById($pdo, $id);

if (!$book) {
    header("Location: index.php");
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        die("Aksi ditolak: Token CSRF tidak valid!");
    }

    $judul      = trim($_POST['judul'] ?? '');
    $kategori   = trim($_POST['kategori'] ?? '');
    $harga_sewa = (int)($_POST['harga_sewa'] ?? 0);
    $stok       = (int)($_POST['stok'] ?? 0);

    $errors = validateBookInput($pdo, $judul, $harga_sewa, $stok, $id);

    if (empty($errors)) {
        updateBook($pdo, $id, $judul, $kategori, $harga_sewa, $stok);
        header("Location: index.php?status=success_update");
        exit;
    }
}

$csrf_token = generateCSRFToken();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Buku - Aqyun Library System</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #fbf9f5;
        }
        .card-custom {
            border-radius: 24px;
            border: none;
            background: #ffffff;
        }
        .form-control {
            border-radius: 12px;
            padding: 10px 15px;
        }
    </style>
</head>
<body>

<div class="container my-5" style="max-width: 550px;">
    <div class="card card-custom shadow-sm p-4">
        <h4 class="fw-bold mb-3" style="color: #6c5ce7;">✏️ Edit Data Buku</h4>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger border-0 rounded-3 mb-3">
                <ul class="mb-0 ps-3">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="edit.php?id=<?= $id; ?>" method="POST">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token; ?>">
            
            <div class="mb-3">
                <label class="form-label">Judul Buku</label>
                <input type="text" name="judul" class="form-control" value="<?= htmlspecialchars($book['judul'], ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Kategori</label>
                <input type="text" name="kategori" class="form-control" value="<?= htmlspecialchars($book['kategori'], ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Harga Sewa (Rp)</label>
                    <input type="number" name="harga_sewa" class="form-control" value="<?= htmlspecialchars($book['harga_sewa'], ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Stok</label>
                    <input type="number" name="stok" class="form-control" value="<?= htmlspecialchars($book['stok'], ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <a href="index.php" class="btn btn-light rounded-pill px-4">Kembali</a>
                <button type="submit" class="btn text-white rounded-pill px-4" style="background-color: #6c5ce7;">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>