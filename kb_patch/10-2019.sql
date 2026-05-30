#tusher @ 01/10/2019

--
-- Table structure for table `slider`
--
DROP TABLE IF EXISTS `slider`;
CREATE TABLE IF NOT EXISTS `slider` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `slider_image` varchar(100) DEFAULT NULL,
  `slider_title` varchar(20) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0=inactive,1=active',
  `listorder` int(11) DEFAULT '0',
  `target_link` text,
  `create_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
COMMIT;

#shafiq @ 13/10/2019
ALTER TABLE `slider` CHANGE `slider_title` `slider_title` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `users` CHANGE `first_name` `first_name` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `last_name` `last_name` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `username` `username` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `phone` `phone` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `email` `email` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `country` `country` VARCHAR(5) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `password` `password` VARCHAR(150) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `users` CHANGE `user_type` `user_type` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `properties` CHANGE `property_name` `property_name` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `property_description` `property_description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `property_location` `property_location` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `property_type` `property_type` VARCHAR(5) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `price` `price` INT(11) NOT NULL DEFAULT '0', CHANGE `activated` `activated` TINYINT(4) NOT NULL DEFAULT '0', CHANGE `business_type` `business_type` TINYINT(1) NULL DEFAULT '0' COMMENT '0=simple, 1=classified, 2=latest, 3=archived, 4=premium';
ALTER TABLE `comments` CHANGE `name` `name` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `email` `email` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `message` `message` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

#tusher @ 12/10/2019
--
-- Table structure for table `admin_users`
--
CREATE TABLE `admin_users` (
  `uid` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `username` varchar(20) NOT NULL,
  `fullname` varchar(50) NOT NULL,
  `password` varchar(150) NOT NULL,
  `password_updated` datetime NOT NULL,
  `email` varchar(50) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(4) NOT NULL,
  `last_access_time` datetime NOT NULL,
  `last_access_ip` varchar(20) NOT NULL,
  `accesslevel` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `admin_users` CHANGE `username` `username` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `fullname` `fullname` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `password` `password` VARCHAR(150) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `email` `email` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `phone` `phone` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `status` `status` TINYINT(1) NOT NULL DEFAULT '0', CHANGE `last_access_ip` `last_access_ip` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `accesslevel` `accesslevel` TINYINT(1) NOT NULL DEFAULT '0';

#tusher @ 20/10/2019
ALTER TABLE `admin_users` ADD `user_type` VARCHAR(20) NULL DEFAULT NULL AFTER `status`;
ALTER TABLE `slider` ADD `slider_subtitle` VARCHAR(100) NULL DEFAULT NULL AFTER `slider_title`, ADD `button_text` VARCHAR(20) NULL DEFAULT NULL AFTER `slider_subtitle`;

#tusher @ 28/10/2019
CREATE TABLE `agents` (
  `id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `agent_name` varchar(50) DEFAULT NULL,
  `agent_title` varchar(50) DEFAULT NULL,
  `agent_image` varchar(255) DEFAULT NULL,
  `agent_phone` varchar(30) DEFAULT NULL,
  `facebook_link` varchar(255) DEFAULT NULL,
  `twitter_link` varchar(255) DEFAULT NULL,
  `linkedin_link` varchar(255) DEFAULT NULL,
  `vimeo_link` varchar(255) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#shafiq @ 30/10/2019
ALTER TABLE `properties` CHANGE `bedrooms` `bedrooms` TINYINT(1) NULL DEFAULT '0', CHANGE `bathrooms` `bathrooms` TINYINT(1) NULL DEFAULT '0', CHANGE `garages` `garages` TINYINT(1) NULL DEFAULT '0', CHANGE `kitchen` `kitchen` TINYINT(1) NULL DEFAULT '0', CHANGE `ac_rooms` `ac_rooms` VARCHAR(1) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '0';