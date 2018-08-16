/*Table structure for table `articles` */

DROP TABLE IF EXISTS `articles`;

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

/*Table structure for table `authors` */

DROP TABLE IF EXISTS `authors`;

CREATE TABLE `authors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `photo_id` int(11) DEFAULT '-1' COMMENT 'ID photo -> filestorage',
  `email` char(100) DEFAULT NULL COMMENT 'EMail',
  `orcid` char(20) NOT NULL DEFAULT '' COMMENT 'ORCId',
  `phone` char(60) DEFAULT NULL COMMENT 'Телефон',
  `is_es` smallint(11) DEFAULT '0' COMMENT 'Входит ли автор в редколлегию',
  `estaff_role` smallint(11) DEFAULT '0' COMMENT 'роль в редколлегии',
  `name_en` char(100) DEFAULT NULL COMMENT 'ФИО автора (англ)',
  `name_ru` char(100) DEFAULT NULL COMMENT 'ФИО автора (рус)',
  `name_ua` char(100) DEFAULT NULL COMMENT 'ФИО автора (укр)',
  `workplace_en` char(254) DEFAULT NULL COMMENT 'Место работы (англ)',
  `workplace_ru` char(254) DEFAULT NULL COMMENT 'Место работы (рус)',
  `workplace_ua` char(254) DEFAULT NULL COMMENT 'Место работы (укр)',
  `title_en` char(100) DEFAULT NULL COMMENT 'учёное звание (англ)',
  `title_ru` char(100) DEFAULT NULL COMMENT 'учёное звание (рус)',
  `title_ua` char(100) DEFAULT NULL COMMENT 'учёное звание (укр)',
  `bio_en` longtext COMMENT 'Биография автора (англ)',
  `bio_ru` longtext COMMENT 'Биография автора (рус)',
  `bio_ua` longtext COMMENT 'Биография автора (укр)',
  `stat_date_insert` datetime DEFAULT CURRENT_TIMESTAMP,
  `stat_date_update` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `banners` */

DROP TABLE IF EXISTS `banners`;

CREATE TABLE `banners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `data_url_image` varchar(250) DEFAULT NULL COMMENT 'URL к изображению баннера',
  `data_url_href` varchar(250) DEFAULT NULL COMMENT 'HREF на ресурс, предоставивший баннер',
  `data_alt` varchar(100) DEFAULT NULL COMMENT 'альтернативный текст для баннера',
  `data_is_visible` tinyint(1) DEFAULT NULL COMMENT 'показывать ли баннер',
  `data_comment` varchar(128) DEFAULT NULL COMMENT 'комментарий',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `books` */

DROP TABLE IF EXISTS `books`;

CREATE TABLE `books` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(80) DEFAULT NULL COMMENT 'название сборника',
  `published_status` tinyint(4) DEFAULT '0' COMMENT 'статус: опубликован или в работе?',
  `published_date` date DEFAULT NULL COMMENT 'дата публикации сборника',
  `contentpages` char(60) DEFAULT NULL COMMENT 'страницы сборника с нашими статьями',
  `file_cover` int(11) DEFAULT '-1' COMMENT 'ID->filestorage обложки',
  `file_title_ru` int(11) DEFAULT '-1' COMMENT 'ID->filestorage русского титульника',
  `file_title_en` int(11) DEFAULT '-1' COMMENT 'ID->filestorage англ. титульника',
  `file_toc_ru` int(11) DEFAULT '-1' COMMENT 'ID->filestorage русского содержания',
  `file_toc_en` int(11) DEFAULT '-1' COMMENT 'ID->filestorage англ. содержания',
  `stat_date_insert` datetime DEFAULT CURRENT_TIMESTAMP,
  `stat_date_update` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `cross_aa` */

DROP TABLE IF EXISTS `cross_aa`;

