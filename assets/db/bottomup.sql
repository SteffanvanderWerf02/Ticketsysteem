-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Gegenereerd op: 13 jan 2022 om 14:08
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
(3, 'Beheerder', 'De beheerder van het systeem'),
(4, 'Issuemaster', 'De issue beheerder van het systeem');

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
(4, 'Nhl Stenden', 'test@gmail.com', 'Emmen', 'Pindakaasstraat', '3421TH', 33, '49604059', 0, 2147483647),
(5, 'Test bedrijf', 'Test2343@gmail.com', 'Borger', 'Deksteen', '9531PG', 1, '0611775675', 0, 232134234);

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
(5, 10, NULL, 1, 'Dienst/service', 'Vijvers', 'Vijver aanleggen', 'Ik wil graag een vijver in mijn tuin.', 'De vijver grote moet ongeveer 200 m3 zijn&#13;&#10;&#13;&#10;.is dit voor jullie mogelijk?', '2022-01-13', NULL, 'N.V.T', NULL, '2022-01-13 12:45:40', 1, 2),
(7, 10, NULL, 1, 'Dienst/service', 'Vijvers', 'Volière in tuin', 'ik wil een volière in mijn tuin laten bouwen', 'Ik wil graag een volière waar 2 pagaaien in kunnen', '2022-01-13', '2022-01-13', 'N.V.T', '../assets/issueFiles/7/IMG_2316-min.JPEG', '2022-01-13 13:01:30', 4, 2),
(8, 10, NULL, 1, 'Ticket', 'Feedback', 'Klacht personeel', 'Het geleverde werk was super goed. maar volgende keer wel graag de schoenen uit in het huis.', 'Geen vieze schoenen in het huis', '2022-01-13', NULL, 'N.V.T', NULL, '2022-01-13 12:48:21', 1, NULL),
(9, 10, NULL, 1, 'Product', 'Planten', 'Heggen', 'ik zou graag 4 heggen willen die ik niet veel hoef te snoeien', '4 volwassen heggen', '2022-01-13', NULL, 'N.V.T', NULL, '2022-01-13 12:49:54', 1, NULL);

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

--
-- Gegevens worden geëxporteerd voor tabel `issue_message`
--

INSERT INTO `issue_message` (`id`, `issue_id`, `message_id`, `date`) VALUES
(47, 5, 47, '2022-01-13 12:45:40'),
(48, 5, 48, '2022-01-13 12:45:40'),
(49, 7, 49, '2022-01-13 12:51:44'),
(50, 7, 50, '2022-01-13 12:51:47'),
(51, 7, 51, '2022-01-13 12:53:46'),
(52, 7, 52, '2022-01-13 12:54:50'),
(53, 7, 53, '2022-01-13 12:54:50'),
(54, 7, 54, '2022-01-13 12:57:04'),
(55, 7, 55, '2022-01-13 12:57:06'),
(56, 7, 56, '2022-01-13 13:00:59'),
(57, 7, 57, '2022-01-13 13:00:59'),
(58, 7, 58, '2022-01-13 13:01:27');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `message`
--

CREATE TABLE `message` (
  `message_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `message` varchar(528) NOT NULL,
  `appendex_url` varchar(128) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Gegevens worden geëxporteerd voor tabel `message`
--

INSERT INTO `message` (`message_id`, `user_id`, `date`, `message`, `appendex_url`) VALUES
(47, 10, '2022-01-13', 'Ik wil graag zo&#039;n soort vijver als het kan', '../assets/issueFiles/5/download.png'),
(48, 10, '2022-01-13', 'De actie ligt bij: Bottom Up', NULL),
(49, 1, '2022-01-13', 'Hallo Henk,\n\nWij zouden graag op 15-01-2022 de volière willen plaatsen komt dit uit voor u?\n\nmet vriendelijk groet,\n\nBart Hemming\nBottom Up', NULL),
(50, 1, '2022-01-13', 'De actie ligt bij: De klant', NULL),
(51, 1, '2022-01-13', 'De status van uw issue is: In behandeling', NULL),
(52, 10, '2022-01-13', 'Hallo Bart,\r\n\r\nDit komt ons zeker goed uit :)!\r\n\r\nTot snel!\r\nMet vriendelijke groet,\r\n\r\nHenk', NULL),
(53, 10, '2022-01-13', 'De actie ligt bij: Bottom Up', NULL),
(54, 1, '2022-01-13', 'Hallo Henk,\r\n\r\nIk hoop dat u tevreden bent met u nieuwe voli&egrave;re.\r\nWij sluiten deze issue indien u tevreden bent met u resultaat.\r\n\r\nMet vriendelijk groet,\r\n\r\nBart Hemming\r\nBottom Up', NULL),
(55, 1, '2022-01-13', 'De actie ligt bij: De klant', NULL),
(56, 10, '2022-01-13', 'Hallo,\r\n\r\nJazeker erg tevreden met jullie werk\r\n\r\nErg bedankt fijn zaken doen!\r\nDe volgens zitten fijn op hun nieuwe plek\r\n\r\nmet vriendelijke groet,\r\n\r\nHenk', '../assets/issueFiles/7/vogel-voliere.jpg'),
(57, 10, '2022-01-13', 'De actie ligt bij: Bottom Up', NULL),
(58, 1, '2022-01-13', 'De status van uw issue is: Gesloten', NULL);

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
(1, 1, 3, '../assets/img/pfpic/1/20986040.jpg', 'Beheer', '4953PG', 'Emmen', 'Steenstraat', 23, '394394', 'Steffan.van.der.werf@student.nhlstenden.com', '$2y$10$mjHDMve0IZArJEIDurvNtOLN16iRdStIOBHl8yvdomJ2olGtxXUxS', 1, NULL, NULL),
(4, 1, 4, NULL, 'Issuebeheer', '6574AR', 'Amsterdam', 'straatingen', 98, '0623241210', 'Steffan.van.der.werf@student.nhlstenden.com', '$2y$10$mjHDMve0IZArJEIDurvNtOLN16iRdStIOBHl8yvdomJ2olGtxXUxS', 1, NULL, NULL),
(10, NULL, 0, NULL, 'Henk', '9394PE', 'Emmen', 'Wilaministraat', 112, '394349', 'Steffanhenrybart@gmail.com', '$2y$10$.LkHAPxs3eINDnYNafbwfet6Ts4yRZ1HHF3ViD4yPY6f2Cz9WV9Rm', 1, NULL, NULL),
(11, 4, 2, NULL, 'Nhl Stenden', '3421TH', 'Emmen', 'Pindakaasstraat', 33, '49604059', 'test@gmail.com', '$2y$10$SH7itHprzSP.xMQm9fpD1O6xz66KXDUzjYe4t7OajPl1u5Phz/PcK', 0, NULL, NULL),
(12, 5, 2, NULL, 'Test bedrijf', '9531PG', 'Borger', 'Deksteen', 1, '0611775675', 'Test2343@gmail.com', '$2y$10$O.zmr5ypSJxMd6Fmahf7/ew7yj1lxfOoBPwRmYa7vjyp72PcL5yDW', 0, NULL, NULL);

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
  MODIFY `company_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT voor een tabel `issue`
--
ALTER TABLE `issue`
  MODIFY `issue_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT voor een tabel `issue_message`
--
ALTER TABLE `issue_message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT voor een tabel `message`
--
ALTER TABLE `message`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT voor een tabel `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

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
