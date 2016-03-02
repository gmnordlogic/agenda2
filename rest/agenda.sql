-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 02, 2016 at 08:41 AM
-- Server version: 5.5.47-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `agenda2`
--

-- --------------------------------------------------------

--
-- Table structure for table `agenda`
--

CREATE TABLE IF NOT EXISTS `agenda` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fname` varchar(64) NOT NULL,
  `lname` varchar(64) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=26 ;

--
-- Dumping data for table `agenda`
--

INSERT INTO `agenda` (`id`, `fname`, `lname`, `email`, `phone`) VALUES
(1, 'Coco', 'Loco', 'coco@loco.com', '03030303003'),
(2, 'Johnny', 'Bravo', 'bravo@johnny.de', '05050550555'),
(3, 'test', 'testl', 'teste@test.com', '40440440444'),
(4, 'mircea', 'bravo', 'coco@mircea.ro', '343432342323'),
(5, 'natanael', 'oanea', 'oanea@suge.ro', '3222322332'),
(6, 'teodor', 'croitur', 'magar@impaiat.ro', '34034034004'),
(7, 'gigi', 'becali', 'gigi@becali.bg', '000099999'),
(8, 'romulus', 'ladea', 'arta@moderna.cj', '444444444'),
(9, 'cernea', 'homo', 'rc@gubv.ro', '2323343242'),
(10, 'cici', 'ema', 'ema234@yahoo.co', '353340449'),
(11, 'ricki', 'povery', 'Y@ricki.it', '3405656050'),
(12, 'ciociolina', 'besina', 'p@r.no', '8989898989'),
(13, 'java', 'oracl', 'java@lava.de', '345535345354'),
(14, 'pepe', 'molnar', 'm@pepe.pg', '343404566060'),
(16, 'claudiu', 'lacatus', 'cl@gmal.ro', '3453453454'),
(17, 'radu', 'mazare', 'constant@a.ro', '343453344565'),
(18, 'ponta', 'jr', 'junioru@psd.ro', '4595409095'),
(19, 'ilici', 'iliescu', 'laba@ursului.ru', '345554556555'),
(20, 'base', 'traienut', 'troian@eliada.gr', 't45596565699'),
(21, 'ioha', 'nis', 'limb@ncur.de', '3465455445655'),
(24, 'Pampas', 'Veronica', 'vero@pampa.su', '89866878788'),
(25, 'aristidel', 'buhaci', 'buha@buha.com', '49549595966');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
