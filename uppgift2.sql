-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Värd: db
-- Tid vid skapande: 22 maj 2024 kl 09:32
-- Serverversion: 10.6.17-MariaDB-1:10.6.17+maria~ubu2004
-- PHP-version: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databas: `uppgift2`
--

-- --------------------------------------------------------

--
-- Tabellstruktur `newsletters`
--

CREATE TABLE `newsletters` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `owner` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumpning av Data i tabell `newsletters`
--

INSERT INTO `newsletters` (`id`, `title`, `description`, `owner`) VALUES
(29, 'ribbans tidning', 'ribbans fantastiska tidning!!!!!!', 45),
(30, 'bosses', 'allt om bosse', 46),
(31, 'tidning om skor', 'handlar om skor', 49),
(32, 'mat tidningen', 'handlar om mat', 50),
(34, 'vatten är gott', 'vatten', 56);

-- --------------------------------------------------------

--
-- Tabellstruktur `resetPassword`
--

CREATE TABLE `resetPassword` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `code` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumpning av Data i tabell `resetPassword`
--

INSERT INTO `resetPassword` (`id`, `user_id`, `code`, `email`) VALUES
(7, 44, 'igfFlU', 'sanna.s-96@hotmail.com'),
(8, 44, 'mhaCIk', 'sanna.s-96@hotmail.com'),
(9, 44, 'VbJIle', 'sanna.s-96@hotmail.com'),
(10, 44, 'uVE2HV', 'sanna.s-96@hotmail.com'),
(11, 44, 'GlgDtS', 'sanna.s-96@hotmail.com'),
(12, 44, 'UUXHpo', 'sanna.s-96@hotmail.com'),
(13, 44, 'eOjr3Z', 'sanna.s-96@hotmail.com'),
(14, 44, '75CrhT', 'sanna.s-96@hotmail.com');

-- --------------------------------------------------------

--
-- Tabellstruktur `subscriptions`
--

CREATE TABLE `subscriptions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `newsletter_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumpning av Data i tabell `subscriptions`
--

INSERT INTO `subscriptions` (`id`, `user_id`, `newsletter_id`) VALUES
(52, 44, 31),
(61, 52, 29),
(62, 52, 31),
(71, 53, 29),
(72, 53, 31),
(74, 44, 29),
(75, 44, 30);

-- --------------------------------------------------------

--
-- Tabellstruktur `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `firstName` varchar(30) NOT NULL,
  `lastName` varchar(30) NOT NULL,
  `role` enum('customer','subscriber') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumpning av Data i tabell `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `firstName`, `lastName`, `role`) VALUES
(44, 'sanna.s-96@hotmail.com', '$2y$10$8hmnB3DJeGCEOB3PMZNo0./i8NNKG8XdG/lv3UXjrAk1Tc5jfLmwW', 'Sanna', 'Siljebäck', 'subscriber'),
(45, 'ribban@ribb.se', '$2y$10$oiy57aisKExabCejYdlrpuoWU9HppPKdiVxKBimYA9/XVUMhwQV8O', 'ribban', 'ribbsson', 'customer'),
(46, 'bosse@bosse.se', '$2y$10$NABo1gmY9DLm1buZ9FCkTekycs./ssn20xAQrM2HW8J9DowCL/tYG', 'bosse', 'bus', 'customer'),
(47, 'nur@nur.se', '$2y$10$x/Jw7yIxMqTyq6Sc1qeX7OPvbnQPZ0r4Ys/BRgRSlKqgonJk8CnIO', 'nur', 'nursson', 'subscriber'),
(48, 'katt@katt.se', '$2y$10$jpn6.UOUkX27A/8RL1xSneaNsc7iAGwWkDvQhUI5Lq0FgiAcWZgRa', 'katt', 'kattsson', 'subscriber'),
(49, 'skor@skor.se', '$2y$10$ano/0GVhgp1iDCG.nUg20OlTE4qcO1r451H90b9fofV7cTEFh0QYW', 'skor', 'skorsson', 'customer'),
(50, 'mat@mat.se', '$2y$10$ID4i3oVZMGT2qY3jxEwJIuz5Iw5WNNEh6s7S8vOfuWzj/reOx9MhS', 'mat', 'matsson', 'customer'),
(51, 'fanta@fanta.se', '$2y$10$O3XmC7ele/HE8/7PGgjkCuRtOBcdt9OclJezTD.ziN2i7mj1w42la', 'fanta', 'fanta', 'subscriber'),
(52, 'albin@albin.se', '$2y$10$Y2cNGEE8jC2PXtbY/5SXXeGSZzMFzgg0ZVTAwMBNzgpEw0EggweS6', 'albin', 'albin', 'subscriber'),
(53, 'jess@jess.se', '$2y$10$OZ1r7HqnxkTFOQwlZnoIz.1h1XVoCTlXe734ANYr5xZ.k2t91slFu', 'jess', 'jess', 'subscriber'),
(56, 'vatten@vatten.se', '$2y$10$o8ld1nMN/xPSAbtmv5H8huqFna9NlHtZv5FSLb5VFHGqsaqTDmEne', 'vatten', 'vatten', 'customer');

--
-- Index för dumpade tabeller
--

--
-- Index för tabell `newsletters`
--
ALTER TABLE `newsletters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_owner` (`owner`);

--
-- Index för tabell `resetPassword`
--
ALTER TABLE `resetPassword`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index för tabell `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `newsletter_id` (`newsletter_id`);

--
-- Index för tabell `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT för dumpade tabeller
--

--
-- AUTO_INCREMENT för tabell `newsletters`
--
ALTER TABLE `newsletters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT för tabell `resetPassword`
--
ALTER TABLE `resetPassword`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT för tabell `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT för tabell `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- Restriktioner för dumpade tabeller
--

--
-- Restriktioner för tabell `newsletters`
--
ALTER TABLE `newsletters`
  ADD CONSTRAINT `fk_owner` FOREIGN KEY (`owner`) REFERENCES `users` (`id`);

--
-- Restriktioner för tabell `resetPassword`
--
ALTER TABLE `resetPassword`
  ADD CONSTRAINT `resetPassword_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Restriktioner för tabell `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD CONSTRAINT `subscriptions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `subscriptions_ibfk_2` FOREIGN KEY (`newsletter_id`) REFERENCES `newsletters` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
