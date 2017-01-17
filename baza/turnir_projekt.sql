-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jan 11, 2017 at 06:43 PM
-- Server version: 10.1.19-MariaDB
-- PHP Version: 5.6.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `turnir_projekt`
--

-- --------------------------------------------------------

--
-- Table structure for table `grupa`
--

CREATE TABLE `grupa` (
  `id_grupa` int(11) NOT NULL,
  `oznaka` varchar(127) NOT NULL COMMENT 'oznaka/naziv grupe npr. A, B, C...itd',
  `id_turnir` int(11) NOT NULL,
  `aktivan` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `knockout`
--

CREATE TABLE `knockout` (
  `id_knockout` int(11) NOT NULL,
  `pobjeda_sudionik1` int(11) NOT NULL COMMENT 'broj pobjeda sudionika 1 u meču',
  `pobjeda_sudionik2` int(11) NOT NULL COMMENT 'broj pobjeda sudionika 2 u meču',
  `aktivan` tinyint(4) NOT NULL,
  `id_sudionik1` int(11) DEFAULT NULL,
  `id_sudionik2` int(11) DEFAULT NULL,
  `zavrsen` tinyint(4) NOT NULL,
  `id_turnir` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `korisnik`
--

CREATE TABLE `korisnik` (
  `id_korisnik` int(11) NOT NULL,
  `email` varchar(255) NOT NULL COMMENT 'email korisnika',
  `lozinka` varchar(64) NOT NULL COMMENT 'lozinka korisnika',
  `ime` varchar(255) NOT NULL COMMENT 'ime korisnika',
  `datum_registracije` date NOT NULL COMMENT 'datum registracije korisnika',
  `aktivan` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `korisnik`
--

INSERT INTO `korisnik` (`id_korisnik`, `email`, `lozinka`, `ime`, `datum_registracije`, `aktivan`) VALUES
(1, 'test@test', 'test123', 'test', '2016-11-12', 1);

-- --------------------------------------------------------

--
-- Table structure for table `mec`
--

CREATE TABLE `mec` (
  `id_mec` int(11) NOT NULL,
  `id_turnir` int(11) NOT NULL COMMENT 'meč je od određenog turnira',
  `id_domacin` int(11) NOT NULL COMMENT 'domaćin meča',
  `id_gost` int(11) NOT NULL COMMENT 'gost meča',
  `rez_domacin` int(11) DEFAULT NULL COMMENT 'rezultat domaćina',
  `rez_gost` int(11) DEFAULT NULL COMMENT 'rezultat gosta',
  `id_knockout` int(11) DEFAULT NULL COMMENT 'tip knockouta',
  `datum` datetime DEFAULT NULL COMMENT 'datum meča',
  `aktivan` tinyint(4) NOT NULL,
  `zavrsen` tinyint(4) NOT NULL COMMENT '1 -> zavrsen mec'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sudionik`
--

CREATE TABLE `sudionik` (
  `id_sudionik` int(11) NOT NULL,
  `naziv` varchar(255) NOT NULL COMMENT 'naziv sudionika',
  `bodovi_grupe` int(11) NOT NULL COMMENT 'sveukupni bodovi sudionika u grupi',
  `id_grupa` int(11) DEFAULT NULL COMMENT 'grupa turnira',
  `aktivan` tinyint(4) NOT NULL,
  `id_turnir` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `turnir`
--

CREATE TABLE `turnir` (
  `id_turnir` int(11) NOT NULL,
  `naziv` varchar(255) NOT NULL COMMENT 'naziv turnira',
  `tip` tinyint(4) NOT NULL COMMENT 'tip turnira: 1-grupno, 2-grupno+knock-out, 3-knock-out',
  `datum_pocetka` datetime DEFAULT NULL COMMENT 'datum pocetka turnira',
  `admin` int(11) NOT NULL COMMENT 'korisnik koji je napravio turnir',
  `broj_natjecatelja` int(11) NOT NULL COMMENT 'broj ekipa/natjecatelja u turniru',
  `aktivan` tinyint(4) NOT NULL,
  `bod_pobjeda` int(11) DEFAULT NULL COMMENT 'Količina koliko bodova se dobija za pobjedu u grupi',
  `bod_nerijeseno` int(11) DEFAULT NULL COMMENT 'Koliko bodova se dobija za neriješeno',
  `bod_poraz` int(11) DEFAULT NULL COMMENT 'Koliko bodova se dobija za poraz',
  `privatno` tinyint(4) NOT NULL DEFAULT '2' COMMENT '0-privatno, 1-registrirani, 2-svi',
  `broj_susreta` int(11) DEFAULT NULL COMMENT 'Koliko puta ce medusobno igrat (1-4)',
  `bodovi_omjer` tinyint(4) DEFAULT NULL COMMENT '0->Omjer, 1->Bodovi',
  `najbolji_od` int(11) DEFAULT NULL,
  `zavrsen` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `grupa`
--
ALTER TABLE `grupa`
  ADD PRIMARY KEY (`id_grupa`);

--
-- Indexes for table `knockout`
--
ALTER TABLE `knockout`
  ADD PRIMARY KEY (`id_knockout`);

--
-- Indexes for table `korisnik`
--
ALTER TABLE `korisnik`
  ADD PRIMARY KEY (`id_korisnik`);

--
-- Indexes for table `mec`
--
ALTER TABLE `mec`
  ADD PRIMARY KEY (`id_mec`);

--
-- Indexes for table `sudionik`
--
ALTER TABLE `sudionik`
  ADD PRIMARY KEY (`id_sudionik`);

--
-- Indexes for table `turnir`
--
ALTER TABLE `turnir`
  ADD PRIMARY KEY (`id_turnir`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `grupa`
--
ALTER TABLE `grupa`
  MODIFY `id_grupa` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `knockout`
--
ALTER TABLE `knockout`
  MODIFY `id_knockout` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=225;
--
-- AUTO_INCREMENT for table `korisnik`
--
ALTER TABLE `korisnik`
  MODIFY `id_korisnik` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `mec`
--
ALTER TABLE `mec`
  MODIFY `id_mec` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=441;
--
-- AUTO_INCREMENT for table `sudionik`
--
ALTER TABLE `sudionik`
  MODIFY `id_sudionik` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=380;
--
-- AUTO_INCREMENT for table `turnir`
--
ALTER TABLE `turnir`
  MODIFY `id_turnir` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
