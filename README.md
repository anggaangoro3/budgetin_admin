# 🛡️ Budgetin Admin Dashboard

> **Projek Akhir Mata Kuliah Pemrograman Web**
>
> Dashboard Super Admin berbasis web untuk mengelola data pengguna aplikasi *Budgetin*.

---

## 📖 Deskripsi

**Budgetin Admin** adalah panel administrasi sederhana yang dibangun menggunakan **PHP Native**. Web ini berfungsi sebagai pusat kontrol bagi Super Admin untuk melakukan manajemen data pengguna (*User Management*) pada ekosistem aplikasi Budgetin.

Proyek ini dikembangkan untuk memenuhi tugas akhir mata kuliah Pemrograman Web, dengan fokus pada implementasi logika **CRUD (Create, Read, Update, Delete)** dan koneksi database MySQL tanpa menggunakan framework (*Native*).

---

## ✨ Fitur Utama

Aplikasi ini memiliki fitur-fitur esensial untuk administrasi data:

* **📋 User Dashboard**: Menampilkan daftar seluruh pengguna yang terdaftar dalam sistem.
* **🔍 Search User**: Fitur pencarian *real-time* atau berbasis kueri untuk menemukan pengguna spesifik dengan cepat.
* **➕ Add User**: Formulir untuk mendaftarkan pengguna baru ke dalam database.
* **✏️ Edit User**: Fungsionalitas untuk memperbarui informasi profil pengguna.
* **🗑️ Delete User**: Menghapus data pengguna yang sudah tidak aktif atau tidak valid.
* **📱 Responsive Design**: Tampilan antarmuka yang rapi menggunakan CSS kustom (`styles.css`).

---

## 🛠️ Tech Stack

Teknologi yang digunakan dalam pengembangan proyek ini:

* **Backend**: PHP (Native)
* **Database**: MySQL
* **Frontend**: HTML5, CSS3
* **Web Server**: Apache (via XAMPP/Laragon/MAMP)
* **IDE/Tools**: Visual Studio Code / JetBrains PHPStorm

---

## 📂 Struktur File

Berikut adalah struktur direktori dari source code proyek:

```text
budgetin_admin/
│
├── 📂 .idea/             # Konfigurasi Project (jika menggunakan IntelliJ/PHPStorm)
├── 📂 img/               # Aset gambar (Logo, Favicon, Background)
│
├── 📄 db.php             # File konfigurasi koneksi ke Database MySQL
├── 📄 index.php          # Halaman Utama (Menampilkan list user)
├── 📄 add_user.php       # Halaman form tambah user baru
├── 📄 edit_user.php      # Halaman form edit data user
├── 📄 delete_user.php    # Script logika penghapusan user
├── 📄 search_user.php    # Script logika pencarian user
├── 📄 styles.css         # File CSS utama untuk styling halaman
├── 📄 phpinfo.php        # Utility untuk mengecek versi & konfigurasi PHP
└── 📄 qodana.yaml        # Konfigurasi analisis kode (Quality Gate)
