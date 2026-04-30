-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 30, 2026 at 12:40 PM
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
-- Database: `team_project`
--

-- --------------------------------------------------------

--
-- Table structure for table `attachments`
--

CREATE TABLE `attachments` (
  `id` int(11) NOT NULL,
  `related_type` enum('issue','task','report') DEFAULT NULL,
  `related_id` int(11) DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `file_size` varchar(50) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attachments`
--

INSERT INTO `attachments` (`id`, `related_type`, `related_id`, `file_name`, `file_path`, `file_size`, `uploaded_at`) VALUES
(73, 'report', 14, '2022-10-14 (1).jpg', 'uploads/1775820172_69d8dd8c666f9.jpg', NULL, '2026-04-10 11:22:52'),
(74, 'report', 14, '2022-10-14 (2).jpg', 'uploads/1775820172_69d8dd8c672ae.jpg', NULL, '2026-04-10 11:22:52'),
(75, 'report', 14, '2022-10-14.jpg', 'uploads/1775820172_69d8dd8c67f8a.jpg', NULL, '2026-04-10 11:22:52'),
(79, 'issue', 15, '3223c01d91948d24d76d09c6ed2c64e2.jpg', 'uploads/1776397398_3223c01d91948d24d76d09c6ed2c64e2.jpg', '6.24 KB', '2026-04-17 03:43:18'),
(80, 'issue', 15, '442469-eagle-flying.jpg', 'uploads/1776397398_442469-eagle-flying.jpg', '328.6 KB', '2026-04-17 03:43:18'),
(81, 'issue', 15, '1677918083335.jpg', 'uploads/1776397398_1677918083335.jpg', '3467.57 KB', '2026-04-17 03:43:18'),
(90, 'issue', 15, 'background-640x360.jpg', 'uploads/1776415076_background-640x360.jpg', '55.01 KB', '2026-04-17 08:37:56'),
(91, 'issue', 15, 'DSC05745-01-1.jpeg', 'uploads/1776415076_DSC05745-01-1.jpeg', '221.72 KB', '2026-04-17 08:37:56'),
(92, 'issue', 15, 'foto cv formal.jpg', 'uploads/1776415076_foto cv formal.jpg', '23.65 KB', '2026-04-17 08:37:56'),
(93, 'issue', 17, 'background-640x360.jpg', 'uploads/1777021816_background-640x360.jpg', '55.01 KB', '2026-04-24 09:10:16'),
(94, 'issue', 5, 'aesthetic-computer-4k-c9qdhe02pr84wh3a.jpg', 'uploads/1777021992_aesthetic-computer-4k-c9qdhe02pr84wh3a.jpg', '34.49 KB', '2026-04-24 09:13:12'),
(97, 'issue', 18, 'istockphoto-1317257861-170667a.jpg', 'uploads/1777451801_istockphoto-1317257861-170667a.jpg', '109.16 KB', '2026-04-29 08:36:41'),
(98, 'issue', 18, 'ss tes.PNG', 'uploads/1777536207_ss tes.PNG', '88.35 KB', '2026-04-30 08:03:27'),
(99, 'issue', 18, 'thumb-1920-577684.jpg', 'uploads/1777536207_thumb-1920-577684.jpg', '53.33 KB', '2026-04-30 08:03:27'),
(100, 'issue', 18, 'tsukuyomi.jpg.jpeg', 'uploads/1777536207_tsukuyomi.jpg.jpeg', '162.7 KB', '2026-04-30 08:03:27'),
(101, 'issue', 18, 'wallpaperflare.com_wallpaper.jpg', 'uploads/1777536207_wallpaperflare.com_wallpaper.jpg', '37.77 KB', '2026-04-30 08:03:27'),
(109, 'issue', 22, 'WIN_20240118_11_38_32_Pro.jpg', 'uploads/1777545103_WIN_20240118_11_38_32_Pro.jpg', '154.2 KB', '2026-04-30 10:31:43');

-- --------------------------------------------------------

--
-- Table structure for table `issues`
--

CREATE TABLE `issues` (
  `id` int(11) NOT NULL,
  `project_id` int(11) DEFAULT NULL,
  `issue_type` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `resolution` varchar(255) NOT NULL,
  `priority` enum('low','medium','high','critical') DEFAULT NULL,
  `status` enum('pending','in-progress','completed') DEFAULT NULL,
  `area` varchar(20) DEFAULT NULL,
  `error_code` varchar(100) DEFAULT NULL,
  `system_logs` text DEFAULT NULL,
  `network_status` varchar(255) DEFAULT NULL,
  `root_cause` text DEFAULT NULL,
  `reported_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `issues`
--

INSERT INTO `issues` (`id`, `project_id`, `issue_type`, `description`, `resolution`, `priority`, `status`, `area`, `error_code`, `system_logs`, `network_status`, `root_cause`, `reported_date`, `created_at`) VALUES
(1, 1, 'Access Control System Not Responding', 'Users unable to access entry system', 'Ya dibenerin lah', 'high', 'in-progress', 'room', 'ACS_ERR_001', 'Device timeout logs detected', 'Partial outage', 'Controller malfunction', '2026-01-15', '2026-04-03 01:37:24'),
(2, 1, 'CCTV Camera Connection Lost', 'Camera disconnected from system', 'Ya dibenerin lah', 'medium', 'completed', 'room', 'CCTV_ERR_002', 'Connection lost logs', 'Stable', 'Loose cable', '2026-01-10', '2026-04-03 01:37:24'),
(3, 1, 'Smart Lighting Flickering Issue', 'Lights flickering intermittently', 'Ya dibenerin lah', 'low', 'pending', 'room', 'LIGHT_ERR_003', 'Voltage fluctuation detected', 'Stable', 'Power inconsistency', '2026-01-20', '2026-04-03 01:37:24'),
(4, 1, 'Network Connectivity Issues', 'Network affecting access control system', 'Ya dibenerin lah', 'critical', 'completed', 'room', 'NET_CONN_045', 'Connection timeout errors detected', 'Partial outage - 60% affected', 'Network switch configuration error', '2026-01-06', '2026-04-03 01:37:24'),
(5, 1, 'Test Backend Issue', 'Testing insert from PHP\r\nTesting insert from PHP\r\n\r\nTesting insert from PHP\r\n', 'Ya dibenerin lah\r\nYa dibenerin lah\r\n\r\nYa dibenerin lah\r\n', 'high', 'pending', 'room', '', '', '', '', '2026-04-04', '2026-04-03 01:55:55');

-- --------------------------------------------------------

--
-- Table structure for table `issue_notes`
--

CREATE TABLE `issue_notes` (
  `id` int(11) NOT NULL,
  `issue_id` int(11) DEFAULT NULL,
  `author` varchar(100) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `issue_notes`
--

INSERT INTO `issue_notes` (`id`, `issue_id`, `author`, `content`, `created_at`) VALUES
(1, 4, 'Network Team', 'Issue reported by multiple users. Initial assessment shows network connectivity problems affecting access control systems.', '2026-01-06 07:45:00'),
(2, 4, 'John Technician', 'Root cause identified as switch configuration error. Temporary bypass implemented.', '2026-01-06 09:30:00'),
(15, 5, 'abah', 'apee', '2026-04-10 09:34:33');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `status` enum('active','completed','on-hold') DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `pic` varchar(30) DEFAULT NULL,
  `progress` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `name`, `status`, `start_date`, `end_date`, `pic`, `progress`, `description`, `created_at`) VALUES
(1, 'Tower Bersama Indonesia Group - Bali', 'active', '2026-01-01', '2026-04-30', 'Arifin', 45, 'Installation and integration of smart building systems for Bali location', '2026-04-03 01:37:24'),
(2, 'Menara Mandiri', 'completed', '2026-04-05', '2026-04-30', 'Renaldy', 55, 'rwgsdadsd', '2026-04-05 07:50:19');

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `project_id` int(11) DEFAULT NULL,
  `progress_summary` text DEFAULT NULL,
  `report_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`id`, `project_id`, `progress_summary`, `report_date`, `created_at`) VALUES
(1, 1, 'Weekly Progress Report - Week 1', '2026-01-04', '2026-04-03 01:37:24'),
(2, 1, 'Weekly Progress Report - Week 2', '2026-01-06', '2026-04-03 01:37:24'),
(14, 1, 'asdasd', '2026-04-10', '2026-04-07 09:40:25');

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `id` int(11) NOT NULL,
  `task_name` varchar(255) NOT NULL,
  `description` varchar(1000) NOT NULL,
  `project` varchar(255) NOT NULL,
  `schedule_date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `assigned_to` varchar(255) DEFAULT NULL,
  `priority` enum('low','medium','high') DEFAULT 'medium',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedules`
--

INSERT INTO `schedules` (`id`, `task_name`, `description`, `project`, `schedule_date`, `start_time`, `assigned_to`, `priority`, `created_at`) VALUES
(15, 'Installasi Speaker Ceiling', 'asdkadadoasdoia osdaoksndoandoanwdo iawdawdawd adadaw', 'Akraya', '2026-04-27', '12:59:00', 'Melody', 'high', '2026-04-24 05:59:33'),
(17, 'asadasd', 'asdasda', 'asdasd', '2026-05-23', '08:26:00', 'qwqw', 'medium', '2026-04-30 10:25:48');

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `project_id` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` varchar(1000) DEFAULT NULL,
  `type` enum('installation','programming') DEFAULT NULL,
  `status` enum('pending','in-progress','completed') DEFAULT NULL,
  `progress` int(11) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `area` varchar(255) DEFAULT NULL,
  `priority` enum('low','medium','high','critical') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `project_id`, `title`, `description`, `type`, `status`, `progress`, `start_date`, `end_date`, `created_at`, `area`, `priority`) VALUES
