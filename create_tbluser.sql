-- Create tbluser table for admin users
CREATE TABLE `tbluser` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(50) NOT NULL,
  `middlename` varchar(50) DEFAULT NULL,
  `lastname` varchar(50) NOT NULL,
  `suffix` varchar(10) DEFAULT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `birthdate` date NOT NULL,
  `nationality` varchar(50) DEFAULT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `is_password_changed` tinyint(1) DEFAULT 0,
  `verification_code` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `userlevel_id` int(11) NOT NULL DEFAULT 2,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert admin user (userlevel_id = 1 for admin)
INSERT INTO `tbluser` (
  `firstname`, 
  `middlename`, 
  `lastname`, 
  `suffix`, 
  `gender`, 
  `birthdate`, 
  `nationality`, 
  `contact`, 
  `email`, 
  `password`, 
  `image`, 
  `is_verified`, 
  `is_password_changed`, 
  `userlevel_id`
) VALUES (
  'Admin',
  '',
  'User',
  '',
  'Male',
  '1990-01-01',
  'Filipino',
  '9123456789',
  'admin@example.com',
  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: "password"
  'avatar.png',
  1,
  1,
  1 -- userlevel_id = 1 for admin
);
