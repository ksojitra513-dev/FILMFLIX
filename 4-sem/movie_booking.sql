-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 12, 2026 at 10:00 AM
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
-- Database: `movie_boking`
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
  `description` text,
  `status` varchar(20) DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `about_cards`
--

INSERT INTO `about_cards` (`id`, `card_type`, `icon_class`, `title`, `description`, `status`) VALUES
(1, 'feature', 'fas fa-bolt', 'Instant Booking', 'Skip the queues and book your favorite seats in less than 30 seconds.', 'active'),
(2, 'feature', 'fas fa-couch', 'Premium Comfort', 'Partnership with top-tier cinemas for best reclining experience.', 'active'),
(3, 'feature', 'fas fa-wallet', 'Best Prices', 'No hidden charges. Get the best deals and exclusive combos.', 'active'),
(4, 'info', '🎬', 'Latest Releases', 'Stay updated with newly released movies and trending shows worldwide.', 'active'),
(5, 'info', '📺', 'HD Streaming', 'Enjoy high-definition streaming with smooth playback experience..', 'active'),
(6, 'info', '🔒', 'Secure Platform', 'Your data and privacy are always protected with advanced security.', 'active');

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
(1, 'Experience Entertainment Anytime.', 'Action Movie.jpg', 'WELCOME TO FILMFLI 123', 'The Best Movie Experience', 'We provide the best seat booking experience for movie lovers.', 95, 100, '500+', '10k+');

-- --------------------------------------------------------

--
-- Table structure for table `action_movies`
--

