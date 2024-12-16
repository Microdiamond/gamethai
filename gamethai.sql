-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 14, 2023 at 10:12 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gamethai`
--

-- --------------------------------------------------------

--
-- Table structure for table `information`
--

CREATE TABLE `information` (
  `info_id` int(11) NOT NULL,
  `info_name` varchar(255) NOT NULL,
  `info_audio_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `information`
--

INSERT INTO `information` (`info_id`, `info_name`, `info_audio_name`) VALUES
(77, 'กฎเกณฑ์', ''),
(78, 'กฎหมาย', ''),
(79, 'กตเวที', ''),
(80, 'กตัญญู', ''),
(81, 'กติกา', ''),
(82, 'ก้น', ''),
(83, 'กร', ''),
(84, 'กรม', ''),
(85, 'กรมพระปรมานุชิตชิโนรส', ''),
(86, 'กระจก', ''),
(87, 'กระบวย', ''),
(88, 'กระบี่กระบอง', ''),
(89, 'กระปุก', ''),
(90, 'กระโพ้ง', ''),
(91, 'กระไร', ''),
(92, 'กระวีกระวาด', ''),
(93, 'กระสับกระส่าย', ''),
(94, 'กระแส', ''),
(95, 'ขนาง', ''),
(96, 'ขบ', ''),
(97, 'ขมวด', ''),
(98, 'ขมิ้น', ''),
(99, 'ขยับ', ''),
(100, 'ขยำ', ''),
(101, 'ขยุ้ม', ''),
(102, 'ขลัง', ''),
(103, 'ขวบ', ''),
(104, 'ขอม ', ''),
(105, 'ขัง', ''),
(106, 'ขัดข้อง', ''),
(107, 'ขัดสน', ''),
(108, 'ข้าวเปลือก', ''),
(109, 'ขืน', ''),
(110, 'ขุกคิด', ''),
(111, 'ขุน', ''),
(112, 'ขุ่น', ''),
(113, 'ขุ่นข้น', ''),
(114, 'ขุนเขา', ''),
(115, 'ขุนตาน', ''),
(116, 'ขุนบาลเมือง', ''),
(117, 'ขุนวัง', ''),
(118, 'ขุนศรีอินทราทิตย์', ''),
(119, 'เข็ญ', ''),
(120, 'เข็ด', ''),
(121, 'เข็ดขอน', ''),
(122, 'เขม้น', ''),
(123, 'เขยื้อน', ''),
(124, 'เขลา', ''),
(125, 'คำนวณ', ''),
(126, 'คำนับ', ''),
(127, 'คำนึง', ''),
(128, 'คำราม', ''),
(129, 'คำสั่ง', ''),
(130, 'คิ้ว', ''),
(131, 'คีรี', ''),
(132, 'คุณค่า', ''),
(133, 'คุณธรรม', ''),
(134, 'คุณภาพ', ''),
(135, 'คุณสมบัติ', ''),
(136, 'เครื่องปรับอากาศ', ''),
(137, 'เครื่องปิ้งขนมปัง', ''),
(138, 'เครื่องราชบรรณาการ', ''),
(139, 'เครื่องสมองกล', ''),
(140, 'เคล็ดลับ', ''),
(141, 'เคลิ้ม', ''),
(142, 'เคว้งคว้าง', ''),
(143, 'เค้า', ''),
(144, 'เคารพ', ''),
(145, 'เคียว', ''),
(146, 'แคร่', ''),
(147, 'โค้ง', ''),
(148, 'โคจร', ''),
(149, 'โคม', ''),
(150, 'โครงกระดูก', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `information`
--
ALTER TABLE `information`
  ADD PRIMARY KEY (`info_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `information`
--
ALTER TABLE `information`
  MODIFY `info_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=151;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
