SET FOREIGN_KEY_CHECKS = 0;

START TRANSACTION;

CREATE TABLE IF NOT EXISTS `attendances` (
  `id` INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
  `faculty_id` INT NOT NULL,
  `date` DATE NOT NULL,
  `time_in` DATETIME,
  `time_out` DATETIME,
  `time_in_photo` VARCHAR(255),
  `time_out_photo` VARCHAR(255),
  `time_in_location` VARCHAR(255),
  `time_out_location` VARCHAR(255),
  `total_hours` DECIMAL(10,2) NOT NULL DEFAULT '0.00',
  `status` ENUM('present', 'absent', 'late', 'early_departure', 'half_day') NOT NULL DEFAULT 'present',
  `notes` TEXT,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  FOREIGN KEY (`faculty_id`) REFERENCES `faculties`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `cache` (
  `key` VARCHAR(255) NOT NULL,
  `value` TEXT NOT NULL,
  `expiration` INT NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` VARCHAR(255) NOT NULL,
  `owner` VARCHAR(255) NOT NULL,
  `expiration` INT NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `clearance_requests` (
  `id` INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
  `faculty_id` INT NOT NULL,
  `clearance_type` VARCHAR(255) NOT NULL,
  `reason` TEXT NOT NULL,
  `status` ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
  `admin_remarks` TEXT,
  `requested_at` DATETIME NOT NULL,
  `processed_at` DATETIME,
  `processed_by` INT,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  FOREIGN KEY (`faculty_id`) REFERENCES `faculties`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`processed_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `clearances` (
  `id` INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
  `faculty_id` INT NOT NULL,
  `clearance_type` VARCHAR(255) NOT NULL,
  `issued_date` DATE NOT NULL,
  `expiration_date` DATE,
  `is_cleared` TINYINT(1) NOT NULL DEFAULT 0,
  `remarks` TEXT,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  FOREIGN KEY (`faculty_id`) REFERENCES `faculties`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `evaluations` (
  `id` INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
  `faculty_id` INT NOT NULL,
  `teaching_history_id` INT,
  `evaluation_period` VARCHAR(255) NOT NULL,
  `academic_year` INT NOT NULL,
  `semester` VARCHAR(255) NOT NULL,
  `teaching_effectiveness` DECIMAL(5,2) NOT NULL DEFAULT '0.00',
  `subject_matter_knowledge` DECIMAL(5,2) NOT NULL DEFAULT '0.00',
  `classroom_management` DECIMAL(5,2) NOT NULL DEFAULT '0.00',
  `communication_skills` DECIMAL(5,2) NOT NULL DEFAULT '0.00',
  `student_engagement` DECIMAL(5,2) NOT NULL DEFAULT '0.00',
  `overall_rating` DECIMAL(5,2) NOT NULL DEFAULT '0.00',
  `strengths` TEXT,
  `areas_for_improvement` TEXT,
  `recommendations` TEXT,
  `is_published` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  FOREIGN KEY (`teaching_history_id`) REFERENCES `teaching_histories`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`faculty_id`) REFERENCES `faculties`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `faculties` (
  `id` INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
  `professor_id` VARCHAR(255) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `picture` VARCHAR(255),
  `status` VARCHAR(255) NOT NULL DEFAULT 'active',
  `skills` TEXT,
  `experiences` TEXT,
  `remember_token` VARCHAR(100),
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  `role` VARCHAR(255) NOT NULL DEFAULT 'professor',
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  `employment_type` VARCHAR(255) NOT NULL DEFAULT 'Full-Time'
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `faculty_salary_grade` (
  `id` INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
  `faculty_id` INT NOT NULL,
  `salary_grade_id` INT NOT NULL,
  `effective_date` DATE NOT NULL,
  `end_date` DATE,
  `notes` TEXT,
  `is_current` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  FOREIGN KEY (`faculty_id`) REFERENCES `faculties`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`salary_grade_id`) REFERENCES `salary_grades`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
  `uuid` VARCHAR(255) NOT NULL,
  `connection` TEXT NOT NULL,
  `queue` TEXT NOT NULL,
  `payload` TEXT NOT NULL,
  `exception` TEXT NOT NULL,
  `failed_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` VARCHAR(255) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `total_jobs` INT NOT NULL,
  `pending_jobs` INT NOT NULL,
  `failed_jobs` INT NOT NULL,
  `failed_job_ids` TEXT NOT NULL,
  `options` TEXT,
  `cancelled_at` INT,
  `created_at` INT NOT NULL,
  `finished_at` INT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `jobs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
  `queue` VARCHAR(255) NOT NULL,
  `payload` TEXT NOT NULL,
  `attempts` INT NOT NULL,
  `reserved_at` INT,
  `available_at` INT NOT NULL,
  `created_at` INT NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `leave_requests` (
  `id` INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
  `faculty_id` INT NOT NULL,
  `type` VARCHAR(255) NOT NULL,
  `reason` TEXT,
  `start_date` DATE NOT NULL,
  `end_date` DATE NOT NULL,
  `status` VARCHAR(255) NOT NULL DEFAULT 'pending',
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  `file_path` VARCHAR(255),
  FOREIGN KEY (`faculty_id`) REFERENCES `faculties`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `migrations` (
  `id` INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
  `migration` VARCHAR(255) NOT NULL,
  `batch` INT NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` VARCHAR(255) NOT NULL,
  `token` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `payslips` (
  `id` INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
  `faculty_id` INT NOT NULL,
  `year` INT NOT NULL,
  `month` INT NOT NULL,
  `employment_type` VARCHAR(255) NOT NULL,
  `total_hours` DECIMAL(10,2) NOT NULL,
  `base_salary` DECIMAL(15,2) NOT NULL,
  `total_deductions` DECIMAL(15,2) NOT NULL DEFAULT '0.00',
  `net_salary` DECIMAL(15,2) NOT NULL,
  `present_days` INT NOT NULL,
  `absent_days` INT NOT NULL,
  `late_days` INT NOT NULL,
  `attendance_summary` TEXT,
  `status` VARCHAR(255) NOT NULL DEFAULT 'draft',
  `generated_at` DATETIME NOT NULL,
  `finalized_at` DATETIME,
  `paid_at` DATETIME,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  FOREIGN KEY (`faculty_id`) REFERENCES `faculties`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `salary_grades` (
  `id` INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
  `grade` INT NOT NULL,
  `step` INT NOT NULL DEFAULT 1,
  `allowance` DECIMAL(15,2) NOT NULL DEFAULT '0.00',
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  `full_time_base_salary` DECIMAL(15,2),
  `part_time_base_salary` DECIMAL(15,2)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `schedule_assignments` (
  `id` INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
  `faculty_id` INT NOT NULL,
  `subject_code` VARCHAR(255) NOT NULL,
  `subject_name` VARCHAR(255) NOT NULL,
  `section` VARCHAR(255) NOT NULL,
  `year_level` ENUM('1st Year', '2nd Year', '3rd Year', '4th Year', '5th Year') NOT NULL,
  `units` INT NOT NULL,
  `hours_per_week` INT NOT NULL,
  `schedule_day` ENUM('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday') NOT NULL,
  `start_time` TIME NOT NULL,
  `end_time` TIME NOT NULL,
  `room` VARCHAR(255),
  `academic_year` INT NOT NULL,
  `semester` ENUM('1st Semester', '2nd Semester', 'Summer') NOT NULL,
  `status` ENUM('active', 'inactive', 'completed') NOT NULL DEFAULT 'active',
  `source` ENUM('direct', 'subject_load_tracker') NOT NULL DEFAULT 'direct',
  `notes` TEXT,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  FOREIGN KEY (`faculty_id`) REFERENCES `faculties`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `sessions` (
  `id` VARCHAR(255) NOT NULL,
  `user_id` INT,
  `ip_address` VARCHAR(45),
  `user_agent` TEXT,
  `payload` TEXT NOT NULL,
  `last_activity` INT NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `subject_load_trackers` (
  `id` INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
  `faculty_id` INT NOT NULL,
  `subject_code` VARCHAR(255) NOT NULL,
  `subject_name` VARCHAR(255) NOT NULL,
  `section` VARCHAR(255) NOT NULL,
  `units` INT NOT NULL,
  `hours_per_week` INT NOT NULL,
  `schedule_day` ENUM('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday') NOT NULL,
  `start_time` TIME NOT NULL,
  `end_time` TIME NOT NULL,
  `room` VARCHAR(255),
  `academic_year` INT NOT NULL,
  `semester` ENUM('1st Semester', '2nd Semester', 'Summer') NOT NULL,
  `status` ENUM('active', 'inactive', 'completed') NOT NULL DEFAULT 'active',
  `source` ENUM('direct', 'subject_load_tracker') NOT NULL DEFAULT 'subject_load_tracker',
  `year_level` VARCHAR(255),
  `notes` TEXT,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  FOREIGN KEY (`faculty_id`) REFERENCES `faculties`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `teaching_histories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
  `faculty_id` INT NOT NULL,
  `course_code` VARCHAR(255) NOT NULL,
  `course_title` VARCHAR(255) NOT NULL,
  `semester` VARCHAR(255) NOT NULL,
  `academic_year` INT NOT NULL,
  `units` INT NOT NULL DEFAULT 3,
  `schedule` VARCHAR(255),
  `start_time` TIME,
  `end_time` TIME,
  `room` VARCHAR(255),
  `number_of_students` INT NOT NULL DEFAULT 0,
  `rating` DECIMAL(5,2),
  `remarks` TEXT,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  FOREIGN KEY (`faculty_id`) REFERENCES `faculties`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `email_verified_at` TIMESTAMP NULL DEFAULT NULL,
  `password` VARCHAR(255) NOT NULL,
  `remember_token` VARCHAR(100),
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  `role` VARCHAR(255) NOT NULL DEFAULT 'professor'
) ENGINE=InnoDB;

-- Insert data
INSERT INTO `faculties` (`id`, `professor_id`, `name`, `email`, `password`, `picture`, `status`, `skills`, `experiences`, `remember_token`, `created_at`, `updated_at`, `role`, `deleted_at`, `employment_type`) VALUES 
(1, 'PROF-2025-0001', 'Emma Watson', 'abadenisraymond@gmail.com', '$2y$12$Wezcbv5Rnsc1abG2SDjM3ueEX2uIEA2EOtn3jgjxwZDKpD6uUolEK', NULL, 'active', NULL, NULL, NULL, '2025-09-03 08:00:35', '2025-09-03 14:57:58', 'professor', NULL, 'Full-Time');

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `role`) VALUES 
(1, 'Test User', 'test@example.com', '2025-09-03 07:54:44', '$2y$12$I0tzNh1LcP7lcnjtL.XCyus4/gmVJgViej13LVEN/RzsJJI8Y6hXC', 'svN9btO61N', '2025-09-03 07:54:44', '2025-09-03 07:54:44', 'professor'),
(2, 'Admin', 'admin@bestlink.edu.ph', NULL, '$2y$12$jmy5UKDReon7bvjZhZbQ8eFTsIfFMPr6jCtlBwTsv0/snZY9h/vE6', NULL, '2025-09-03 07:59:49', '2025-09-03 07:59:49', 'admin');

INSERT INTO `salary_grades` (`id`, `grade`, `step`, `allowance`, `created_at`, `updated_at`, `full_time_base_salary`, `part_time_base_salary`) VALUES 
(2, 1, 1, 0, '2025-09-03 15:31:40', '2025-09-03 15:31:40', 500, 300);

INSERT INTO `faculty_salary_grade` (`id`, `faculty_id`, `salary_grade_id`, `effective_date`, `end_date`, `notes`, `is_current`, `created_at`, `updated_at`) VALUES 
(1, 1, 2, '2025-09-03', NULL, NULL, 1, '2025-09-03 15:31:57', '2025-09-03 15:31:57');

INSERT INTO `attendances` (`id`, `faculty_id`, `date`, `time_in`, `time_out`, `time_in_photo`, `time_out_photo`, `time_in_location`, `time_out_location`, `total_hours`, `status`, `notes`, `created_at`, `updated_at`) VALUES 
(1, 1, '2025-09-03', '2025-09-03 14:58:34', '2025-09-03 15:32:55', 'attendance_photos/1/2025-09-03/attendance_time_in_1_2025-09-03_1756882714.jpg', 'attendance_photos/1/2025-09-03/attendance_time_out_1_2025-09-03_1756884775.jpg', '14.626300, 121.039900', '14.626300, 121.039900', 0.57, 'half_day', NULL, '2025-09-03 14:58:35', '2025-09-03 15:32:55');

INSERT INTO `clearance_requests` (`id`, `faculty_id`, `clearance_type`, `reason`, `status`, `admin_remarks`, `requested_at`, `processed_at`, `processed_by`, `created_at`, `updated_at`) VALUES 
(1, 1, 'grade_submission_confirmation', 'asdasasd', 'pending', NULL, '2025-09-03 15:03:32', NULL, NULL, '2025-09-03 15:03:32', '2025-09-03 15:03:32');

INSERT INTO `leave_requests` (`id`, `faculty_id`, `type`, `reason`, `start_date`, `end_date`, `status`, `created_at`, `updated_at`, `file_path`) VALUES 
(1, 1, 'sick', 'lagnat', '2025-09-04', '2025-09-06', 'pending', '2025-09-03 15:04:35', '2025-09-03 15:04:35', 'leave_attachments/C3KvCfgWLbHMxJmCHK9dETBvTJ4YjyNYhXPjBPAk.pdf');

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES 
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2024_09_01_203400_create_payslips_table', 1),
(5, '2025_01_01_000001_create_teaching_histories_table', 1),
(6, '2025_01_01_000002_create_clearances_table', 1),
(7, '2025_01_01_000003_create_evaluations_table', 1),
(8, '2025_01_01_000004_create_faculty_salary_grade_table', 1),
(9, '2025_01_02_000000_create_attendances_table', 1),
(10, '2025_08_21_145653_add_role_to_users_table', 1),
(11, '2025_08_22_055005_create_faculties_table', 1),
(12, '2025_08_25_092929_add_profile_fields_to_faculty_table', 1),
(13, '2025_08_27_060551_add_role_to_faculty_table', 1),
(14, '2025_08_27_062440_update_role_in_faculty_table', 1),
(15, '2025_08_28_000000_create_leave_requests_table', 1),
(16, '2025_08_28_010000_create_salary_grades_table', 1),
(17, '2025_08_28_020000_add_hourly_rates_to_salary_grades_table', 1),
(18, '2025_08_28_220434_add_deleted_at_to_faculty_table', 1),
(19, '2025_09_01_015810_add_employment_type_to_faculty_table', 1),
(20, '2025_09_01_030303_add_attachment_to_leave_requests_table', 1),
(21, '2025_09_01_221613_simplify_salary_grades_table', 1),
(22, '2025_09_01_222054_update_payslips_table_structure', 1),
(23, '2025_09_01_230616_create_clearance_requests_table', 1),
(24, '2025_09_02_112740_create_subject_load_trackers_table', 1),
(25, '2025_09_02_143040_create_schedule_assignments_table', 1),
(26, '2025_09-02_200225_fix_attendances_foreign_key_constraint', 1),
(27, '2025_09_02_203611_fix_evaluations_foreign_key_constraint', 1),
(28, '2025_09_02_211343_fix_faculty_foreign_key_constraints', 1);

INSERT INTO `payslips` (`id`, `faculty_id`, `year`, `month`, `employment_type`, `total_hours`, `base_salary`, `total_deductions`, `net_salary`, `present_days`, `absent_days`, `late_days`, `attendance_summary`, `status`, `generated_at`, `finalized_at`, `paid_at`, `created_at`, `updated_at`) VALUES 
(1, 1, 2025, 9, 'Full-Time', 0, 500, 500, 0, 0, 0, 1, '{"total_records":1,"present_days":0,"late_days":1,"absent_days":0,"total_hours":0,"average_hours_per_day":0}', 'draft', '2025-09-03 15:32:12', NULL, NULL, '2025-09-03 15:32:12', '2025-09-03 15:32:12');

INSERT INTO `schedule_assignments` (`id`, `faculty_id`, `subject_code`, `subject_name`, `section`, `year_level`, `units`, `hours_per_week`, `schedule_day`, `start_time`, `end_time`, `room`, `academic_year`, `semester`, `status`, `source`, `notes`, `created_at`, `updated_at`, `deleted_at`) VALUES 
(1, 1, 'ITE102', 'IT Elective 2', 'BSIT 1101', '1st Year', 3, 3, 'monday', '15:00:00', '17:00:00', '207', 2025, '1st Semester', 'active', 'direct', NULL, '2025-09-03 08:07:27', '2025-09-03 08:15:05', '2025-09-03 08:15:05'),
(2, 1, 'ITE101', 'IT Elective 1', 'BSIT 1101', '1st Year', 3, 3, 'monday', '15:00:00', '17:00:00', '207', 2025, '1st Semester', 'active', 'direct', NULL, '2025-09-03 08:21:24', '2025-09-03 08:21:24', NULL);

INSERT INTO `subject_load_trackers` (`id`, `faculty_id`, `subject_code`, `subject_name`, `section`, `units`, `hours_per_week`, `schedule_day`, `start_time`, `end_time`, `room`, `academic_year`, `semester`, `status`, `source`, `year_level`, `notes`, `deleted_at`, `created_at`, `updated_at`) VALUES 
(1, 1, 'CC102', 'Computer Programming 1', 'BSIT 1101', 3, 3, 'monday', '13:00:00', '15:00:00', '207', 2025, '1st Semester', 'active', 'subject_load_tracker', '1st Year', NULL, '2025-09-03 08:06:36', '2025-09-03 08:04:44', '2025-09-03 08:06:36'),
(2, 1, 'ITE102', 'IT Elective 2', 'BSIT 1101', 3, 3, 'monday', '13:00:00', '15:00:00', '207', 2025, '1st Semester', 'active', 'subject_load_tracker', '1st Year', NULL, NULL, '2025-09-03 08:18:48', '2025-09-03 08:18:48');

-- Create indexes
CREATE INDEX `attendances_date_index` ON `attendances` (`date`);
CREATE INDEX `attendances_faculty_id_date_index` ON `attendances` (`faculty_id`, `date`);
CREATE UNIQUE INDEX `attendances_faculty_id_date_unique` ON `attendances` (`faculty_id`, `date`);
CREATE INDEX `attendances_status_index` ON `attendances` (`status`);
CREATE INDEX `clearance_requests_clearance_type_status_index` ON `clearance_requests` (`clearance_type`, `status`);
CREATE INDEX `clearance_requests_faculty_id_status_index` ON `clearance_requests` (`faculty_id`, `status`);
CREATE INDEX `evaluations_faculty_id_academic_year_semester_index` ON `evaluations` (`faculty_id`, `academic_year`, `semester`);
CREATE UNIQUE INDEX `eval_faculty_teaching_period_unique` ON `evaluations` (`faculty_id`, `teaching_history_id`, `evaluation_period`);
CREATE UNIQUE INDEX `faculties_email_unique` ON `faculties` (`email`);
CREATE UNIQUE INDEX `faculties_professor_id_unique` ON `faculties` (`professor_id`);
CREATE INDEX `faculty_salary_grade_faculty_id_is_current_index` ON `faculty_salary_grade` (`faculty_id`, `is_current`);
CREATE UNIQUE INDEX `faculty_salary_grade_unique` ON `faculty_salary_grade` (`faculty_id`, `salary_grade_id`, `effective_date`);
CREATE UNIQUE INDEX `failed_jobs_uuid_unique` ON `failed_jobs` (`uuid`);
CREATE INDEX `jobs_queue_index` ON `jobs` (`queue`);
CREATE UNIQUE INDEX `payslips_faculty_id_year_month_unique` ON `payslips` (`faculty_id`, `year`, `month`);
CREATE INDEX `payslips_status_index` ON `payslips` (`status`);
CREATE INDEX `payslips_year_month_index` ON `payslips` (`year`, `month`);
CREATE INDEX `schedule_assignments_faculty_id_academic_year_semester_index` ON `schedule_assignments` (`faculty_id`, `academic_year`, `semester`);
CREATE INDEX `schedule_assignments_schedule_day_start_time_end_time_index` ON `schedule_assignments` (`schedule_day`, `start_time`, `end_time`);
CREATE INDEX `schedule_assignments_status_index` ON `schedule_assignments` (`status`);
CREATE INDEX `schedule_assignments_subject_code_section_index` ON `schedule_assignments` (`subject_code`, `section`);
CREATE INDEX `sessions_last_activity_index` ON `sessions` (`last_activity`);
CREATE INDEX `sessions_user_id_index` ON `sessions` (`user_id`);
CREATE INDEX `subject_load_trackers_faculty_id_academic_year_semester_index` ON `subject_load_trackers` (`faculty_id`, `academic_year`, `semester`);
CREATE INDEX `subject_load_trackers_schedule_day_start_time_end_time_index` ON `subject_load_trackers` (`schedule_day`, `start_time`, `end_time`);
CREATE INDEX `subject_load_trackers_status_index` ON `subject_load_trackers` (`status`);
CREATE INDEX `subject_load_trackers_subject_section_year_sem_index` ON `subject_load_trackers` (`subject_code`, `section`, `academic_year`, `semester`);
CREATE INDEX `teaching_histories_faculty_id_academic_year_semester_index` ON `teaching_histories` (`faculty_id`, `academic_year`, `semester`);
CREATE UNIQUE INDEX `unique_subject_assignment` ON `subject_load_trackers` (`faculty_id`, `subject_code`, `section`, `academic_year`, `semester`);
CREATE UNIQUE INDEX `users_email_unique` ON `users` (`email`);

SET FOREIGN_KEY_CHECKS = 1;

COMMIT;