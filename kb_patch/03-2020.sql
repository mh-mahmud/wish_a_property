ALTER TABLE `admin_users` ADD `login_attempts` TINYINT NOT NULL DEFAULT '0' AFTER `accesslevel`, ADD `locked_until` DATETIME NULL DEFAULT NULL AFTER `login_attempts`;
ALTER TABLE `users` ADD `address` VARCHAR(100) NULL DEFAULT NULL AFTER `email`;
ALTER TABLE `users` ADD `city` VARCHAR(50) NULL DEFAULT NULL AFTER `address`;