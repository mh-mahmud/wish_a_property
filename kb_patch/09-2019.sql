#shafiq @ 2019-08-27
RENAME TABLE `property_sales`.`property_attachement` TO `property_sales`.`property_attachment`;

#tusher
ALTER TABLE `users` ADD `password_updated` DATETIME NOT NULL AFTER `created`;

#tusher 20/09/2019
CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `message` text NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

#tusher 24/09/2019
ALTER TABLE `users` ADD `user_type` VARCHAR(50) NOT NULL AFTER `username`;

#tusher 29/09/2019
ALTER TABLE `properties` ADD `activated` TINYINT NOT NULL AFTER `pool`, ADD `business_type` INT NOT NULL COMMENT '0=simple, 1=classified, 2=latest, 3=archived, 4=premium' AFTER `activated`;