<?php

/* для выгрузки скриптов создания сайтов надо использовать:
SHOW CREATE TABLE `anytable`
*/

$all_tables = array(
    'articles' =>
"CREATE TABLE `articles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `udc` char(30) DEFAULT NULL,
  `title_en` char(100) DEFAULT NULL,
  `title_ru` char(100) DEFAULT NULL,
  `title_uk` char(100) DEFAULT NULL,
  `abstract_en` longtext,
  `abstract_ru` longtext,
  `abstract_uk` longtext,
  `keywords_en` longtext,
  `keywords_ru` longtext,
  `keywords_uk` longtext,
  `refs` longtext,
  `book` int(11) DEFAULT NULL,
  `add_date` char(60) DEFAULT NULL,
  `deleted` int(11) DEFAULT '0',
  `pdfid` int(11) DEFAULT NULL,
  `topic` int(11) DEFAULT NULL,
  `pages` char(60) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8",

    'authors' =>
"CREATE TABLE `authors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name_ru` char(100) DEFAULT NULL,
  `name_en` char(100) DEFAULT NULL,
  `name_uk` char(100) DEFAULT NULL,
  `workplace` char(200) DEFAULT NULL,
  `email` char(100) DEFAULT NULL,
  `deleted` int(11) DEFAULT '0',
  `title_en` char(100) DEFAULT NULL,
  `title_ru` char(100) DEFAULT NULL,
  `title_uk` char(100) DEFAULT NULL,
  `phone` char(60) DEFAULT NULL,
  `is_es` int(11) DEFAULT '0',
  `bio` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8",

    'books' =>
    "CREATE TABLE `books` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` char(80) DEFAULT NULL,
  `date` char(30) DEFAULT NULL,
  `deleted` int(11) DEFAULT '0',
  `published` int(11) DEFAULT '0',
  `contentpages` char(60) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8",

    'topics' =>
    "CREATE TABLE `topics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `deleted` int(11) DEFAULT '0',
  `title_ru` char(80) DEFAULT NULL,
  `title_en` char(80) DEFAULT NULL,
  `title_uk` char(80) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8",

    'cross_aa' =>
    "CREATE TABLE `cross_aa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `author` int(11) DEFAULT NULL,
  `article` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8",

    'pdfdata' =>
    "CREATE TABLE `pdfdata` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` longblob,
  `username` char(200) DEFAULT NULL,
  `tempname` char(200) DEFAULT NULL,
  `filesize` int(11) DEFAULT NULL,
  `articleid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8",

    'users' =>
    "CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(100) DEFAULT NULL,
  `email` char(100) DEFAULT NULL,
  `permissions` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT '0',
  `login` char(100) DEFAULT NULL,
  `password` char(100) DEFAULT NULL,
  `phone` varchar(60) DEFAULT NULL,
  `md5password` char(32) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8"
);
$root_user = array(
    'name' => 'Root administator',
    'email' => mysql_real_escape_string('karel.wintersky@gmail.com'),
    'permissions' => '255',
    'login' => 'root',
    'password' => 'root',
    'phone' => '-',
    'md5password' => md5('root')
);

ConnectDB();

$tpl = new kwt('install.tpl');
$tpl -> contentstart();

foreach ($all_tables as $table => $table_script)
{
    if (!DBIsTableExists($table)) {
        if (!mysql_query($table_script)) {
            echo "<span class='error'>ERROR:</span> unable to create table `$table` ! <br>";
        } else {
            echo "<span class='ok'>OK:</span> table `$table` created! <br>";
        }
    } else {
        echo "<span class='ok'>OK:</span> `$table` exists! <br>";
    }
}

echo '<hr>';
echo 'Test for root: <br>';
$r_root = mysql_query("SELECT `id` FROM users WHERE login='root'");
if (mysql_num_rows($r_root)==0)
{
    if (!mysql_query(MakeInsert($root_user,'users'))) {
        echo "<span class='error'>ERROR:</span> unable to create *root* user! <br>";
    } else {
        echo "<span class='ok'>OK:</span>*Root* user created!  <br>";
    }
} else {
    echo "<span class='ok'>OK:</span>*Root* user found.  <br>";
}

$tpl->contentend('message'); // при использовании шаблона и выводе инфомрации еще в скрипте вызываем обязательно!!!
$tpl->out();
?>