#tusher 11/12/2019
CREATE TABLE `subscribers` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `email` varchar(50) NOT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#tusher 14/11/2019
ALTER TABLE `properties` ADD `flat_size` FLOAT(10,2) NULL AFTER `full_area`;

#tusher 24/11/2019
CREATE TABLE `latest_news` (
  `id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `news_title` varchar(50) DEFAULT NULL,
  `news_description` text DEFAULT NULL,
  `news_image` varchar(255) DEFAULT NULL,
  `published_date` datetime DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#25/11/2019
ALTER TABLE `latest_news` ADD `status` TINYINT NULL AFTER `news_image`;
ALTER TABLE `latest_news` CHANGE `news_title` `news_title` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

#tusher 26/11/2019
ALTER TABLE `latest_news` ADD `listorder` INT NULL DEFAULT '0' AFTER `status`;

#shafiq 26/11/2019
ALTER TABLE `properties` ADD `status` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '0=available,1=sold,2=processing' AFTER `business_type`;