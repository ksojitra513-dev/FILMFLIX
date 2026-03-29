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
-- Database: `filmflix`
--

-- --------------------------------------------------------

--
-- Table structure for table `about_cards`
--

DROP TABLE IF EXISTS `about_cards`;

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

DROP TABLE IF EXISTS `about_content`;

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

DROP TABLE IF EXISTS `banner`;

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
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;

CREATE TABLE `categories` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `count` int DEFAULT '0',
  `color` varchar(20) DEFAULT '#6366f1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `icon`, `count`, `color`) VALUES
(1, 'Action', 'fa-fist-raised', 142, '#ef4444'),
(2, 'Sci-Fi', 'fa-rocket', 98, '#6366f1'),
(3, 'Horror', 'fa-ghost', 74, '#8b5cf6'),
(4, 'Drama', 'fa-masks-theater', 210, '#f59e0b'),
(5, 'Comedy', 'fa-face-laugh', 167, '#10b981'),
(6, 'Thriller', 'fa-user-secret', 89, '#064e3b'),
(7, 'Animation', 'fa-wand-magic-sparkles', 55, '#ec4899'),
(8, 'Documentary', 'fa-camera', 43, '#0ea5e9');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

DROP TABLE IF EXISTS `feedback`;

CREATE TABLE `feedback` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `category` varchar(50) DEFAULT 'Other',
  `msg` text NOT NULL,
  `priority` enum('LOW','MED','HIGH') DEFAULT 'LOW',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `name`, `email`, `category`, `msg`, `priority`) VALUES
(1, 'Alex Morgan', 'alex@example.com', 'Bug Report', 'The video player crashed when I tried to enter full-screen mode on Safari.', 'HIGH'),
(2, 'Sarah Jenkins', 'sarah.j@outlook.com', 'Feature Request', 'It would be amazing to have a "Watch Party" feature to stream with friends!', 'MED'),
(3, 'Michael Chen', 'm.chen@gmail.com', 'Suggestion', 'Maybe add a dark mode toggle specifically for the subtitles?', 'LOW'),
(4, 'Emily Rodriguez', 'emily_r@gmail.com', 'Other', 'I love the new layout! Everything feels so much faster now. Great work!', 'LOW');

-- --------------------------------------------------------

--
-- Table structure for table `gallery`
--

DROP TABLE IF EXISTS `gallery`;

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
-- Table structure for table `actors`
--

DROP TABLE IF EXISTS `actors`;

CREATE TABLE `actors` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `knownFor` varchar(255) DEFAULT '',
  `role` varchar(50) NOT NULL,
  `status` enum('Active','Inactive','Retired') DEFAULT 'Active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `actors`
--

INSERT INTO `actors` (`id`, `name`, `knownFor`, `role`, `status`) VALUES
(1, 'Leonardo DiCaprio', 'Inception, Titanic, The Revenant', 'Actor', 'Active'),
(2, 'Christopher Nolan', 'Oppenheimer, Interstellar, Dark Knight', 'Director', 'Active'),
(3, 'Hans Zimmer', 'Inception, Dune, Interstellar', 'Composer', 'Active'),
(4, 'Quentin Tarantino', 'Pulp Fiction, Kill Bill, Django', 'Director', 'Active'),
(5, 'Meryl Streep', 'Devil Wears Prada, Sophie\'s Choice', 'Actor', 'Active'),
(6, 'Gene Hackman', 'The French Connection, Unforgiven', 'Actor', 'Retired');

-- --------------------------------------------------------


-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;

CREATE TABLE `comments` (
  `id` int NOT NULL,
  `user` varchar(100) NOT NULL,
  `movie` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `time` varchar(50) NOT NULL,
  `flagged` tinyint(1) DEFAULT '0',
  `status` enum('Pending','Approved') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `user`, `movie`, `text`, `time`, `flagged`, `status`) VALUES
