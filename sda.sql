-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 04, 2018 at 09:50 AM
-- Server version: 5.7.14
-- PHP Version: 7.0.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sda`
--

-- --------------------------------------------------------

--
-- Table structure for table `academicperiod`
--

CREATE TABLE `academicperiod` (
  `academicperiodid` int(11) NOT NULL,
  `academicperiod` varchar(15) NOT NULL,
  `groupid` int(11) NOT NULL,
  `datecreated` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `academicperiod`
--

INSERT INTO `academicperiod` (`academicperiodid`, `academicperiod`, `groupid`, `datecreated`) VALUES
(1, 'Year1Semester1', 1, '2017-02-11'),
(2, 'Year1Semester2', 1, '2018-02-08');

-- --------------------------------------------------------

--
-- Table structure for table `course`
--

CREATE TABLE `course` (
  `courseid` int(11) NOT NULL,
  `coursecode` varchar(10) NOT NULL,
  `coursename` varchar(50) NOT NULL,
  `courseshortname` varchar(15) NOT NULL,
  `lecturer` varchar(30) NOT NULL,
  `academicperiodid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `course`
--

INSERT INTO `course` (`courseid`, `coursecode`, `coursename`, `courseshortname`, `lecturer`, `academicperiodid`) VALUES
(1, 'CSC111', 'Introduction to Computing', 'Intro to Comp', 'Mr ABC', 1),
(2, 'CSC112', 'Introduction to Computing2', 'Intro to Comp2', 'Mr XYZ', 1),
(3, 'CSS 122', 'Intro to computer science 2', 'Intro to CS', 'Mr Nobody', 2);

-- --------------------------------------------------------

--
-- Table structure for table `filemetadata`
--

CREATE TABLE `filemetadata` (
  `fileid` int(11) NOT NULL,
  `filename` varchar(50) NOT NULL,
  `courseid` int(11) NOT NULL,
  `folder` varchar(50) NOT NULL,
  `who` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `filemetadata`
--

INSERT INTO `filemetadata` (`fileid`, `filename`, `courseid`, `folder`, `who`) VALUES
(1, 'Enenim Asukwo.docx', 2, 'assignments', 'ADMIN'),
(2, 'BOB Marley - Redemption Song.mp3', 2, 'voicenotes', 'ADMIN'),
(3, 'jesse greatest.mp3', 3, 'voicenotes', 'ADMIN');

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `studentid` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `activationstatus` varchar(15) NOT NULL DEFAULT 'Active',
  `membertype` varchar(15) NOT NULL,
  `groupid` int(11) NOT NULL,
  `password_reset_status` varchar(50) NOT NULL,
  `email_hash` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`studentid`, `email`, `name`, `password`, `activationstatus`, `membertype`, `groupid`, `password_reset_status`, `email_hash`) VALUES
(1, 'charlesudoh1@gmail.com', 'Charles Udoh', '7417026759c3b1263441683ed7fc7972', 'ACTIVE', 'ADMIN', 1, '', '836c0d41e3ec533b356d5ae07ff703b3'),
(2, 'charlieudoh@yahoo.com', 'Nsikak Udoh', '7417026759c3b1263441683ed7fc7972', 'ACTIVE', 'MEMBER', 1, '', 'd00f92c015a3cf0625fdc98db2fcb0c5');

-- --------------------------------------------------------

--
-- Table structure for table `studyprogram`
--

CREATE TABLE `studyprogram` (
  `groupid` int(11) NOT NULL,
  `groupname` varchar(50) NOT NULL,
  `groupshortname` varchar(15) DEFAULT NULL,
  `institution` varchar(50) NOT NULL,
  `faculty` varchar(50) NOT NULL,
  `department` varchar(50) NOT NULL,
  `courseofstudy` varchar(50) NOT NULL,
  `programme` varchar(50) NOT NULL,
  `datecreated` date NOT NULL,
  `status` varchar(15) NOT NULL,
  `adminphone` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `studyprogram`
--

INSERT INTO `studyprogram` (`groupid`, `groupname`, `groupshortname`, `institution`, `faculty`, `department`, `courseofstudy`, `programme`, `datecreated`, `status`, `adminphone`) VALUES
(1, 'Class of 2016', NULL, 'UNILAG', 'science', 'computer science', 'computer science', 'BA/BEng/BSc', '2017-02-11', 'INACTIVE', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `academicperiod`
--
ALTER TABLE `academicperiod`
  ADD PRIMARY KEY (`academicperiodid`);

--
-- Indexes for table `course`
--
ALTER TABLE `course`
  ADD PRIMARY KEY (`courseid`);

--
-- Indexes for table `filemetadata`
--
ALTER TABLE `filemetadata`
  ADD PRIMARY KEY (`fileid`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`studentid`);

--
-- Indexes for table `studyprogram`
--
ALTER TABLE `studyprogram`
  ADD PRIMARY KEY (`groupid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `academicperiod`
--
ALTER TABLE `academicperiod`
  MODIFY `academicperiodid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `course`
--
ALTER TABLE `course`
  MODIFY `courseid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `filemetadata`
--
ALTER TABLE `filemetadata`
  MODIFY `fileid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `student`
--
ALTER TABLE `student`
  MODIFY `studentid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `studyprogram`
--
ALTER TABLE `studyprogram`
  MODIFY `groupid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
