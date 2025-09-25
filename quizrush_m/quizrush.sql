-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Wrz 09, 2025 at 04:43 AM
-- Wersja serwera: 10.4.32-MariaDB
-- Wersja PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `quizrush`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `answers`
--

CREATE TABLE `answers` (
  `id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `answer_text` text NOT NULL,
  `is_correct` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `answers`
--

INSERT INTO `answers` (`id`, `question_id`, `answer_text`, `is_correct`) VALUES
(1, 1, 'Warszawa', 1),
(2, 1, 'Kraków', 0),
(3, 1, 'Poznań', 0),
(4, 1, 'Wrocław', 0),
(5, 2, '6', 0),
(6, 2, '8', 1),
(7, 2, '10', 0),
(8, 2, '12', 0),
(9, 3, 'Tlen', 1),
(10, 3, 'Złoto', 0),
(11, 3, 'Azot', 0),
(12, 3, 'Hel', 0),
(13, 4, 'Geralt z Rivii', 1),
(14, 4, 'Altair', 0),
(15, 4, 'Kratos', 0),
(16, 4, 'Lara Croft', 0),
(17, 5, 'Queen', 1),
(18, 5, 'The Beatles', 0),
(19, 5, 'ABBA', 0),
(20, 5, 'Metallica', 0),
(21, 6, 'Obi Wan Kenobi', 1),
(22, 6, 'Kit Fisto', 0),
(23, 6, 'Yoda', 0),
(24, 6, 'Darth Vader', 0),
(25, 7, 'Łódź', 1),
(26, 7, 'Wrocław', 0),
(27, 7, 'Katowice', 0),
(28, 7, 'Mielno', 0),
(29, 8, 'Berlin', 1),
(30, 8, 'Monachium', 0),
(31, 8, 'Hamburg', 0),
(32, 8, 'Frankfurt', 0),
(45, 13, '8', 0),
(46, 13, '7', 1),
(47, 13, '10', 0),
(48, 13, '5', 0),
(49, 15, 'Tony', 1),
(50, 15, 'John', 0),
(51, 15, 'Tomas', 0),
(52, 15, 'January', 0),
(53, 16, 'Kit Fisto', 0),
(54, 16, 'Ki Adi Mundi', 0),
(55, 16, 'Obi Wan Kenobi', 1),
(56, 16, 'Yoda', 0);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `best_scores`
--

CREATE TABLE `best_scores` (
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `best_score` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `best_scores`
--

INSERT INTO `best_scores` (`user_id`, `category_id`, `best_score`) VALUES
(2, 3, 98),
(2, 4, 195),
(2, 5, 94),
(4, 1, 286);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(3, 'Geografia'),
(7, 'Harry Potter'),
(2, 'Historia'),
(6, 'Marvel'),
(9, 'Nauka'),
(4, 'Popkultura'),
(10, 'Sport'),
(5, 'Star Wars'),
(8, 'Technologia'),
(1, 'Wiedza ogólna');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `question_text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `quiz_id`, `question_text`) VALUES
(1, 1, 'Stolica Polski to?'),
(2, 1, 'Ile nóg ma pająk?'),
(3, 1, 'Który pierwiastek ma symbol O?'),
(4, 2, 'Kto jest głównym bohaterem gry „Wiedźmin 3”?'),
(5, 2, 'Który zespół śpiewał „Bohemian Rhapsody”?'),
(6, 3, 'Kto był mistrzem Anakina Skywalkera?'),
(7, 4, 'Najpiękniejsze miasto w Polsce'),
(8, 1, 'Które miasto jest stolicą Niemiec?'),
(13, 5, 'Ile było części filmów z serii Harry Potter?'),
(15, 6, 'Jak ma na imie Iron Man'),
(16, 3, 'Kto zabił Dartha Maula');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `quizzes`
--

CREATE TABLE `quizzes` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `category_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `quizzes`
--

INSERT INTO `quizzes` (`id`, `title`, `description`, `category`, `created_by`, `created_at`, `category_id`) VALUES
(1, 'Wiedza ogólna', 'Sprawdź swoją wiedzę z różnych dziedzin!', NULL, 1, '2025-07-01 20:14:37', 1),
(2, 'Popkultura', 'Filmy, gry i muzyka – coś dla fanów rozrywki!', NULL, 1, '2025-07-01 20:14:37', 4),
(3, 'Zgłoszenia', 'Auto-quiz', NULL, 1, '2025-07-01 21:41:23', 5),
(4, 'Zgłoszenia', 'Auto-quiz', NULL, 1, '2025-07-01 22:15:29', 3),
(5, 'Zgłoszenia', 'Auto-quiz', NULL, 1, '2025-07-01 23:11:06', 7),
(6, 'Auto-quiz', 'Quiz utworzony automatycznie dla kategorii', NULL, NULL, '2025-09-08 23:12:33', 6);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `results`
--

CREATE TABLE `results` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `score` int(11) NOT NULL,
  `total` int(11) NOT NULL,
  `completed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `results`
--

