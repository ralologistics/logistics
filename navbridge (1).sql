-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 10, 2026 at 09:00 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.1.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `navbridge`
--

-- --------------------------------------------------------

--
-- Table structure for table `additional_services`
--

CREATE TABLE `additional_services` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(120) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_name` varchar(160) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_code` varchar(40) DEFAULT NULL,
  `name` varchar(160) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dg_types`
--

CREATE TABLE `dg_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `import_job_bookings`
--

CREATE TABLE `import_job_bookings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `customer_name_code` varchar(255) NOT NULL,
  `document_received_at` datetime NOT NULL,
  `document_upload_path` text DEFAULT NULL,
  `reference_no` varchar(100) NOT NULL,
  `container_no` varchar(50) NOT NULL,
  `iso_code` varchar(20) NOT NULL,
  `weight_kg` decimal(10,2) NOT NULL,
  `from_location` varchar(255) NOT NULL,
  `to_location` varchar(255) NOT NULL,
  `return_to_location` varchar(255) DEFAULT NULL,
  `customer_location_grid` varchar(100) DEFAULT NULL,
  `door_type` varchar(100) DEFAULT NULL,
  `security_check` varchar(100) DEFAULT NULL,
  `random_number` varchar(100) DEFAULT NULL,
  `release_ecn_number` varchar(100) DEFAULT NULL,
  `port_pin_no` varchar(100) DEFAULT NULL,
  `available_date` date DEFAULT NULL,
  `vb_slot_date` date DEFAULT NULL,
  `demurrage_date` date DEFAULT NULL,
  `detention_days` int(11) NOT NULL DEFAULT 0,
  `shipping` varchar(255) DEFAULT NULL,
  `vessel` varchar(255) DEFAULT NULL,
  `voyage` varchar(100) DEFAULT NULL,
  `is_xray` tinyint(1) NOT NULL DEFAULT 0,
  `is_dgs` tinyint(1) NOT NULL DEFAULT 0,
  `is_live_ul` tinyint(1) NOT NULL DEFAULT 0,
  `hold_sh` tinyint(1) NOT NULL DEFAULT 0,
  `hold_customs` tinyint(1) NOT NULL DEFAULT 0,
  `hold_mpi` tinyint(1) NOT NULL DEFAULT 0,
  `add_notes` varchar(255) DEFAULT NULL,
  `additional_services` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `import_job_bookings`
--

INSERT INTO `import_job_bookings` (`id`, `created_by`, `customer_name_code`, `document_received_at`, `document_upload_path`, `reference_no`, `container_no`, `iso_code`, `weight_kg`, `from_location`, `to_location`, `return_to_location`, `customer_location_grid`, `door_type`, `security_check`, `random_number`, `release_ecn_number`, `port_pin_no`, `available_date`, `vb_slot_date`, `demurrage_date`, `detention_days`, `shipping`, `vessel`, `voyage`, `is_xray`, `is_dgs`, `is_live_ul`, `hold_sh`, `hold_customs`, `hold_mpi`, `add_notes`, `additional_services`, `created_at`, `updated_at`) VALUES
(1, 1, 'Tucker Fisher', '1989-01-20 05:12:00', NULL, 'Aute nulla labore te', 'Aut ullamco quia deb', 'Laboris rerum numqua', 82.00, 'Et libero ad qui tem', 'Doloremque consequat', 'Ad sint eius non in ', 'Nulla tempor tempor ', 'Totam accusamus omni', 'Corporis qui est fu', '353', '416', 'Obcaecati rem offici', '1978-10-02', '2009-03-07', '0000-00-00', 2, 'Reprehenderit minim ', 'Praesentium sit sunt', '0', 1, 0, 1, 1, 0, 0, 'Sunt ullam enim id f', 'Rerum non porro veli', '2025-12-12 13:12:19', '2025-12-12 13:12:19'),
(2, 1, 'Carissa Sutton', '1993-02-01 07:39:00', NULL, 'Amet nostrum libero', 'Ut nihil voluptate v', 'Consequatur fugiat', 29.00, 'Omnis illum nihil q', 'Minim consequatur i', 'Labore qui necessita', 'Esse officiis ea rep', 'Officiis obcaecati v', 'Nihil explicabo Per', '977', '413', 'Qui porro quasi natu', '1979-11-10', '1997-05-26', '0000-00-00', 16, 'Culpa placeat aute ', 'Corrupti velit dui', '0', 0, 0, 1, 1, 1, 1, 'Impedit esse accusa', 'Sint culpa tempor ', '2025-12-12 14:25:23', '2025-12-12 14:25:23'),
(3, 1, 'Vanna Hayes', '1981-07-02 21:28:00', NULL, 'Ut minima error mole', 'Possimus voluptate ', 'Consequat Aut sint ', 81.00, 'Ad molestias ea sunt', 'Pariatur Aute id t', 'Saepe consequatur ea', 'Nam atque do nulla d', 'Possimus assumenda ', 'Qui maxime exercitat', '484', '826', 'Rerum rerum dolorem ', '2006-06-03', '1998-05-15', '0000-00-00', 3, 'Tenetur ea et nesciu', 'Et velit eligendi ob', '0', 1, 1, 1, 1, 1, 1, 'Aliquid eos provide', 'Velit qui irure qui ', '2025-12-12 14:25:54', '2025-12-12 14:25:54'),
(4, 1, 'Raya Nunez', '2024-04-07 03:47:00', NULL, 'Raya Nunez', 'Voluptate praesentiu', 'Debitis quo beatae v', 21.00, 'Reprehenderit non sa', 'Officia sed ex elit', 'Ea mollitia officiis', 'Quisquam aut molliti', 'Consectetur debitis ', 'Ipsum qui id itaque', '712', '598', 'Velit enim et quidem', '1984-05-22', '2010-08-29', '0000-00-00', 12, 'Consequat Rem archi', 'Provident tempore ', '0', 1, 0, 1, 1, 0, 0, 'Aspernatur aut neque', 'Ut blanditiis est pr', '2025-12-12 14:26:23', '2025-12-12 14:26:23'),
(5, 1, 'Erin Mccarty', '1998-11-05 21:11:00', NULL, 'Aut est dolor nihil', 'Asperiores dolore ex', 'Consequat Ut quidem', 49.00, 'Velit laboriosam es', 'Est nisi exercitati', 'Dolorem quaerat qui ', 'Praesentium et vel e', 'Labore sunt ipsa vo', 'Accusamus enim aliqu', '542', '68', 'Dolore eum dolorem v', '2024-07-29', '2009-08-14', '0000-00-00', 8, 'Consequat At autem ', 'Iure asperiores eius', '0', 1, 0, 1, 0, 0, 1, 'Nihil excepturi eum ', 'Laborum Nulla dolor', '2025-12-16 08:25:12', '2025-12-16 08:25:12'),
(6, 1, 'Lavinia Leon', '2012-01-24 15:58:00', NULL, 'This is reference', 'Dignissimos ut est a', 'In iste eos nesciun', 18.00, 'Libero sunt non faci', 'Nam rem corrupti no', 'Laboris velit volupt', 'In deleniti eveniet', 'Ex sit sint praesent', 'Optio totam non est', '993', '324', 'Nostrum voluptates a', '2022-01-16', '2013-01-20', '0000-00-00', 5, 'Sit fugiat molesti', 'Quia maiores suscipi', '0', 1, 0, 0, 1, 0, 1, 'Quasi qui pariatur ', 'Facilis nisi sit su', '2025-12-16 08:35:35', '2025-12-16 08:35:35'),
(7, 1, 'Plato Henson', '1993-02-22 21:36:00', NULL, 'Similique proident ', 'Duis velit perspici', 'Consequatur labore v', 88.00, 'Distinctio Incididu', 'Soluta distinctio C', 'Eveniet anim velit ', 'Dolore quis id ab vo', 'Aut aliquip impedit', 'Eos ea qui consectet', '514', '356', 'Fugiat cum recusand', '2014-05-22', '2017-10-27', '0000-00-00', 5, 'Corporis est dolore ', 'Voluptate aute volup', '0', 0, 1, 1, 1, 0, 1, 'Excepturi porro modi', 'Duis molestiae iste ', '2025-12-20 16:52:06', '2025-12-20 16:52:06');

-- --------------------------------------------------------

--
-- Table structure for table `job_additional_information`
--

CREATE TABLE `job_additional_information` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `job_booking_id` bigint(20) UNSIGNED NOT NULL,
  `insurance_type` varchar(100) DEFAULT NULL,
  `dg_signatory` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_addresses`
