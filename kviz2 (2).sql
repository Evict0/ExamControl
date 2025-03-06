-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 06, 2025 at 07:05 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kviz2`
--

-- --------------------------------------------------------

--
-- Table structure for table `ep_korisnik`
--

CREATE TABLE `ep_korisnik` (
  `ID` int(11) NOT NULL,
  `ime` varchar(100) NOT NULL,
  `lozinka` varchar(32) NOT NULL,
  `razinaID` int(11) NOT NULL,
  `aktivan` tinyint(1) NOT NULL DEFAULT 1,
  `email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ep_korisnik`
--

INSERT INTO `ep_korisnik` (`ID`, `ime`, `lozinka`, `razinaID`, `aktivan`, `email`) VALUES
(1, 'Profesor', '70cf5c0095d91b8f2b9798700651df25', 1, 1, 'profesor@example.com'),
(2, 'Ucenik1', '118c91b1488301c9bbbb7bab7b8e3cf6', 2, 1, 'ucenik1@example.com'),
(4, 'Martin', 'dd178ef9a3803ae2273a46dbbbe2c062', 2, 1, 'ball@gmail.com'),
(5, 'zmija', '8a84ada5490270156394a08e6ab492cb', 2, 1, 'lol@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `ep_pitanja_na_testu`
--

CREATE TABLE `ep_pitanja_na_testu` (
  `ID` int(11) NOT NULL,
  `testID` int(11) NOT NULL,
  `pitanjeID` int(11) NOT NULL,
  `odgovorID` int(11) NOT NULL,
  `odabrano` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ep_pitanje`
--

