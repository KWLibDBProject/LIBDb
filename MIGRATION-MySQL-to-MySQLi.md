# ФИКС языкового кода

Во ВСЕХ таблицах в именах полей `uk` меняем на `ua`

# Таблица news

Для конвертации legacy-даты в правильный формат недостаточно просто изменить тип ячейки. 

- Добавляем поле `publish_date`
- Конвертируем:
```
UPDATE news SET publish_date = STR_TO_DATE(date_add, '%d.%m.%Y')
```
- Удаляем legacy-поля `date_add`, `date_year`, `timestamp`

- Меняем дефолтные значения полей 
```
`stat_date_insert` DATETIME DEFAULT CURRENT_TIMESTAMP,
`stat_date_update` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
```


# Таблица staticpages

- Удаляем поле `deleted` 
- Меняем дефолтные значения полей 
```
`stat_date_insert` DATETIME DEFAULT CURRENT_TIMESTAMP,
`stat_date_update` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
```

# Таблица authors

- Добавляем поле orcid CHAR 16 DEFAULT ''
- Удаляем поле `deleted`
- переименовываем поле `selfhood` в `estaff_role`

- Меняем дефолтные значения полей 
```
`stat_date_insert` DATETIME DEFAULT CURRENT_TIMESTAMP,
`stat_date_update` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
```

# selfhood -> estaff_roles

- Переименовываем таблицу `ref_selfhood` в `ref_estaff_roles`

# Таблица books

- Удаляем поле `deleted`
- Меняем дефолтные значения полей 
```
`stat_date_insert` DATETIME DEFAULT CURRENT_TIMESTAMP,
`stat_date_update` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
```
- Поле `published` переименовываем в `published_status`

- Создаем поле `published_date` типа `DATE`

- Конвертируем:
```
UPDATE books SET published_date = STR_TO_DATE(date, '%d.%m.%Y')
```

Теперь поля `date`, `timestamp` и `year` не нужны, их можно удалить. 

# Таблица articles

- Удаляем поле `deleted`
- Переименовываем поле `add_date` в `date_add_legacy`
- Создаем поле `date_add DATE`
- конвертируем:
```
UPDATE articles SET `date_add` = STR_TO_DATE(date_add_legacy, '%d/%m/%Y')
```
- Удаляем `date_add_legacy`

- Меняем дефолтные значения полей
```
`stat_date_insert` DATETIME DEFAULT CURRENT_TIMESTAMP,
`stat_date_update` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
```

[todo] Переименовываем поле `date_add` в `publish_date` ?

# Таблица users

- Удаляем поле `password`
- Меняем тип полей `name`, `email`, `login`, `phone` на `varchar(250)`
- Меняем дефолтные значения полей 
```
`stat_date_insert` DATETIME DEFAULT CURRENT_TIMESTAMP,
`stat_date_update` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
```

# Таблица topics

- Удаляем поле `deleted`

# Таблица filestorage
- Меняем дефолтные значения полей 
```
`stat_date_insert` DATETIME DEFAULT CURRENT_TIMESTAMP,
`stat_date_update` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
```
- переименовываем поле `collection` в `collection_legacy`
- создаем поле `collection` типа `ENUM('articles', 'authors', 'books', 'pages')`
- выполняем обновление поля:
```
UPDATE filestorage SET `collection` = `collection_legacy`
```
- удаляем поле `collection_legacy` 
- Накладываем индексы на поля: `relation` и `collection`

# Таблица cross_aa

Накладываем индексы на все поля.

# Таблица ref_selfhood

Переименовываем в `ref_estaff_roles`

# Таблица eventlog

- Меняем тип `ip` на `char(16)`
- Меняем datetime на `DEFAULT CURRENT_TIMESTAMP`
- Меняем тип `user` на smallint
- Тип таблицы MyISAM

# Таблица eventlog_download
```

CREATE TABLE `eventlog_download` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `element` int(11) DEFAULT NULL,
  `referrer` varchar(255) DEFAULT NULL,
  `ip` char(16) DEFAULT NULL,
  `datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8

```
Переносим данные из eventlog в eventlog_download :

```
INSERT INTO eventlog_download (element, referrer, ip, `datetime`)

SELECT element, `comment`, ip, `datetime` FROM eventlog
WHERE `action` = 'Download'
```

И удаляем из старой таблицы:
```
DELETE FROM eventlog WHERE `action` = 'Download'
```

