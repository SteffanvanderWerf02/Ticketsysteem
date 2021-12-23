-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Gegenereerd op: 23 dec 2021 om 12:11
-- Serverversie: 10.3.16-MariaDB
-- PHP-versie: 7.3.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
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
(3, 'Beheerder', 'De beheerder van het systeem'),
(4, 'Ticketmaster', 'De ticket beheerder van het systeem');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `company`
--

CREATE TABLE `company` (
  `company_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email_adres` varchar(64) NOT NULL,
  `city` varchar(50) NOT NULL,
  `streetname` varchar(50) NOT NULL,
  `postalcode` varchar(6) NOT NULL,
  `house_number` int(6) NOT NULL,
  `phone_number` varchar(12) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `kvk` int(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Gegevens worden geëxporteerd voor tabel `company`
--

INSERT INTO `company` (`company_id`, `name`, `email_adres`, `city`, `streetname`, `postalcode`, `house_number`, `phone_number`, `status`, `kvk`) VALUES
(1, 'Bottom up', 'Steffan.van.der.werf@student.nhlstenden.com', 'Emmen', 'Steenstraat', '3421TH', 3, '496040', 1, 37112677),
(2, 'Mac Donalds', 'test@gmail.com', 'Gieten', 'Gietenstraat', '9531pg', 1, '324435', 0, 2147483647),
(3, 'FIA', 'Fia@gmail.com', 'London', 'Kingstreet', '5843TG', 343, '394439', 0, 2147483647);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `issue`
--

CREATE TABLE `issue` (
  `issue_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `priority` int(3) NOT NULL,
  `category` varchar(255) NOT NULL,
  `sub_category` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `result` varchar(255) NOT NULL,
  `created_at` date NOT NULL,
  `closed_at` date DEFAULT NULL,
  `frequency` varchar(50) DEFAULT NULL,
  `appendex_url` varchar(128) DEFAULT NULL,
  `status_timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` int(10) NOT NULL,
  `issue_action` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Gegevens worden geëxporteerd voor tabel `issue`
--

INSERT INTO `issue` (`issue_id`, `user_id`, `company_id`, `priority`, `category`, `sub_category`, `title`, `description`, `result`, `created_at`, `closed_at`, `frequency`, `appendex_url`, `status_timestamp`, `status`, `issue_action`) VALUES
(1, 1, 1, 1, 'Ticket', 'Klachten', 'Mijn schep is kapot', 'Ik ging 1 keer scheppen en toen ging die kapot', 'Een nieuwe schep', '2021-12-23', NULL, 'N.V.T', NULL, '2021-12-23 10:51:21', 1, NULL),
(2, 1, 1, 1, 'Dienst/service', 'Vijvers', 'Nieuw vijver', 'Ik wil graag een nieuwe vijver voor in mijn tuin', 'Een nieuwe vijver', '2021-12-23', NULL, 'Dagelijks', NULL, '2021-12-23 10:51:53', 1, NULL),
(3, 1, 1, 1, 'Product', 'Gereedschap opslag', 'Boormachine', 'Ik wil een nieuwe boormachine', 'nieuwe boormachine', '2021-12-23', NULL, 'N.V.T', NULL, '2021-12-23 10:52:17', 1, NULL);

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
  `message` varchar(100) NOT NULL,
  `appendex_url` varchar(128) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `company_id` int(6) DEFAULT NULL,
  `auth_id` int(5) NOT NULL,
  `profilepicture` varchar(250) DEFAULT NULL,
  `name` varchar(65) NOT NULL,
  `postalcode` varchar(6) NOT NULL,
  `city` varchar(50) NOT NULL,
  `streetname` varchar(60) NOT NULL,
  `house_number` int(8) NOT NULL,
  `phone_number` varchar(12) NOT NULL,
  `email_adres` varchar(100) NOT NULL,
  `hash_password` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `passwordForget_token` varchar(50) DEFAULT NULL,
  `token_expireDate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Gegevens worden geëxporteerd voor tabel `user`
--

INSERT INTO `user` (`user_id`, `company_id`, `auth_id`, `profilepicture`, `name`, `postalcode`, `city`, `streetname`, `house_number`, `phone_number`, `email_adres`, `hash_password`, `status`, `passwordForget_token`, `token_expireDate`) VALUES
(1, 1, 3, NULL, 'admin', '4953PG', 'Emmen', 'Steenstraat', 23, '394394', 'steffanhenrybart@gmail.com', '$2y$10$TvYzf.Zi96CKrO2wt1FyUO42lx8TH0SkBU.ga039chPuZNYuTgCI.', 1, NULL, NULL),
(2, 2, 1, NULL, 'Mac Donalds', '9531pg', 'Borger', 'Deksteen', 1, '324435', 'Donald@gmail.com', '$2y$10$LXSLDp3sCREnc3Al1zoHxucFokLTwFTyLEKhUpHl3IE3OkhDBgXba', 0, NULL, NULL),
(3, 3, 2, NULL, 'fia', '3939PG', 'London', 'Kingstreet', 343, '934939', 'Fia@gmail.com', '$2y$10$7vrIGvKXUS.YbKFUgFP6HOjJlBq4RANJblBbZ9gRXGG2R6yX/K3Ui', 0, NULL, NULL),
(4, NULL, 0, NULL, 'Andr&eacute;', '9531PG', 'Borger', 'Deksteen', 1, '0611775675', 'Test@gmail.com', '$2y$10$kZHavAjnf.OkwwSUlGMYfuHZVYXWmfcLkWERo2NUtQgPdat3A/Oo2', 1, NULL, NULL),
(9, 1, 4, NULL, 'Ticketbeheer', '6574AR', 'Amsterdam', 'straatingen', 98, '0623241210', 'koertvanhaan@gmail.com', '$2y$10$vR89oCj3k8/JncKXPTgVuO3sRxfmj6iPW02pUtebAwkmisx2mrfrq', 1, NULL, NULL);

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
  ADD KEY `company_id` (`company_id`),
  ADD KEY `user_id` (`user_id`);

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
  MODIFY `auth_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT voor een tabel `company`
--
ALTER TABLE `company`
  MODIFY `company_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT voor een tabel `issue`
--
ALTER TABLE `issue`
  MODIFY `issue_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT voor een tabel `issue_message`
--
ALTER TABLE `issue_message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT voor een tabel `message`
--
ALTER TABLE `message`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT voor een tabel `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Beperkingen voor geëxporteerde tabellen
--

--
-- Beperkingen voor tabel `issue`
--
ALTER TABLE `issue`
  ADD CONSTRAINT `CompanyRelaties` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`),
  ADD CONSTRAINT `UserissueRelatie` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Beperkingen voor tabel `issue_message`
--
ALTER TABLE `issue_message`
  ADD CONSTRAINT `issuesRelatie` FOREIGN KEY (`issue_id`) REFERENCES `issue` (`issue_id`),
  ADD CONSTRAINT `messageRelatie` FOREIGN KEY (`message_id`) REFERENCES `message` (`message_id`);

--
-- Beperkingen voor tabel `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `CompanyRelatie` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