--

CREATE TABLE `job_addresses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `country_code` char(2) DEFAULT NULL,
  `name` varchar(120) DEFAULT NULL,
  `building` varchar(80) DEFAULT NULL,
  `street_no` varchar(20) DEFAULT NULL,
  `street` varchar(120) DEFAULT NULL,
  `suburb` varchar(80) DEFAULT NULL,
  `city_town` varchar(80) DEFAULT NULL,
  `state` varchar(80) DEFAULT NULL,
  `postcode` varchar(20) DEFAULT NULL,
  `contact_person` varchar(120) DEFAULT NULL,
  `mobile` varchar(30) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `email` varchar(160) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `job_addresses`
--

INSERT INTO `job_addresses` (`id`, `country_code`, `name`, `building`, `street_no`, `street`, `suburb`, `city_town`, `state`, `postcode`, `contact_person`, `mobile`, `phone`, `email`, `created_at`) VALUES
(5, 'PK', 'Travis Carver', 'In a accusamus ea ab', 'Eum voluptas veniam', 'Excepteur laboriosam', 'Facilis et cum venia', 'Beatae fuga Autem e', 'Assumenda magni volu', 'Cumque quia elit du', 'Vitae omnis nostrud', 'Eu sed modi dolor hi', '+1 (375) 695-6793', 'kuxerima@mailinator.com', '2026-01-10 06:35:57'),
(6, 'PK', 'Kai Solis', 'Reprehenderit volup', 'Sunt quaerat quas fu', 'Lorem nihil consecte', 'Labore sed molestiae', 'In qui rerum nisi ve', 'Ut cupiditate rerum', 'Quia quo laboris qui', 'Sunt illum distinct', 'Pariatur Laboris se', '+1 (487) 113-5453', 'ximocobar@mailinator.com', '2026-01-10 06:35:57');

-- --------------------------------------------------------

--
-- Table structure for table `job_attachments`
--

CREATE TABLE `job_attachments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `job_booking_id` bigint(20) UNSIGNED NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `mime_type` varchar(120) DEFAULT NULL,
  `file_size_bytes` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_bookings`
--

CREATE TABLE `job_bookings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `customer_reference` varchar(80) DEFAULT NULL,
  `receiver_reference` varchar(80) DEFAULT NULL,
  `freight_ready_by` datetime DEFAULT NULL,
  `sender_address_id` bigint(20) UNSIGNED NOT NULL,
  `receiver_address_id` bigint(20) UNSIGNED NOT NULL,
  `pickup_instruction` varchar(500) DEFAULT NULL,
  `delivery_instruction` varchar(500) DEFAULT NULL,
  `signature_required` tinyint(1) NOT NULL DEFAULT 0,
  `status` varchar(30) NOT NULL DEFAULT 'draft',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_packages`
--

CREATE TABLE `job_packages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `job_booking_id` bigint(20) UNSIGNED NOT NULL,
  `package_name` varchar(80) DEFAULT NULL,
  `units` int(10) UNSIGNED NOT NULL,
  `weight_kg` decimal(10,3) NOT NULL,
  `length_cm` decimal(10,2) DEFAULT NULL,
  `width_cm` decimal(10,2) DEFAULT NULL,
  `height_cm` decimal(10,2) DEFAULT NULL,
  `cubic_m3` decimal(10,4) DEFAULT NULL,
  `package_type_id` bigint(20) UNSIGNED NOT NULL,
  `dg_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_tracking_notifications`
