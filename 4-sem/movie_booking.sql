-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 30, 2026 at 10:17 AM
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
-- Database: `movie_booking`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_book_tickets` (IN `p_user_id` INT, IN `p_show_id` INT, IN `p_seats` TEXT, IN `p_payment_method` VARCHAR(20), IN `p_coupon_code` VARCHAR(50))   BEGIN
    DECLARE v_seat_count INT;
    DECLARE v_ticket_price DECIMAL(10,2);
    DECLARE v_convenience_fee DECIMAL(10,2);
    DECLARE v_total_amount DECIMAL(10,2);
    DECLARE v_discount_amount DECIMAL(10,2) DEFAULT 0;
    DECLARE v_booking_number VARCHAR(50);
    DECLARE v_movie_title VARCHAR(200);
    DECLARE v_movie_poster VARCHAR(500);
    DECLARE v_show_date DATE;
    DECLARE v_show_time TIME;
    DECLARE v_theater_name VARCHAR(150);
    DECLARE v_screen_name VARCHAR(50);
    
    -- Get show details
    SELECT st.ticket_price, st.convenience_fee, st.show_date, st.show_time,
           m.title, m.poster_url, t.name, sc.screen_name
    INTO v_ticket_price, v_convenience_fee, v_show_date, v_show_time,
         v_movie_title, v_movie_poster, v_theater_name, v_screen_name
    FROM show_times st
    JOIN movies m ON st.movie_id = m.id
    JOIN screens sc ON st.screen_id = sc.id
    JOIN theaters t ON sc.theater_id = t.id
    WHERE st.id = p_show_id;
    
    -- Count selected seats
    SET v_seat_count = LENGTH(p_seats) - LENGTH(REPLACE(p_seats, ',', '')) + 1;
    
    -- Calculate total
    SET v_total_amount = (v_ticket_price * v_seat_count) + v_convenience_fee;
    
    -- Apply coupon if provided
    IF p_coupon_code IS NOT NULL THEN
        SELECT discount_value, discount_type INTO v_discount_amount, @discount_type
        FROM coupons 
        WHERE coupon_code = p_coupon_code 
        AND is_active = 1 
        AND valid_until >= CURDATE()
        AND used_count < usage_limit;
        
        IF @discount_type = 'percentage' THEN
            SET v_discount_amount = (v_total_amount * v_discount_amount) / 100;
        END IF;
        
        SET v_total_amount = v_total_amount - v_discount_amount;
    END IF;
    
    -- Generate booking number
    SET v_booking_number = CONCAT('BK', DATE_FORMAT(NOW(), '%Y%m%d%H%i%s'), p_user_id);
    
    -- Start transaction
    START TRANSACTION;
    
    -- Update available seats
    UPDATE show_times 
    SET available_seats = available_seats - v_seat_count,
        booked_seats = booked_seats + v_seat_count
    WHERE id = p_show_id;
    
    -- Create booking
    INSERT INTO bookings (
        booking_number, user_id, show_id, movie_title, movie_poster,
        show_date, show_time, theater_name, screen_name,
        seats_booked, total_seats, ticket_price, convenience_fee,
        total_amount, coupon_code, discount_amount, payment_method, payment_status, booking_status
    ) VALUES (
        v_booking_number, p_user_id, p_show_id, v_movie_title, v_movie_poster,
        v_show_date, v_show_time, v_theater_name, v_screen_name,
        p_seats, v_seat_count, v_ticket_price, v_convenience_fee,
        v_total_amount, p_coupon_code, v_discount_amount, p_payment_method, 'pending', 'confirmed'
    );
    
    -- Update coupon usage if applied
    IF p_coupon_code IS NOT NULL AND v_discount_amount > 0 THEN
        UPDATE coupons SET used_count = used_count + 1 
        WHERE coupon_code = p_coupon_code;
    END IF;
    
    COMMIT;
    
    SELECT v_booking_number as booking_number, v_total_amount as total_amount;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_update_payment` (IN `p_booking_number` VARCHAR(50), IN `p_payment_status` VARCHAR(20), IN `p_transaction_id` VARCHAR(100), IN `p_payment_method` VARCHAR(20), IN `p_gateway_response` TEXT)   BEGIN
    DECLARE v_booking_id INT;
    DECLARE v_user_id INT;
    DECLARE v_amount DECIMAL(10,2);
    DECLARE v_convenience_fee DECIMAL(10,2);
    DECLARE v_total_amount DECIMAL(10,2);
    
    -- Get booking details
    SELECT id, user_id, total_amount, convenience_fee 
    INTO v_booking_id, v_user_id, v_total_amount, v_convenience_fee
    FROM bookings WHERE booking_number = p_booking_number;
    
    SET v_amount = v_total_amount - v_convenience_fee;
    
    START TRANSACTION;
    
    -- Update booking payment status
    UPDATE bookings 
    SET payment_status = p_payment_status,
        payment_id = p_transaction_id,
        payment_method = p_payment_method
    WHERE booking_number = p_booking_number;
    
    -- Insert payment transaction record
    INSERT INTO payment_transactions (
        booking_id, user_id, transaction_id, payment_method, 
        amount, convenience_fee, total_amount, gateway_response, status
    ) VALUES (
        v_booking_id, v_user_id, p_transaction_id, p_payment_method,
        v_amount, v_convenience_fee, v_total_amount, p_gateway_response, p_payment_status
    );
    
    -- Create notification for user
    IF p_payment_status = 'success' THEN
        INSERT INTO notifications (user_id, booking_id, title, message, type)
        VALUES (
            v_user_id, v_booking_id, 
            'Payment Successful', 
            CONCAT('Your payment of ₹', v_total_amount, ' for booking ', p_booking_number, ' was successful.'),
            'payment'
        );
    ELSEIF p_payment_status = 'failed' THEN
        INSERT INTO notifications (user_id, booking_id, title, message, type)
        VALUES (
            v_user_id, v_booking_id,
            'Payment Failed',
            CONCAT('Your payment of ₹', v_total_amount, ' for booking ', p_booking_number, ' failed. Please try again.'),
            'payment'
        );
    END IF;
    
    COMMIT;
