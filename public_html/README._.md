
# 📝 Blog Website – Sistem Pengunjung dan Admin (CMS)

Website ini merupakan sistem blog sederhana yang memungkinkan pengunjung membaca artikel berdasarkan kategori, serta memberikan sistem backend (admin) untuk mengelola konten melalui fitur CRUD dan login.

---

## 🌐 Link Akses

- **Live Website (Frontend - Pengunjung):** [https://namablogmu.com](https://namablogmu.com)
- **Admin Panel (Backend - CMS):** [https://namablogmu.com/admin](https://namablogmu.com/admin)
- **Repository GitHub:** [https://github.com/username/blog-website](https://github.com/username/blog-website)

---

## 🔐 Informasi Login

| Role   | Username     | Password     |
|--------|--------------|--------------|
| Admin  | `adminuser`  | `admin1234`  |

---

## 🏠 Struktur Halaman

### 1. Halaman Pengunjung

#### a. Homepage
- **Header:** Judul/Tema Blog
- **Navigasi:** Home, Kategori, Tentang
- **Bagian Kiri:** Menampilkan 7 artikel terbaru
- **Bagian Kanan (Sidebar):** 
  - Form pencarian
  - Menu kategori
  - Informasi tentang blog
- **Footer:** Hak cipta © Tahun

#### b. Halaman Detail Artikel
- **Navigasi:** Sama seperti homepage
- **Bagian Kiri:** Judul artikel, tanggal, isi konten lengkap
- **Bagian Kanan (Sidebar):**
  - Form pencarian
  - Daftar artikel terkait
- **Footer:** Hak cipta © Tahun

#### c. Halaman Artikel per Kategori
- **Header:** Judul Blog
- **Navigasi:** Sama
- **Bagian Kiri:** Menampilkan artikel berdasarkan kategori tertentu
- **Bagian Kanan (Sidebar):** Sama seperti homepage
- **Footer:** Sama

---

### 2. Sistem Pengelolaan Konten (CMS)

#### 🔐 Autentikasi
- Login menggunakan kombinasi username dan password
- Proteksi halaman admin agar tidak bisa diakses tanpa login

#### ⚙️ CRUD Fitur
- **Penulis:** Tambah, edit, hapus data penulis
- **Kategori:** Kelola daftar kategori artikel
- **Artikel:** Buat artikel baru, edit artikel, hapus artikel

#### 🔓 Logout
- Tersedia tombol logout untuk mengakhiri sesi

---

## 💾 Teknologi yang Digunakan

- **Frontend:** HTML, CSS, JavaScript
- **Backend:** PHP (Native atau Framework)
- **Database:** MySQL
- **Web Server:** Apache (via XAMPP/Laragon)
- **Hosting:** (Contoh: 000webhost, InfinityFree, dsb)

---

## 🗃️ Struktur Database (MySQL)

### Tabel `users`
| Field        | Tipe          |
|--------------|---------------|
| id           | INT (PK)      |
| username     | VARCHAR(50)   |
| password     | VARCHAR(255)  |

### Tabel `categories`
| Field        | Tipe          |
|--------------|---------------|
| id           | INT (PK)      |
| name         | VARCHAR(100)  |

### Tabel `authors`
| Field        | Tipe          |
|--------------|---------------|
| id           | INT (PK)      |
| name         | VARCHAR(100)  |

### Tabel `articles`
| Field        | Tipe          |
|--------------|---------------|
| id           | INT (PK)      |
| title        | VARCHAR(200)  |
| content      | TEXT          |
| category_id  | INT (FK)      |
| author_id    | INT (FK)      |
| created_at   | DATETIME      |

---

## ⚙️ Cara Menjalankan Proyek

### 🔧 Lokal (via Laragon/XAMPP)
1. Clone repo:
   ```
   git clone https://github.com/username/blog-website.git
   ```
2. Pindahkan ke folder `htdocs` (jika pakai XAMPP)
3. Buat database baru, import file `database.sql`
4. Sesuaikan konfigurasi koneksi database pada file `config.php`
5. Jalankan server Apache + MySQL
6. Akses via `http://localhost/blog-website/`

### ☁️ Hosting Online
1. Upload semua file via FTP/File Manager
2. Buat database & import file SQL
3. Edit `config.php` agar sesuai dengan data hosting (host, user, pass, db)
4. Akses domain secara online

---

## 📌 Catatan Tambahan

- Gunakan password hashing (`password_hash()` di PHP) untuk keamanan
- Tambahkan validasi input dan proteksi SQL injection
- Template frontend dapat diganti sesuai preferensi estetika

---

## 👨‍💻 Kontributor

- Nama Anda – Developer
- Email – [email@domain.com](mailto:email@domain.com)