--

CREATE TABLE `job_tracking_notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `job_booking_id` bigint(20) UNSIGNED NOT NULL,
  `communication_type` varchar(50) DEFAULT NULL,
  `communication_detail` varchar(255) DEFAULT NULL,
  `notification_type` varchar(100) DEFAULT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `package_types`
--

CREATE TABLE `package_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `permission_key` varchar(150) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `permission_key`, `description`, `created_at`, `updated_at`) VALUES
(1, 'add_import_jobs', 'Permission to add new import job records', '2025-12-03 05:53:16', '2025-12-03 05:53:16'),
(2, 'view_import_jobs', 'Permission to view import job records', '2025-12-03 05:53:16', '2025-12-03 05:53:16');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role_name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `role_name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Manager', 'User responsible for managing import jobs', '2025-12-03 05:53:02', '2025-12-03 05:53:02');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `permission_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role_permissions`
--

INSERT INTO `role_permissions` (`id`, `role_id`, `permission_id`) VALUES
(1, 1, 1),
(2, 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Johnny Do', 'john.doe@example.com', 'f91e15dbec69fc40f81f0876e7009648', '1234567890', 1, '2025-12-03 05:53:42', '2025-12-11 05:12:45'),
(2, 'Tatiana Park', 'qiwuha@mailinator.com', '$2y$10$AVg3/lS16Tu9Wz02V1RNOecA/Y5kd78i67DOeierZUV.ZQrSNx4wq', '+1 (973) 556-6612', 1, '2025-12-13 12:57:26', '2025-12-13 12:57:26'),
(3, 'Ivan Hawkins', 'kuhu@mailinator.com', '$2y$10$45L/Rj/9SAHDaCCPStygm.OxmqIISDsTuoyq0CWQXHRtIBaFOuTEa', '+1 (733) 149-9276', 1, '2025-12-13 12:59:29', '2025-12-13 12:59:29'),
(4, 'test', 'test@test.com', '$2y$10$9SM5Tb4VWet6kXie9sru8.eqtFoQAKO4uULFQ71BMGCZ.yu7Br87.', '123456789', 1, '2025-12-16 08:17:58', '2025-12-16 08:17:58'),
(5, 'last', 'last@last.com', '$2y$10$UBnytKAGgI5HaTe0aBRWAONLffNJ7Bmp20TNQcW60wi4Q.xg61P5a', '12345678', 1, '2025-12-20 16:52:32', '2025-12-20 16:52:32');

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`id`, `user_id`, `role_id`) VALUES
(1, 1, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `additional_services`
--
ALTER TABLE `additional_services`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customer_code` (`customer_code`);

