CREATE TABLE IF NOT EXISTS `cloud_folders` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `tenant_id` int(11) NOT NULL,
    `parent_id` int(11) DEFAULT NULL,
    `name` varchar(255) NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
CREATE TABLE IF NOT EXISTS `cloud_files` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `tenant_id` int(11) NOT NULL,
    `folder_id` int(11) DEFAULT NULL,
    `name` varchar(255) NOT NULL,
    `path` varchar(255) NOT NULL,
    `type` varchar(50) DEFAULT 'file',
    `size_bytes` bigint(20) DEFAULT 0,
    `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    FOREIGN KEY (`folder_id`) REFERENCES `cloud_folders` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;