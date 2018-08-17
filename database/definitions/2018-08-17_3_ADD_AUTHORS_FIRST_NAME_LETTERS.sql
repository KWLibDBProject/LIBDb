/* Create fields */

ALTER TABLE `authors` ADD COLUMN `firstletter_name_en` VARCHAR(1) DEFAULT NULL COMMENT 'Первая буква фамилии (англ)' AFTER `name_ua` ;
ALTER TABLE `authors` ADD COLUMN `firstletter_name_ru` VARCHAR(1) DEFAULT NULL COMMENT 'Первая буква фамилии (рус)' AFTER `firstletter_name_en`;
ALTER TABLE `authors` ADD COLUMN `firstletter_name_ua` VARCHAR(1) DEFAULT NULL COMMENT 'Первая буква фамилии (укр)' AFTER `firstletter_name_ru`;

/* Create indexes */

CREATE INDEX `firstletter_name_en` ON `authors`(firstletter_name_en);
CREATE INDEX `firstletter_name_ru` ON `authors`(firstletter_name_ru);
CREATE INDEX `firstletter_name_ua` ON `authors`(firstletter_name_ua);
