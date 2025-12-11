-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 22, 2025 at 05:33 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `crime_portal`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `h_id` varchar(50) NOT NULL,
  `h_pass` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`h_id`, `h_pass`) VALUES
('Likhi@admin', 'admin@123');

-- --------------------------------------------------------

--
-- Table structure for table `complaint`
--

CREATE TABLE `complaint` (
  `c_id` varchar(20) NOT NULL,
  `a_no` bigint(12) NOT NULL,
  `location` varchar(50) NOT NULL,
  `type_crime` varchar(50) NOT NULL,
  `d_o_c` date NOT NULL,
  `description` varchar(7000) NOT NULL,
  `inc_status` varchar(50) DEFAULT 'Unassigned',
  `pol_status` varchar(50) DEFAULT NULL,
  `p_id` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `complaint`
--

INSERT INTO `complaint` (`c_id`, `a_no`, `location`, `type_crime`, `d_o_c`, `description`, `inc_status`, `pol_status`, `p_id`) VALUES
('BANG2025-0001', 312032476907, 'bangalore', 'Robbery', '2025-06-26', 'My valuable things(Mobile,money,ring,etc) are robbered at night while i was comming to home', 'Assigned', 'In Process', 'bhavish@bangalore'),
('NELA2025-0001', 312032476907, 'Nelamangala', 'Pick Pocket', '2025-06-01', 'Mobile has pick pocketed at bus stop.', 'Assigned', 'ChargeSheet Filed', 'sanjay@nelamangala'),
('TIPT2025-0001', 633410729161, 'Tiptur', 'Pick Pocket', '2025-06-28', 'My wallet was stolen when i was going to the office at bus stand .', 'Assigned', 'In Process', 'jagadesh@tiptur'),
('TUMK2025-0001', 123456789013, 'Tumkur', 'Robbery', '2025-06-24', 'My house has been roberred when there were no peoples in the house.', 'Assigned', 'In Process', 'manish@tumkur');

-- --------------------------------------------------------

--
-- Table structure for table `incharge`
--

CREATE TABLE `incharge` (
  `i_id` varchar(50) NOT NULL,
  `i_name` varchar(50) NOT NULL,
  `i_email` varchar(50) NOT NULL,
  `location` varchar(50) NOT NULL,
  `i_pass` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `incharge`
--

INSERT INTO `incharge` (`i_id`, `i_name`, `i_email`, `location`, `i_pass`) VALUES
('abhishek@tumkur', 'Abhishek', 'incharge6362@gmail.com', 'Tumkur', 'abhishek'),
('bargav@tiptur', 'Bhargav', 'incharge6362@gmail.com', 'Tiptur', 'bargav'),
('naveen@bangalore', 'naveen', 'incharge6362@gmail.com', 'bangalore', 'naveen'),
('shivam@nelamangala', 'Shivam', 'incharge6362@gmail.com', 'Nelamangala', 'shivam');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset`
--

CREATE TABLE `password_reset` (
  `email` varchar(250) NOT NULL,
  `token` int(50) DEFAULT NULL,
  `expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `police`
--

CREATE TABLE `police` (
  `p_name` varchar(50) NOT NULL,
  `p_id` varchar(50) NOT NULL,
  `p_email` varchar(50) NOT NULL,
  `spec` varchar(50) NOT NULL,
  `location` varchar(50) NOT NULL,
  `p_pass` char(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `police`
--

INSERT INTO `police` (`p_name`, `p_id`, `p_email`, `spec`, `location`, `p_pass`) VALUES
('Bhavish', 'bhavish@bangalore', 'example@gmail.com', 'all', 'bangalore', '$2y$10$Lp1MlruTt42Xu076BpOUnOxr652UibELrlxPy6JFRblDgm4BwZGAi'),
('Jagadesh', 'jagadesh@tiptur', '', 'All', 'Tiptur', '$2y$10$Zbbt808B/poT.HKKlBYSDuTfI0SCvsTs4moOHpREi9kjQMrT/KZpa'),
('Manish', 'manish@tumkur', '', 'All', 'Tumkur', '$2y$10$B3TP7C.NWS3AmUtdQiUFKelgay2ve122.CJnfTIIW9r3IcHdMEN6a'),
('Sanjay', 'sanjay@nelamangala', '', 'All', 'Nelamangala', '$2y$10$xhqN7w9daJUIKvBzzT554elcfy166Sd4RkrXB0PwI0fOE4gg1Gz9e');

-- --------------------------------------------------------

--
-- Table structure for table `update_case`
--

CREATE TABLE `update_case` (
  `c_id` varchar(20) NOT NULL,
  `d_o_u` timestamp NOT NULL DEFAULT current_timestamp(),
  `case_update` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `update_case`
--

INSERT INTO `update_case` (`c_id`, `d_o_u`, `case_update`) VALUES
('NELA2025-0001', '2025-06-02 14:31:15', 'Criminal Verified'),
('NELA2025-0001', '2025-06-06 14:33:16', 'Criminal Caught'),
('NELA2025-0001', '2025-06-07 07:40:04', 'Criminal Interrogated'),
('NELA2025-0001', '2025-06-07 08:20:55', 'Criminal Accepted the Crime'),
('NELA2025-0001', '2025-06-07 08:35:32', 'FIR Filed'),
('NELA2025-0001', '2025-06-08 06:56:13', 'Criminal was punished .'),
('TUMK2025-0001', '2025-06-27 15:55:19', 'Criminal Verified'),
('BANG2025-0001', '2025-06-27 15:59:54', 'Criminal Verified'),
('TUMK2025-0001', '2025-06-28 00:48:31', 'Criminal Caught'),
('BANG2025-0001', '2025-06-28 00:50:15', 'Criminal Caught'),
('TUMK2025-0001', '2025-06-28 08:44:21', 'Criminal Interrogated'),
('TUMK2025-0001', '2025-07-09 01:13:37', 'Criminal Accepted the Crime');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `u_id` varchar(50) NOT NULL,
  `u_name` varchar(50) NOT NULL,
  `u_pass` char(60) NOT NULL DEFAULT 'NOT NULL',
  `u_addr` varchar(100) NOT NULL,
  `a_no` varchar(12) DEFAULT NULL,
  `gen` varchar(15) NOT NULL,
  `mob` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`u_id`, `u_name`, `u_pass`, `u_addr`, `a_no`, `gen`, `mob`) VALUES
('girish@gmail.com', 'Girish', '$2y$10$ypBRoaZkorDk6iR0VsUvgefG2vDBOvNmsxz3VgKFHBEKYIijMVYUu', 'Tumkur', '123456789013', 'Male', '0987654321'),
('likhith@gmail.com', 'Likhith H E', '$2y$10$ypBRoaZkorDk6iR0VsUvgefG2vDBOvNmsxz3VgKFHBEKYIijMVYUu', 'Banglore', '123456789012', 'Male', '0192837465'),
('mahadevaiah@gmail.com', 'Mahadevaiah k s', '$2y$10$ypBRoaZkorDk6iR0VsUvgefG2vDBOvNmsxz3VgKFHBEKYIijMVYUu', 'Mysore', '098765432109', 'Male', '1234567890');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `complaint`
--
ALTER TABLE `complaint`
  ADD PRIMARY KEY (`c_id`);

--
-- Indexes for table `incharge`
--
ALTER TABLE `incharge`
  ADD PRIMARY KEY (`i_id`),
  ADD UNIQUE KEY `location` (`location`);

--
-- Indexes for table `police`
--
ALTER TABLE `police`
  ADD PRIMARY KEY (`p_id`);

--
-- Indexes for table `update_case`
--
ALTER TABLE `update_case`
  ADD UNIQUE KEY `d_o_u` (`d_o_u`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`u_id`),
  ADD UNIQUE KEY `a_no` (`a_no`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