CREATE TABLE `cross_aa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `author` int(11) DEFAULT NULL COMMENT '-> authors.id',
  `article` int(11) DEFAULT NULL COMMENT '-> articles.id',
  PRIMARY KEY (`id`),
  KEY `author` (`author`),
  KEY `article` (`article`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `eventlog` */

DROP TABLE IF EXISTS `eventlog`;

CREATE TABLE `eventlog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `action` enum('Add','Delete','Download','Login','Logout','Update') DEFAULT NULL COMMENT 'тип события',
  `table` varchar(64) DEFAULT NULL,
  `element` varchar(64) DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL COMMENT 'комментарий',
  `ip` char(16) DEFAULT NULL COMMENT 'IP',
  `datetime` datetime DEFAULT CURRENT_TIMESTAMP COMMENT 'дата события',
  `user` int(6) DEFAULT NULL COMMENT '->users.id',
  PRIMARY KEY (`id`),
  KEY `action` (`action`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `eventlog_download` */

DROP TABLE IF EXISTS `eventlog_download`;

CREATE TABLE `eventlog_download` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `element` int(11) DEFAULT NULL,
  `referrer` varchar(255) DEFAULT NULL,
  `ip` char(16) DEFAULT NULL,
  `datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `filestorage` */

DROP TABLE IF EXISTS `filestorage`;

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
  `content` longblob COMMENT 'бинарный контент, не используется',
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

/*Table structure for table `filestorage_blob` */

DROP TABLE IF EXISTS `filestorage_blob`;

CREATE TABLE `filestorage_blob` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` longblob,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `news` */

DROP TABLE IF EXISTS `news`;

CREATE TABLE `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `publish_date` date DEFAULT NULL COMMENT 'дата публикации новости',
  `title_en` varchar(100) DEFAULT NULL COMMENT 'Заголовок новости (англ)',
  `title_ru` varchar(100) DEFAULT NULL COMMENT 'Заголовок новости (рус)',
  `title_ua` varchar(100) DEFAULT NULL COMMENT 'Заголовок новости (укр)',
  `comment` varchar(100) DEFAULT NULL COMMENT 'внутренний комментарий',
  `text_en` longtext COMMENT 'Текст новости (англ)',
  `text_ru` longtext COMMENT 'Текст новости (рус)',
  `text_ua` longtext COMMENT 'Текст новости (укр)',
  `stat_date_insert` datetime DEFAULT CURRENT_TIMESTAMP,
  `stat_date_update` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `publish_date` (`publish_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `ref_estaff_roles` */

DROP TABLE IF EXISTS `ref_estaff_roles`;

CREATE TABLE `ref_estaff_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `data_str` varchar(64) DEFAULT NULL COMMENT 'Роль в редколлегии',
  `data_int` int(11) DEFAULT NULL COMMENT 'Not used',
  `data_comment` varchar(64) DEFAULT NULL COMMENT 'Комментарий',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

/*Table structure for table `staticpages` */

DROP TABLE IF EXISTS `staticpages`;

CREATE TABLE `staticpages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias` char(64) DEFAULT NULL COMMENT 'идентификатор страницы',
  `public` int(11) DEFAULT '1' COMMENT 'Опубликована ли страница?',
  `title_en` varchar(200) DEFAULT NULL COMMENT 'Заголовок страницы (англ)',
  `title_ru` varchar(200) DEFAULT NULL COMMENT 'Заголовок страницы (рус)',
  `title_ua` varchar(200) DEFAULT NULL COMMENT 'Заголовок страницы (укр)',
  `content_en` longtext COMMENT 'Контент страницы (англ)',
  `content_ru` longtext COMMENT 'Контент страницы (рус)',
  `content_ua` longtext COMMENT 'Контент страницы (укр)',
  `comment` varchar(100) DEFAULT NULL COMMENT 'Комментарий',
  `stat_date_insert` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `stat_date_update` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `topicgroups` */

DROP TABLE IF EXISTS `topicgroups`;

CREATE TABLE `topicgroups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title_ru` varchar(80) DEFAULT NULL COMMENT 'Название (рус)',
  `title_en` varchar(80) DEFAULT NULL COMMENT 'Название (англ)',
  `title_ua` varchar(80) DEFAULT NULL COMMENT 'Название (укр)',
  `display_order` int(11) NOT NULL DEFAULT '0' COMMENT 'порядок отображения (больше - позже)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `topics` */

DROP TABLE IF EXISTS `topics`;

CREATE TABLE `topics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title_ru` char(80) DEFAULT NULL COMMENT 'Название (рус)',
  `title_en` char(80) DEFAULT NULL COMMENT 'Название (англ)',
  `title_ua` char(80) DEFAULT NULL COMMENT 'Название (укр)',
  `rel_group` int(11) DEFAULT NULL COMMENT '-> topic_groups',
  PRIMARY KEY (`id`),
  KEY `rel_group` (`rel_group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) DEFAULT NULL COMMENT 'ФИО',
  `email` varchar(250) DEFAULT NULL COMMENT 'мыло',
  `permissions` int(11) DEFAULT NULL COMMENT 'права доступа',
  `login` varchar(250) DEFAULT NULL COMMENT 'логин',
  `phone` varchar(250) DEFAULT NULL COMMENT 'телефон',
  `md5password` char(32) DEFAULT NULL COMMENT 'хэш пароля',
  `stat_date_insert` datetime DEFAULT NULL,
  `stat_date_update` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `login` (`login`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;


