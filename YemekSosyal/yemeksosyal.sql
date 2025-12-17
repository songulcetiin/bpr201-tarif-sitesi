-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 17 Ara 2025, 21:45:06
-- Sunucu sürümü: 10.4.32-MariaDB
-- PHP Sürümü: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `yemeksosyal`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `recipe_id` int(11) DEFAULT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `follows`
--

CREATE TABLE `follows` (
  `follower_id` int(11) NOT NULL,
  `following_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `follows`
--

INSERT INTO `follows` (`follower_id`, `following_id`, `created_at`) VALUES
(1, 2, '2025-12-17 20:36:19'),
(2, 1, '2025-12-17 20:28:53');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `likes`
--

CREATE TABLE `likes` (
  `user_id` int(11) NOT NULL,
  `recipe_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `likes`
--

INSERT INTO `likes` (`user_id`, `recipe_id`, `created_at`) VALUES
(1, 2, '2025-12-17 16:03:15');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `recipes`
--

CREATE TABLE `recipes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `ingredients` text DEFAULT NULL,
  `instructions` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `recipes`
--

INSERT INTO `recipes` (`id`, `user_id`, `title`, `description`, `image`, `ingredients`, `instructions`, `created_at`) VALUES
(2, 1, 'Kremalı Mantarlı Tavuk', 'Bu tarif, tavuğun kurumasını engelleyen kremsi sosuyla damakta harika bir tat bırakır.', 'indir.jpg', '500g tavuk göğsü veya kemiksiz kalça şiş (kuşbaşı doğranmış)\r\n\r\n400g kültür mantarı (dilimlenmiş)\r\n\r\n1 paket sıvı krema (200 ml)\r\n\r\n2 diş sarımsak (ezilmiş)\r\n\r\n3 yemek kaşığı sıvı yağ\r\n\r\n1 tatlı kaşığı tereyağı\r\n\r\nBaharatlar: Tuz, karabiber, kekik (isteğe bağlı bir tutam köri)', 'Tavukları Mühürleyin: Geniş bir tavaya sıvı yağı alın ve yüksek ateşte tavukları ekleyin. Tavuklar suyunu salıp tekrar çekene kadar ara ara karıştırarak pişirin.\r\n\r\nMantarları Ekleyin: Tavuklar hafifçe kızarmaya başlayınca tereyağını ve dilimlediğiniz mantarları ilave edin. Mantarlar suyunu salıp çekene kadar yüksek ateşte sotelemeye devam edin.\r\n\r\nAroma Katın: Ezilmiş sarımsakları, tuzu ve baharatları ekleyip 1-2 dakika daha çevirin.\r\n\r\nKremayı Bağlayın: Son olarak sıvı kremayı tavaya dökün. Altını biraz kısın ve krema hafifçe koyulaşıp tavuklarla bütünleşene kadar (yaklaşık 3-4 dakika) pişirin.\r\n\r\nServis: Sıcak servis yapın. Üzerine taze maydanoz serperek görselliği artırabilirsiniz.', '2025-12-17 15:49:04');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `saved_recipes`
--

CREATE TABLE `saved_recipes` (
  `user_id` int(11) NOT NULL,
  `recipe_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `saved_recipes`
--

INSERT INTO `saved_recipes` (`user_id`, `recipe_id`, `created_at`) VALUES
(1, 2, '2025-12-17 16:03:30');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `avatar` varchar(255) DEFAULT 'default.png',
  `verification_code` varchar(10) DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `bio` text DEFAULT NULL,
  `role` enum('user','admin','manager') DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `users`
--

INSERT INTO `users` (`id`, `username`, `full_name`, `email`, `phone`, `password`, `avatar`, `verification_code`, `is_verified`, `created_at`, `bio`, `role`) VALUES
(1, 'yasinozen', 'Yasin ÖZEN', 'yasin@gmail.com', '05555555555', '$2y$10$0R4JpYKimoOE55C352YrJu9CdaMWM6oKi8k/Aci5bdjWubMggiHLW', '6942d02c8ad5c.jpg', NULL, 1, '2025-12-17 15:41:12', 'gurmE', 'manager'),
(2, 'songul', 'Songül ÇETİN', 'songul@gmail.com', '05555555555', '$2y$10$cuJf75hugpzsyrU13OxPGuywiNi3nGogzyRUHvSb4muxb5naPeZQO', '6943126f5e57d.jpg', NULL, 1, '2025-12-17 20:28:31', NULL, 'user');

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `recipe_id` (`recipe_id`);

--
-- Tablo için indeksler `follows`
--
ALTER TABLE `follows`
  ADD PRIMARY KEY (`follower_id`,`following_id`),
  ADD KEY `following_id` (`following_id`);

--
-- Tablo için indeksler `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`user_id`,`recipe_id`),
  ADD KEY `recipe_id` (`recipe_id`);

--
-- Tablo için indeksler `recipes`
--
ALTER TABLE `recipes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Tablo için indeksler `saved_recipes`
--
ALTER TABLE `saved_recipes`
  ADD PRIMARY KEY (`user_id`,`recipe_id`),
  ADD KEY `recipe_id` (`recipe_id`);

--
-- Tablo için indeksler `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tablo için AUTO_INCREMENT değeri `recipes`
--
ALTER TABLE `recipes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Tablo için AUTO_INCREMENT değeri `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `follows`
--
ALTER TABLE `follows`
  ADD CONSTRAINT `follows_ibfk_1` FOREIGN KEY (`follower_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `follows_ibfk_2` FOREIGN KEY (`following_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `recipes`
--
ALTER TABLE `recipes`
  ADD CONSTRAINT `recipes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `saved_recipes`
--
ALTER TABLE `saved_recipes`
  ADD CONSTRAINT `saved_recipes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `saved_recipes_ibfk_2` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