INSERT INTO `results` (`id`, `user_id`, `quiz_id`, `score`, `total`, `completed_at`) VALUES
(1, 2, 2, 2, 2, '2025-07-01 20:20:57');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','user','guest') DEFAULT 'user',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `nickname` varchar(50) DEFAULT 'Użytkownik'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `avatar`, `password_hash`, `role`, `is_active`, `created_at`, `nickname`) VALUES
(1, 'admin', 'admin@example.com', NULL, '$2y$10$hE3hKxXfS3/ktwS/1R6f7.x1.zwKJGxV4Cy2Y1rqy/gbFoX7el2kS', 'admin', 1, '2025-07-01 19:22:34', 'Użytkownik'),
(2, 'janogin', 'oginski2@op.pl', '/quizrush/uploads/ava_2_68645306dc311.jpg', '$2y$10$J0EEITRANTurVhC6N7U4aeivVYajj0ZRM4D/UmMcKed0/AxC7N6LS', 'admin', 1, '2025-07-01 19:29:03', 'Oginnez'),
(4, 'oginnez', 'oginski1@gmail.com', '/quizrush/uploads/ava_4_68645deb3af13.jpg', '$2y$10$poEz0QJle.rgyEMwvNUR8.GyKIg3eC1W0GF32F/BsD/boDImtfwEO', 'user', 1, '2025-07-01 20:44:39', 'Użytkownik'),
(5, 'eloelo', 'email@email.com', NULL, '$2y$10$RMGfiQlqHrntPXtk2jp8Ee6WL9O7tybIo3wLgFdehR88gZI/IXVS2', 'user', 1, '2025-07-01 21:18:08', 'Użytkownik'),
(6, 'nowy_admin', 'nowy_admin@example.com', NULL, '$2y$10$abcdefghijklmnopqrstuvCDEFGHIJKLMNOPQRSTUV1234567890abcd', 'admin', 1, '2025-09-08 22:30:24', 'Administrator'),
(8, 'AdamLutka', 'example@example.com', NULL, 'Admin', 'admin', 1, '2025-09-08 22:33:18', 'Administrator'),
(9, 'Adminez', 'email@email.pl', NULL, '$2y$10$AyZ7CtCVUJiOdAiBC/53WO/uxBhD4j/IcUE1HvXitqOelvH7QyEbC', 'admin', 1, '2025-09-08 22:34:41', 'Użytkownik');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `user_submissions`
--

CREATE TABLE `user_submissions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `answer1` varchar(255) NOT NULL,
  `answer2` varchar(255) NOT NULL,
  `answer3` varchar(255) NOT NULL,
  `answer4` varchar(255) NOT NULL,
  `correct_idx` tinyint(4) NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `approved` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_submissions`
--

INSERT INTO `user_submissions` (`id`, `user_id`, `category_id`, `question_text`, `answer1`, `answer2`, `answer3`, `answer4`, `correct_idx`, `submitted_at`, `approved`) VALUES
(1, 2, 5, 'Kto był mistrzem Anakina Skywalkera?', 'Obi Wan Kenobi', 'Kit Fisto', 'Yoda', 'Darth Vader', 1, '2025-07-01 21:41:08', 1),
(2, 4, 3, 'Najpiękniejsze miasto w Polsce', 'Łódź', 'Wrocław', 'Katowice', 'Mielno', 1, '2025-07-01 22:14:58', 1),
(3, 4, 7, 'Ile było części filmów z serii Harry Potter?', '8', '7', '10', '5', 2, '2025-07-01 23:10:35', 1),
(4, 9, 6, 'Jak ma na imie Iron Man', 'Tony', 'John', 'Tomas', 'January', 1, '2025-09-08 23:08:42', 1),
(5, 9, 5, 'Kto zabił Dartha Maula', 'Kit Fisto', 'Ki Adi Mundi', 'Obi Wan Kenobi', 'Yoda', 3, '2025-09-09 02:18:10', 1);

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `answers`
--
ALTER TABLE `answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indeksy dla tabeli `best_scores`
--
ALTER TABLE `best_scores`
  ADD PRIMARY KEY (`user_id`,`category_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indeksy dla tabeli `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indeksy dla tabeli `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quiz_id` (`quiz_id`);

--
-- Indeksy dla tabeli `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `fk_category` (`category_id`);

--
-- Indeksy dla tabeli `results`
--
ALTER TABLE `results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `quiz_id` (`quiz_id`);

--
-- Indeksy dla tabeli `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeksy dla tabeli `user_submissions`
--
ALTER TABLE `user_submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category_id` (`category_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `answers`
--
ALTER TABLE `answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `results`
--
ALTER TABLE `results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `user_submissions`
--
ALTER TABLE `user_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `answers`
--
ALTER TABLE `answers`
  ADD CONSTRAINT `answers_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `best_scores`
--
ALTER TABLE `best_scores`
  ADD CONSTRAINT `best_scores_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `best_scores_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD CONSTRAINT `fk_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `quizzes_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `results`
--
ALTER TABLE `results`
  ADD CONSTRAINT `results_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `results_ibfk_2` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`);

--
-- Constraints for table `user_submissions`
--
ALTER TABLE `user_submissions`
  ADD CONSTRAINT `user_submissions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_submissions_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
