/* Create fields */

ALTER TABLE `authors` ADD COLUMN `firstletter_name_en` VARCHAR(1) DEFAULT NULL COMMENT 'Первая буква фамилии (англ)' AFTER `name_ua` ;
ALTER TABLE `authors` ADD COLUMN `firstletter_name_ru` VARCHAR(1) DEFAULT NULL COMMENT 'Первая буква фамилии (рус)' AFTER `firstletter_name_en`;
ALTER TABLE `authors` ADD COLUMN `firstletter_name_ua` VARCHAR(1) DEFAULT NULL COMMENT 'Первая буква фамилии (укр)' AFTER `firstletter_name_ru`;

/* Create indexes */

CREATE INDEX `firstletter_name_en` ON `authors`(firstletter_name_en);
CREATE INDEX `firstletter_name_ru` ON `authors`(firstletter_name_ru);
CREATE INDEX `firstletter_name_ua` ON `authors`(firstletter_name_ua);

/* Update current data */

UPDATE `authors` SET `firstletter_name_en` = SUBSTRING(`authors`.`name_en`, 1, 1);
UPDATE `authors` SET `firstletter_name_ru` = SUBSTRING(`authors`.`name_ru`, 1, 1);
UPDATE `authors` SET `firstletter_name_ua` = SUBSTRING(`authors`.`name_ua`, 1, 1);