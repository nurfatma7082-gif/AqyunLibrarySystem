# 📚 Aqyun Library System

**Aqyun Library System** adalah aplikasi manajemen perpustakaan dan penyewaan buku digital berbasis web. Sistem ini dirancang untuk memudahkan pengelolaan katalog buku, pencatatan transaksi peminjaman/pengembalian, serta manajemen pengguna secara efisien dan terstruktur.

---

## 🚀 Fitur Utama

- 📖 **Manajemen Buku**: Tambah, edit, hapus, dan tampilkan katalog buku secara teratur.
- 🔄 **Sistem Peminjaman & Pengembalian**: Pencatatan riwayat transaksi buku secara realtime.
- 🛡️ **Keamanan CSRF Token**: Perlindungan pada form untuk mencegah serangan *Cross-Site Request Forgery*.
- 🗄️ **Database MySQL/PDO**: Menggunakan standar relasi data yang aman dan handal.

---

## 🛠️ Teknologi yang Digunakan

- **Backend**: PHP (PHP Data Objects / PDO)
- **Database**: MySQL / MariaDB
- **Frontend**: HTML5, CSS3, JavaScript
- **Server Local**: XAMPP (Apache)

---

## 📂 Struktur Folder Project

```text
AqyunLibrarySystem/
├── config.php      # Konfigurasi koneksi database
├── functions.php   # Fungsi logika backend & validasi CSRF
├── index.php       # Halaman utama / daftar buku
├── edit.php        # Form edit data buku
├── database.sql    # Skema & query pendukung database
└── README.md       # Dokumentasi project
