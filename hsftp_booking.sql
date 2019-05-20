-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:2286
-- Erstellungszeit: 19. Mai 2019 um 16:47
-- Server-Version: 10.3.15-MariaDB
-- PHP-Version: 7.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `hsftp_booking`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `bild`
--

CREATE TABLE `bild` (
  `bild_id` int(11) NOT NULL,
  `wohn_id` int(11) NOT NULL,
  `alt` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `bild` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `chat`
--

CREATE TABLE `chat` (
  `vm_id` int(11) NOT NULL,
  `m_id` int(11) NOT NULL,
  `cnachricht` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `favorit`
--

CREATE TABLE `favorit` (
  `m_id` int(11) NOT NULL,
  `wohn_id` int(11) NOT NULL,
  `fdate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `livestream`
--

CREATE TABLE `livestream` (
  `ls_id` int(11) NOT NULL,
  `wohn_id` int(11) NOT NULL,
  `vid_url` text COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mieter`
--

CREATE TABLE `mieter` (
  `m_id` int(11) NOT NULL,
  `anrede` enum('Frau','Herr') COLLATE utf8mb4_unicode_ci NOT NULL,
  `vname` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nname` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pwort` char(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `profil` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `vermieter`
--

CREATE TABLE `vermieter` (
  `vm_id` int(11) NOT NULL,
  `anrede` enum('Frau','Herr') CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `vname` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nname` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pwort` char(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tel_nr` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mob_nr` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `profil` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `video`
--

CREATE TABLE `video` (
  `video_id` int(11) NOT NULL,
  `wohn_id` int(11) NOT NULL,
  `video` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `wohnung`
--

CREATE TABLE `wohnung` (
  `wohn_id` int(11) NOT NULL,
  `vm_id` int(11) NOT NULL,
  `name` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `beschr` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `preis` float NOT NULL,
  `plz` int(11) NOT NULL,
  `ort` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entf_meter` float NOT NULL,
  `entf_min` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `bild`
--
ALTER TABLE `bild`
  ADD PRIMARY KEY (`bild_id`),
  ADD KEY `wohn_id` (`wohn_id`);

--
-- Indizes für die Tabelle `chat`
--
ALTER TABLE `chat`
  ADD KEY `vm_id` (`vm_id`),
  ADD KEY `m_id` (`m_id`);

--
-- Indizes für die Tabelle `favorit`
--
ALTER TABLE `favorit`
  ADD PRIMARY KEY (`m_id`,`wohn_id`),
  ADD KEY `wohn_id` (`wohn_id`);

--
-- Indizes für die Tabelle `livestream`
--
ALTER TABLE `livestream`
  ADD PRIMARY KEY (`ls_id`),
  ADD KEY `wohn_id` (`wohn_id`);

--
-- Indizes für die Tabelle `mieter`
--
ALTER TABLE `mieter`
  ADD PRIMARY KEY (`m_id`);

--
-- Indizes für die Tabelle `vermieter`
--
ALTER TABLE `vermieter`
  ADD PRIMARY KEY (`vm_id`);

--
-- Indizes für die Tabelle `video`
--
ALTER TABLE `video`
  ADD PRIMARY KEY (`video_id`),
  ADD KEY `wohn_id` (`wohn_id`);

--
-- Indizes für die Tabelle `wohnung`
--
ALTER TABLE `wohnung`
  ADD PRIMARY KEY (`wohn_id`),
  ADD KEY `vm_id` (`vm_id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `bild`
--
ALTER TABLE `bild`
  MODIFY `bild_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `livestream`
--
ALTER TABLE `livestream`
  MODIFY `ls_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `mieter`
--
ALTER TABLE `mieter`
  MODIFY `m_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `vermieter`
--
ALTER TABLE `vermieter`
  MODIFY `vm_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `video`
--
ALTER TABLE `video`
  MODIFY `video_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `wohnung`
--
ALTER TABLE `wohnung`
  MODIFY `wohn_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `bild`
--
ALTER TABLE `bild`
  ADD CONSTRAINT `bild_ibfk_1` FOREIGN KEY (`wohn_id`) REFERENCES `wohnung` (`wohn_id`);

--
-- Constraints der Tabelle `chat`
--
ALTER TABLE `chat`
  ADD CONSTRAINT `chat_ibfk_1` FOREIGN KEY (`vm_id`) REFERENCES `vermieter` (`vm_id`),
  ADD CONSTRAINT `chat_ibfk_2` FOREIGN KEY (`m_id`) REFERENCES `mieter` (`m_id`);

--
-- Constraints der Tabelle `favorit`
--
ALTER TABLE `favorit`
  ADD CONSTRAINT `favorit_ibfk_2` FOREIGN KEY (`m_id`) REFERENCES `mieter` (`m_id`),
  ADD CONSTRAINT `favorit_ibfk_3` FOREIGN KEY (`wohn_id`) REFERENCES `wohnung` (`wohn_id`);

--
-- Constraints der Tabelle `livestream`
--
ALTER TABLE `livestream`
  ADD CONSTRAINT `livestream_ibfk_1` FOREIGN KEY (`wohn_id`) REFERENCES `wohnung` (`wohn_id`);

--
-- Constraints der Tabelle `video`
--
ALTER TABLE `video`
  ADD CONSTRAINT `video_ibfk_1` FOREIGN KEY (`wohn_id`) REFERENCES `wohnung` (`wohn_id`);

--
-- Constraints der Tabelle `wohnung`
--
ALTER TABLE `wohnung`
  ADD CONSTRAINT `wohnung_ibfk_1` FOREIGN KEY (`vm_id`) REFERENCES `vermieter` (`vm_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


ALTER TABLE `chat`
CHANGE `cnachricht` `cnachricht` varchar(3070) COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `m_id`,
CHANGE `date` `date` datetime NOT NULL AFTER `cnachricht`;

ALTER TABLE `bild`
CHANGE `alt` `alt` varchar(127) COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `wohn_id`,
CHANGE `bild` `bild` varchar(127) COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `alt`;

ALTER TABLE `favorit`
CHANGE `fdate` `fdate` datetime NOT NULL AFTER `wohn_id`;

ALTER TABLE `livestream`
CHANGE `vid_url` `vid_url` varchar(511) COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `wohn_id`;

ALTER TABLE `mieter`
CHANGE `vname` `vname` varchar(31) COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `anrede`,
CHANGE `nname` `nname` varchar(31) COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `vname`,
CHANGE `email` `email` varchar(31) COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `nname`,
CHANGE `pwort` `pwort` varbinary(20) NOT NULL AFTER `email`,
CHANGE `profil` `profil` varchar(127) COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `pwort`;

ALTER TABLE `vermieter`
CHANGE `vname` `vname` varchar(31) COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `anrede`,
CHANGE `nname` `nname` varchar(31) COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `vname`,
CHANGE `email` `email` varchar(31) COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `nname`,
CHANGE `pwort` `pwort` char(20) COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `email`,
CHANGE `tel_nr` `tel_nr` varchar(31) COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `pwort`,
CHANGE `mob_nr` `mob_nr` varchar(31) COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `tel_nr`,
CHANGE `profil` `profil` varchar(127) COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `mob_nr`;


ALTER TABLE `video`
CHANGE `video` `video` varchar(255) COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `wohn_id`

ALTER TABLE `wohnung`
CHANGE `name` `name` varchar(47) COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `vm_id`,
CHANGE `beschr` `beschr` varchar(3070) COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `name`,
CHANGE `plz` `plz` varchar(15) NOT NULL AFTER `beschr`,
CHANGE `ort` `ort` varchar(47) COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `plz`,
CHANGE `preis` `preis` float NOT NULL AFTER `ort`;

ALTER TABLE `wohnung`
CHANGE `preis` `preis` decimal(6,2) NOT NULL AFTER `ort`,
CHANGE `entf_meter` `entf_meter` int NOT NULL AFTER `preis`,
CHANGE `entf_min` `entf_min` int NOT NULL AFTER `entf_meter`;

ALTER TABLE `wohnung`
ADD INDEX `preis` (`preis`),
ADD INDEX `entf_meter` (`entf_meter`),
ADD INDEX `entf_min` (`entf_min`);
