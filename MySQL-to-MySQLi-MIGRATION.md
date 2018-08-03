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

# Таблица articles

- Переименовываем поле `add_date` в `date_add_legacy`
- Создаем поле `date_add DATE`
- конвертируем:
```
UPDATE articles SET `date_add` = STR_TO_DATE(date_add_legacy, '%d/%m/%Y')
```

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



