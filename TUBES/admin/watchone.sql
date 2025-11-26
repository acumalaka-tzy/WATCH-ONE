-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 26 Nov 2025 pada 08.19
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `watchone`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Action'),
(2, 'Drama'),
(3, 'Comedy'),
(4, 'Horror'),
(5, 'Adventure'),
(6, 'Romance');

-- --------------------------------------------------------

--
-- Struktur dari tabel `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `film_id` int(11) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `rating` tinyint(4) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `favorites`
--

CREATE TABLE `favorites` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `film_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `films`
--

CREATE TABLE `films` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `poster_path` varchar(255) DEFAULT NULL,
  `video_url` varchar(255) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_featured` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `films`
--

INSERT INTO `films` (`id`, `title`, `slug`, `description`, `duration`, `poster_path`, `video_url`, `category_id`, `created_at`, `is_featured`) VALUES
(4, 'kembalinya raja iblis', 'kembalinya-raja-iblis', 'ganteng sekali', NULL, 'https://i.ytimg.com/vi/lAWTPuTqbTA/hqdefault.jpg', 'https://youtu.be/lAWTPuTqbTA?si=TJiXMvM_ya99_nRs', 4, '2025-11-26 06:15:19', 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `role`, `created_at`) VALUES
(8, 'gabriel', 'yayayaya@gmail.com', '$2y$10$435aidWonqpEwTyEBoQ.d.Zhiuv8182U36uqu4raT.Cmmr.TEmaWu', 'admin', '2025-11-19 15:37:02'),
(9, 'gabriel', 'awawaw@gmail.com', '$2y$10$UZUnECG3cuU5FdKDqVvFDeFtKSAYvSk21pQmvcBKsC02okeKhgtLG', 'user', '2025-11-21 01:28:40'),
(10, 'gabriel', 'cfgjn@gmail.com', '$2y$10$Owq6iw3LUzlLOiA4EQ7ileX016yT20EsUrJXUZb0s7PPha1ZA7Ox2', 'admin', '2025-11-21 01:53:52'),
(11, 'william', 'gyaigiuia@gmail.com', '$2y$10$k53KI55nYYAVetIEEOfDReO1uNVMgY2EJHZL.ahv5aGcQixLo1Cwy', 'user', '2025-11-21 03:05:29'),
(12, 'gabriel', 'awawadw@gmail.com', '$2y$10$Dmutc8bOolkV3MPDgN3tNekjYN8l5zPEfpvFSOzCaUk79FbHIq2.u', 'user', '2025-11-24 07:52:57'),
(13, 'xfg', 'sscfgjn@gmail.com', '$2y$10$IITL/dGGbtwphciB.qAqoOrgxv6eEcAmoRhP3eO3DawS9EMvbcaOS', 'user', '2025-11-24 09:02:16'),
(14, 'xfgcujfyfoi', 'yayay@gmail.com', '$2y$10$38n6TJIrWqVFvOg3MRXI0.IycqmV5rYwVfB0thg7d4jdBhYlRH1p2', 'admin', '2025-11-24 12:09:17'),
(15, 'gabriel', '234567@gmail.com', '$2y$10$ZoIKA7ez6iaqOddkjaSik.xjYppFVyQQls055EsFqz2z8uxK72P2y', 'admin', '2025-11-25 03:36:21');

-- --------------------------------------------------------

--
-- Struktur dari tabel `watch_history`
--

CREATE TABLE `watch_history` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `film_id` int(11) DEFAULT NULL,
  `position_seconds` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`film_id`);

--
-- Indeks untuk tabel `films`
--
ALTER TABLE `films`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `category_id` (`category_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeks untuk tabel `watch_history`
--
ALTER TABLE `watch_history`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `films`
--
ALTER TABLE `films`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT untuk tabel `watch_history`
--
ALTER TABLE `watch_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `films`
--
ALTER TABLE `films`
  ADD CONSTRAINT `films_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
