<?php
require_once 'functions.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        die("Aksi ditolak: Token CSRF tidak valid!");
    }

    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $judul      = trim($_POST['judul'] ?? '');
        $kategori   = trim($_POST['kategori'] ?? '');
        $harga_sewa = (int)($_POST['harga_sewa'] ?? 0);
        $stok       = (int)($_POST['stok'] ?? 0);

        $errors = validateBookInput($pdo, $judul, $harga_sewa, $stok);

        if (empty($errors)) {
            createBook($pdo, $judul, $kategori, $harga_sewa, $stok);
            header("Location: index.php?status=success_add");
            exit;
        }
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            deleteBook($pdo, $id);
            header("Location: index.php?status=success_delete");
            exit;
        }
    }
}

$books = getAllBooks($pdo);
$csrf_token = generateCSRFToken();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aqyun Library System 🌸</title>
    <!-- Google Fonts & FontAwesome -->
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --bg-gradient: linear-gradient(135deg, #fff0f5 0%, #e6e6fa 50%, #f0f8ff 100%);
            --primary-cute: #8a70d6;
            --primary-hover: #7353cc;
            --pink-soft: #ffb6c1;
            --pink-accent: #ff85a1;
            --text-main: #4a4a68;
            --card-shadow: 0 10px 25px rgba(138, 112, 214, 0.12);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg-gradient);
            background-attachment: fixed;
            color: var(--text-main);
            min-height: 100vh;
        }

        h1, h2, h3, h4, .brand-title {
            font-family: 'Fredoka', cursive;
        }

        /* Cute Decorative Background Floating Elements */
        .cute-blob {
            position: fixed;
            border-radius: 50%;
            filter: blur(40px);
            z-index: -1;
            opacity: 0.6;
        }
        .blob-1 { top: -50px; left: -50px; width: 250px; height: 250px; background: #ffc0cb; }
        .blob-2 { bottom: -50px; right: -50px; width: 300px; height: 300px; background: #e6e6fa; }

        /* Navbar Style */
        .navbar-custom {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            border-bottom: 3px dashed #f1e4ff;
            border-radius: 0 0 25px 25px;
        }

        .brand-title {
            color: var(--primary-cute);
            font-size: 1.5rem;
        }

        /* Cute Button */
        .btn-cute-primary {
            background: linear-gradient(45deg, #ff9a9e, #fecfef);
            color: #5a3e61;
            border-radius: 25px;
            padding: 10px 24px;
            font-family: 'Fredoka', cursive;
            font-weight: 500;
            border: none;
            box-shadow: 0 4px 15px rgba(255, 154, 158, 0.4);
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .btn-cute-primary:hover {
            transform: scale(1.05) rotate(-1deg);
            background: linear-gradient(45deg, #fecfef, #ff9a9e);
            color: #4a2c52;
            box-shadow: 0 6px 20px rgba(255, 154, 158, 0.6);
        }

        /* Banner Hero */
        .hero-banner {
            background: rgba(255, 255, 255, 0.9);
            border: 3px solid #f8e1ee;
            border-radius: 30px;
            box-shadow: var(--card-shadow);
            position: relative;
            overflow: hidden;
        }

        /* Card Books Cute */
        .book-card {
            background: #ffffff;
            border: 2px solid #f3e8ff;
            border-radius: 24px;
            transition: all 0.3s ease;
            position: relative;
            box-shadow: 0 8px 20px rgba(0,0,0,0.03);
        }

        .book-card:hover {
            transform: translateY(-8px) rotate(1deg);
            border-color: #d8b4fe;
            box-shadow: var(--card-shadow);
        }

        .badge-kategori {
            background-color: #f3e8ff;
            color: var(--primary-cute);
            font-family: 'Fredoka', cursive;
            padding: 6px 14px;
            border-radius: 15px;
            font-size: 0.85rem;
        }

        .price-badge {
            background: #e6fffa;
            color: #234e52;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 12px;
            border: 1px dashed #38b2ac;
        }

        .badge-kritis {
            background: #fff5f5;
            color: #e53e3e;
            border: 1px solid #feb2b2;
            border-radius: 12px;
            padding: 4px 10px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        /* Modal Custom Cute */
        .modal-content {
            border-radius: 30px;
            border: 3px solid #f3e8ff;
            background: #ffffff;
        }

        .form-control {
            border-radius: 15px;
            padding: 10px 18px;
            border: 2px solid #eee5ff;
            background-color: #faf8ff;
        }

        .form-control:focus {
            border-color: var(--primary-cute);
            box-shadow: 0 0 0 4px rgba(138, 112, 214, 0.15);
            background-color: #fff;
        }

        /* Action Buttons Inside Cards */
        .btn-action-edit {
            background-color: #fffbea;
            color: #d69e2e;
            border-radius: 12px;
            border: 1px solid #fefcbf;
            font-weight: 600;
        }

        .btn-action-edit:hover {
            background-color: #fefcbf;
            color: #b7791f;
        }

        .btn-action-delete {
            background-color: #fff5f5;
            color: #e53e3e;
            border-radius: 12px;
            border: 1px solid #fed7d7;
            font-weight: 600;
        }

        .btn-action-delete:hover {
            background-color: #fed7d7;
            color: #c53030;
        }
    </style>
</head>
<body>

<!-- Background Glowing Blobs -->
<div class="cute-blob blob-1"></div>
<div class="cute-blob blob-2"></div>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-custom sticky-top py-3">
    <div class="container">
        <a class="navbar-brand brand-title d-flex align-items-center gap-2" href="index.php">
            <span class="fs-3">🌸</span>
            <span>Aqyun Library System</span>
        </a>
        <button class="btn btn-cute-primary ms-auto" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="fa-solid fa-wand-magic-sparkles me-1"></i> Tambah Buku
        </button>
    </div>
</nav>

<div class="container my-4 my-md-5">
    
    <!-- Banner Header Estetik dengan Gambar Ilustrasi Cute -->
    <div class="hero-banner p-4 p-md-5 mb-4 text-center text-md-start">
        <div class="row align-items-center">
            <div class="col-md-8">
                <span class="badge bg-white text-dark shadow-sm px-3 py-2 rounded-pill mb-2 border">
                    ✨ Welcome to Aqyun's Book Nook ✨
                </span>
                <h1 class="display-6 fw-bold mb-2" style="color: var(--primary-cute);">
                    Kelola Koleksi Buku Favoritmu! 📚💖
                </h1>
                <p class="text-muted mb-0 fs-6">
                    Tempat rapi & imut untuk mencatat koleksi penyewaan buku digital. Seru, simpel, dan menyenangkan!
                </p>
            </div>
            <div class="col-md-4 text-center mt-3 mt-md-0">
                <!-- Stiker / Ilustrasi Lucu Kategori Buku -->
                <img src="https://illustrations.pouch.cool/pack/kawaii/preview.png" alt="Kawaii Books" class="img-fluid" style="max-height: 140px; filter: drop-shadow(0px 8px 15px rgba(0,0,0,0.1));">
            </div>
        </div>
    </div>

    <!-- Alert Notifikasi Selesai -->
    <?php if (isset($_GET['status'])): ?>
        <div class="alert alert-success border-0 rounded-4 shadow-sm fade show mb-4 d-flex justify-content-between align-items-center" style="background-color: #e6fffa; color: #234e52; border: 2px dashed #38b2ac !important;">
            <div>
                <i class="fa-solid fa-star text-warning me-2"></i>
                <b>Yayy!</b> <?= $_GET['status'] === 'success_add' ? 'Buku baru berhasil disimpan ke rak!' : 'Buku berhasil dihapus dari sistem!'; ?> 🎉
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Alert Validasi Error -->
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4" style="background-color: #fff5f5; color: #9b2c2c; border: 2px dashed #feb2b2 !important;">
            <div class="fw-bold mb-1"><i class="fa-solid fa-heart-crack me-1"></i> Ups, ada yang perlu diperbaiki:</div>
            <ul class="mb-0 ps-3">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Grid Kartu Koleksi Buku -->
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php if (empty($books)): ?>
            <div class="col-12 text-center py-5">
                <img src="https://cdn-icons-png.flaticon.com/512/7486/7486747.png" alt="Empty" style="width: 120px;" class="mb-3 opacity-75">
                <h5 class="fw-bold text-muted">Rak bukunya masih kosong nih... ☁️</h5>
                <p class="text-muted small">Klik tombol <b>'Tambah Buku'</b> di atas buat isi buku pertamamu ya!</p>
            </div>
        <?php else: ?>
            <?php foreach ($books as $book): ?>
                <div class="col">
                    <div class="card book-card h-100 p-3">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="badge-kategori">
                                    🏷️ <?= htmlspecialchars($book['kategori'], ENT_QUOTES, 'UTF-8'); ?>
                                </span>
                                <?php if ($book['stok'] < 3): ?>
                                    <span class="badge-kritis">
                                        ⚠️ Stok Tinggal <?= $book['stok']; ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <h5 class="fw-bold mb-3 text-dark">
                                📖 <?= htmlspecialchars($book['judul'], ENT_QUOTES, 'UTF-8'); ?>
                            </h5>

                            <div class="mt-auto pt-3 border-top border-light d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted d-block" style="font-size: 0.75rem;">Sewa / Minggu</small>
                                    <span class="price-badge">
                                        Rp <?= number_format($book['harga_sewa'], 0, ',', '.'); ?>
                                    </span>
                                </div>
                                <div class="text-end">
                                    <small class="text-muted d-block" style="font-size: 0.75rem;">Stok Rak</small>
                                    <span class="fw-bold fs-6 text-purple">
                                        <?= htmlspecialchars($book['stok'], ENT_QUOTES, 'UTF-8'); ?> Pcs 📦
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Aksi Imut -->
                        <div class="card-footer bg-transparent border-0 pt-0 d-flex justify-content-end gap-2">
                            <a href="edit.php?id=<?= $book['id']; ?>" class="btn btn-sm btn-action-edit px-3">
                                <i class="fa-solid fa-pen-nib me-1"></i> Edit
                            </a>
                            
                            <form action="index.php" method="POST" onsubmit="return confirm('Apakah kamu yakin mau menghapus buku ini dari rak? 🥺');">
                                <input type="hidden" name="csrf_token" value="<?= $csrf_token; ?>">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $book['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-action-delete px-3">
                                    <i class="fa-solid fa-trash me-1"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Form Tambah Buku Cutie -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content p-3" action="index.php" method="POST">
            <div class="modal-header border-0 pb-0">
                <h4 class="modal-title fw-bold" style="color: var(--primary-cute);">✨ Tambah Koleksi Baru</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token; ?>">
                <input type="hidden" name="action" value="create">
                
                <div class="mb-3">
                    <label class="form-label font-weight-bold">Judul Buku 📚</label>
                    <input type="text" name="judul" class="form-control" placeholder="Misal: Belajar PHP Santai..." required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Kategori 🏷️️</label>
                    <input type="text" name="kategori" class="form-control" placeholder="Misal: Pemrograman, Novel, Komik..." required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Harga Sewa (Rp) 💰</label>
                        <input type="number" name="harga_sewa" class="form-control" placeholder="15000" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Stok Awal 📦</label>
                        <input type="number" name="stok" class="form-control" placeholder="5" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-cute-primary">Simpan Buku 💖</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>