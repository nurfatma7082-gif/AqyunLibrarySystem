<?php
require_once 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Generate Token CSRF
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verifikasi Token CSRF
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Read All Books
function getAllBooks($pdo) {
    $stmt = $pdo->prepare("SELECT * FROM books ORDER BY id DESC");
    $stmt->execute();
    return $stmt->fetchAll();
}

// Read Book by ID
function getBookById($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM books WHERE id = :id");
    $stmt->execute(['id' => $id]);
    return $stmt->fetch();
}

// Validasi Input
function validateBookInput($pdo, $judul, $harga_sewa, $stok, $id = null) {
    $errors = [];

    if (strlen(trim($judul)) < 3) {
        $errors[] = "Judul buku minimal harus 3 karakter yaa!";
    }

    if ($harga_sewa <= 0) {
        $errors[] = "Harga sewa harus lebih dari Rp 0.";
    }

    if ($stok < 0) {
        $errors[] = "Stok tidak boleh bernilai negatif.";
    }

    // Cek Judul Unik
    if ($id) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM books WHERE judul = :judul AND id != :id");
        $stmt->execute(['judul' => $judul, 'id' => $id]);
    } else {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM books WHERE judul = :judul");
        $stmt->execute(['judul' => $judul]);
    }

    if ($stmt->fetchColumn() > 0) {
        $errors[] = "Judul buku ini sudah terdaftar sebelumnya (Judul harus unik).";
    }

    return $errors;
}

// Create Book
function createBook($pdo, $judul, $kategori, $harga_sewa, $stok) {
    $stmt = $pdo->prepare("INSERT INTO books (judul, kategori, harga_sewa, stok) VALUES (:judul, :kategori, :harga_sewa, :stok)");
    return $stmt->execute([
        'judul'      => $judul,
        'kategori'   => $kategori,
        'harga_sewa' => $harga_sewa,
        'stok'       => $stok
    ]);
}

// Update Book
function updateBook($pdo, $id, $judul, $kategori, $harga_sewa, $stok) {
    $stmt = $pdo->prepare("UPDATE books SET judul = :judul, kategori = :kategori, harga_sewa = :harga_sewa, stok = :stok WHERE id = :id");
    return $stmt->execute([
        'id'         => $id,
        'judul'      => $judul,
        'kategori'   => $kategori,
        'harga_sewa' => $harga_sewa,
        'stok'       => $stok
    ]);
}

// Delete Book
function deleteBook($pdo, $id) {
    $stmt = $pdo->prepare("DELETE FROM books WHERE id = :id");
    return $stmt->execute(['id' => $id]);
}
?>