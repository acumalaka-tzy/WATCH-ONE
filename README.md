WATCHONE — WEB APLIKASI STREAMING FILM SEDERHANA
=================================================

WatchOne adalah web aplikasi katalog dan daftar film sederhana yang dibangun menggunakan PHP + MySQL, 
dilengkapi dengan fitur login, registrasi, pencarian film, favorit, riwayat tontonan, serta fitur 
manajemen film untuk admin.


=================================================
KONTRIBUTOR PROYEK
=================================================

Frontend (User Page)
- Ainuha Aliyah
  * FE User
  * Index
  * Category
  * Header
  * Favorite

Frontend (Admin Page)
- Izyan
  * FE Home Admin
  * FE Pengelolaan Film

CSS (Semua Bagian)
- William
- Naufal

Backend (PHP & Database)
- Gabriel
  * Backend PHP + koneksi database
  * Backend + Frontend History
  * Backend + Frontend Admin Kelola User
  * Backend + Frontend Admin Kelola Kategori


=================================================
FITUR UTAMA
=================================================

1. Autentikasi
   - Login
   - Registrasi
   - Logout
   - Session protection

2. Manajemen Film
   - Daftar film
   - Detail film
   - Pencarian
   - CRUD film (admin)

3. Fitur User
   - Favorit (tambah/hapus)
   - Daftar favorit
   - Riwayat tontonan

4. Database MySQL
   - User
   - Film
   - Kategori
   - Favorit
   - Riwayat


=================================================
STRUKTUR FOLDER (UMUM)
=================================================

/assets
   /css
   /img
   /js

/config
   database.php

/pages
   home.php
   login.php
   register.php
   detail.php
   favorite.php
   history.php

/admin
   home.php
   film_add.php
   film_edit.php
   film_delete.php
   user_manage.php
   category_manage.php

index.php
README.txt


=================================================
TEKNOLOGI YANG DIGUNAKAN
=================================================

- PHP 7/8
- MySQL
- HTML, CSS, JavaScript
- XAMPP / LAMPP / WAMP


=================================================
CARA INSTALASI
=================================================

1. Clone / download proyek:
   git clone https://github.com/username/WatchOne.git

2. Pindahkan ke folder htdocs:
   C:/xampp/htdocs/WatchOne/

3. Import database:
   - Buka phpMyAdmin
   - Buat database: watchone
   - Import file watchone.sql

4. Konfigurasi database:
   Buka: /config/database.php
   Edit:
     $host = "localhost";
     $user = "root";
     $pass = "";
     $db   = "watchone";

5. Jalankan aplikasi:
   http://localhost/WatchOne/


=================================================
AKUN DEMO (OPSIONAL)
=================================================

Admin:
Email: admin@watchone.com
Password: admin123

User:
Email: user@example.com
Password: user123


=================================================
STRUKTUR DATABASE (SINGKAT)
=================================================

Tabel user:
id, username, email, password, role

Tabel film:
id, title, genre, year, thumbnail, description, category_id

Tabel favorit:
id, user_id, film_id

Tabel riwayat:
id, user_id, film_id, tanggal


=================================================
KONTRIBUSI
=================================================

1. Fork repository
2. Buat branch baru
3. Commit perubahan
4. Ajukan pull request