CREATE TABLE `action_movies` (
  `id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `language` varchar(50) DEFAULT NULL,
  `badge` varchar(100) DEFAULT NULL,
  `image_path` varchar(500) DEFAULT NULL,
  `rating` decimal(3,1) DEFAULT NULL,
  `duration` varchar(20) DEFAULT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `action_movies`
--

INSERT INTO `action_movies` (`id`, `title`, `language`, `badge`, `image_path`, `rating`, `duration`, `description`, `created_at`) VALUES
(1, 'Pathaan', 'Hindi', 'HIT', 'pathaan.jpg', 4.2, '2h 26m', 'An Indian spy fights against a deadly terrorist organization.', '2026-04-09 02:47:19'),
(2, 'Jawan', 'Hindi', 'BLOCKBUSTER', 'jawan.jpg', 4.5, '2h 49m', 'A prison warden fights corruption and injustice.', '2026-04-09 02:47:19'),
(3, 'Animal', 'Hindi', 'TRENDING', 'animal.jpg', 4.3, '3h 21m', 'A violent son seeks revenge against his father.', '2026-04-09 02:47:19'),
(4, 'Fighter', 'Hindi', 'NEW', 'fighter.jpg', 4.1, '2h 46m', 'Indian Air Force pilots fight against terrorists.', '2026-04-09 02:47:19'),
(5, 'War', 'Hindi', 'SUPER HIT', 'war.jpg', 4.4, '2h 34m', 'An Indian soldier hunts down his former mentor.', '2026-04-09 02:47:19');

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) DEFAULT 'admin',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `animated_movies`
--

CREATE TABLE `animated_movies` (
  `id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `language` varchar(50) DEFAULT NULL,
  `badge` varchar(100) DEFAULT NULL,
  `image_path` varchar(500) DEFAULT NULL,
  `rating` decimal(3,1) DEFAULT NULL,
  `duration` varchar(20) DEFAULT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `animated_movies`
--

INSERT INTO `animated_movies` (`id`, `title`, `language`, `badge`, `image_path`, `rating`, `duration`, `description`, `created_at`) VALUES
(1, 'Encanto', 'English', 'OSCAR', 'encanto.jpg', 4.5, '1h 42m', 'A magical family in Colombia hides secrets.', '2026-04-09 02:47:20'),
(2, 'Frozen', 'English', 'HIT', 'frozen.jpg', 4.3, '1h 42m', 'Two sisters save their kingdom from eternal winter.', '2026-04-09 02:47:20'),
(3, 'Toy Story', 'English', 'CLASSIC', 'toy_story.jpg', 4.6, '1h 21m', 'Toys come to life when humans are away.', '2026-04-09 02:47:20'),
(4, 'Coco', 'English', 'AWARD', 'coco.jpg', 4.7, '1h 45m', 'A boy journeys through the Land of the Dead.', '2026-04-09 02:47:20'),
(5, 'Spider-Verse', 'English', 'AWARD', 'spiderverse.jpg', 4.8, '1h 57m', 'Miles Morales becomes Spider-Man.', '2026-04-09 02:47:20');

-- --------------------------------------------------------

--
-- Table structure for table `banner`
--

CREATE TABLE `banner` (
  `id` int NOT NULL,
  `title` varchar(100) NOT NULL,
  `subtitle` varchar(100) NOT NULL,
  `imagurl` varchar(100) NOT NULL,
  `status` varchar(10) DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `banner`
--

INSERT INTO `banner` (`id`, `title`, `subtitle`, `imagurl`, `status`) VALUES
(1, '123', 'Your ultimate destination for cinematic excellence.', 'Laalo-wallpaper.jpg', 'active'),
(2, 'Silent Spectacle', 'Experience the thriller of the year.', 'uri.webp', 'active'),
(3, 'Silent Spectacle', 'Experience the thriller of the year.', 'satyamevjayte.webp', 'active'),
(4, 'Silent Spectacle', 'Experience the thriller of the year.', 'war 11.jpg', 'active'),
(5, 'Silent Spectacle', 'Experience the thriller of the year.', 'Saiyaara.jpg', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `bollywood_movies`
--

CREATE TABLE `bollywood_movies` (
  `id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `language` varchar(50) DEFAULT NULL,
  `badge` varchar(100) DEFAULT NULL,
  `image_path` varchar(500) DEFAULT NULL,
  `rating` decimal(3,1) DEFAULT NULL,
  `duration` varchar(20) DEFAULT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `bollywood_movies`
--

INSERT INTO `bollywood_movies` (`id`, `title`, `language`, `badge`, `image_path`, `rating`, `duration`, `description`, `created_at`) VALUES
(1, 'Pathaan', 'Hindi', 'BLOCKBUSTER', 'pathaan.jpg', 4.2, '2h 26m', 'SRK returns as a spy in this action thriller.', '2026-04-09 02:47:20'),
(2, 'Jawan', 'Hindi', 'SUPER HIT', 'jawan.jpg', 4.5, '2h 49m', 'Atlee directorial with SRK in dual role.', '2026-04-09 02:47:20'),
(3, 'Animal', 'Hindi', 'TRENDING', 'animal.jpg', 4.3, '3h 21m', 'Ranbir Kapoor in a violent family drama.', '2026-04-09 02:47:20'),
(4, 'Dunki', 'Hindi', 'HIT', 'dunki.jpg', 4.1, '2h 41m', 'SRK-Rajkumar Hirani film about illegal immigration.', '2026-04-09 02:47:20'),
(5, 'Tiger 3', 'Hindi', 'HIT', 'tiger3.jpg', 4.0, '2h 35m', 'Salman Khan returns as Tiger in this spy thriller.', '2026-04-09 02:47:20');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `booking_number` varchar(50) NOT NULL,
  `movie_title` varchar(255) NOT NULL,
  `show_time` varchar(50) NOT NULL,
  `seats_booked` varchar(255) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `offer_id` int DEFAULT NULL,
  `offer_tag` varchar(100) DEFAULT NULL,
  `original_amount` decimal(10,2) DEFAULT NULL,
  `payment_method` varchar(50) NOT NULL,
  `payment_status` varchar(20) DEFAULT 'Paid',
  `booking_status` varchar(20) DEFAULT 'Confirmed',
  `booking_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `booking_number`, `movie_title`, `show_time`, `seats_booked`, `total_amount`, `offer_id`, `offer_tag`, `original_amount`, `payment_method`, `payment_status`, `booking_status`, `booking_date`) VALUES
(27, 41, 'BK-FB8F3DAF', 'Now Showing', '10:10 AM', '5', 210.00, 0, '', 0.00, 'Cashfree', 'Paid', 'Confirmed', '2026-04-12 06:53:26'),
(28, 41, 'BK-F7D27F85', 'Pushpa 2', '1:30 PM', '3', 150.00, 4, 'Special Offer', 230.00, 'Cashfree', 'Paid', 'Confirmed', '2026-04-12 06:54:32'),
(29, 41, 'BK-2D46E055', 'The Batman', '10:10 AM', '5', 199.00, 2, 'HOT DEAL', 400.00, 'Cashfree', 'Paid', 'Confirmed', '2026-04-12 07:00:49'),
(30, 41, 'BK-4D1C5DAC', 'The Batman', '10:10 AM', '6', 199.00, 2, 'HOT DEAL', 400.00, 'Cashfree', 'Paid', 'Confirmed', '2026-04-12 07:08:47'),
(31, 41, 'BK-D99A4798', 'Now Showing', '10:10 AM', '3', 210.00, NULL, NULL, NULL, 'Cashfree', 'Paid', 'Confirmed', '2026-04-12 07:15:10'),
(32, 41, 'BK-7A2C3A6F', 'Now Showing', '10:00 PM', '60', 170.00, NULL, NULL, NULL, 'Cashfree', 'Paid', 'Confirmed', '2026-04-12 07:17:49'),
(33, 41, 'BK-EA6406FC', 'Now Showing', '4:00 PM', '57', 170.00, NULL, NULL, NULL, 'Cashfree', 'Paid', 'Confirmed', '2026-04-12 07:32:37'),
(34, 42, 'BK-54914B50', 'Now Showing', '4:00 PM', '54,59,58', 510.00, NULL, NULL, NULL, 'Cashfree', 'Paid', 'Confirmed', '2026-04-12 07:37:49'),
(36, 1, 'TEST123', 'Test Movie', '10:10 AM', 'A1', 100.00, NULL, NULL, NULL, 'Cashfree', 'Paid', 'Confirmed', '2026-04-12 07:51:15'),
(37, 42, 'BK-602E11E0', 'Now Showing', '1:30 PM', '27', 170.00, NULL, NULL, NULL, 'Cashfree', 'Pending', 'Pending', '2026-04-12 07:57:06'),
(38, 42, 'BK-AB57CBF1', 'The Batman', '7:30 PM', '18', 199.00, 2, 'HOT DEAL', 400.00, 'Cashfree', 'Paid', 'Confirmed', '2026-04-12 08:09:42'),
(39, 43, 'BK-5AC98BCC', 'Now Showing', '10:00 PM', '18', 210.00, 0, '', 0.00, 'Cashfree', 'Paid', 'Confirmed', '2026-04-12 09:48:53'),
(40, 43, 'BK-7EBF8903', 'Inception', '10:10 AM', '4', 200.00, 3, 'FREE ACCESS', 300.00, 'Cashfree', 'Paid', 'Confirmed', '2026-04-12 09:55:46');

-- --------------------------------------------------------

--
-- Table structure for table `classic_movies`
--

CREATE TABLE `classic_movies` (
  `id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `language` varchar(50) DEFAULT NULL,
  `badge` varchar(100) DEFAULT NULL,
  `image_path` varchar(500) DEFAULT NULL,
  `rating` decimal(3,1) DEFAULT NULL,
  `duration` varchar(20) DEFAULT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `classic_movies`
--

INSERT INTO `classic_movies` (`id`, `title`, `language`, `badge`, `image_path`, `rating`, `duration`, `description`, `created_at`) VALUES
(1, 'Sholay', 'Hindi', 'EVERGREEN', 'sholay.jpg', 4.8, '3h 18m', 'Two criminals fight a ruthless dacoit.', '2026-04-09 02:47:20'),
(2, 'Mughal E Azam', 'Hindi', 'EPIC', 'mughal_e_azam.jpg', 4.7, '2h 53m', 'A prince falls in love with a court dancer.', '2026-04-09 02:47:20'),
(3, 'DDLJ', 'Hindi', 'ICONIC', 'ddlj.jpg', 4.6, '2h 59m', 'A young couple fights for their love.', '2026-04-09 02:47:20'),
(4, 'The Godfather', 'English', 'MASTERPIECE', 'godfather.jpg', 4.9, '2h 55m', 'A mafia family faces power struggles.', '2026-04-09 02:47:20'),
(5, 'Pulp Fiction', 'English', 'CULT', 'pulp_fiction.jpg', 4.7, '2h 34m', 'Interconnected crime stories in Los Angeles.', '2026-04-09 02:47:20');

-- --------------------------------------------------------

--
-- Table structure for table `comedy_movies`
--

CREATE TABLE `comedy_movies` (
  `id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `language` varchar(50) DEFAULT NULL,
  `badge` varchar(100) DEFAULT NULL,
  `image_path` varchar(500) DEFAULT NULL,
  `rating` decimal(3,1) DEFAULT NULL,
  `duration` varchar(20) DEFAULT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `comedy_movies`
--

INSERT INTO `comedy_movies` (`id`, `title`, `language`, `badge`, `image_path`, `rating`, `duration`, `description`, `created_at`) VALUES
(1, 'Hindi Medium', 'Hindi', 'HILARIOUS', 'hindi_medium.jpg', 4.3, '2h 12m', 'A couple struggles to get their daughter into a prestigious school.', '2026-04-09 02:47:19'),
(2, 'Stree', 'Hindi', 'HIT', 'stree.jpg', 4.2, '2h 8m', 'A horror-comedy about a ghost who kidnaps men.', '2026-04-09 02:47:19'),
(3, 'Dream Girl', 'Hindi', 'TRENDING', 'dream_girl.jpg', 3.9, '2h 12m', 'A man who can talk like a woman gets into funny situations.', '2026-04-09 02:47:19'),
(4, 'Bhool Bhulaiyaa', 'Hindi', 'SUPER HIT', 'bhool_bhulaiyaa.jpg', 4.4, '2h 39m', 'A horror-comedy about a haunted mansion.', '2026-04-09 02:47:19'),
(5, 'Golmaal', 'Hindi', 'FUN', 'golmaal.jpg', 4.0, '2h 15m', 'Four friends get into hilarious trouble.', '2026-04-09 02:47:19');

-- --------------------------------------------------------

--
-- Table structure for table `contact`
--

CREATE TABLE `contact` (
  `id` int NOT NULL,
  `fullname` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `message` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(20) DEFAULT 'unread'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `contact`
--

INSERT INTO `contact` (`id`, `fullname`, `email`, `message`, `created_at`, `status`) VALUES
(1, 'fdggfh', 'shriya@example.com', 'fnd fjrjg rfj jfjfhjh fhjhjkdhdkjkg', '2026-03-09 12:55:57', 'unread'),
(2, 'vekariya shriya', 's@gmail.com', 'hyy shriya how are you?', '2026-03-16 08:27:04', 'unread'),
(3, 'abc', 'abc@gmail.com', 'hfhfgjgjugfgsdfsgfhghfff', '2026-03-23 14:47:33', 'unread'),
(5, 'jadav priyanshi manubhai', 'abc@gmail.com', 'how are you ? are you fine ?', '2026-03-23 17:55:18', 'unread'),
(6, 'vekariya vishava kiritbhai', 'vekariyashriya@gmail.com', 'vgr hfdffdfj ehfh rehrhe', '2026-03-30 07:20:35', 'unread'),
(7, 'jadav jaldhara manubhai', 'vekariyashriya@gmail.com', 'gjhkghdkjkggggggggggggg', '2026-03-31 07:20:19', 'unread'),
(8, 'vekariya shreeya', 'vekariyashriya@gmail.com', 'dhg  dvfheeben b', '2026-04-02 16:41:06', 'unread');

-- --------------------------------------------------------

--
-- Table structure for table `documentary_movies`
--

CREATE TABLE `documentary_movies` (
  `id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `language` varchar(50) DEFAULT NULL,
  `badge` varchar(100) DEFAULT NULL,
  `image_path` varchar(500) DEFAULT NULL,
  `rating` decimal(3,1) DEFAULT NULL,
  `duration` varchar(20) DEFAULT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `documentary_movies`
--

INSERT INTO `documentary_movies` (`id`, `title`, `language`, `badge`, `image_path`, `rating`, `duration`, `description`, `created_at`) VALUES
(1, 'Our Planet', 'English', 'NETFLIX', 'our_planet.jpg', 4.8, '50m', 'Nature documentary showcasing Earth\'s beauty.', '2026-04-09 02:47:20'),
(2, 'Free Solo', 'English', 'OSCAR', 'free_solo.jpg', 4.7, '1h 40m', 'A climber attempts a rope-free climb.', '2026-04-09 02:47:20'),
(3, 'Blackfish', 'English', 'HIT', 'blackfish.jpg', 4.5, '1h 23m', 'The story of a captive killer whale.', '2026-04-09 02:47:20'),
(4, 'Won\'t You Be My Neighbor', 'English', 'HEARTFELT', 'won_t_you.jpg', 4.6, '1h 34m', 'The life of Fred Rogers.', '2026-04-09 02:47:20'),
(5, 'The Social Dilemma', 'English', 'HIT', 'social_dilemma.jpg', 4.4, '1h 34m', 'The dangers of social media.', '2026-04-09 02:47:20');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int NOT NULL,
  `star_rating` int NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `submitted_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(20) DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `star_rating`, `subject`, `message`, `submitted_at`, `status`) VALUES
(1, 3, '542', 'fgdcn fh jfncj c', '2026-03-30 07:29:24', 'pending'),
(2, 2, 'bnbn', 'fgjn ehf hfghegh', '2026-03-30 08:00:14', 'pending'),
(3, 3, 'adsf', 'it is powerful\r\n', '2026-03-30 08:05:40', 'pending'),
(4, 3, 'bnbn', 'hg hthd hdhfhdjjs', '2026-03-30 08:10:04', 'pending'),
(5, 4, 'fdg ', 'yfu rhjh fdjfj', '2026-03-30 10:03:09', 'pending'),
(6, 3, 'vhuyjji', 'hjjh iuiu uuiuiuiui', '2026-03-30 16:08:39', 'pending'),
(7, 3, 'adsf', 'jfnsmcn fjfjenfhrjghrjghjr', '2026-03-31 05:50:17', 'pending'),
(8, 5, 'movie', 'it is greate', '2026-03-31 06:15:29', 'pending'),
(9, 3, 'bnbn', 'gghhjhjhh hjhhj', '2026-04-07 12:00:31', 'pending'),
(10, 5, 'adsf', 'df dgfhjgty', '2026-04-07 12:01:36', 'pending'),
(11, 3, '542', '554v hhjb ', '2026-04-07 12:11:01', 'pending'),
(12, 4, 'vhuyjji', 'g fhjnhgmhm ', '2026-04-07 12:12:36', 'pending'),
(13, 3, 'adsf', 'egthti yjhuy', '2026-04-07 12:35:02', 'pending'),
(14, 3, 'cdgere yt', 'jbde jrjetjj4t', '2026-04-07 13:36:08', 'pending'),
(15, 3, 'erfet', 'dntbvbrthrtjh', '2026-04-07 13:37:05', 'pending'),
(16, 3, 'dxsfdf', 'dhjhf fdfhjgjghj', '2026-04-09 15:37:34', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `gallery`
--

CREATE TABLE `gallery` (
  `id` int UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  `search_tags` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `gallery`
--

INSERT INTO `gallery` (`id`, `title`, `description`, `image_path`, `link`, `search_tags`, `created_at`) VALUES
(1, 'South Indian Movies', 'Blockbuster action dramas from South', 'south.jpg', 'south_movie.php', 'south tamil telugu kannada malayalam', '2026-04-07 12:59:36'),
(2, 'Horror Movies', 'Scary and thrilling horror films', 'Horrer.jpg', 'discover.php', 'horror scary ghost thriller', '2026-04-07 12:59:36'),
(3, 'Action Movies', 'High-octane action entertainers', 'war 11.jpg', 'action.php', 'action fight thriller', '2026-04-07 12:59:36'),
(4, 'Comedy Movies', 'Laugh riot comedy films', 'c.jpg', 'comedy.php', 'comedy funny hilarious', '2026-04-07 12:59:36'),
(5, 'Bollywood Hits', 'Top Bollywood blockbusters', 'hq720.jpg', 'bollywood.php', 'bollywood hindi shahrukh salman', '2026-04-07 12:59:36'),
(6, 'Hollywood Movies', 'International Hollywood hits', 'action.jpg', 'hollywood.php', 'hollywood english marvel dc', '2026-04-07 12:59:36'),
(7, 'Romantic Movies', 'Love and romance stories', 'r.jpg', 'romantic.php', 'romantic love heart', '2026-04-07 12:59:36'),
(8, 'Thriller Movies', 'Suspense thriller edge of seat', 'as3.jpg', 'thriller.php', 'thriller suspense mystery', '2026-04-07 12:59:36'),
(9, 'Animated Movies', 'Family friendly animations', 'as2.jpg', 'animated.php', 'animated cartoon kids', '2026-04-07 12:59:36'),
(10, 'Sci-Fi Movies', 'Science fiction futuristic', 's1.jpg', 'scifi.php', 'sci-fi science fiction future', '2026-04-07 12:59:36'),
(11, 'Documentary', 'Real stories and documentaries', 'as1.jpg', 'documentary.php', 'documentary real true story', '2026-04-07 12:59:36'),
(12, 'Classic Movies', 'Timeless classic cinema', 'as4.jpg', 'classic.php', 'classic old retro', '2026-04-07 12:59:36');

-- --------------------------------------------------------

--
-- Table structure for table `hollywood_movies`
--

CREATE TABLE `hollywood_movies` (
  `id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `language` varchar(50) DEFAULT NULL,
  `badge` varchar(100) DEFAULT NULL,
  `image_path` varchar(500) DEFAULT NULL,
  `rating` decimal(3,1) DEFAULT NULL,
  `duration` varchar(20) DEFAULT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `hollywood_movies`
--

INSERT INTO `hollywood_movies` (`id`, `title`, `language`, `badge`, `image_path`, `rating`, `duration`, `description`, `created_at`) VALUES
(1, 'Oppenheimer', 'English', 'OSCAR', 'oppenheimer.jpg', 4.8, '3h 1m', 'The story of J. Robert Oppenheimer and the atomic bomb.', '2026-04-09 02:47:20'),
(2, 'Barbie', 'English', 'HIT', 'barbie.jpg', 4.2, '1h 54m', 'Barbie and Ken go on a journey of self-discovery.', '2026-04-09 02:47:20'),
(3, 'Mission Impossible', 'English', 'SUPER HIT', 'mission_impossible.jpg', 4.6, '2h 43m', 'Ethan Hunt faces his most dangerous mission yet.', '2026-04-09 02:47:20'),
(4, 'John Wick', 'English', 'HIT', 'john_wick.jpg', 4.5, '2h 49m', 'A retired hitman seeks vengeance.', '2026-04-09 02:47:20'),
(5, 'Fast X', 'English', 'TRENDING', 'fast_x.jpg', 4.1, '2h 21m', 'Dom Toretto faces a dangerous new enemy.', '2026-04-09 02:47:20');

-- --------------------------------------------------------

--
-- Table structure for table `horror_movies`
--

CREATE TABLE `horror_movies` (
  `id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `language` varchar(50) DEFAULT NULL,
  `badge` varchar(100) DEFAULT NULL,
  `image_path` varchar(500) DEFAULT NULL,
  `rating` decimal(3,1) DEFAULT NULL,
  `duration` varchar(20) DEFAULT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `horror_movies`
--

INSERT INTO `horror_movies` (`id`, `title`, `language`, `badge`, `image_path`, `rating`, `duration`, `description`, `created_at`) VALUES
(1, 'Evil Dead Rise', 'English', '18+', 'hooror.jpg', 4.2, '1h 36m', 'A twisted tale of two sisters whose reunion is cut short by the rise of flesh-possessing demons.', '2026-04-09 16:37:05'),
(2, 'The Nun II', 'English', 'HIT', 'h1.jpg', 4.0, '1h 50m', 'Sister Irene once again comes face to face with the demonic nun Valak.', '2026-04-09 16:37:05'),
(3, 'Insidious: Red Door', 'English', 'TRENDING', 'h3.jpg', 3.8, '1h 47m', 'The Lambert family returns to the Further to put their demons to rest.', '2026-04-09 16:37:05'),
(4, 'Talk To Me', 'English', 'NEW', 'h4.jpg', 4.3, '1h 35m', 'A group of friends discover how to conjure spirits using an embalmed hand.', '2026-04-09 16:37:05'),
(5, 'Smile', 'English', 'HD', 'h5.jpg', 4.1, '1h 55m', 'A doctor witnesses a traumatic incident and begins to experience frightening occurrences.', '2026-04-09 16:37:05');

-- --------------------------------------------------------

--
-- Table structure for table `movies`
--

CREATE TABLE `movies` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `poster_img` varchar(500) NOT NULL,
  `is_new` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `movies`
--

INSERT INTO `movies` (`id`, `name`, `poster_img`, `is_new`) VALUES
(1, 'Deadpool 3', 'movie1.jpg', 1),
(2, 'Kalki 2898 AD', 'movie2.jpg', 1),
(3, 'Inside Out 2', 'movie3.jpg', 1),
(4, 'The Fall Guy', 'movie4.jpg', 0),
(5, 'Kingdom of the Planet of the Apes', 'movie5.jpg', 0);

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
  `expiry_date` date DEFAULT NULL,
  `status` varchar(20) DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `movie_offers`
--

INSERT INTO `movie_offers` (`id`, `movie_name`, `movie_image`, `original_price`, `discount_price`, `offer_tag`, `expiry_date`, `status`) VALUES
(1, 'Spider-Man: No Way Home', 'https://m.media-amazon.com/images/M/MV5BZWMyYzFjYTYtNTRjYi00OGExLWE2YzgtOGRmYjAxZTU3NzBiXkEyXkFqcGdeQXVyMzQ0MzA0NTM@._V1_.jpg', 500.00, 249.00, '50% OFF', '2026-12-31', 'active'),
(2, 'The Batman', 'batman.jpg', 400.00, 199.00, 'HOT DEAL', '2026-05-15', 'active'),
(3, 'Inception', 'https://m.media-amazon.com/images/M/MV5BMjAxMzY3NjcxNF5BMl5BanBnXkFtZTcwNTI5OTM0Mw@@._V1_.jpg', 300.00, 200.00, 'FREE ACCESS', '2026-08-20', 'active'),
(4, 'Pushpa ', 'pushpa.jpg', 230.00, 150.00, 'Special Offer', '2026-12-31', 'active');

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

-- --------------------------------------------------------

--
-- Table structure for table `romantic_movies`
--

CREATE TABLE `romantic_movies` (
  `id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `language` varchar(50) DEFAULT NULL,
  `badge` varchar(100) DEFAULT NULL,
  `image_path` varchar(500) DEFAULT NULL,
  `rating` decimal(3,1) DEFAULT NULL,
  `duration` varchar(20) DEFAULT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `romantic_movies`
--

INSERT INTO `romantic_movies` (`id`, `title`, `language`, `badge`, `image_path`, `rating`, `duration`, `description`, `created_at`) VALUES
(1, 'Yeh Jawaani Hai Deewani', 'Hindi', 'BLOCKBUSTER', 'yjhd.jpg', 4.4, '2h 40m', 'A story of friendship, love, and self-discovery.', '2026-04-09 02:47:20'),
(2, 'Rockstar', 'Hindi', 'HIT', 'rockstar.jpg', 4.3, '2h 39m', 'A struggling musician finds love and fame.', '2026-04-09 02:47:20'),
(3, 'Barfi', 'Hindi', 'HEART TOUCHING', 'barfi.jpg', 4.5, '2h 31m', 'A deaf-mute man falls in love with an autistic girl.', '2026-04-09 02:47:20'),
(4, 'Jab We Met', 'Hindi', 'SUPER HIT', 'jab_we_met.jpg', 4.6, '2h 18m', 'A depressed businessman meets a bubbly girl.', '2026-04-09 02:47:20'),
(5, 'Aashiqui 2', 'Hindi', 'SUPER HIT', 'aashiqui2.jpg', 4.4, '2h 12m', 'A singer battles alcoholism and love.', '2026-04-09 02:47:20');

-- --------------------------------------------------------

--
-- Table structure for table `scifi_movies`
--

CREATE TABLE `scifi_movies` (
  `id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `language` varchar(50) DEFAULT NULL,
  `badge` varchar(100) DEFAULT NULL,
  `image_path` varchar(500) DEFAULT NULL,
  `rating` decimal(3,1) DEFAULT NULL,
  `duration` varchar(20) DEFAULT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `scifi_movies`
--

INSERT INTO `scifi_movies` (`id`, `title`, `language`, `badge`, `image_path`, `rating`, `duration`, `description`, `created_at`) VALUES
(1, 'Interstellar', 'English', 'MASTERPIECE', 'interstellar.jpg', 4.9, '2h 49m', 'Astronauts search for a new habitable planet.', '2026-04-09 02:47:20'),
(2, 'Inception', 'English', 'HIT', 'inception.jpg', 4.8, '2h 28m', 'A thief steals secrets from within dreams.', '2026-04-09 02:47:20'),
(3, 'Avatar', 'English', 'BLOCKBUSTER', 'avatar.jpg', 4.5, '2h 42m', 'A marine on an alien planet falls in love.', '2026-04-09 02:47:20'),
(4, 'Dune', 'English', 'HIT', 'dune.jpg', 4.4, '2h 35m', 'A noble family fights for a dangerous desert planet.', '2026-04-09 02:47:20'),
(5, 'The Martian', 'English', 'HIT', 'martian.jpg', 4.6, '2h 24m', 'An astronaut is stranded alone on Mars.', '2026-04-09 02:47:20');

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
-- Table structure for table `site_settings`
--

CREATE TABLE `site_settings` (
  `id` int NOT NULL,
  `site_title` varchar(255) DEFAULT NULL,
  `tagline` varchar(255) DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `social_link` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `south_movies`
--

CREATE TABLE `south_movies` (
  `id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `language` varchar(50) DEFAULT NULL,
  `badge` varchar(100) DEFAULT NULL,
  `image_path` varchar(500) DEFAULT NULL,
  `rating` decimal(3,1) DEFAULT NULL,
  `duration` varchar(20) DEFAULT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `south_movies`
--

INSERT INTO `south_movies` (`id`, `title`, `language`, `badge`, `image_path`, `rating`, `duration`, `description`, `created_at`) VALUES
(1, 'KGF Chapter 2', 'Kannada', 'BLOCKBUSTER', 'kgf.jpg', 4.8, '2h 48m', 'Rocky rises to power in the gold mines.', '2026-04-09 02:47:20'),
(2, 'RRR', 'Telugu', 'OSCAR WINNER', 'rrr.jpg', 4.9, '3h 7m', 'Two revolutionaries fight against British rule.', '2026-04-09 02:47:20'),
(3, 'Pushpa: The Rise', 'Telugu', 'TRENDING', 'pushpa.jpg', 4.5, '2h 59m', 'A red sandalwood smuggler rises to power.', '2026-04-09 02:47:20'),
(4, 'Kantara', 'Kannada', 'NATIONAL AWARD', 'kantara.jpg', 4.7, '2h 28m', 'A clash between nature and man.', '2026-04-09 02:47:20'),
(5, 'Jailer', 'Tamil', 'SUPER HIT', 'jailer.jpg', 4.4, '2h 48m', 'A retired jailer hunts down a gang.', '2026-04-09 02:47:20');

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
(4, 'Galaxy Cinema', 'Race Course Ring Road', 'Rajkot', 'Gujarat', NULL, '02812345680', NULL, 2, 'Budget Friendly, Snacks Counter', 'active', '2026-03-30 03:57:18'),
(5, 'rajkot cinema', 'dj hjhjdhffjhfjhfj hefjhejf', 'rajkot', 'gujarat', NULL, '7894561234', NULL, 1, 'Budget Friendly, Snacks Counter', 'active', '2026-03-30 16:44:17');

-- --------------------------------------------------------

--
-- Table structure for table `thriller_movies`
--

CREATE TABLE `thriller_movies` (
  `id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `language` varchar(50) DEFAULT NULL,
  `badge` varchar(100) DEFAULT NULL,
  `image_path` varchar(500) DEFAULT NULL,
  `rating` decimal(3,1) DEFAULT NULL,
  `duration` varchar(20) DEFAULT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `thriller_movies`
--

INSERT INTO `thriller_movies` (`id`, `title`, `language`, `badge`, `image_path`, `rating`, `duration`, `description`, `created_at`) VALUES
(1, 'Drishyam', 'Hindi', 'MASTERPIECE', 'drishyam.jpg', 4.7, '2h 23m', 'A man goes to extreme lengths to protect his family.', '2026-04-09 02:47:20'),
(2, 'Kahaani', 'Hindi', 'HIT', 'kahaani.jpg', 4.4, '2h 2m', 'A pregnant woman searches for her missing husband.', '2026-04-09 02:47:20'),
(3, 'Andhadhun', 'Hindi', 'UNIQUE', 'andhadhun.jpg', 4.6, '2h 17m', 'A blind pianist gets caught in a murder mystery.', '2026-04-09 02:47:20'),
(4, 'Talaash', 'Hindi', 'HIT', 'talaash.jpg', 4.1, '2h 19m', 'A cop investigates a mysterious death.', '2026-04-09 02:47:20'),
(5, 'It Chapter 2', 'English', 'HIT', 'it_chapter2.jpg', 4.0, '2h 49m', 'The Losers Club returns to defeat Pennywise.', '2026-04-09 02:47:20');

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
(14, '1774885821_h1.jpg', 'vekariya shreeya', 'arjun@gmail.com', '9624272424', '2001-12-04', '12345678', 'dancing', 'Rajkot', 'user', 'active', '2026-03-30 15:50:21'),
(42, 'default.png', 'nita jadav', 'nm8@gmail.com', '1234567890', '2026-04-08', '123456789', 'Action', 'Rajkot', 'user', 'active', '2026-04-12 07:35:59');

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
-- Indexes for table `action_movies`
--
ALTER TABLE `action_movies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `animated_movies`
--
ALTER TABLE `animated_movies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `banner`
--
ALTER TABLE `banner`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bollywood_movies`
--
ALTER TABLE `bollywood_movies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `classic_movies`
--
ALTER TABLE `classic_movies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comedy_movies`
--
ALTER TABLE `comedy_movies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact`
--
ALTER TABLE `contact`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `documentary_movies`
--
ALTER TABLE `documentary_movies`
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
-- Indexes for table `hollywood_movies`
--
ALTER TABLE `hollywood_movies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `horror_movies`
--
ALTER TABLE `horror_movies`
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
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_token`
--
ALTER TABLE `password_token`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `romantic_movies`
--
ALTER TABLE `romantic_movies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `scifi_movies`
--
ALTER TABLE `scifi_movies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `south_movies`
--
ALTER TABLE `south_movies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `theaters`
--
ALTER TABLE `theaters`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `thriller_movies`
--
ALTER TABLE `thriller_movies`
  ADD PRIMARY KEY (`id`);

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `about_content`
--
ALTER TABLE `about_content`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `action_movies`
--
ALTER TABLE `action_movies`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `animated_movies`
--
ALTER TABLE `animated_movies`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `banner`
--
ALTER TABLE `banner`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `bollywood_movies`
--
ALTER TABLE `bollywood_movies`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `classic_movies`
--
ALTER TABLE `classic_movies`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `comedy_movies`
--
ALTER TABLE `comedy_movies`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `contact`
--
ALTER TABLE `contact`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `documentary_movies`
--
ALTER TABLE `documentary_movies`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `hollywood_movies`
--
ALTER TABLE `hollywood_movies`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `horror_movies`
--
ALTER TABLE `horror_movies`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `movies`
--
ALTER TABLE `movies`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `movie_offers`
--
ALTER TABLE `movie_offers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `romantic_movies`
--
ALTER TABLE `romantic_movies`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `scifi_movies`
--
ALTER TABLE `scifi_movies`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `site_settings`
--
ALTER TABLE `site_settings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `south_movies`
--
ALTER TABLE `south_movies`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `theaters`
--
ALTER TABLE `theaters`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `thriller_movies`
--
ALTER TABLE `thriller_movies`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `watchlist`
--
ALTER TABLE `watchlist`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;