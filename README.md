# 📚 Aqyun Library System (Product Manager)

Aplikasi Web Manajemen Buku berbasis PHP-MySQL PDO yang aman, responsif, dan estetik.

## 🌟 Fitur Utama & Keamanan
- **CRUD Lengkap:** Create, Read (Card Responsif), Update by ID, Delete (POST + CSRF).
- **Keamanan Query:** Menggunakan PDO Prepared Statements (`$pdo->prepare`).
- **Sanitasi Output:** Menggunakan `htmlspecialchars()` dengan flag `ENT_QUOTES` untuk mencegah XSS.
- **Validasi Data:** Judul minimal 3 karakter & unik, Harga > 0, Stok >= 0.
- **Post-Redirect-Get:** Menghindari input ganda saat halaman di-refresh.
- **UI Estetik:** Menggunakan konsep warna pastel modern dan Google Font Plus Jakarta Sans.