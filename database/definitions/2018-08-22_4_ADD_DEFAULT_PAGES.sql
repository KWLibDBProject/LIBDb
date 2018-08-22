/**
Вставляет данные только если их нет в таблице

see https://stackoverflow.com/a/3025332/5127037
 */

INSERT INTO `staticpages` (public, alias, `comment`)
SELECT 1, 'imprint', 'О ЖУРНАЛЕ / Реквизиты журнала' FROM staticpages
WHERE NOT EXISTS (SELECT 1 FROM staticpages WHERE alias = 'imprint')
LIMIT 1;

INSERT INTO `staticpages` (public, alias, `comment`)
SELECT 1, 'details', 'О ЖУРНАЛЕ / Рецензирование статей' FROM staticpages
WHERE NOT EXISTS (SELECT 1 FROM staticpages WHERE alias = 'details')
LIMIT 1;

INSERT INTO `staticpages` (public, alias, `comment`)
SELECT 1, 'pubethics', 'О ЖУРНАЛЕ / Издательская этика' FROM staticpages
WHERE NOT EXISTS (SELECT 1 FROM staticpages WHERE alias = 'pubethics')
LIMIT 1;

INSERT INTO `staticpages` (public, alias, `comment`)
SELECT 1, 'tom', 'ДЛЯ АВТОРОВ / Порядок рассмотрения статей' FROM staticpages
WHERE NOT EXISTS (SELECT 1 FROM staticpages WHERE alias = 'tom')
LIMIT 1;

INSERT INTO `staticpages` (public, alias, `comment`)
SELECT 1, 'default', 'ДЛЯ АВТОРОВ - Стартовая страница' FROM staticpages
WHERE NOT EXISTS (SELECT 1 FROM staticpages WHERE alias = 'default')
LIMIT 1;

INSERT INTO `staticpages` (public, alias, `comment`)
SELECT 1, 'forauthors', 'О ЖУРНАЛЕ / Аннотация к журналу' FROM staticpages
WHERE NOT EXISTS (SELECT 1 FROM staticpages WHERE alias = 'forauthors')
LIMIT 1;

INSERT INTO `staticpages` (public, alias, `comment`)
SELECT 1, 'about', 'Главная - О ЖУРНАЛЕ' FROM staticpages
WHERE NOT EXISTS (SELECT 1 FROM staticpages WHERE alias = 'about')
LIMIT 1;

INSERT INTO `staticpages` (public, alias, `comment`)
SELECT 1, 'contacts', 'О ЖУРНАЛЕ / Контакты' FROM staticpages
WHERE NOT EXISTS (SELECT 1 FROM staticpages WHERE alias = 'contacts')
LIMIT 1;

INSERT INTO `staticpages` (public, alias, `comment`)
SELECT 1, 'format', 'ДЛЯ АВТОРОВ / Требования для авторов к оформлению научных статей' FROM staticpages
WHERE NOT EXISTS (SELECT 1 FROM staticpages WHERE alias = 'format')
LIMIT 1;