(1, 1, 'Install Access Control System', 'Setup access control devices at main entrance', 'installation', '', 90, '2026-01-02', '2026-01-05', '2026-04-03 01:37:24', 'Room', 'low'),
(2, 1, 'Install CCTV Cameras', 'Install CCTV across building A and B', 'installation', '', 85, '2026-01-06', '2026-01-20', '2026-04-03 01:37:24', 'asd', 'critical'),
(3, 1, 'Program Smart Lighting', 'Configure smart lighting automation system', 'programming', 'in-progress', 10, '2026-01-10', '2026-02-10', '2026-04-03 01:37:24', 'Room', 'high'),
(4, 1, 'System Testing Setup', 'Prepare testing scripts for system validation', 'programming', 'pending', 0, '2026-02-01', '2026-02-20', '2026-04-03 01:37:24', 'Room', 'critical'),
(5, 1, 'control dsp', 'asdasd', 'programming', 'completed', 100, '2026-04-05', '2026-04-21', '2026-04-05 22:14:40', 'Room', 'medium'),
(6, 1, 'Interkoneksi CP4', 'hello nama kamu siapa', 'installation', 'completed', 100, '2026-04-09', '2026-04-30', '2026-04-05 22:15:19', 'Room', 'high'),
(18, 1, 'Installasi Speaker Ceiling', 'install yang bner ya speakernya', 'installation', 'in-progress', 12, '2026-04-13', '2026-04-19', '2026-04-10 07:34:52', 'Room', 'critical'),
(27, NULL, NULL, 'adasds', NULL, '', 12, '2026-04-09', '2026-04-17', '2026-04-10 09:04:52', 'qweqe', 'critical'),
(28, NULL, NULL, 'qdas', NULL, '', 12, '2026-04-17', '2026-04-11', '2026-04-10 09:05:13', 'asad', 'critical'),
(29, NULL, NULL, 'asdasdads', NULL, '', 12, '2026-04-24', '2026-04-22', '2026-04-10 09:06:11', 'asdasd', 'critical');

