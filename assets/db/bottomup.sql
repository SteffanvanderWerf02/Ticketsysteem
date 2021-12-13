-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Gegenereerd op: 13 dec 2021 om 11:41
-- Serverversie: 10.4.20-MariaDB
-- PHP-versie: 8.0.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bottomup`
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `authentication`
--

CREATE TABLE `authentication` (
  `auth_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Gegevens worden geëxporteerd voor tabel `authentication`
--

INSERT INTO `authentication` (`auth_id`, `name`, `description`) VALUES
(1, 'particulier', 'de particulieren klant'),
(2, 'zakelijk account', 'de zakelijke klant/medewerker'),
(3, 'Beheerder', 'De beheerder van het systeem');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `company`
--

CREATE TABLE `company` (
  `company_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `postalcode` varchar(6) NOT NULL,
  `house_number` int(6) NOT NULL,
  `phone_number` varchar(6) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `kvk` int(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Gegevens worden geëxporteerd voor tabel `company`
--

INSERT INTO `company` (`company_id`, `name`, `postalcode`, `house_number`, `phone_number`, `status`, `kvk`) VALUES
(1, 'Bottom up', '3421TH', 3, '496040', 1, 37112677),
(2, 'Mac Donalds', '9531pg', 1, '324435', 0, 2147483647);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `issue`
--

CREATE TABLE `issue` (
  `issue_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `priority` int(11) NOT NULL,
  `category` varchar(30) NOT NULL,
  `sub_category` varchar(100) NOT NULL,
  `title` varchar(50) NOT NULL,
  `description` varchar(200) NOT NULL,
  `result` varchar(100) NOT NULL,
  `created at` timestamp NULL DEFAULT NULL,
  `closed at` timestamp NULL DEFAULT NULL,
  `frequency` varchar(30) NOT NULL,
  `status_timestamp` timestamp NULL DEFAULT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `issue_message`
--

CREATE TABLE `issue_message` (
  `id` int(11) NOT NULL,
  `issue_id` int(11) NOT NULL,
  `message_id` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `message`
--

CREATE TABLE `message` (
  `message_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `message` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `company_id` int(6) DEFAULT NULL,
  `auth_id` int(5) NOT NULL,
  `name` varchar(65) NOT NULL,
  `postalcode` varchar(6) NOT NULL,
  `house_number` int(8) NOT NULL,
  `phone_number` varchar(6) NOT NULL,
  `email_adres` varchar(100) NOT NULL,
  `hash_password` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `passwordForget_token` varchar(50) DEFAULT NULL,
  `token_expireDate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Gegevens worden geëxporteerd voor tabel `user`
--

INSERT INTO `user` (`user_id`, `company_id`, `auth_id`, `name`, `postalcode`, `house_number`, `phone_number`, `email_adres`, `hash_password`, `status`, `passwordForget_token`, `token_expireDate`) VALUES
(1, 1, 1, 'admin', '4953PG', 23, '394394', 'steffanhenrybart@gmail.com', '$2y$10$MKpK1vS1fX6tAro.zljOpOYSkRkODuqh9pPs56baie.vc5PM.QLxa', 1, '758265bfe56538eca2e93c0212ef5a30', '2021-12-07 01:12:53'),
(2, 2, 1, 'Mac Donalds', '9531pg', 1, '324435', 'Donald@gmail.com', '$2y$10$LXSLDp3sCREnc3Al1zoHxucFokLTwFTyLEKhUpHl3IE3OkhDBgXba', 0, NULL, NULL);

--
-- Indexen voor geëxporteerde tabellen
--

--
-- Indexen voor tabel `authentication`
--
ALTER TABLE `authentication`
  ADD PRIMARY KEY (`auth_id`);

--
-- Indexen voor tabel `company`
--
ALTER TABLE `company`
  ADD PRIMARY KEY (`company_id`);

--
-- Indexen voor tabel `issue`
--
ALTER TABLE `issue`
  ADD PRIMARY KEY (`issue_id`),
  ADD KEY `userIsuess` (`user_id`),
  ADD KEY `CompanyRelation` (`company_id`);

--
-- Indexen voor tabel `issue_message`
--
ALTER TABLE `issue_message`
  ADD PRIMARY KEY (`id`),
  ADD KEY `issue_id` (`issue_id`),
  ADD KEY `message_id` (`message_id`);

--
-- Indexen voor tabel `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`message_id`);

--
-- Indexen voor tabel `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `company_id` (`company_id`);

--
-- AUTO_INCREMENT voor geëxporteerde tabellen
--

--
-- AUTO_INCREMENT voor een tabel `authentication`
--
ALTER TABLE `authentication`
  MODIFY `auth_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT voor een tabel `company`
--
ALTER TABLE `company`
  MODIFY `company_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT voor een tabel `issue`
--
ALTER TABLE `issue`
  MODIFY `issue_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `issue_message`
--
ALTER TABLE `issue_message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `message`
--
ALTER TABLE `message`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Beperkingen voor geëxporteerde tabellen
--

--
-- Beperkingen voor tabel `issue`
--
ALTER TABLE `issue`
  ADD CONSTRAINT `CompanyRelation` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`),
  ADD CONSTRAINT `userIsuess` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
