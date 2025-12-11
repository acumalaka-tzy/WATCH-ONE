WATCHONE — WEB APLIKASI STREAMING FILM SEDERHANA
=================================================

WatchOne adalah web aplikasi katalog dan daftar film sederhana yang dibangun menggunakan PHP + MySQL, 
dilengkapi dengan fitur login, registrasi, pencarian film, favorit, riwayat tontonan, serta fitur 
manajemen film untuk admin.


=================================================
KONTRIBUTOR PROYEK
=================================================

1. Ainuha
- front end index
- front end category film
- PPT
- Laporan

2. Aliyah
- front end header
- bagian search film
- front end favorite
- toggle favorit 
- PPT 
- Laporan 

3. Gabriel
- All back end
- buat database
- history
- Kelola user 
- Kelola Kategori 

4. Izyan
- front end index admin
- front end kelola film
- Drop down profil

5. Naufal
- membantu menyamakan semua Style css ke semua code
- Laporan

6. William
- bertanggung jawab membuat style css juga untk semua link dan webpage 
- Membuat PP di akun 
- membuat Readme

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
   git clone https://github.com/acumalaka-tzy/WATCH-ONE.git

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



