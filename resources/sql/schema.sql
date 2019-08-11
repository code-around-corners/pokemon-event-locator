SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

DROP TABLE IF EXISTS `events`;
CREATE TABLE `events` (
  `tournamentID` int(10) UNSIGNED NOT NULL,
  `category` varchar(64) NOT NULL,
  `date` date NOT NULL,
  `product` varchar(64) NOT NULL,
  `premierEvent` varchar(128) NOT NULL,
  `premierGroup` varchar(128) NOT NULL,
  `countryName` varchar(64) NOT NULL,
  `provinceState` varchar(64) NOT NULL,
  `postalZipCode` varchar(20) NOT NULL,
  `eventJson` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `lastUpdated` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `periods`;
CREATE TABLE `periods` (
  `id` int(11) NOT NULL,
  `seasonId` int(11) NOT NULL,
  `periodName` varchar(200) NOT NULL,
  `startDate` date NOT NULL,
  `endDate` date NOT NULL,
  `premierGroups` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `products` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `isTravelAward` tinyint(1) NOT NULL,
  `isFormatPeriod` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `seasons`;
CREATE TABLE `seasons` (
  `id` int(11) NOT NULL,
  `season` int(11) NOT NULL,
  `startDate` date NOT NULL,
  `endDate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `timezones`;
CREATE TABLE `timezones` (
  `timezone` varchar(64) NOT NULL,
  `vTimezone` longtext NOT NULL,
  `lastUpdated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


ALTER TABLE `events`
  ADD PRIMARY KEY (`tournamentID`);

ALTER TABLE `periods`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `seasons`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `timezones`
  ADD PRIMARY KEY (`timezone`);


ALTER TABLE `periods`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `seasons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;
