<?php
require_once('core.php');
require_once('db.php');

$link = ConnectDB();

$query = "SELECT id,title_rus,title_eng,title_ukr,udc,pdfid,add_date FROM articles WHERE deleted=0";
// получаем ВСЕ статьи, кроме удаленных @todo: это опция
$res = mysql_query($query) or die("Death on : $query");
$articles_count = @mysql_num_rows($res);

if ($articles_count>0) {
    while ($an_article = mysql_fetch_assoc($res))
    {
        $id = $an_article['id']; // айди статьи
        $all_articles[$id]['article'] = $an_article; // ВСЯ статья

        // получить информацию о связанной ПДФке
        $qp = "SELECT id,username,filesize FROM pdfdata WHERE articleid = $id";
        $rp = mysql_query($qp) or Die("Death on $qp");
        if (@mysql_num_rows($rp) > 0)
        {
            $all_articles[$id]['pdffile'] = mysql_fetch_assoc($rp);
        }

        // получить информацию об авторах

        $r_auths = mysql_query("SELECT authors.name_rus,authors.title_rus,authors.id FROM authors,cross_aa WHERE authors.id=cross_aa.author AND cross_aa.article=$id ORDER BY cross_aa.id");
        $r_auths_count = @mysql_num_rows($r_auths);

        if ($r_auths_count>0)
        {
            $i=1;
            while ($an_author = mysql_fetch_assoc($r_auths))
            {
                $all_articles[$id]['authors'] .= $an_author['name_rus']." (".$an_author['title_rus']." )<br>";
                $i++;
            }
            $all_articles[$id]['authors'] = substr($all_articles[$id]['authors'],0,-4);
        }
    }
}
CloseDB($link);
?>
<!DOCTYPE HTML>
<html>
<head>
    <title>Список статей</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <script src="js/jquery-1.10.2.min.js"></script>
    <link rel="stylesheet" type="text/css" href="ref_articles/articles.css">
    <style>
        .button-large {
            height: 60px;
        }
    </style>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.edit_button').on('click',function(){
                location.href = 'articles.edit.php?id='+$(this).attr('name');
            });
            $("#button-addnewarticle").on('click',function(){
                location.href = 'articles.new.php';
            });
            $("#button-exittoadminpanel").on('click',function(){
                location.href = 'admin.html';
            });
            $('.download-pdf').on('click',function(){
                id = $(this).attr('name');
                window.location.href="getpdf.php?id="+id;
            });
        });
    </script>

</head>

<body>
<button id="button-exittoadminpanel" class="button-large">Выход в админку </button>
<button id="button-addnewarticle" class="button-large">Добавить новую статью </button>
<table border="1" width="100%">
    <tr>
        <th width="3%">id</th>
        <th width="7%">УДК</th>
        <th>Авторы</th>
        <th>Название</th>
        <th width="10%">Дата</th>
        <th width="15%">Размер файла PDF</th>
        <th width="10%" colspan="2">Control</th>
    </tr>
    <?php
    if ($articles_count > 0) {
        foreach ($all_articles as $a_id => $a_article)
        {
            $row = $a_article;
            echo <<<REF_ANYARTICLE
<tr>
<td>{$row['article']['id']}</td>
<td>{$row['article']['udc']}</td>
<td><small>{$row['authors']}</small></td>
<td><small>Eng: {$row['article']['title_eng']}<br>Рус: {$row['article']['title_rus']}<br>Укр: {$row['article']['title_ukr']}</small></td>
<td>{$row['article']['add_date']}</td>
<td>{$row['pdffile']['username']} ({$row['pdffile']['filesize']} bytes)</td>
<td><button class="download-pdf" name="{$row['pdffile']['id']}">Show PDF</button></td>
<td class="centred_cell">
<button class="edit_button" name="{$row['article']['id']}">Edit</button>
</td>
</tr>
REF_ANYARTICLE;
        }
    } else {
        echo <<<REF_NUMROWS_ZERO
<tr><td colspan="8">$ref_message</td></tr>
REF_NUMROWS_ZERO;
    }

    ?>

</table>
</body>
