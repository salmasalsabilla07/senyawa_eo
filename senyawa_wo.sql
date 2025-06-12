-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 12, 2025 at 03:10 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `senyawa_wo`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `order_id` varchar(100) NOT NULL,
  `package_id` int(11) NOT NULL,
  `tanggal_event` date NOT NULL,
  `nama_lengkap` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `nomor_telepon` varchar(50) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `jumlah` int(11) NOT NULL DEFAULT 0,
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `bank` varchar(50) DEFAULT NULL,
  `uploaded_at` datetime DEFAULT NULL,
  `status_pembayaran` enum('pending','terima','tolak','berhasil','gagal') NOT NULL DEFAULT 'pending',
  `payment_type` varchar(100) DEFAULT NULL,
  `progress_status` enum('on progress','done') NOT NULL DEFAULT 'on progress'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `brides`
--

CREATE TABLE `brides` (
  `id` int(11) NOT NULL,
  `order_id` varchar(50) NOT NULL,
  `nama_pria` varchar(100) NOT NULL,
  `nama_wanita` varchar(100) NOT NULL,
  `alamat_pria` text DEFAULT NULL,
  `alamat_wanita` text DEFAULT NULL,
  `panggilan_pria` varchar(100) DEFAULT NULL,
  `ayah_pria` varchar(255) DEFAULT NULL,
  `ibu_pria` varchar(255) DEFAULT NULL,
  `panggilan_wanita` varchar(100) DEFAULT NULL,
  `ayah_wanita` varchar(255) DEFAULT NULL,
  `ibu_wanita` varchar(255) DEFAULT NULL,
  `tanggal_event` date DEFAULT NULL,
  `jam_event` varchar(50) DEFAULT NULL,
  `lokasi_event` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `packages`
--

CREATE TABLE `packages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(15,2) NOT NULL,
  `category` varchar(100) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `packages`
--

INSERT INTO `packages` (`id`, `name`, `description`, `price`, `category`) VALUES
(1, 'Engagement Regular', 'Crew 2 orang (PIC Event, Show Director)\r\nKonsultasi Konsep\r\nRundown Acara\r\nGames\r\nFree Bouquet bunga\r\nFree MC\r\n', 1250000.00, ''),
(2, 'Engagement VIP', 'Crew 4 orang (PIC Event, Show Director, Cheeker, PIC Media)\r\nKonsultasi Konsep\r\nRundown Acara\r\nGames\r\nFree Bouqet bunga\r\nFree Undangan Digital\r\nFree Video Moment\r\nFree MC', 1850000.00, ''),
(3, 'Akad Nikah Reguler', 'Crew 4 orang (PIC Event, Show Director, Cheeker, PIC Tamu)\r\nKonsultasi Konsep\r\nRundown Acara\r\nTeks Sungkeman\r\nTeks Mohon Restu\r\nTen Card\r\nShort Video Moment\r\nFree MC', 2250000.00, ''),
(4, 'Akad Nikah VIP', 'Crew 5 orang (PIC Event, Show Director, Cheeker, PIC Tamu, Asisten Pengantin)\r\nKonsultasi Konsep\r\nRundown Acara\r\nTeks Sungkeman\r\nTeks Mohon Restu\r\nTen Card Akrilik\r\nFree Fotografer\r\nShort Video Moment\r\nFree MC', 2750000.00, ''),
(5, 'Resepsi Reguler', 'Crew 7 orang\r\nKonsultasi Konsep\r\nMeeting 1 Kali Pertemuan\r\nGladi Resik\r\nRundown Acara\r\nGames\r\nFree Short Video Moment\r\nFree Undangan Digital\r\nFree Popper Party\r\nFree Beras Kunyil', 5450000.00, ''),
(6, 'Resepsi VIP', 'Crew 9 orang\r\nUnlimited Konsultasi\r\nRundown Acara\r\nMeeting 1 Kali Pertemuan\r\nMeeting Reservasi Vendor\r\nGladi Resik\r\nAkrilik VIP\r\nGames\r\nFree Short Video Moment\r\nFree Undangan Digital\r\nFree Popper Party\r\nFree Beras Kunyil\r\nFree Properti Wedding Cake', 6300000.00, 'reception'),
(7, 'Resepsi VIP Spektakuler', 'Crew 12 orang\r\nUnlimited Konsultasi\r\nRundown Acara\r\nMeeting 2 Kali Pertemuan\r\nMeeting Reservasi Vendor\r\nGladi Resik\r\nAkrilik VIP\r\nGames\r\nFree Short Video Moment\r\nFree Undangan Digital\r\nFree Popper Party\r\nFree Beras Kunyil\r\nFree Properti Wedding Cake', 7750000.00, ''),
(8, 'Intimate Wedding', 'Crew 6 orang\r\nUnlimited Konsultasi\r\nRundown Acara\r\nMeeting 1 Kali Pertemuan\r\nMeeting Reservasi Vendor\r\nGladi Resik\r\nGames\r\nFree Short Video Moment\r\nFree Undangan Digital\r\nFree Popper Party\r\nFree Properti Wedding Cake', 3700000.00, 'intimate'),
(9, 'Akad Nikah+Resepsi VIP', 'Crew 9 orang\r\nUnlimited Konsultasi\r\nRundown Acara\r\nMeeting 1 Kali Pertemuan\r\nMeeting Reservasi Vendor\r\nGladi Resik\r\nAkrilik VIP\r\nGames\r\nFree Short Video Moment\r\nFree Undangan Digital\r\nFree Popper Party\r\nFree Beras Kunyil\r\nFree Properti Wedding Cake', 6750000.00, ''),
(10, 'Akad Nikah+Resepsi Spektakuler', 'Crew 12 orang\r\nUnlimited Konsultasi\r\nRundown Acara\r\nMeeting 2 Kali Pertemuan\r\nMeeting Reservasi Vendor\r\nGladi Resik\r\nAkrilik VIP\r\nGames\r\nFree Tari Persembahan\r\nFree Short Video Moment\r\nFree Undangan Digital\r\nFree Popper Party\r\nFree Beras Kunyil\r\nFree Properti Wedding Cake', 8200000.00, 'akad'),
(11, 'Akad & Nikah  Beda Hari', '+ Rp. 500.000 (Jika Akad dan Nikah Beda Hari) ', 500000.00, 'akad'),
(12, 'Event Reguler (100-300 Peserta)', 'Crew 5 orang\r\nKonsultasi Konsep\r\nRundown Acara\r\nGames\r\nFree Design Pamflet\r\nFree Digital Invitation\r\nFree Short Video', 3250000.00, ''),
(13, 'Event VIP (300-500 Peserta)', 'Crew 7 orang\r\nKonsultasi Konsep\r\nRundown Acara\r\nGames\r\nFree Popper Party / Smoke Bomb\r\nFree Design Pamflet\r\nFree Digital Invitation\r\nFree Short Video', 3250000.00, ''),
(14, 'Event VIP Spektakuler (500-1000 Peserta)', 'Crew 10 orang\r\n100 Kursi + Sarung Kursi\r\nKonsultasi Konsep\r\nRundown Acara\r\nGames\r\nFree Popper Party / Smoke Bomb\r\nFree Design Pamflet\r\nFree Digital Invitation\r\nFree Short Video', 5350000.00, '');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(9, 'admin1', 'admin123', 'admin'),
(11, 'salma', '$2y$10$0Hai/bLe9jkmq4syw8JT5u.8eiV6pm1HkgTxZA5NCLmDuaX4DLnuC', 'user');

-- --------------------------------------------------------

--
-- Table structure for table `user_profile`
--

CREATE TABLE `user_profile` (
  `username` varchar(50) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_profile`
--

INSERT INTO `user_profile` (`username`, `nama`, `email`, `phone`, `alamat`, `foto`) VALUES
('salma', 'salma salsabilla', 'salmasalsabilla0706@gmail.com', '083838784668', 'rt 15', 'salma_1747704153.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `order_id` (`order_id`) USING BTREE,
  ADD KEY `package_id` (`package_id`);

--
-- Indexes for table `brides`
--
ALTER TABLE `brides`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_order_id` (`order_id`),
  ADD KEY `order_id` (`order_id`) USING BTREE;

--
-- Indexes for table `packages`
--
ALTER TABLE `packages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `user_profile`
--
ALTER TABLE `user_profile`
  ADD PRIMARY KEY (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT for table `brides`
--
ALTER TABLE `brides`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `packages`
--
ALTER TABLE `packages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`);

--
-- Constraints for table `brides`
--
ALTER TABLE `brides`
  ADD CONSTRAINT `fk_order` FOREIGN KEY (`order_id`) REFERENCES `bookings` (`order_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_profile`
--
ALTER TABLE `user_profile`
  ADD CONSTRAINT `user_profile_ibfk_1` FOREIGN KEY (`username`) REFERENCES `users` (`username`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
