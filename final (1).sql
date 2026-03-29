-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 24, 2026 at 06:38 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `final`
--

-- --------------------------------------------------------

--
-- Table structure for table `about_cards`
--

CREATE TABLE `about_cards` (
  `id` int NOT NULL,
  `card_type` enum('feature','info') NOT NULL,
  `icon_class` varchar(100) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `about_cards`
--

INSERT INTO `about_cards` (`id`, `card_type`, `icon_class`, `title`, `description`) VALUES
(1, 'feature', 'fas fa-bolt', 'Instant Booking', 'Skip the queues and book your favorite seats in less than 30 seconds.'),
(2, 'feature', 'fas fa-couch', 'Premium Comfort', 'Partnership with top-tier cinemas for best reclining experience.'),
(3, 'feature', 'fas fa-wallet', 'Best Prices', 'No hidden charges. Get the best deals and exclusive combos.'),
(4, 'info', '🎬', 'Latest Releases', 'Stay updated with newly released movies and trending shows worldwide.'),
(5, 'info', '📺', 'HD Streaming', 'Enjoy high-definition streaming with smooth playback experience.'),
(6, 'info', '🔒', 'Secure Platform', 'Your data and privacy are always protected with advanced security.');

-- --------------------------------------------------------

--
-- Table structure for table `about_content`
--

CREATE TABLE `about_content` (
  `id` int NOT NULL,
  `hero_subtitle` text,
  `main_image` varchar(255) DEFAULT NULL,
  `about_welcome_title` varchar(255) DEFAULT NULL,
  `about_main_title` varchar(255) DEFAULT NULL,
  `about_full_desc` text,
  `user_exp_val` int DEFAULT NULL,
  `secure_pay_val` int DEFAULT NULL,
  `stats_movies` varchar(50) DEFAULT NULL,
  `stats_users` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `about_content`
--

INSERT INTO `about_content` (`id`, `hero_subtitle`, `main_image`, `about_welcome_title`, `about_main_title`, `about_full_desc`, `user_exp_val`, `secure_pay_val`, `stats_movies`, `stats_users`) VALUES
(1, 'Experience Entertainment Anytime.', 'Action Movie.jpg', 'WELCOME TO FILMFLIX', 'The Best Movie Experience', 'We provide the best seat booking experience for movie lovers.', 95, 100, '500+', '10k+');

-- --------------------------------------------------------

--
-- Table structure for table `banner`
--

CREATE TABLE `banner` (
  `id` int NOT NULL,
  `title` varchar(100) NOT NULL,
  `subtitle` varchar(100) NOT NULL,
  `imagurl` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `banner`
--

INSERT INTO `banner` (`id`, `title`, `subtitle`, `imagurl`) VALUES
(1, 'Welcome to FILMFLIX', 'Your ultimate destination for cinematic excellence.', 'Laalo-wallpaper.jpg'),
(2, 'Silent Spectacle', 'Experience the thriller of the year.', 'uri.webp'),
(3, 'Silent Spectacle', 'Experience the thriller of the year.', 'satyamevjayte.webp'),
(4, 'Silent Spectacle', 'Experience the thriller of the year.', 'war 11.jpg'),
(5, 'Silent Spectacle', 'Experience the thriller of the year.', 'Saiyaara.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `contact`
--

CREATE TABLE `contact` (
  `id` int NOT NULL,
  `fullname` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `message` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `contact`
--

INSERT INTO `contact` (`id`, `fullname`, `email`, `message`, `created_at`) VALUES
(1, 'fdggfh', 'shriya@example.com', 'fnd fjrjg rfj jfjfhjh fhjhjkdhdkjkg', '2026-03-09 12:55:57'),
(2, 'vekariya shriya', 's@gmail.com', 'hyy shriya how are you?', '2026-03-16 08:27:04'),
(3, 'abc', 'abc@gmail.com', 'hfhfgjgjugfgsdfsgfhghfff', '2026-03-23 14:47:33'),
(5, 'jadav priyanshi manubhai', 'abc@gmail.com', 'how are you ? are you fine ?', '2026-03-23 17:55:18');

-- --------------------------------------------------------

--
-- Table structure for table `gallery`
--

CREATE TABLE `gallery` (
  `id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `category_tag` varchar(255) NOT NULL,
  `description` text,
  `image_url` varchar(255) DEFAULT NULL,
  `link_url` varchar(255) DEFAULT 'discover.php',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `gallery`
--

INSERT INTO `gallery` (`id`, `title`, `category_tag`, `description`, `image_url`, `link_url`, `created_at`) VALUES
(4, 'URI', 'action', 'indian army', 'uploads/1774288164_uri.webp', 'discover.php', '2026-03-23 17:49:24');

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

CREATE TABLE `login` (
  `login_id` int NOT NULL,
  `fullname` varchar(100) DEFAULT NULL,
  `email` varchar(50) NOT NULL,
  `number` varchar(15) DEFAULT NULL,
  `password` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `movies`
--

CREATE TABLE `movies` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `poster img` varchar(100) NOT NULL,
  `is_new` tinyint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `movies`
--

INSERT INTO `movies` (`id`, `name`, `poster img`, `is_new`) VALUES
(1, 'The Great Escape', 'AumErL9.jpg', 0),
(2, 'Night Forest', 'Humko_Tumse_Pyaar_Hai.jpg', 0),
(3, 'Hero Rising', 'taj story.jpg', 1),
(4, 'Future World', 'jay shree krish.PNG', 0),
(5, 'The Last Stand', 'chhava.jpg', 0);

-- --------------------------------------------------------

--
-- Table structure for table `password_token`
--

CREATE TABLE `password_token` (
  `email` varchar(100) NOT NULL,
  `otp` int DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `otp_attempts` int DEFAULT '0',
  `last_resend` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `resend_count` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `password_token`
--

INSERT INTO `password_token` (`email`, `otp`, `created_at`, `expires_at`, `otp_attempts`, `last_resend`, `resend_count`) VALUES
('nmrajput11@gmail.com', 758541, NULL, '2026-03-24 11:09:56', 0, '2026-03-24 05:37:56', 3);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `category_id` int DEFAULT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `discount` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `final_price` decimal(10,2) GENERATED ALWAYS AS (round((`price` - ((`price` * ifnull(`discount`,0)) / 100)),2)) STORED,
  `stock` int DEFAULT '0',
  `description` text,
  `long_description` longtext,
  `image` varchar(255) DEFAULT NULL,
  `gallery_images` json DEFAULT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `number` varchar(15) NOT NULL,
  `birthdate` date NOT NULL,
  `password` varchar(255) NOT NULL,
  `genres` varchar(255) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `role` varchar(20) DEFAULT 'user',
  `status` varchar(20) DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `image`, `fullname`, `email`, `number`, `birthdate`, `password`, `genres`, `city`, `role`, `status`, `created_at`) VALUES
(8, NULL, 'vekariya shriya', 'h@gmail.com', '4457658765', '2005-05-02', '78945612', 'horror', 'Rajkot', 'user', 'active', '2026-03-09 15:23:28'),
(17, NULL, 'vekariya arjun', 'arjun@gmail.com', '9979467953', '2013-04-12', '12345678', 'reading', 'Bhavnagar', 'user', 'active', '2026-03-20 08:32:24'),
(18, NULL, 'vekariya vishava', 'v@gmail.com', '9924912138', '2013-04-12', '12345678', 'reading', 'Bhavnagar', 'user', 'active', '2026-03-20 08:48:15'),
(27, NULL, 'vekariya mahi', 'm@gmail.com', '7862996308', '2001-05-11', '98765432', 'dancing', 'Bhavnagar', 'user', 'active', '2026-03-21 09:06:28'),
(28, NULL, 'vekariya mahi', 'a@gmail.com', '7862996308', '2013-04-12', '12345678', 'reading', 'Bhavnagar', 'user', 'active', '2026-03-21 09:28:57'),
(29, '1774251254_h2.jpg', 'jadav jaldhara', 's@gmail.com', '9904242493', '2006-11-29', '12345678', 'reading', 'Rajkot', 'user', 'active', '2026-03-23 07:34:14'),
(35, 'default.png', 'priyanshi jadav', 'nmrajput11@gmail.com', '1234567890', '2026-03-05', '12345678', 'dancing', 'Rajkot', 'user', 'active', '2026-03-24 05:24:49');

-- --------------------------------------------------------

--
-- Table structure for table `watchlist`
--

CREATE TABLE `watchlist` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `movie_name` varchar(255) DEFAULT NULL,
  `added_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `about_cards`
--
ALTER TABLE `about_cards`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `about_content`
--
ALTER TABLE `about_content`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `banner`
--
ALTER TABLE `banner`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact`
--
ALTER TABLE `contact`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`login_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `movies`
--
ALTER TABLE `movies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_token`
--
ALTER TABLE `password_token`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `watchlist`
--
ALTER TABLE `watchlist`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `about_cards`
--
ALTER TABLE `about_cards`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `about_content`
--
ALTER TABLE `about_content`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `banner`
--
ALTER TABLE `banner`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `contact`
--
ALTER TABLE `contact`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `login`
--
ALTER TABLE `login`
  MODIFY `login_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `watchlist`
--
ALTER TABLE `watchlist`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
