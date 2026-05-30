CREATE TABLE `newsticker` (
  `id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `news_title` text DEFAULT NULL,
  `status` TINYINT DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#tusher 05/12/2019
CREATE TABLE `services` (
  `id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `service_name` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#shafiq 11/12/2019
ALTER TABLE `properties` CHANGE `status` `status` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '0=available,1=sold,2=under contractions';
ALTER TABLE `properties` CHANGE `activated` `activated` TINYINT(4) NOT NULL DEFAULT '0' COMMENT '0=inactive,1=active';
ALTER TABLE `properties` CHANGE `activated` `activated` TINYINT(4) NOT NULL DEFAULT '0' COMMENT '0=inactive,1=active,2=pending';
ALTER TABLE `properties` CHANGE `status` `status` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '0=available,1=sold,2=under contractions,3=Under demolition,4=Under renovation';

#shafiq 25/12/2019
CREATE TABLE IF NOT EXISTS `property_whitelist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `property_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
COMMIT;

CREATE TABLE IF NOT EXISTS `users_search_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `location` text,
  `type` varchar(30) DEFAULT NULL,
  `bedrooms` tinyint(1) NOT NULL,
  `bathrooms` tinyint(1) NOT NULL,
  `search_type` tinyint(1) NOT NULL COMMENT '0=property,1=agents',
  `created_date` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
COMMIT;