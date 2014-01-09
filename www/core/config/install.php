<?php

/* Таблицы пока не создаем */

ConnectDB();
$q_root = "SELECT id FROM users WHERE login='root'";
$r_root = mysql_query($q_root);

$tpl = new kwt('install.tpl');
$tpl->contentstart();

if (mysql_num_rows($r_root)==0)
{
    // root not found
    $root = array(
        'name' => 'Root administator',
        'email' => mysql_real_escape_string('karel.wintersky@gmail.com'),
        'permissions' => '255',
        'login' => 'root',
        'password' => 'root',
        'phone' => '',
        'md5password' => md5('root')
    );
    $q = MakeInsert($root,'users');
    $r = mysql_query($q) or Die("Unable to insert data to DB!   ".$q);
    echo 'Root user created!';
} else {
    echo 'Root user found.';
}
$tpl->contentend('message'); // при использовании шаблона и выводе инфомрации еще в скрипте вызываем обязательно!!!
$tpl->out();

?>