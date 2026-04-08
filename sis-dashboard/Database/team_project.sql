-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 08, 2026 at 02:28 AM
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
(3, 'issue', 4, 'Equipment Photos.zip', '/uploads/equipment_photos.zip', '5.8 MB', '2026-04-03 01:37:24'),
(14, 'issue', 4, '6.png', 'uploads/1775527366_6.png', '58.18 KB', '2026-04-07 02:02:46'),
(15, 'issue', 4, '5.png', 'uploads/1775527366_5.png', '57.31 KB', '2026-04-07 02:02:46'),
(16, 'issue', 4, '4.png', 'uploads/1775527366_4.png', '58.18 KB', '2026-04-07 02:02:46'),
(17, 'issue', 4, '3.png', 'uploads/1775527366_3.png', '409.13 KB', '2026-04-07 02:02:46'),
(45, 'issue', 5, '6.png', 'uploads/1775551621_6.png', '58.18 KB', '2026-04-07 08:47:01'),
(46, 'issue', 5, '5.png', 'uploads/1775551621_5.png', '57.31 KB', '2026-04-07 08:47:01'),
(47, 'issue', 5, '4.png', 'uploads/1775551621_4.png', '58.18 KB', '2026-04-07 08:47:01'),
(48, 'issue', 5, '3.png', 'uploads/1775551621_3.png', '409.13 KB', '2026-04-07 08:47:01'),
(55, 'report', 14, 'KNX Integration.pdf', 'uploads/1775554825_69d4d109457fe.pdf', '0.89 MB', '2026-04-07 09:40:25'),
(56, 'report', 14, '6.png', 'uploads/1775554825_69d4d1094673f.png', '0.06 MB', '2026-04-07 09:40:25'),
(57, 'report', 14, '4.png', 'uploads/1775584168_69d543a87ce57.png', NULL, '2026-04-07 17:49:28');

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
  `reported_date` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `issues`
--

INSERT INTO `issues` (`id`, `project_id`, `issue_type`, `description`, `resolution`, `priority`, `status`, `area`, `error_code`, `system_logs`, `network_status`, `root_cause`, `reported_date`, `created_at`) VALUES
(1, 1, 'Access Control System Not Responding', 'Users unable to access entry system', 'Ya dibenerin lah', 'high', 'in-progress', 'room', 'ACS_ERR_001', 'Device timeout logs detected', 'Partial outage', 'Controller malfunction', '2026-01-15 10:00:00', '2026-04-03 01:37:24'),
(2, 1, 'CCTV Camera Connection Lost', 'Camera disconnected from system', 'Ya dibenerin lah', 'medium', 'completed', 'room', 'CCTV_ERR_002', 'Connection lost logs', 'Stable', 'Loose cable', '2026-01-10 09:00:00', '2026-04-03 01:37:24'),
(3, 1, 'Smart Lighting Flickering Issue', 'Lights flickering intermittently', 'Ya dibenerin lah', 'low', 'pending', 'room', 'LIGHT_ERR_003', 'Voltage fluctuation detected', 'Stable', 'Power inconsistency', '2026-01-20 11:00:00', '2026-04-03 01:37:24'),
(4, 1, 'Network Connectivity Issues', 'Network affecting access control system', 'Ya dibenerin lah', 'critical', 'completed', 'room', 'NET_CONN_045', 'Connection timeout errors detected', 'Partial outage - 60% affected', 'Network switch configuration error', '2026-01-06 14:00:00', '2026-04-03 01:37:24'),
(5, 1, 'Test Backend Issue', 'Testing insert from PHP\r\nTesting insert from PHP\r\n\r\nTesting insert from PHP\r\n', 'Ya dibenerin lah\r\nYa dibenerin lah\r\n\r\nYa dibenerin lah\r\n', 'low', 'pending', 'room', '', '', '', '', '2026-04-03 03:55:00', '2026-04-03 01:55:55');

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
(9, 5, 'Fahmi', 'vintek pantek', '2026-04-07 18:50:34'),
(13, 5, 'fahmi', 'woi', '2026-04-07 23:49:10');

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
  `team_size` int(11) DEFAULT NULL,
  `progress` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `name`, `status`, `start_date`, `end_date`, `team_size`, `progress`, `description`, `created_at`) VALUES