END$$

DELIMITER ;

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
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int NOT NULL,
  `booking_number` varchar(50) NOT NULL,
  `movie_title` varchar(255) NOT NULL,
  `show_time` time NOT NULL,
  `seats_booked` varchar(255) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `payment_status` varchar(20) DEFAULT 'Paid',
  `booking_status` varchar(20) DEFAULT 'Confirmed',
  `booking_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `booking_number`, `movie_title`, `show_time`, `seats_booked`, `total_amount`, `payment_method`, `payment_status`, `booking_status`, `booking_date`) VALUES
(1, 'FLX33317', 'Evil Dead Rise', '16:30:00', '31', 280.00, 'UPI', 'Paid', 'Confirmed', '2026-03-30 07:19:00'),
(2, 'FLX51057', 'Evil Dead Rise', '13:45:00', '30', 280.00, 'UPI', 'Paid', 'Confirmed', '2026-03-30 07:29:12'),
(3, 'FLX90383', 'Evil Dead Rise', '16:30:00', '31', 280.00, 'UPI', 'Paid', 'Confirmed', '2026-03-30 07:59:45'),
(4, 'FLX68716', 'Evil Dead Rise', '13:45:00', '44', 280.00, 'UPI', 'Paid', 'Confirmed', '2026-03-30 08:05:27'),
(5, 'FLX19067', 'Evil Dead Rise', '23:30:00', '43', 280.00, 'UPI', 'Paid', 'Confirmed', '2026-03-30 08:09:53'),
(6, 'FLX43227', 'Evil Dead Rise', '13:45:00', '39,52', 560.00, 'UPI', 'Paid', 'Confirmed', '2026-03-30 10:03:00'),
(7, 'FLX69392', 'Evil Dead Rise', '13:45:00', '55', 280.00, 'UPI', 'Paid', 'Confirmed', '2026-03-30 10:08:52'),
(8, 'FLX33532', 'Evil Dead Rise', '13:45:00', '55', 280.00, 'UPI', 'Paid', 'Confirmed', '2026-03-30 10:09:17');

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
(5, 'jadav priyanshi manubhai', 'abc@gmail.com', 'how are you ? are you fine ?', '2026-03-23 17:55:18'),
(6, 'vekariya vishava kiritbhai', 'vekariyashriya@gmail.com', 'vgr hfdffdfj ehfh rehrhe', '2026-03-30 07:20:35');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int NOT NULL,
  `star_rating` int NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `submitted_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `star_rating`, `subject`, `message`, `submitted_at`) VALUES
(1, 3, '542', 'fgdcn fh jfncj c', '2026-03-30 07:29:24'),
(2, 2, 'bnbn', 'fgjn ehf hfghegh', '2026-03-30 08:00:14'),
(3, 3, 'adsf', 'it is powerful\r\n', '2026-03-30 08:05:40'),
(4, 3, 'bnbn', 'hg hthd hdhfhdjjs', '2026-03-30 08:10:04'),
(5, 4, 'fdg ', 'yfu rhjh fdjfj', '2026-03-30 10:03:09');

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
(2, 'hjgdfsdsd', 'action', 'it is verypowerful', 'h1.jpg', 'discover.php', '2026-03-30 07:41:40'),
(4, 'URI', 'action', 'indian army', 'uploads/1774288164_uri.webp', 'discover.php', '2026-03-23 17:49:24');

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
-- Table structure for table `movie_offers`
--

