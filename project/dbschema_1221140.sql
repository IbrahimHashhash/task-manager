-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 16, 2025 at 02:05 AM
-- Server version: 8.0.40
-- PHP Version: 8.3.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `web1221140_taskDB`
--

-- --------------------------------------------------------

--
-- Table structure for table `addresses`
--

CREATE TABLE `addresses` (
  `address_id` int NOT NULL,
  `flat_no` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `street` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `city` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `country` varchar(100) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `addresses`
--

INSERT INTO `addresses` (`address_id`, `flat_no`, `street`, `city`, `country`) VALUES
(41, '12', 'Irsal street', 'Ramallah', 'Palestinian Authority'),
(42, '12', 'irsal', 'ramallah', 'Palestine'),
(43, '12', '12', '12', '12'),
(44, '12', '12', '12', '12'),
(45, '12', '12', '12', '12');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `project_id` varchar(11) COLLATE utf8mb4_general_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci NOT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `budget` decimal(10,2) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `project_leader_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`project_id`, `title`, `description`, `customer_name`, `budget`, `start_date`, `end_date`, `project_leader_id`) VALUES
('ABCD-12345', 'Proj1', 'test', 'omar', 200.00, '2025-01-15', '2025-01-17', 1000000042);

-- --------------------------------------------------------

--
-- Table structure for table `project_documents`
--

CREATE TABLE `project_documents` (
  `document_id` int NOT NULL,
  `project_id` varchar(11) COLLATE utf8mb4_general_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `task_id` int NOT NULL,
  `task_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci NOT NULL,
  `project_id` varchar(11) COLLATE utf8mb4_general_ci NOT NULL,
  `priority` enum('Low','Medium','High') COLLATE utf8mb4_general_ci NOT NULL,
  `status` enum('Pending','In Progress','Completed') COLLATE utf8mb4_general_ci NOT NULL,
  `due_date` date NOT NULL,
  `start_date` date NOT NULL,
  `progress` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`task_id`, `task_name`, `description`, `project_id`, `priority`, `status`, `due_date`, `start_date`, `progress`) VALUES
(12, 'Test1', 'test', 'ABCD-12345', 'Low', 'In Progress', '2025-01-17', '2025-01-15', 10);

-- --------------------------------------------------------

--
-- Table structure for table `task_assignments`
--

CREATE TABLE `task_assignments` (
  `task_assignment_id` int NOT NULL,
  `task_id` int NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `role` enum('Developer','Designer','Tester','Analyst','Support') COLLATE utf8mb4_general_ci NOT NULL,
  `contribution` int NOT NULL,
  `is_accepted` tinyint(1) DEFAULT '0',
  `progress` int DEFAULT '0'
) ;

--
-- Dumping data for table `task_assignments`
--

INSERT INTO `task_assignments` (`task_assignment_id`, `task_id`, `user_id`, `role`, `contribution`, `is_accepted`, `progress`) VALUES
(31, 12, 1000000043, 'Developer', 20, 1, 0),
(32, 12, 1000000044, 'Designer', 22, 1, 0),
(33, 12, 1000000045, 'Developer', 10, 1, 100);

-- --------------------------------------------------------

--
-- Table structure for table `task_team_members`
--

CREATE TABLE `task_team_members` (
  `task_id` int NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `status` enum('Pending','Accepted','Completed') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Pending',
  `role` enum('Team Member','Leader') COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` bigint UNSIGNED NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `full_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `phone` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `address_id` int NOT NULL,
  `dob` date DEFAULT NULL,
  `idNumber` varchar(8) COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('Manager','Project Leader','Team Member') COLLATE utf8mb4_general_ci NOT NULL,
  `qualification` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `skills` text COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `full_name`, `email`, `phone`, `address_id`, `dob`, `idNumber`, `role`, `qualification`, `skills`) VALUES
(1000000038, 'ibrahim1', 'a123a123', 'Ibrahim Abuhashhash', 'ibrahim@gmail.com', '0594542602', 41, '2003-12-12', '48888888', 'Manager', 'Programmer', 'Problem solving'),
(1000000042, 'A123A123', 'A123A123', 'Omar Khalil', 'test@gmail.com', '0594542632', 42, '2004-01-15', '12345638', 'Project Leader', 'Doplome', 'problem solving'),
(1000000043, 'B123B123', 'B123B123', 'test1 test1', 'test01@gmail.com', '0394542632', 43, '2004-12-28', '11245678', 'Team Member', 'k', 'k'),
(1000000044, 'C123C123', 'C123C123', 'test2 test2', 'test77@gmail.com', '0594333632', 44, '2025-01-01', '12345558', 'Team Member', 'test', 'test'),
(1000000045, 'F123F123', 'F123F123', 'test3 test3', 'test00@gmail.com', '0394542632', 45, '2025-01-17', '92345558', 'Team Member', '123', '123');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`address_id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`project_id`);

--
-- Indexes for table `project_documents`
--
ALTER TABLE `project_documents`
  ADD PRIMARY KEY (`document_id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`task_id`),
  ADD KEY `fk_tasks_project_id` (`project_id`);

--
-- Indexes for table `task_assignments`
--
ALTER TABLE `task_assignments`
  ADD PRIMARY KEY (`task_assignment_id`),
  ADD KEY `task_id` (`task_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `task_team_members`
--
ALTER TABLE `task_team_members`
  ADD PRIMARY KEY (`task_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_user_address` (`address_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `addresses`
--
ALTER TABLE `addresses`
  MODIFY `address_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `project_documents`
--
ALTER TABLE `project_documents`
  MODIFY `document_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `task_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `task_assignments`
--
ALTER TABLE `task_assignments`
  MODIFY `task_assignment_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1000000046;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `project_documents`
--
ALTER TABLE `project_documents`
  ADD CONSTRAINT `project_documents_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE;

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `fk_tasks_project_id` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`);

--
-- Constraints for table `task_assignments`
--
ALTER TABLE `task_assignments`
  ADD CONSTRAINT `task_assignments_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`task_id`),
  ADD CONSTRAINT `task_assignments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `task_team_members`
--
ALTER TABLE `task_team_members`
  ADD CONSTRAINT `task_team_members_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`task_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `task_team_members_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_address` FOREIGN KEY (`address_id`) REFERENCES `addresses` (`address_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_user_address` FOREIGN KEY (`address_id`) REFERENCES `addresses` (`address_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`address_id`) REFERENCES `addresses` (`address_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