(1, 'Tower Bersama Indonesia Group - Bali', 'active', '2026-01-01', '2026-04-30', 5, 45, 'Installation and integration of smart building systems for Bali location', '2026-04-03 01:37:24'),
(2, 'Menara Mandiri', 'completed', '2026-04-05', '2026-04-30', 6, 55, 'rwgsdadsd', '2026-04-05 07:50:19');

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
(1, 1, 'Weekly Progress Report - Week 1', '2026-01-07', '2026-04-03 01:37:24'),
(2, 1, 'Weekly Progress Report - Week 2', '2026-01-14', '2026-04-03 01:37:24'),
(14, 1, 'asdasd', '2026-04-10', '2026-04-07 09:40:25');

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `id` int(11) NOT NULL,
  `task_name` varchar(255) NOT NULL,
  `description` varchar(1000) NOT NULL,
  `project_id` int(11) DEFAULT NULL,
  `schedule_date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `assigned_to` varchar(255) DEFAULT NULL,
  `priority` enum('low','medium','high') DEFAULT 'medium',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedules`
--

INSERT INTO `schedules` (`id`, `task_name`, `description`, `project_id`, `schedule_date`, `start_time`, `assigned_to`, `priority`, `created_at`) VALUES
(1, 'Network Setup', 'Lakukan Maintenance bla bla bla lorem ipsum bla bla bla \r\nLakukan Maintenance bla bla bla lorem ipsum bla bla bla \r\nLakukan Maintenance bla bla bla lorem ipsum bla bla bla \r\nLakukan Maintenance bla bla bla lorem ipsum bla bla bla \r\n', 1, '2026-04-10', '08:00:00', 'Melody', 'high', '2026-04-07 18:33:31'),
(2, 'Testing System', 'Lakukan Maintenance bla bla bla lorem ipsum bla bla bla ', 1, '2026-04-15', '09:00:00', 'Budi', 'medium', '2026-04-07 18:33:31'),
(3, 'Deployment', 'Lakukan Maintenance bla bla bla lorem ipsum bla bla bla ', 2, '2026-04-20', '10:00:00', 'Rudi', 'high', '2026-04-07 18:33:31'),
(5, 'asdasdasd', 'asdasd adwa dawdawd awdad aw\r\n\r\n\r\nasdas dawdaw dawd\r\n\r\nasdasdasd', 1, '2026-04-08', '06:00:00', 'Melody REEE', 'medium', '2026-04-07 22:13:40');

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
(1, 1, 'Install Access Control System', 'Setup access control devices at main entrance', 'installation', 'in-progress', 90, '2026-01-02', '2026-01-05', '2026-04-03 01:37:24', 'Room', 'low'),
(2, 1, 'Install CCTV Cameras', 'Install CCTV across building A and B', 'installation', 'in-progress', 85, '2026-01-06', '2026-01-20', '2026-04-03 01:37:24', 'asd', 'critical'),
(3, 1, 'Program Smart Lighting', 'Configure smart lighting automation system', 'programming', 'in-progress', 10, '2026-01-10', '2026-02-10', '2026-04-03 01:37:24', 'Room', 'high'),
(4, 1, 'System Testing Setup', 'Prepare testing scripts for system validation', 'programming', 'pending', 0, '2026-02-01', '2026-02-20', '2026-04-03 01:37:24', 'Room', 'critical'),
(5, 1, 'control dsp', 'asdasd', 'programming', 'completed', 100, '2026-04-05', '2026-04-21', '2026-04-05 22:14:40', 'Room', 'medium'),
(6, 1, 'Interkoneksi CP4', 'asdasdasd\r\nqweqw\r\n\r\nqweqwe', 'installation', 'pending', 25, '2026-04-09', '2026-04-30', '2026-04-05 22:15:19', 'Room', 'high'),
(17, 1, '12wqeqwe', 'asdas123\r\n\r\nasdasd\r\nasdsad', 'programming', 'completed', 12, '2026-04-07', '2026-04-09', '2026-04-07 23:04:56', 'qweqwe', 'low');

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
(4, 1, '', 0, '', '');

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
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `issues`
--
ALTER TABLE `issues`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `issue_notes`
--
ALTER TABLE `issue_notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `task_specifications`
--
ALTER TABLE `task_specifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
-- Constraints for table `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `schedules_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`);

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
