SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE `organizations` (
  `ID` int(3) NOT NULL,
  `TypeID` int(2) DEFAULT NULL,
  `Name` varchar(250) DEFAULT NULL,
  `City` varchar(24) DEFAULT NULL,
  `State` varchar(30) DEFAULT NULL,
  `Country` varchar(30) DEFAULT NULL,
  `AliasOf` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `proposal` (
  `ID` int(4) NOT NULL,
  `OrgID` int(3) DEFAULT NULL,
  `OrgOptID` int(3) DEFAULT NULL,
  `Title` varchar(255) DEFAULT NULL,
  `Description` text,
  `Catchword` varchar(255) DEFAULT NULL,
  `Faculty` varchar(114) DEFAULT NULL,
  `Section` varchar(255) DEFAULT NULL,
  `LID` int(3) DEFAULT NULL,
  `SID` int(3) DEFAULT NULL,
  `SSID` int(1) DEFAULT NULL,
  `Current` varchar(255) DEFAULT NULL,
  `Raw` int(1) DEFAULT NULL,
  `Ass` int(1) DEFAULT NULL,
  `W1` int(1) DEFAULT NULL,
  `W2` int(1) DEFAULT NULL,
  `W3` int(1) DEFAULT NULL,
  `C1` int(1) DEFAULT NULL,
  `C2` int(1) DEFAULT NULL,
  `C3` int(1) DEFAULT NULL,
  `C4` int(4) DEFAULT NULL,
  `Found` int(1) DEFAULT NULL,
  `Tenure` int(1) DEFAULT NULL,
  `Note` varchar(255) DEFAULT NULL,
  `Enddate` date DEFAULT NULL,
  `ASAP` int(1) DEFAULT NULL,
  `Publisher1` varchar(100) DEFAULT NULL,
  `Pdate1` date DEFAULT NULL,
  `Pissue1` int(4) DEFAULT NULL,
  `Pyear1` int(4) DEFAULT NULL,
  `Publisher2` varchar(100) DEFAULT NULL,
  `Pdate2` date DEFAULT NULL,
  `Pissue2` int(4) DEFAULT NULL,
  `Pyear2` int(4) DEFAULT NULL,
  `Publisher3` varchar(100) DEFAULT NULL,
  `Pdate3` date DEFAULT NULL,
  `Pissue3` int(4) DEFAULT NULL,
  `Pyear3` int(4) DEFAULT NULL,
  `Publisher4` varchar(100) DEFAULT NULL,
  `Pdate4` date DEFAULT NULL,
  `Pissue4` int(4) DEFAULT NULL,
  `Pyear4` int(4) DEFAULT NULL,
  `Link` varchar(300) DEFAULT NULL,
  `SaveTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `subject_culture` int(3) DEFAULT NULL,
  `subject_area` int(3) DEFAULT NULL,
  `subject` int(3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `subject` (
  `ID` int(3) NOT NULL,
  `Name` varchar(200) NOT NULL,
  `ParentID` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `subject_area` (
  `ID` int(3) NOT NULL,
  `Name` varchar(200) NOT NULL,
  `ParentID` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `subject_culture` (
  `ID` int(3) NOT NULL,
  `Name` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `systematics` (
  `ID` int(3) DEFAULT NULL,
  `L1` int(1) DEFAULT NULL,
  `L2` int(1) DEFAULT NULL,
  `L3` int(1) DEFAULT NULL,
  `LID` int(3) DEFAULT NULL,
  `SID` int(3) DEFAULT NULL,
  `SSID` varchar(2) DEFAULT NULL,
  `Name` varchar(133) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `organizations`
  ADD UNIQUE KEY `ID` (`ID`);

ALTER TABLE `proposal`
  ADD UNIQUE KEY `ID` (`ID`);

ALTER TABLE `subject`
  ADD PRIMARY KEY (`ID`);

ALTER TABLE `subject_area`
  ADD PRIMARY KEY (`ID`);

ALTER TABLE `subject_culture`
  ADD PRIMARY KEY (`ID`);

ALTER TABLE `systematics`
  ADD UNIQUE KEY `ID` (`ID`);


ALTER TABLE `organizations`
  MODIFY `ID` int(3) NOT NULL AUTO_INCREMENT;
ALTER TABLE `proposal`
  MODIFY `ID` int(4) NOT NULL AUTO_INCREMENT;
ALTER TABLE `subject`
  MODIFY `ID` int(3) NOT NULL AUTO_INCREMENT;
ALTER TABLE `subject_area`
  MODIFY `ID` int(3) NOT NULL AUTO_INCREMENT;
ALTER TABLE `subject_culture`
  MODIFY `ID` int(3) NOT NULL AUTO_INCREMENT;

CREATE TABLE `keywords` (
  `ID` int(4) NOT NULL,
  `TypeID` int(2) NOT NULL,
  `Keyword` varchar(100) DEFAULT NULL,
  `Strict` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `keywords` (`ID`, `TypeID`, `Keyword`, `Strict`) VALUES
(1, 1, 'universität', 1),
(2, 1, 'university', 1),
(3, 2, 'hochschule', 1),
(4, 2, 'fachhochschule', 1),
(5, 2, 'FH', 1),
(6, 3, 'universitätsmedizin', 1),
(7, 3, 'universitätsklinik', 1),
(8, 3, 'universitätsklinikum', 1),
(9, 4, 'pädagogische hochschule', 1),
(10, 4, 'PH', 1),
(11, 1, 'technische unvisersität', 1),
(12, 1, 'TU', 1),
(13, 6, 'akademie', 0),
(14, 7, 'forschung', 0);

CREATE TABLE `types` (
  `ID` int(2) NOT NULL,
  `Bezeichnung` varchar(42) DEFAULT NULL,
  `Abbrev` varchar(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `types` (`ID`, `Bezeichnung`, `Abbrev`) VALUES
(1, 'Universität', 'U'),
(2, 'FH / Hochschule', 'FH'),
(3, 'Klinik', 'UK'),
(4, 'Pädagogische Hochschule', 'PH'),
(5, 'TU', 'TU'),
(6, 'Akademie', 'AK'),
(7, 'Forschung', 'FZ');


ALTER TABLE `keywords`
  ADD UNIQUE KEY `ID` (`ID`);

ALTER TABLE `types`
  ADD UNIQUE KEY `ID` (`ID`);


ALTER TABLE `keywords`
  MODIFY `ID` int(4) NOT NULL AUTO_INCREMENT;
ALTER TABLE `types`
  MODIFY `ID` int(2) NOT NULL AUTO_INCREMENT;