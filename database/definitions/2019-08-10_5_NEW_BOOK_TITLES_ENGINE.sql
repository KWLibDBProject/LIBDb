-- 1
-- переименовываем books.title в books.title_en
ALTER TABLE `books` CHANGE `title` `title_en` VARCHAR(80);

-- 2
-- добавляем после title_en поля title_ru, title_ua типа varchar(80)
ALTER TABLE `books` ADD COLUMN `title_ru` VARCHAR(80) AFTER `title_en`;
ALTER TABLE `books` ADD COLUMN `title_ua` VARCHAR(80) AFTER `title_ru`;