--
-- Indexes for table `dg_types`
--
ALTER TABLE `dg_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `import_job_bookings`
--
ALTER TABLE `import_job_bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_import_jobs_created_by` (`created_by`);

--
-- Indexes for table `job_additional_information`
--
ALTER TABLE `job_additional_information`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_job` (`job_booking_id`);

--
-- Indexes for table `job_addresses`
--
ALTER TABLE `job_addresses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `job_attachments`
--
ALTER TABLE `job_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_job` (`job_booking_id`);

--
-- Indexes for table `job_bookings`
--
ALTER TABLE `job_bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_customer` (`customer_id`),
  ADD KEY `idx_company` (`company_id`),
  ADD KEY `idx_freight_ready_by` (`freight_ready_by`);

--
-- Indexes for table `job_packages`
--
ALTER TABLE `job_packages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_job` (`job_booking_id`);

--
-- Indexes for table `job_tracking_notifications`
--
ALTER TABLE `job_tracking_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_job` (`job_booking_id`);

--
-- Indexes for table `package_types`
--
ALTER TABLE `package_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permission_key` (`permission_key`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `role_id` (`role_id`),
  ADD KEY `permission_id` (`permission_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `additional_services`
--
ALTER TABLE `additional_services`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dg_types`
--
ALTER TABLE `dg_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `import_job_bookings`
--
ALTER TABLE `import_job_bookings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `job_additional_information`
--
ALTER TABLE `job_additional_information`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `job_addresses`
--
ALTER TABLE `job_addresses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `job_attachments`
--
ALTER TABLE `job_attachments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `job_bookings`
--
ALTER TABLE `job_bookings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `job_packages`
--
ALTER TABLE `job_packages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `job_tracking_notifications`
--
ALTER TABLE `job_tracking_notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `package_types`
--
ALTER TABLE `package_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `role_permissions`
--
ALTER TABLE `role_permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `import_job_bookings`
--
ALTER TABLE `import_job_bookings`
  ADD CONSTRAINT `fk_import_jobs_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `job_additional_information`
--
ALTER TABLE `job_additional_information`
  ADD CONSTRAINT `fk_additional_info_job` FOREIGN KEY (`job_booking_id`) REFERENCES `job_bookings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `job_attachments`
--
ALTER TABLE `job_attachments`
  ADD CONSTRAINT `fk_attach_job` FOREIGN KEY (`job_booking_id`) REFERENCES `job_bookings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `job_packages`
--
ALTER TABLE `job_packages`
  ADD CONSTRAINT `fk_pkg_job` FOREIGN KEY (`job_booking_id`) REFERENCES `job_bookings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `job_tracking_notifications`
--
ALTER TABLE `job_tracking_notifications`
  ADD CONSTRAINT `fk_track_job` FOREIGN KEY (`job_booking_id`) REFERENCES `job_bookings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `user_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
