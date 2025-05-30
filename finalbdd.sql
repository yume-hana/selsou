-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 27, 2025 at 09:44 PM
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
-- Database: `projectdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `ask`
--

CREATE TABLE `ask` (
  `quest_nbr` int(11) NOT NULL,
  `registration_nbr` int(11) NOT NULL,
  `date_ask` date DEFAULT NULL,
  `time_ask` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ask`
--

INSERT INTO `ask` (`quest_nbr`, `registration_nbr`, `date_ask`, `time_ask`) VALUES
(3, 999999, NULL, NULL),
(4, 999999, NULL, NULL),
(5, 999999, NULL, NULL),
(6, 999999, NULL, NULL),
(7, 999999, NULL, NULL),
(8, 999999, NULL, NULL),
(9, 999999, NULL, NULL),
(10, 999999, NULL, NULL),
(11, 999999, NULL, NULL),
(12, 999999, NULL, NULL),
(13, 999999, NULL, NULL),
(14, 999999, NULL, NULL),
(15, 999999, NULL, NULL),
(16, 999999, NULL, NULL),
(17, 999999, NULL, NULL),
(18, 999999, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `associate`
--

CREATE TABLE `associate` (
  `answer_code` int(11) NOT NULL,
  `ques_nbr` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `consult`
--

CREATE TABLE `consult` (
  `FAQ_nbr` int(11) NOT NULL,
  `registration_nbr` int(11) NOT NULL,
  `date_consult` date DEFAULT NULL,
  `time_consult` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `consult`
--

INSERT INTO `consult` (`FAQ_nbr`, `registration_nbr`, `date_consult`, `time_consult`) VALUES
(1, 999999, '2025-05-01', '23:52:40'),
(1, 123456789, '2025-04-23', '21:09:02'),
(2, 999999, '2025-05-01', '23:52:40'),
(2, 123456789, '2025-04-23', '21:09:02');

-- --------------------------------------------------------

--
-- Table structure for table `meeting`
--

CREATE TABLE `meeting` (
  `Tutor_ID` int(11) NOT NULL,
  `registration_nbr` int(11) NOT NULL,
  `Meeting_time` time DEFAULT NULL,
  `Meeting_date` date NOT NULL,
  `Meeting_location` varchar(255) DEFAULT NULL,
  `content_MT` text DEFAULT NULL,
  `state_MT` enum('pending','accepted','rejected','rescheduled','completed','missed','canceled') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `message_answer`
--

CREATE TABLE `message_answer` (
  `answer_code` int(11) NOT NULL,
  `contentA` text DEFAULT NULL,
  `ans_date` date DEFAULT NULL,
  `ans_time` time DEFAULT NULL,
  `quest_nbr` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `message_quest`
--

CREATE TABLE `message_quest` (
  `quest_nbr` int(11) NOT NULL,
  `contentQ` text DEFAULT NULL,
  `quest_date` date DEFAULT NULL,
  `quest_time` time DEFAULT NULL,
  `state` enum('pending','answered') DEFAULT 'pending',
  `registration_nbr` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `message_quest`
--

INSERT INTO `message_quest` (`quest_nbr`, `contentQ`, `quest_date`, `quest_time`, `state`, `registration_nbr`) VALUES
(3, 'hi can u help me', '2025-04-18', '22:51:41', 'pending', 0),
(4, 'helo if we have any dear', '2025-04-18', '23:02:11', 'pending', 0),
(5, 'helo if we have any deai', '2025-04-18', '23:03:47', 'pending', 0),
(6, 'hello aya ?', '2025-05-01', '14:38:42', 'answered', 0),
(7, 'jn rjr rj', '2025-05-01', '14:39:39', 'pending', 0),
(8, 'wissalll', '2025-05-01', '16:09:40', 'pending', 0),
(9, 'hi ior', '2025-05-10', '01:03:21', 'pending', 0),
(10, 'hi do u', '2025-05-13', '11:02:18', 'pending', 0),
(11, 'jfr rg hyhy', '2025-05-13', '11:06:03', 'pending', 0),
(12, 'hello my frinds', '2025-05-13', '11:40:25', 'pending', 0),
(13, 'rgrtg', '2025-05-13', '12:18:45', 'pending', 0),
(14, 'if you have', '2025-05-25', '13:47:06', 'pending', 0),
(15, 'loulou', '2025-05-25', '14:41:14', 'pending', 0),
(16, 'yujhf rjf\nyu', '2025-05-25', '21:21:46', 'pending', 0),
(17, 'hiiiiii f', '2025-05-26', '17:33:17', 'pending', 0),
(18, 'can you help me', '2025-05-27', '19:03:42', 'pending', 0);

-- --------------------------------------------------------

--
-- Table structure for table `quesfaq`
--

CREATE TABLE `quesfaq` (
  `FAQ_nbr` int(11) NOT NULL,
  `question_content` text DEFAULT NULL,
  `answer_question` text DEFAULT NULL,
  `Tutor_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quesfaq`
--

INSERT INTO `quesfaq` (`FAQ_nbr`, `question_content`, `answer_question`, `Tutor_ID`) VALUES
(1, 'How to reset my password?', 'You can reset it from your profile page.', 8790908),
(2, 'What to do if my tutor doesn’t reply?', 'Please contact administration after 3 days.', 8790908);

-- --------------------------------------------------------

--
-- Table structure for table `send`
--

CREATE TABLE `send` (
  `Tutor_ID` int(11) NOT NULL,
  `answer_code` int(11) NOT NULL,
  `date_sent` date DEFAULT NULL,
  `time_sent` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `registration_nbr` int(11) NOT NULL,
  `ST_first_name` varchar(50) NOT NULL,
  `ST_last_name` varchar(50) NOT NULL,
  `ST_email_address` text DEFAULT NULL,
  `ST_password` varchar(255) NOT NULL,
  `ST_gender` enum('Male','Female') DEFAULT NULL,
  `ST_Nationality` varchar(50) NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `ST_address` varchar(200) NOT NULL,
  `Tutor_ID` int(11) DEFAULT NULL,
  `status` enum('pending','accepted','rejected') DEFAULT 'pending',
  `profile_image` varchar(255) DEFAULT 'use.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`registration_nbr`, `ST_first_name`, `ST_last_name`, `ST_email_address`, `ST_password`, `ST_gender`, `ST_Nationality`, `date_of_birth`, `ST_address`, `Tutor_ID`, `status`, `profile_image`) VALUES
(293809, 'barhmi', 'ilyes', 'bra@gmail.com', '$2y$10$iiqDpORcexz8vMcK7r8AMeHnuxvWmOa8Z/qTbLB10ueUJLDj1rLu2', 'Male', 'algerian', '2000-03-01', 'gazarnaa', NULL, 'pending', 'user.jpg'),
(293983, 'boudjahem', 'brahim', 'br@gmail.com', '$2y$10$NzpdmMRAE6uBhtfFGktGzup4xS6qU8xTMfdfaQylJHrRBz55BkzJq', 'Male', 'algerian', '2000-03-02', 'gu', NULL, 'pending', 'user.jpg'),
(999999, 'kac', 'ay', 'au@gmail.com', '$2y$10$CP5gVzP8DrDVDDYbMyWDpeOJD4Tzpr9T99cmKnpYqbsjrebOvKmVy', 'Female', 'algerine', '2003-04-02', 'gazarnaa', 8790908, '', 'img_6835f8388e88f0.27259799.jpg'),
(1234739, 'ouammar', 'nesrine', 'lo@gmail.com', '$2y$10$ASe.pvzzRqURxOBp.L51Kuk0fYI9oaMjGZKyXMUKgepF3lst6Mc1O', 'Female', 'algerian', '2005-12-25', 'guelma', NULL, 'pending', 'user.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `tutor`
--

CREATE TABLE `tutor` (
  `Tutor_ID` int(11) NOT NULL,
  `first_nameT` varchar(255) DEFAULT NULL,
  `last_nameT` varchar(255) DEFAULT NULL,
  `date_of_birthT` date DEFAULT NULL,
  `PasswordT` varchar(255) DEFAULT NULL,
  `Email_addressT` varchar(255) DEFAULT NULL,
  `phone_numberT` varchar(255) DEFAULT NULL,
  `quality` enum('professor','master_student') DEFAULT NULL,
  `gender` enum('Male','Female') DEFAULT NULL,
  `Address` text DEFAULT NULL,
  `statusT` enum('pending','accepted','rejected') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tutor`
--

INSERT INTO `tutor` (`Tutor_ID`, `first_nameT`, `last_nameT`, `date_of_birthT`, `PasswordT`, `Email_addressT`, `phone_numberT`, `quality`, `gender`, `Address`, `statusT`) VALUES
(8790908, 'Lafifi', 'Yacine', '1975-02-02', NULL, 'lafifi@gmail.com', '06769823', '', 'Male', 'guelma', 'pending');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ask`
--
ALTER TABLE `ask`
  ADD PRIMARY KEY (`quest_nbr`,`registration_nbr`),
  ADD KEY `Registration_nbr` (`registration_nbr`);

--
-- Indexes for table `associate`
--
ALTER TABLE `associate`
  ADD PRIMARY KEY (`answer_code`,`ques_nbr`),
  ADD KEY `ques_nbr` (`ques_nbr`);

--
-- Indexes for table `consult`
--
ALTER TABLE `consult`
  ADD PRIMARY KEY (`FAQ_nbr`,`registration_nbr`),
  ADD KEY `Registration_nbr` (`registration_nbr`);

--
-- Indexes for table `meeting`
--
ALTER TABLE `meeting`
  ADD PRIMARY KEY (`registration_nbr`,`Tutor_ID`,`Meeting_date`),
  ADD KEY `Registration_nbr` (`registration_nbr`);

--
-- Indexes for table `message_answer`
--
ALTER TABLE `message_answer`
  ADD PRIMARY KEY (`answer_code`),
  ADD KEY `fk_quest_nbr` (`quest_nbr`);

--
-- Indexes for table `message_quest`
--
ALTER TABLE `message_quest`
  ADD PRIMARY KEY (`quest_nbr`);

--
-- Indexes for table `quesfaq`
--
ALTER TABLE `quesfaq`
  ADD PRIMARY KEY (`FAQ_nbr`),
  ADD KEY `Tutor_ID` (`Tutor_ID`);

--
-- Indexes for table `send`
--
ALTER TABLE `send`
  ADD PRIMARY KEY (`Tutor_ID`,`answer_code`),
  ADD KEY `answer_code` (`answer_code`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`registration_nbr`),
  ADD KEY `Tutor_ID` (`Tutor_ID`);

--
-- Indexes for table `tutor`
--
ALTER TABLE `tutor`
  ADD PRIMARY KEY (`Tutor_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `consult`
--
ALTER TABLE `consult`
  MODIFY `FAQ_nbr` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `message_answer`
--
ALTER TABLE `message_answer`
  MODIFY `answer_code` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `message_quest`
--
ALTER TABLE `message_quest`
  MODIFY `quest_nbr` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `quesfaq`
--
ALTER TABLE `quesfaq`
  MODIFY `FAQ_nbr` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `student`
--
ALTER TABLE `student`
  MODIFY `registration_nbr` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2147483648;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ask`
--
ALTER TABLE `ask`
  ADD CONSTRAINT `ask_ibfk_1` FOREIGN KEY (`quest_nbr`) REFERENCES `message_quest` (`quest_nbr`),
  ADD CONSTRAINT `ask_ibfk_2` FOREIGN KEY (`Registration_nbr`) REFERENCES `student` (`registration_nbr`);

--
-- Constraints for table `associate`
--
ALTER TABLE `associate`
  ADD CONSTRAINT `associate_ibfk_1` FOREIGN KEY (`answer_code`) REFERENCES `message_answer` (`answer_code`),
  ADD CONSTRAINT `associate_ibfk_2` FOREIGN KEY (`ques_nbr`) REFERENCES `message_quest` (`quest_nbr`);

--
-- Constraints for table `consult`
--
ALTER TABLE `consult`
  ADD CONSTRAINT `consult_ibfk_1` FOREIGN KEY (`FAQ_nbr`) REFERENCES `quesfaq` (`FAQ_nbr`),
  ADD CONSTRAINT `consult_ibfk_2` FOREIGN KEY (`Registration_nbr`) REFERENCES `student` (`registration_nbr`);

--
-- Constraints for table `meeting`
--
ALTER TABLE `meeting`
  ADD CONSTRAINT `meeting_ibfk_1` FOREIGN KEY (`Tutor_ID`) REFERENCES `tutor` (`Tutor_ID`),
  ADD CONSTRAINT `meeting_ibfk_2` FOREIGN KEY (`Registration_nbr`) REFERENCES `student` (`registration_nbr`);

--
-- Constraints for table `message_answer`
--
ALTER TABLE `message_answer`
  ADD CONSTRAINT `fk_quest_nbr` FOREIGN KEY (`quest_nbr`) REFERENCES `message_quest` (`quest_nbr`) ON DELETE CASCADE;

--
-- Constraints for table `quesfaq`
--
ALTER TABLE `quesfaq`
  ADD CONSTRAINT `quesfaq_ibfk_1` FOREIGN KEY (`Tutor_ID`) REFERENCES `tutor` (`Tutor_ID`);

--
-- Constraints for table `send`
--
ALTER TABLE `send`
  ADD CONSTRAINT `send_ibfk_1` FOREIGN KEY (`Tutor_ID`) REFERENCES `tutor` (`Tutor_ID`),
  ADD CONSTRAINT `send_ibfk_2` FOREIGN KEY (`answer_code`) REFERENCES `message_answer` (`answer_code`);

--
-- Constraints for table `student`
--
ALTER TABLE `student`
  ADD CONSTRAINT `student_ibfk_1` FOREIGN KEY (`Tutor_ID`) REFERENCES `tutor` (`Tutor_ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