(1, '@Hater123', 'Batman: Dark Knight', "This movie was terrible, don't watch it! Also the stream quality was quite poor for a VIP user.", '5 minutes ago', 1, 'Pending'),
(2, '@Cinephile99', 'Interstellar', "Incredible masterpiece! Christopher Nolan's vision is just unparalleled. Best Sci-Fi ever.", '2 hours ago', 0, 'Pending'),
(3, '@MovieBuff', 'Inception', "The ending still keeps me awake at night. Was it a dream or reality? 10/10.", '4 hours ago', 0, 'Pending'),
(4, '@StreamFan', 'Extraction 2', "Action sequences are insane! Tyler Rake is the new John Wick.", '1 day ago', 0, 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;

CREATE TABLE `payments` (
  `id` int NOT NULL,
  `trx_id` varchar(50) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `method` varchar(50) NOT NULL,
  `icon` varchar(50) NOT NULL,
  `status` enum('Completed','Pending','Failed','Refunded') DEFAULT 'Completed',
  `payment_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `trx_id`, `user_name`, `amount`, `method`, `icon`, `status`, `payment_date`) VALUES
(1, 'TRX-9482', 'Jordan Smith', 14.99, 'Card', 'fa-cc-visa', 'Completed', '2024-03-26'),
(2, 'TRX-9481', 'Sarah Connor', 14.99, 'PayPal', 'fa-paypal', 'Failed', '2024-03-25'),
(3, 'TRX-9480', 'Michael Bay', 120.00, 'Card', 'fa-cc-mastercard', 'Completed', '2024-03-25'),
(4, 'TRX-9479', 'Emily Blunt', 14.99, 'Crypto', 'fa-bitcoin', 'Pending', '2024-03-24'),
(5, 'TRX-9478', 'John Doe', 120.00, 'Card', 'fa-cc-amex', 'Completed', '2024-03-23'),
(6, 'TRX-9477', 'Alice Cooper', 14.99, 'Apple Pay', 'fa-apple-pay', 'Refunded', '2024-03-22');

-- --------------------------------------------------------


--
-- Table structure for table `login`
--

DROP TABLE IF EXISTS `login`;

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

DROP TABLE IF EXISTS `movies`;

CREATE TABLE `movies` (
  `id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `category` varchar(255) DEFAULT 'Action',
  `year` int DEFAULT '2026',
  `description` text,
  `rating` decimal(3,1) DEFAULT '0.0',
  `trailer_url` text,
  `status` enum('Published','Pending','Draft') DEFAULT 'Published',
  `type` enum('Movie','TV Show','Documentary') DEFAULT 'Movie',
  `poster_url` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `movies`
--

INSERT INTO `movies` (`id`, `title`, `category`, `year`, `status`, `type`, `poster_url`) VALUES
(1, 'The Great Escape', 'Action', 2008, 'Published', 'Movie', 'AumErL9.jpg'),
(2, 'Night Forest', 'Horror', 2014, 'Published', 'Movie', 'Humko_Tumse_Pyaar_Hai.jpg'),
(3, 'Hero Rising', 'Action', 2016, 'Published', 'Movie', 'taj story.jpg'),
(4, 'Future World', 'Sci-Fi', 2010, 'Published', 'Movie', 'jay shree krish.PNG'),
(5, 'The Last Stand', 'Action', 2016, 'Published', 'Movie', 'chhava.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `offers`
--

DROP TABLE IF EXISTS `offers`;

CREATE TABLE `offers` (
  `id` int NOT NULL,
  `code` varchar(50) NOT NULL,
  `desc` varchar(255) NOT NULL,
  `status` enum('Active','Expired') DEFAULT 'Active',
  `color` varchar(20) DEFAULT 'primary',
  `used` int DEFAULT '0',
  `exp` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `offers`
--

INSERT INTO `offers` (`id`, `code`, `desc`, `status`, `color`, `used`, `exp`) VALUES
(1, 'SUMMER50', '50% Off Monthly Subscription', 'Active', 'primary', 452, '2026-08-30'),
(2, 'NEWUSER30', '30% Off First 3 Months', 'Active', 'success', 1024, '2026-12-31'),
(3, 'WINTER20', '20% Off Yearly Pass', 'Expired', 'warning', 1240, '2026-01-30');

-- --------------------------------------------------------

--
-- Table structure for table `password_token`
--

DROP TABLE IF EXISTS `password_token`;

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

DROP TABLE IF EXISTS `products`;

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
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `img` varchar(255) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `number` varchar(15) NOT NULL,
  `birthdate` date NOT NULL,
  `password` varchar(255) NOT NULL,
  `genres` varchar(255) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `role` varchar(20) DEFAULT 'Standard User',
  `sub` varchar(50) DEFAULT 'Free Tier',
  `status` varchar(20) DEFAULT 'Active',
  `join_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `img`, `name`, `email`, `number`, `birthdate`, `password`, `genres`, `city`, `role`, `sub`, `status`, `join_date`) VALUES
(1, '1.jpg', 'Jordan Smith', 'jordan@example.com', '1234567890', '1995-05-15', 'pass123', 'Sci-Fi', 'NY', 'Admin', 'Annual ($120.00)', 'Active', CURRENT_TIMESTAMP),
(2, '2.jpg', 'Sarah Connor', 'sarah@skynet.com', '0987654321', '1984-11-20', 'sky123', 'Action', 'LA', 'VIP User', 'Monthly ($14.99)', 'Active', CURRENT_TIMESTAMP),
(3, '3.jpg', 'John Doe', 'john@doe.com', '1122334455', '1990-01-01', 'doe123', 'Drama', 'Chicago', 'Standard User', 'Free Tier', 'Active', CURRENT_TIMESTAMP),
(4, '4.jpg', 'Ellen Ripley', 'ripley@weyland.com', '5544332211', '1992-06-07', 'aliens', 'Horror', 'Space', 'VIP User', 'Monthly ($14.99)', 'Suspended', CURRENT_TIMESTAMP),
(36, '99.jpg', 'floder', 'floder@admin.com', '0000000000', '2000-01-01', 'adminpassword', 'All', 'Admin City', 'Admin', 'Annual ($120.00)', 'Active', CURRENT_TIMESTAMP);

-- --------------------------------------------------------

--
-- Table structure for table `watchlist`
--

DROP TABLE IF EXISTS `watchlist`;

CREATE TABLE `watchlist` (
  `id` int NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `avatar` text,
  `title` varchar(255) NOT NULL,
  `type` enum('Movie','TV Show') NOT NULL,
  `priority` enum('High','Medium','Low') DEFAULT 'Medium',
  `added_date` date NOT NULL,
  `timestamp` bigint NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `watchlist`
--

INSERT INTO `watchlist` (`id`, `user_name`, `avatar`, `title`, `type`, `priority`, `added_date`, `timestamp`) VALUES
(1, 'Emily Blunt', 'https://i.pravatar.cc/150?u=3', 'Inception', 'Movie', 'High', '2026-03-22', 1711065600),
(2, 'Michael Page', 'https://i.pravatar.cc/150?u=4', 'Breaking Bad', 'TV Show', 'Low', '2026-03-20', 1710892800),
(3, 'Sarah Miller', 'https://i.pravatar.cc/150?u=5', 'The Crown', 'TV Show', 'Medium', '2026-03-18', 1710720000),
(4, 'David Cooper', 'https://i.pravatar.cc/150?u=6', 'Spider-Man: No Way Home', 'Movie', 'Low', '2026-03-15', 1710460800);

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
-- Indexes for table `actors`
--
ALTER TABLE `actors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `banner`
--
ALTER TABLE `banner`
  ADD PRIMARY KEY (`id`);


--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact`
--
ALTER TABLE `contact`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
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
-- Indexes for table `offers`
--
ALTER TABLE `offers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
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
-- AUTO_INCREMENT for table `movies`
--
ALTER TABLE `movies`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `offers`
--
ALTER TABLE `offers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `actors`
--
ALTER TABLE `actors`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;


--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;


--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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

-- --------------------------------------------------------

--
-- Table structure for table `banners`
--

DROP TABLE IF EXISTS `banners`;

CREATE TABLE `banners` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `subtitle` varchar(255) DEFAULT '',
  `imagurl` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `banners`
--

INSERT INTO `banners` (`id`, `title`, `subtitle`, `imagurl`) VALUES
(1, 'Welcome to FILMFLIX', 'Your ultimate destination for cinematic excellence...', 'Laalo-wallpaper.jpg'),
(2, 'Silent Spectacle', 'Experience the thriller of the year.', 'uri.webp'),
(3, 'Silent Spectacle', 'Experience the thriller of the year.', 'satyamevjayte.webp'),
(4, 'Silent Spectacle', 'Experience the thriller of the year.', 'war 11.jpg'),
(5, 'Silent Spectacle', 'Experience the thriller of the year.', 'Saiyaara.jpg');

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
