# ---

# Таблица статей
CREATE TABLE `articles` (
                          `id` int(11) NOT NULL AUTO_INCREMENT,
                          `book` int(11) DEFAULT NULL COMMENT 'ID сборника -> books',
                          `topic` int(11) DEFAULT NULL COMMENT 'ID Topic -> topic',
                          `pdfid` int(11) DEFAULT '-1' COMMENT 'ID PDF -> filestorage',
                          `date_add` date DEFAULT NULL COMMENT 'Дата добавления статьи в сборник',
                          `udc` char(30) DEFAULT NULL COMMENT 'УДК',
                          `pages` char(60) DEFAULT NULL COMMENT 'Страницы, на которых опубликована в сборнике статья',
                          `doi` varchar(80) DEFAULT NULL COMMENT 'DOI',
                          `title_en` varchar(250) DEFAULT NULL COMMENT 'Название (англ)',
                          `title_ru` varchar(250) DEFAULT NULL COMMENT 'Название (рус)',
                          `title_ua` varchar(250) DEFAULT NULL COMMENT 'Название (укр)',
                          `abstract_en` longtext COMMENT 'Аннотация (англ)',
                          `abstract_ru` longtext COMMENT 'Аннотация (рус)',
                          `abstract_ua` longtext COMMENT 'Аннотация (укр)',
                          `keywords_en` longtext COMMENT 'Ключевые слова (англ)',
                          `keywords_ru` longtext COMMENT 'Ключевые слова (рус)',
                          `keywords_ua` longtext COMMENT 'Ключевые слова (укр)',
                          `refs_en` longtext COMMENT 'Список литературы (англ)',
                          `refs_ru` longtext COMMENT 'Список литературы (рус)',
                          `refs_ua` longtext COMMENT 'Список литературы (укр)',
                          `stat_date_insert` datetime DEFAULT CURRENT_TIMESTAMP,
                          `stat_date_update` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                          PRIMARY KEY (`id`),
                          KEY `book` (`book`),
                          KEY `topic` (`topic`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Таблица авторов
CREATE TABLE `authors` (
                         `id` int(11) NOT NULL AUTO_INCREMENT,
                         `photo_id` int(11) DEFAULT '-1' COMMENT 'ID photo -> filestorage',
                         `is_es` smallint(6) DEFAULT '0' COMMENT 'Входит ли автор в редколлегию',
                         `estaff_role` smallint(6) DEFAULT '0' COMMENT 'роль в редколлегии',
                         `email` varchar(255) DEFAULT NULL COMMENT 'EMail',
                         `orcid` varchar(20) NOT NULL DEFAULT '' COMMENT 'ORCId',
                         `phone` varchar(255) DEFAULT NULL COMMENT 'Телефон',
                         `name_en` varchar(255) DEFAULT NULL COMMENT 'ФИО автора (англ)',
                         `name_ru` varchar(255) DEFAULT NULL COMMENT 'ФИО автора (рус)',
                         `name_ua` varchar(255) DEFAULT NULL COMMENT 'ФИО автора (укр)',
                         `firstletter_name_en` varchar(1) DEFAULT NULL COMMENT 'Первая буква фамилии (англ)',
                         `firstletter_name_ru` varchar(1) DEFAULT NULL COMMENT 'Первая буква фамилии (рус)',
                         `firstletter_name_ua` varchar(1) DEFAULT NULL COMMENT 'Первая буква фамилии (укр)',
                         `workplace_en` varchar(255) DEFAULT NULL COMMENT 'Место работы (англ)',
                         `workplace_ru` varchar(255) DEFAULT NULL COMMENT 'Место работы (рус)',
                         `workplace_ua` varchar(255) DEFAULT NULL COMMENT 'Место работы (укр)',
                         `title_en` varchar(255) DEFAULT NULL COMMENT 'учёное звание (англ)',
                         `title_ru` varchar(255) DEFAULT NULL COMMENT 'учёное звание (рус)',
                         `title_ua` varchar(255) DEFAULT NULL COMMENT 'учёное звание (укр)',
                         `bio_en` longtext COMMENT 'Биография автора (англ)',
                         `bio_ru` longtext COMMENT 'Биография автора (рус)',
                         `bio_ua` longtext COMMENT 'Биография автора (укр)',
                         `stat_date_insert` datetime DEFAULT CURRENT_TIMESTAMP,
                         `stat_date_update` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                         PRIMARY KEY (`id`),
                         KEY `firstletter_name_en` (`firstletter_name_en`),
                         KEY `firstletter_name_ru` (`firstletter_name_ru`),
                         KEY `firstletter_name_ua` (`firstletter_name_ua`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Таблица кросс-связи автор <--> статья
CREATE TABLE `cross_aa` (
                          `id` int(11) NOT NULL AUTO_INCREMENT,
                          `author` int(11) DEFAULT NULL COMMENT '-> authors.id',
                          `article` int(11) DEFAULT NULL COMMENT '-> articles.id',
                          PRIMARY KEY (`id`),
                          KEY `author` (`author`),
                          KEY `article` (`article`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Таблица сборников
CREATE TABLE `books` (
                       `id` int(11) NOT NULL AUTO_INCREMENT,
                       `title` varchar(80) DEFAULT NULL COMMENT 'название сборника',
                       `published_status` tinyint(4) DEFAULT '0' COMMENT 'статус: опубликован или в работе?',
                       `published_date` date DEFAULT NULL COMMENT 'дата публикации сборника',
                       `contentpages` varchar(60) DEFAULT NULL COMMENT 'страницы сборника с нашими статьями',
                       `file_cover` int(11) DEFAULT '-1' COMMENT 'ID->filestorage обложки',
                       `file_title_ru` int(11) DEFAULT '-1' COMMENT 'ID->filestorage русского титульника',
                       `file_title_en` int(11) DEFAULT '-1' COMMENT 'ID->filestorage англ. титульника',
                       `file_toc_ru` int(11) DEFAULT '-1' COMMENT 'ID->filestorage русского содержания',
                       `file_toc_en` int(11) DEFAULT '-1' COMMENT 'ID->filestorage англ. содержания',
                       `stat_date_insert` datetime DEFAULT CURRENT_TIMESTAMP,
                       `stat_date_update` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                       PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Группировка разделов
CREATE TABLE `topicgroups` (
                             `id` int(11) NOT NULL AUTO_INCREMENT,
                             `title_ru` varchar(80) DEFAULT NULL COMMENT 'Название (рус)',
                             `title_en` varchar(80) DEFAULT NULL COMMENT 'Название (англ)',
                             `title_ua` varchar(80) DEFAULT NULL COMMENT 'Название (укр)',
                             `display_order` int(11) NOT NULL DEFAULT '0' COMMENT 'порядок отображения (больше - позже)',
                             PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Разделы
CREATE TABLE `topics` (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `title_ru` varchar(255) DEFAULT NULL COMMENT 'Название (рус)',
                        `title_en` varchar(255) DEFAULT NULL COMMENT 'Название (англ)',
                        `title_ua` varchar(255) DEFAULT NULL COMMENT 'Название (укр)',
                        `rel_group` int(11) DEFAULT NULL COMMENT '-> topic_groups',
                        PRIMARY KEY (`id`),
                        KEY `rel_group` (`rel_group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Новости
CREATE TABLE `news` (
                      `id` int(11) NOT NULL AUTO_INCREMENT,
                      `publish_date` date DEFAULT NULL COMMENT 'дата публикации новости',
                      `title_en` varchar(255) DEFAULT NULL COMMENT 'Заголовок новости (англ)',
                      `title_ru` varchar(255) DEFAULT NULL COMMENT 'Заголовок новости (рус)',
                      `title_ua` varchar(255) DEFAULT NULL COMMENT 'Заголовок новости (укр)',
                      `comment` varchar(255) DEFAULT NULL COMMENT 'внутренний комментарий',
                      `text_en` longtext COMMENT 'Текст новости (англ)',
                      `text_ru` longtext COMMENT 'Текст новости (рус)',
                      `text_ua` longtext COMMENT 'Текст новости (укр)',
                      `stat_date_insert` datetime DEFAULT CURRENT_TIMESTAMP,
                      `stat_date_update` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                      PRIMARY KEY (`id`),
                      KEY `publish_date` (`publish_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Статические страницы
CREATE TABLE `staticpages` (
                             `id` int(11) NOT NULL AUTO_INCREMENT,
                             `public` int(11) DEFAULT '1' COMMENT 'Опубликована ли страница?',
                             `alias` char(64) DEFAULT NULL COMMENT 'идентификатор страницы',
                             `title_en` varchar(255) DEFAULT NULL COMMENT 'Заголовок страницы (англ)',
                             `title_ru` varchar(255) DEFAULT NULL COMMENT 'Заголовок страницы (рус)',
                             `title_ua` varchar(255) DEFAULT NULL COMMENT 'Заголовок страницы (укр)',
                             `content_en` longtext COMMENT 'Контент страницы (англ)',
                             `content_ru` longtext COMMENT 'Контент страницы (рус)',
                             `content_ua` longtext COMMENT 'Контент страницы (укр)',
                             `comment` varchar(255) DEFAULT NULL COMMENT 'Комментарий',
                             `stat_date_insert` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                             `stat_date_update` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                             PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Баннеры 
CREATE TABLE `banners` (
                         `id` int(11) NOT NULL AUTO_INCREMENT,
                         `data_is_visible` tinyint(1) DEFAULT NULL COMMENT 'показывать ли баннер',
                         `data_url_image` varchar(250) DEFAULT NULL COMMENT 'URL к изображению баннера',
                         `data_url_href` varchar(250) DEFAULT NULL COMMENT 'HREF на ресурс, предоставивший баннер',
                         `data_comment` varchar(128) DEFAULT NULL COMMENT 'комментарий',
                         `data_alt` varchar(100) DEFAULT NULL COMMENT 'альтернативный текст для баннера',
                         PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Пользователи
CREATE TABLE `users` (
                       `id` int(11) NOT NULL AUTO_INCREMENT,
                       `name` varchar(250) DEFAULT NULL COMMENT 'ФИО',
                       `email` varchar(250) DEFAULT NULL COMMENT 'мыло',
                       `permissions` int(11) DEFAULT NULL COMMENT 'права доступа',
                       `login` varchar(250) DEFAULT NULL COMMENT 'логин',
                       `phone` varchar(250) DEFAULT NULL COMMENT 'телефон',
                       `md5password` char(32) DEFAULT NULL COMMENT 'хэш пароля',
                       `stat_date_insert` datetime DEFAULT CURRENT_TIMESTAMP,
                       `stat_date_update` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                       PRIMARY KEY (`id`),
                       KEY `login` (`login`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Роли в редколлегии
CREATE TABLE `ref_estaff_roles` (
                                  `id` int(11) NOT NULL AUTO_INCREMENT,
                                  `data_str` varchar(64) DEFAULT NULL COMMENT 'Роль в редколлегии',
                                  `data_int` int(11) DEFAULT NULL COMMENT 'Not used',
                                  `data_comment` varchar(64) DEFAULT NULL COMMENT 'Комментарий',
                                  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Filestorage
CREATE TABLE `filestorage` (
                             `id` int(11) NOT NULL AUTO_INCREMENT,
                             `store_type` enum('blob','cloud','disk') NOT NULL DEFAULT 'disk' COMMENT 'тип хранилища файла (blob - в БД, cloud - облако, disk - локально)',
                             `collection` enum('articles','authors','books','pages') DEFAULT NULL COMMENT 'коллекция (к чему относится файл)',
                             `relation` int(11) DEFAULT NULL COMMENT 'идентификатор элемента (не внешний ключ!!!)',
                             `filesize` int(11) DEFAULT NULL COMMENT 'размер файла (байт)',
                             `username` varchar(255) DEFAULT NULL COMMENT 'оригинальное имя файла',
                             `tempname` varchar(255) DEFAULT NULL COMMENT 'временное имя файла при обработке',
                             `filetype` varchar(255) DEFAULT NULL COMMENT 'MIME-тип файла',
                             `internal_name` varchar(255) DEFAULT NULL COMMENT 'внутреннее имя файла в каталоге storage',
                             `stat_download_counter` int(11) DEFAULT '0' COMMENT 'количество загрузок файла',
                             `cloud_storage` varchar(255) DEFAULT NULL COMMENT 'идентификатор хранилища в облаке',
                             `cloud_container` varchar(255) DEFAULT NULL COMMENT 'идентификатор контейнера в облаке',
                             `stat_date_insert` datetime DEFAULT CURRENT_TIMESTAMP,
                             `stat_date_update` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                             `stat_date_download` datetime DEFAULT NULL,
                             PRIMARY KEY (`id`),
                             KEY `relation` (`relation`),
                             KEY `collection` (`collection`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# filestorage в blob (не используется)
CREATE TABLE `filestorage_blob` (
                                  `id` int(11) NOT NULL AUTO_INCREMENT,
                                  `content` longblob,
                                  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# логгирование событий
CREATE TABLE `eventlog` (
                          `id` int(11) NOT NULL AUTO_INCREMENT,
                          `action` enum('Add','Delete','Download','Error','Login','Logout','Update') DEFAULT NULL COMMENT 'тип события',
                          `table` varchar(64) DEFAULT NULL,
                          `element` varchar(64) DEFAULT NULL,
                          `comment` varchar(255) DEFAULT NULL COMMENT 'комментарий',
                          `ip` char(16) DEFAULT NULL COMMENT 'IP',
                          `datetime` datetime DEFAULT CURRENT_TIMESTAMP COMMENT 'дата события',
                          `user` smallint(6) DEFAULT NULL COMMENT '->users.id',
                          PRIMARY KEY (`id`),
                          KEY `action` (`action`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# Логгирование скачивания файлов
CREATE TABLE `eventlog_download` (
                                   `id` int(11) NOT NULL AUTO_INCREMENT,
                                   `element` int(11) DEFAULT NULL,
                                   `referrer` varchar(255) DEFAULT NULL,
                                   `ip` char(16) DEFAULT NULL,
                                   `datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                   PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