-- --------------------------------------------------------

--
-- Table structure for table `task_specifications`
--

CREATE TABLE `task_specifications` (
  `id` int(11) NOT NULL,
  `task_id` int(11) DEFAULT NULL,
  `cable_type` varchar(100) DEFAULT NULL,
  `cable_length` float DEFAULT NULL,
  `connection_type` varchar(100) DEFAULT NULL,
  `cable_label` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `task_specifications`
--

INSERT INTO `task_specifications` (`id`, `task_id`, `cable_type`, `cable_length`, `connection_type`, `cable_label`) VALUES
(1, 6, 'cat6', 25, 'rj45', 'cp4.rj45'),
(3, 2, '', 0, '', ''),
(4, 1, '', 0, '', ''),
(8, 18, 'cable audio', 20, 'euroblock', 'spk01');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attachments`
--
ALTER TABLE `attachments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `issues`
--
ALTER TABLE `issues`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `issue_notes`
--
ALTER TABLE `issue_notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `issue_id` (`issue_id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `task_specifications`
--
ALTER TABLE `task_specifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_id` (`task_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attachments`
--
ALTER TABLE `attachments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=112;

--
-- AUTO_INCREMENT for table `issues`
--
ALTER TABLE `issues`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `issue_notes`
--
ALTER TABLE `issue_notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `task_specifications`
--
ALTER TABLE `task_specifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `issues`
--
ALTER TABLE `issues`
  ADD CONSTRAINT `issues_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `issue_notes`
--
ALTER TABLE `issue_notes`
  ADD CONSTRAINT `issue_notes_ibfk_1` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `task_specifications`
--
ALTER TABLE `task_specifications`
  ADD CONSTRAINT `task_specifications_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