CREATE TABLE `ep_pitanje` (
  `ID` int(11) NOT NULL,
  `tekst_pitanja` text NOT NULL,
  `korisnikID` int(11) NOT NULL,
  `brojBodova` int(11) NOT NULL DEFAULT 0,
  `hint` text DEFAULT NULL,
  `broj_ponudenih` int(11) NOT NULL DEFAULT 0,
  `aktivno` tinyint(1) NOT NULL DEFAULT 1,
  `temaID` int(11) NOT NULL,
  `slika` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ep_pitanje`
--

INSERT INTO `ep_pitanje` (`ID`, `tekst_pitanja`, `korisnikID`, `brojBodova`, `hint`, `broj_ponudenih`, `aktivno`, `temaID`, `slika`) VALUES
(21, 'Koji je glavni grad Francuske?', 1, 5, 'Grad ljubavi', 4, 1, 1, NULL),
(22, 'Koja je najveća država na svijetu po površini?', 2, 5, 'Nalazi se u Europi i Aziji', 4, 1, 1, NULL),
(24, 'Koliko kontinenata postoji na Zemlji?', 1, 4, 'Broj je između 5 i 7', 4, 1, 1, NULL),
(25, 'Koja je formula za vodu?', 2, 2, 'Sadrži vodik i kisik', 4, 1, 3, NULL),
(27, 'Koja rijeka teče kroz Zagreb?', 1, 3, 'Počinje slovom S', 4, 1, 1, NULL),
(28, 'Koji je kemijski simbol za kisik?', 2, 3, 'Jedno slovo', 4, 1, 3, NULL),
(30, 'Tko je napisao \"Hamlet\"?', 1, 5, 'Poznati engleski pisac', 4, 1, 5, NULL),
(31, 'Koji je najveći ocean na svijetu?', 1, 5, 'Nalazi se između Amerike i Azije', 4, 1, 1, NULL),
(32, 'Koja država koristi jen kao svoju valutu?', 2, 3, 'Nalazi se u Aziji', 4, 1, 2, NULL),
(34, 'Tko je otkrio gravitaciju?', 1, 5, 'Jabuka mu je pala na glavu', 4, 1, 4, NULL),
(35, 'Koja životinja najbrže trči?', 2, 3, 'Može doseći brzinu od 110 km/h', 4, 1, 5, NULL),
(37, 'Koji je glavni sastojak piva?', 1, 5, 'Napravljen je od žitarica', 4, 1, 6, NULL),
(38, 'Koji je najpoznatiji znanstvenik u povijesti?', 2, 4, 'Ima poznatu formulu E=mc^2', 4, 1, 4, NULL),
(40, 'Koji metal je najlakši?', 1, 3, 'Koristi se u baterijama', 4, 1, 3, NULL),
(46, 'Kad bude Martin ćelavi?', 1, 1, NULL, 0, 1, 4, NULL),
(47, 'Koliko je visoka gora', 1, 1, NULL, 0, 1, 1, NULL),
(48, 'Kaj znači Sis', 1, 1, NULL, 0, 1, 10, NULL),
(49, 'Koje je boja kapa', 1, 1, NULL, 0, 1, 11, NULL),
(50, 'dsada', 1, 1, NULL, 0, 1, 12, 'uploads/1741103966_474501156_1286432389237726_8017745531472963064_n.jpg'),
(51, 'dsadas', 1, 1, NULL, 0, 1, 12, 'uploads/1741104618_playertracker_logo.png'),
(52, 'djksbdja', 1, 1, 'jksdbsadjkabd', 0, 1, 12, 'uploads/1741117652_477000787_596460106540980_574527163491175664_n.jpg'),
(53, 'jdadja', 1, 1, 'dnljasdla', 0, 1, 12, 'uploads/1741118067_playertracker_logo.png');

-- --------------------------------------------------------

--
-- Table structure for table `ep_prava`
--

CREATE TABLE `ep_prava` (
  `ID` int(11) NOT NULL,
  `korisnikID` int(11) NOT NULL,
  `pravoID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ep_prava`
--

INSERT INTO `ep_prava` (`ID`, `korisnikID`, `pravoID`) VALUES
(1, 1, 1),
(2, 2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `ep_razine`
--

CREATE TABLE `ep_razine` (
  `ID` int(11) NOT NULL,
  `opis` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ep_razine`
--

INSERT INTO `ep_razine` (`ID`, `opis`) VALUES
(1, 'Admin\r\n'),
(2, 'Neadmin\r\n');

-- --------------------------------------------------------

--
-- Table structure for table `ep_teme`
--

CREATE TABLE `ep_teme` (
  `ID` int(11) NOT NULL,
  `naziv` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ep_teme`
--

INSERT INTO `ep_teme` (`ID`, `naziv`) VALUES
(2, 'Astronomija'),
(4, 'Biologija'),
(7, 'Ekonomija'),
(6, 'Fizika'),
(1, 'Geografija'),
(8, 'Hrana'),
(3, 'Kemija'),
(5, 'Književnost'),
(12, 'ostalo'),
(10, 'Sigurnost informacijskih sustava'),
(11, 'Slikice'),
(9, 'Sport');

-- --------------------------------------------------------

--
-- Table structure for table `ep_test`
--

CREATE TABLE `ep_test` (
  `ID` int(11) NOT NULL,
  `korisnikID` int(11) NOT NULL,
  `vrijeme_pocetka` datetime NOT NULL,
  `vremensko_ogranicenje` int(11) NOT NULL,
  `vrijeme_kraja` datetime GENERATED ALWAYS AS (`vrijeme_pocetka` + interval `vremensko_ogranicenje` minute) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ep_test`
--

INSERT INTO `ep_test` (`ID`, `korisnikID`, `vrijeme_pocetka`, `vremensko_ogranicenje`) VALUES
(1, 1, '2025-02-27 10:00:00', 30),
(2, 2, '2025-02-27 11:00:00', 40),
(4, 1, '2025-02-27 13:00:00', 60),
(5, 1, '2025-03-01 10:00:00', 30),
(6, 2, '2025-03-02 11:00:00', 40);

-- --------------------------------------------------------

--
-- Table structure for table `op_odgovori`
--

CREATE TABLE `op_odgovori` (
  `ID` int(11) NOT NULL,
  `tekst` text NOT NULL,
  `pitanjeID` int(11) NOT NULL,
  `tocno` tinyint(1) NOT NULL DEFAULT 0,
  `korisnikID` int(11) NOT NULL,
  `aktivno` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `op_odgovori`
--

INSERT INTO `op_odgovori` (`ID`, `tekst`, `pitanjeID`, `tocno`, `korisnikID`, `aktivno`) VALUES
(1, 'Pariz', 21, 1, 1, 1),
(2, 'Lyon', 21, 0, 1, 1),
(3, 'Marseille', 21, 0, 1, 1),
(4, 'Nice', 21, 0, 1, 1),
(5, 'Rusija', 22, 1, 1, 1),
(6, 'Kanada', 22, 0, 1, 1),
(7, 'Kina', 22, 0, 1, 1),
(8, 'SAD', 22, 0, 1, 1),
(13, '7', 24, 1, 1, 1),
(14, '5', 24, 0, 1, 1),
(15, '6', 24, 0, 1, 1),
(16, '8', 24, 0, 1, 1),
(17, 'H2O', 25, 1, 1, 1),
(18, 'CO2', 25, 0, 1, 1),
(19, 'O2', 25, 0, 1, 1),
(20, 'H2', 25, 0, 1, 1),
(25, 'Sava', 27, 1, 1, 1),
(26, 'Dunav', 27, 0, 1, 1),
(27, 'Drava', 27, 0, 1, 1),
(28, 'Kupa', 27, 0, 1, 1),
(29, 'O', 28, 1, 1, 1),
(30, 'O2', 28, 0, 1, 1),
(31, 'O3', 28, 0, 1, 1),
(32, 'C', 28, 0, 1, 1),
(37, 'William Shakespeare', 30, 1, 1, 1),
(38, 'Charles Dickens', 30, 0, 1, 1),
(39, 'J.R.R. Tolkien', 30, 0, 1, 1),
(40, 'George Orwell', 30, 0, 1, 1),
(41, 'Pacifik', 31, 1, 1, 1),
(42, 'Atlantski', 31, 0, 1, 1),
(43, 'Indijski', 31, 0, 1, 1),
(44, 'Arktički', 31, 0, 1, 1),
(45, 'Japan', 32, 1, 1, 1),
(46, 'Kina', 32, 0, 1, 1),
(47, 'Južna Koreja', 32, 0, 1, 1),
(48, 'Indija', 32, 0, 1, 1),
(53, 'Isaac Newton', 34, 1, 1, 1),
(54, 'Albert Einstein', 34, 0, 1, 1),
(55, 'Nikola Tesla', 34, 0, 1, 1),
(56, 'Galileo Galilei', 34, 0, 1, 1),
(57, 'Gepard', 35, 1, 1, 1),
(58, 'Lav', 35, 0, 1, 1),
(59, 'Konj', 35, 0, 1, 1),
(60, 'Soko', 35, 0, 1, 1),
(65, 'Ječam', 37, 1, 1, 1),
(66, 'Hmelj', 37, 0, 1, 1),
(67, 'Pšenica', 37, 0, 1, 1),
(68, 'Riža', 37, 0, 1, 1),
(69, 'Albert Einstein', 38, 1, 1, 1),
(70, 'Nikola Tesla', 38, 0, 1, 1),
(71, 'Isaac Newton', 38, 0, 1, 1),
(72, 'Stephen Hawking', 38, 0, 1, 1),
(77, 'Litij', 40, 1, 1, 1),
(78, 'Aluminij', 40, 0, 1, 1),
(79, 'Magnezij', 40, 0, 1, 1),
(80, 'Bakar', 40, 0, 1, 1),
(181, 'Za 1 godinu', 46, 0, 1, 1),
(182, 'Za 2 godine', 46, 0, 1, 1),
(183, 'Za 3 godine', 46, 0, 1, 1),
(184, 'Sutra', 46, 1, 1, 1),
(185, '100m', 47, 0, 1, 1),
(186, '12m', 47, 0, 1, 1),
(187, '33m', 47, 1, 1, 1),
(188, '22m', 47, 0, 1, 1),
(189, 'Sam i samcat', 48, 0, 1, 1),
(190, 'Sigurnost informacijskih sustava', 48, 1, 1, 1),
(191, 'Strelil bum se ak bum ovo delal jos par sekundi', 48, 0, 1, 1),
(192, 'Glupi je zadatak', 48, 0, 1, 1),
(193, 'Plava', 49, 0, 1, 1),
(194, 'Bijela', 49, 1, 1, 1),
(195, 'Crvena', 49, 0, 1, 1),
(196, 'Glupi si', 49, 0, 1, 1),
(197, 'dasda', 50, 0, 1, 1),
(198, 'dada', 50, 0, 1, 1),
(199, 'dada', 50, 0, 1, 1),
(200, 'dada', 50, 1, 1, 1),
(201, 'dasdas', 51, 0, 1, 1),
(202, 'dasdas', 51, 0, 1, 1),
(203, 'dasda', 51, 0, 1, 1),
(204, 'dsad', 51, 1, 1, 1),
(205, 'sjda', 52, 0, 1, 1),
(206, 'sadjkabda', 52, 0, 1, 1),
(207, 'dasjdbakj', 52, 1, 1, 1),
(208, 'dasjkdbas', 52, 0, 1, 1),
(209, 'askdbasd', 53, 0, 1, 1),
(210, 'askdbaskj', 53, 0, 1, 1),
(211, 'ashdbaks', 53, 1, 1, 1),
(212, 'assdbkasb', 53, 0, 1, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ep_korisnik`
--
ALTER TABLE `ep_korisnik`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `razinaID` (`razinaID`);

--
-- Indexes for table `ep_pitanja_na_testu`
--
ALTER TABLE `ep_pitanja_na_testu`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `testID` (`testID`),
  ADD KEY `pitanjeID` (`pitanjeID`),
  ADD KEY `odgovorID` (`odgovorID`);

--
-- Indexes for table `ep_pitanje`
--
ALTER TABLE `ep_pitanje`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `korisnikID` (`korisnikID`),
  ADD KEY `fk_ep_teme` (`temaID`);

--
-- Indexes for table `ep_prava`
--
ALTER TABLE `ep_prava`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `korisnikID` (`korisnikID`);

--
-- Indexes for table `ep_razine`
--
ALTER TABLE `ep_razine`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `ep_teme`
--
ALTER TABLE `ep_teme`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `naziv` (`naziv`);

--
-- Indexes for table `ep_test`
--
ALTER TABLE `ep_test`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `korisnikID` (`korisnikID`);

--
-- Indexes for table `op_odgovori`
--
ALTER TABLE `op_odgovori`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `pitanjeID` (`pitanjeID`),
  ADD KEY `korisnikID` (`korisnikID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ep_korisnik`
--
ALTER TABLE `ep_korisnik`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `ep_pitanja_na_testu`
--
ALTER TABLE `ep_pitanja_na_testu`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `ep_pitanje`
--
ALTER TABLE `ep_pitanje`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `ep_prava`
--
ALTER TABLE `ep_prava`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `ep_razine`
--
ALTER TABLE `ep_razine`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `ep_teme`
--
ALTER TABLE `ep_teme`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `ep_test`
--
ALTER TABLE `ep_test`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `op_odgovori`
--
ALTER TABLE `op_odgovori`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=213;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ep_korisnik`
--
ALTER TABLE `ep_korisnik`
  ADD CONSTRAINT `ep_korisnik_ibfk_1` FOREIGN KEY (`razinaID`) REFERENCES `ep_razine` (`ID`) ON DELETE CASCADE;

--
-- Constraints for table `ep_pitanja_na_testu`
--
ALTER TABLE `ep_pitanja_na_testu`
  ADD CONSTRAINT `ep_pitanja_na_testu_ibfk_1` FOREIGN KEY (`testID`) REFERENCES `ep_test` (`ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `ep_pitanja_na_testu_ibfk_2` FOREIGN KEY (`pitanjeID`) REFERENCES `ep_pitanje` (`ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `ep_pitanja_na_testu_ibfk_3` FOREIGN KEY (`odgovorID`) REFERENCES `op_odgovori` (`ID`) ON DELETE CASCADE;

--
-- Constraints for table `ep_pitanje`
--
ALTER TABLE `ep_pitanje`
  ADD CONSTRAINT `ep_pitanje_ibfk_1` FOREIGN KEY (`korisnikID`) REFERENCES `ep_korisnik` (`ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ep_teme` FOREIGN KEY (`temaID`) REFERENCES `ep_teme` (`ID`) ON DELETE CASCADE;

--
-- Constraints for table `ep_prava`
--
ALTER TABLE `ep_prava`
  ADD CONSTRAINT `ep_prava_ibfk_1` FOREIGN KEY (`korisnikID`) REFERENCES `ep_korisnik` (`ID`) ON DELETE CASCADE;

--
-- Constraints for table `ep_test`
--
ALTER TABLE `ep_test`
  ADD CONSTRAINT `ep_test_ibfk_1` FOREIGN KEY (`korisnikID`) REFERENCES `ep_korisnik` (`ID`) ON DELETE CASCADE;

--
-- Constraints for table `op_odgovori`
--
ALTER TABLE `op_odgovori`
  ADD CONSTRAINT `op_odgovori_ibfk_1` FOREIGN KEY (`pitanjeID`) REFERENCES `ep_pitanje` (`ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `op_odgovori_ibfk_2` FOREIGN KEY (`korisnikID`) REFERENCES `ep_korisnik` (`ID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
