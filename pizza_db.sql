-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 24, 2025 at 09:57 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pizza_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_email` varchar(100) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `delivery_address` text NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('Pending','Preparing','Out for Delivery','Delivered') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `customer_name`, `customer_email`, `phone_number`, `delivery_address`, `total_amount`, `status`, `created_at`) VALUES
(1, 1, '', '', '', '', 119.00, 'Pending', '2025-12-24 07:59:30'),
(2, 1, '', '', '', '', 83.00, 'Pending', '2025-12-24 08:06:21'),
(3, 1, 'gee', '001@gmail.com', '011-234567', 'heloo', 42.00, 'Pending', '2025-12-24 08:13:43'),
(4, 1, 'yry', '001@gmail.com', 'tytyyt', '5thty', 48.00, 'Pending', '2025-12-24 08:19:43'),
(5, 4, 'Jane', 'ss@gmail.com', '011-2345678', 'No 3, Jalan Ikan 22, Taman Sea, Kelantan', 202.00, 'Pending', '2025-12-24 08:44:08');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `pizza_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price_at_purchase` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `pizza_id`, `quantity`, `price_at_purchase`) VALUES
(1, 1, 1, 1, 35.00),
(2, 1, 3, 2, 42.00),
(3, 2, 1, 1, 35.00),
(4, 2, 2, 1, 48.00),
(5, 3, 3, 1, 42.00),
(6, 4, 2, 1, 48.00),
(7, 5, 2, 1, 48.00),
(8, 5, 4, 1, 32.00),
(9, 5, 7, 1, 52.00),
(10, 5, 8, 1, 36.00),
(11, 5, 10, 1, 34.00);

-- --------------------------------------------------------

--
-- Table structure for table `pizzas`
--

CREATE TABLE `pizzas` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `status` enum('available','unavailable') DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pizzas`
--

INSERT INTO `pizzas` (`id`, `name`, `description`, `price`, `image`, `category`, `status`, `created_at`) VALUES
(1, 'Margherita Royale', 'Fresh buffalo mozzarella, san marzano tomatoes, and organic basil.', 35.00, 'margherita.jpg', 'Classic', 'available', '2025-12-24 07:06:40'),
(2, 'Truffle Mushroom', 'Wild mushrooms, white truffle oil, and creamy béchamel sauce.', 48.00, 'truffle.jpg', 'Premium', 'available', '2025-12-24 07:06:40'),
(3, 'Quattro Formaggi', 'A blend of Gorgonzola, Parmesan, Mozzarella, and Fontina cheese.', 42.00, 'four-cheese.jpg', 'Premium', 'available', '2025-12-24 07:06:40'),
(4, 'Garden Fresh', 'Bell peppers, olives, red onions, and spinach on a whole wheat crust.', 32.00, 'veggie.jpg', 'Vegetarian', 'available', '2025-12-24 07:06:40'),
(5, 'Spicy Pepperoni', 'Premium beef pepperoni with jalapeños and chili-infused honey.', 38.00, 'pepperoni.jpg', 'Classic', 'unavailable', '2025-12-24 07:06:40'),
(6, 'BBQ Chicken Bliss', 'Grilled chicken, red onions, and smoky BBQ sauce with a cilantro finish.', 38.00, 'bbq_chicken.jpg', 'Premium', 'available', '2025-12-24 08:22:23'),
(7, 'Ocean Harvest', 'Succulent prawns, calamari, and mussels with a garlic-lemon butter base.', 52.00, 'seafood.jpg', 'Premium', 'available', '2025-12-24 08:22:23'),
(8, 'Pesto Verde', 'Creamy pesto base, sun-dried tomatoes, and roasted pine nuts.', 36.00, 'pesto.jpg', 'Vegetarian', 'available', '2025-12-24 08:22:23'),
(9, 'Double Beef Pepperoni', 'Extra layers of premium beef pepperoni and triple mozzarella blend.', 40.00, 'pepperoni_plus.jpg', 'Classic', 'available', '2025-12-24 08:22:23'),
(10, 'Aloha Sunshine', 'Classic turkey ham and pineapple chunks on a sweet tomato base.', 34.00, 'hawaiian.jpg', 'Classic', 'available', '2025-12-24 08:22:23'),
(11, 'Mediterranean Veggie', 'Artichokes, feta cheese, kalamata olives, and fresh spinach.', 35.00, 'med_veggie.jpg', 'Vegetarian', 'available', '2025-12-24 08:22:23'),
(12, 'Spicy Sambal Sensation', 'Local-inspired spicy sambal base with anchovies and boiled eggs.', 32.00, 'sambal.jpg', 'Classic', 'available', '2025-12-24 08:22:23'),
(13, 'White Truffle Deluxe', 'Exotic mushrooms with white truffle oil and fresh arugula.', 55.00, 'white_truffle.jpg', 'Premium', 'available', '2025-12-24 08:22:23');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`) VALUES
(1, '001', '001@gmail.com', '$2y$10$mrtMrkpdlxmfBndU3u8FLetNnqskZLKnxIMCQieMzHZrFkz5cQRVu', '2025-12-24 07:14:50'),
(2, '002', '002@gmail.com', '$2y$10$aHIg4gIm.mc72BvPi2KEHe3k4bG/lzJ08Uyi.Ejctb4fBjWC8ZzH.', '2025-12-24 07:16:21'),
(3, '003', '003@gmail.com', '$2y$10$6F9STK8Ec9uEm1XGpXc2V.sk4yIYCQRAojnVRkckxSIjW4HHzpUlm', '2025-12-24 07:25:18'),
(4, 'superman', 'ss@gmail.com', '$2y$10$pwvyQP9B8ykF8xlVIAXKkujMnQm../g20Oz09X7Kcdc9.cbJvii.G', '2025-12-24 08:41:21');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `pizza_id` (`pizza_id`);

--
-- Indexes for table `pizzas`
--
ALTER TABLE `pizzas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `pizzas`
--
ALTER TABLE `pizzas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`pizza_id`) REFERENCES `pizzas` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
