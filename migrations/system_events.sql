CREATE TABLE IF NOT EXISTS `system_events` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) NOT NULL,
  `module` varchar(50) NOT NULL COMMENT 'e.g. CRM, Pay, Shop',
  `event_type` varchar(100) NOT NULL COMMENT 'e.g. invoice.created, lead.won',
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`payload`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `tenant_id` (`tenant_id`),
  KEY `module` (`module`),
  KEY `event_type` (`event_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
