# ФИКС языкового кода

ВЕЗДЕ, включая таблицы `uk` исправляем на `ua`

# Таблица news

Для конвертации legacy-даты в правильный формат недостаточно просто изменить тип ячейки. 

- Переименовываем старое поле в `date_add_legacy`
- Добавляем поле `date_add`
- Конвертируем:
```
UPDATE news SET DATE_ADD = STR_TO_DATE(date_add_legacy, '%d.%m.%Y')
```
- Удаляем legacy-поля `date_add_legacy` и `date_year`

- Соответственно фиксим выборки данных
```
"SELECT id, title_ru, DATE_FORMAT(date_add, '%d.%m.%Y') as date_add FROM news"
```
- ...и вставку контента
```
'date_add'      => DateTime::createFromFormat('d.m.Y', $_POST['date_add'])->format('Y-m-d'),
'timestamp'     => DateTime::createFromFormat('d.m.Y', $_POST['date_add'])->format('U'),
```
- Меняем дефолтные значения полей 
```
`stat_date_insert` DATETIME DEFAULT CURRENT_TIMESTAMP,
`stat_date_update` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
```

# Таблица staticpages

- Меняем дефолтные значения полей 
```
`stat_date_insert` DATETIME DEFAULT CURRENT_TIMESTAMP,
`stat_date_update` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
```

# Таблица authors

- Добавляем поле orcid CHAR 16 DEFAULT ''

- Меняем дефолтные значения полей 
```
`stat_date_insert` DATETIME DEFAULT CURRENT_TIMESTAMP,
`stat_date_update` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
```

# Таблица books

- Меняем дефолтные значения полей 
```
`stat_date_insert` DATETIME DEFAULT CURRENT_TIMESTAMP,
`stat_date_update` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
```


[todo] : поле `date` legacy, поле `timestamp`

потребуют обновления методы:
- Все методы в админке
- LoadArticles_ByAuthor()
- LoadLastBookInfo()


# Таблица articles

- Переименовываем поле `add_date` в `date_add_legacy`
- Создаем поле `date_add DATE`
- конвертируем:
```
UPDATE articles SET `date_add` = STR_TO_DATE(date_add_legacy, '%d/%m/%Y')
```
- Удаляем `date_add_legacy`

# Таблица users

- Удаляем поле `password`
- Меняем дефолтные значения полей 
```
`stat_date_insert` DATETIME DEFAULT CURRENT_TIMESTAMP,
`stat_date_update` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
```

# Таблица filestorage
- Меняем дефолтные значения полей 
```
`stat_date_insert` DATETIME DEFAULT CURRENT_TIMESTAMP,
`stat_date_update` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
```

# Таблица cross_aa

Накладываем индексы на все поля.

# Таблица ref_selfhood

Переименовываем в ref_estaff_roles

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

# selfhood -> estaff_roles

- Переименовываем таблицу `ref_selfhood` в `ref_estaff_roles`

В таблице `authors` переименовываем поле `selfhood` в `estaff_role` 