CREATE TABLE `movie_offers` (
  `id` int NOT NULL,
  `movie_name` varchar(255) NOT NULL,
  `movie_image` varchar(255) NOT NULL,
  `original_price` decimal(10,2) NOT NULL,
  `discount_price` decimal(10,2) NOT NULL,
  `offer_tag` varchar(50) DEFAULT 'Special Offer',
  `expiry_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `movie_offers`
--

INSERT INTO `movie_offers` (`id`, `movie_name`, `movie_image`, `original_price`, `discount_price`, `offer_tag`, `expiry_date`) VALUES
(1, 'Spider-Man: No Way Home', 'https://m.media-amazon.com/images/M/MV5BZWMyYzFjYTYtNTRjYi00OGExLWE2YzgtOGRmYjAxZTU3NzBiXkEyXkFqcGdeQXVyMzQ0MzA0NTM@._V1_.jpg', 500.00, 249.00, '50% OFF', '2026-12-31'),
(2, 'The Batman', 'https://m.media-amazon.com/images/M/MV5BM2MyNjYxNmUtYTAwZC00MTYxLWJmNWYtYzZlODY3ZTk3OTFlXkEyXkFqcGdeQXVyNjY1MTg4Mzc@._V1_.jpg', 400.00, 199.00, 'HOT DEAL', '2026-05-15'),
(3, 'Inception', 'https://m.media-amazon.com/images/M/MV5BMjAxMzY3NjcxNF5BMl5BanBnXkFtZTcwNTI5OTM0Mw@@._V1_.jpg', 300.00, 0.00, 'FREE ACCESS', '2026-08-20');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `booking_id` int DEFAULT NULL,
  `title` varchar(200) DEFAULT NULL,
  `message` text,
  `type` enum('booking','payment','promotion','alert','system') DEFAULT 'system',
  `is_read` tinyint DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
-- Table structure for table `screens`
--

CREATE TABLE `screens` (
  `id` int NOT NULL,
  `theater_id` int NOT NULL,
  `screen_name` varchar(50) NOT NULL,
  `capacity` int DEFAULT '60',
  `screen_type` enum('2D','3D','IMAX','4DX') DEFAULT '2D',
  `seat_layout` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `screens`
--

INSERT INTO `screens` (`id`, `theater_id`, `screen_name`, `capacity`, `screen_type`, `seat_layout`, `created_at`) VALUES
(1, 1, 'Screen 1', 180, 'IMAX', NULL, '2026-03-30 03:57:18'),
(2, 1, 'Screen 2', 150, '3D', NULL, '2026-03-30 03:57:18'),
(3, 2, 'Screen 1', 100, '2D', NULL, '2026-03-30 03:57:18'),
(4, 3, 'Screen 1', 120, '3D', NULL, '2026-03-30 03:57:18');

-- --------------------------------------------------------

--
-- Table structure for table `theaters`
--

CREATE TABLE `theaters` (
  `id` int NOT NULL,
  `name` varchar(150) NOT NULL,
  `address` text,
  `city` varchar(50) NOT NULL,
  `state` varchar(50) DEFAULT NULL,
  `pincode` varchar(10) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `total_screens` int DEFAULT '1',
  `amenities` text,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `theaters`
--

INSERT INTO `theaters` (`id`, `name`, `address`, `city`, `state`, `pincode`, `phone`, `image_url`, `total_screens`, `amenities`, `status`, `created_at`) VALUES
(1, 'PVR Cinemas', 'Phoenix Market City, Kurla', 'Mumbai', 'Maharashtra', NULL, '02212345678', NULL, 8, 'Recliner Seats, Dolby Atmos, Food Court', 'active', '2026-03-30 03:57:18'),
(2, 'INOX', 'Neo Square, Race Course Road', 'Rajkot', 'Gujarat', NULL, '02812345678', NULL, 4, 'Digital 4K, Food Court, Parking', 'active', '2026-03-30 03:57:18'),
(3, 'Cinepolis', 'Vandana Heritage, Gondal Road', 'Rajkot', 'Gujarat', NULL, '02812345679', NULL, 3, '3D Screens, Cafe, Lounge', 'active', '2026-03-30 03:57:18'),
(4, 'Galaxy Cinema', 'Race Course Ring Road', 'Rajkot', 'Gujarat', NULL, '02812345680', NULL, 2, 'Budget Friendly, Snacks Counter', 'active', '2026-03-30 03:57:18');

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
(12, '1774864848_fututre.jpg', 'vekariya arjun', 'arjun@gmail.com', '9624272424', '2013-04-12', '98765432', 'dancing', 'Rajkot', 'user', 'active', '2026-03-30 10:00:48');

-- --------------------------------------------------------

--
-- Table structure for table `vw_available_shows`
--

CREATE TABLE `vw_available_shows` (
  `placeholder` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `watchlist`
--

CREATE TABLE `watchlist` (
  `id` int NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `type` enum('Movie','TV Show') NOT NULL,
  `priority` enum('Low','Medium','High') NOT NULL,
  `added_date` date NOT NULL,
  `timestamp` bigint NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
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
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact`
--
ALTER TABLE `contact`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `movies`
--
ALTER TABLE `movies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `movie_offers`
--
ALTER TABLE `movie_offers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_token`
--
ALTER TABLE `password_token`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `contact`
--
ALTER TABLE `contact`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `movie_offers`
--
ALTER TABLE `movie_offers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `watchlist`
--
ALTER TABLE `watchlist`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
