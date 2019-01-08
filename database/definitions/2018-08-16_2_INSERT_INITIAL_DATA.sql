/*Data for the table `ref_estaff_roles` */

LOCK TABLES `ref_estaff_roles` WRITE;

INSERT INTO `ref_estaff_roles` (`id`, `data_str`, `data_int`, `data_comment`)
VALUES (1, 'Международная редакционная коллегия', 5, ''),
       (2, 'Ответственный секретарь', 6, ''),
       (3, 'Редакционная коллегия', 4, ''),
       (4, 'Заместитель главного редактора', 3, ''),
       (5, 'Главный редактор журнала', 2, ''),
       (6, 'Редактор', 8, ''),
       (7, 'Почетный главный редактор журнала', 1, ''),
       (8, 'Ответственный секретарь', 7, '');

UNLOCK TABLES;

/*Data for the table `users` */

LOCK TABLES `users` WRITE;

INSERT INTO `users` (`id`,
                     `name`,
                     `email`,
                     `permissions`,
                     `login`,
                     `phone`,
                     `md5password`,
                     `stat_date_insert`,
                     `stat_date_update`)
VALUES (1,
        'Root administator',
        'karel.wintersky@gmail.com',
        255,
        'root',
        '-',
        '85aa107b1a91721b4c3350eaa002269c',
        '2014-11-22 06:54:26',
        '2014-11-22 06:54:26');

UNLOCK TABLES;
