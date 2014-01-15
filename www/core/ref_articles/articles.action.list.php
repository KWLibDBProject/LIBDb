<?php
require_once('../core.php');
require_once('../core.db.php');
require_once('../core.kwt.php');

/* файл вызывается черех аякс лоадер
Варианты аргументов:

author - показать статьи АВТОРА
topic - показать статьи в топике
book - показать статьи в сборнике
*/

$link = ConnectDB();

$query = "
SELECT DISTINCT articles.id, articles.title_en AS title_en, articles.title_ru AS title_ru, articles.title_uk AS title_uk, udc, pdfid, add_date, pages,
topics.title_ru AS ttitle,
books.title AS btitle
from articles, cross_aa,topics,books
WHERE
cross_aa.article=articles.id
AND
articles.deleted=0
AND
topics.id=articles.topic
AND
books.id=articles.book";

$query .= (IsSet($_GET['author'])   && $_GET['author']!=0)  ? " AND cross_aa.author = $_GET[author] "   : "";
$query .= (IsSet($_GET['book'])     && $_GET['book']!=0 )   ? " AND articles.book = $_GET[book] "       : "";
$query .= (IsSet($_GET['with=topic'])    && $_GET['with=topic'] !=0 ) ? " AND articles.with=topic = $_GET[topic] "     : "";

$res = mysql_query($query) or die("Death on : $query");
$articles_count = @mysql_num_rows($res);

if ($articles_count>0) {
    while ($an_article = mysql_fetch_assoc($res))
    {
        $id = $an_article['id']; // айди статьи
        $all_articles[$id] = $an_article; // ВСЯ статья

        // получить информацию о связанной ПДФке
        $qp = "SELECT id,username,filesize FROM pdfdata WHERE articleid = $id";
        $rp = mysql_query($qp) or Die("Death on $qp");
        if (@mysql_num_rows($rp) > 0)
        {
            $all_articles[$id]['pdffile'] = mysql_fetch_assoc($rp);
        }

        // получить информацию об авторах

        //@todo: LANGUAGE (в админке все по русски)
        $r_auths = mysql_query("SELECT authors.name_ru,authors.title_ru,authors.id FROM authors,cross_aa WHERE authors.id=cross_aa.author AND cross_aa.article=$id ORDER BY cross_aa.id");
        $r_auths_count = @mysql_num_rows($r_auths);

        if ($r_auths_count>0)
        {
            $i=1;
            while ($an_author = mysql_fetch_assoc($r_auths))
            {
                $all_articles[$id]['authors'] .= $an_author['name_ru']." (".$an_author['title_ru']." )<br>";
                $i++;
            }
            $all_articles[$id]['authors'] = substr($all_articles[$id]['authors'],0,-4);
        }
    }
}
CloseDB($link);
?>
<table border="1" width="100%">
    <tr>
        <th width="3%">id</th>
        <th>Топик</th>
        <th>Сборник</th>
        <th width="7%">УДК</th>
        <th>Авторы</th>
        <th>Название</th>
        <th width="7%">Дата</th>
        <th width="105" colspan="1">Control<br><small>PDF info</small></th>
    </tr>
    <?php
    if ($articles_count > 0) {
        foreach ($all_articles as $a_id => $a_article)
        {
            $row = $a_article;
            //@todo: TEMPLATE : можно переделать под темлейты
            echo <<<REF_ANYARTICLE
<tr>
<td>{$row['id']}</td>
<td>{$row['ttitle']}</td>
<td>{$row['btitle']}</td>
<td>{$row['udc']}</td>
<td><small>{$row['authors']}</small></td>
<td><small>Eng: {$row['title_en']}<br>Рус: {$row['title_ru']}<br>Укр: {$row['title_uk']}</small></td>
<td>{$row['add_date']}</td>
<td>
<!-- <small>{$row['pdffile']['username']} ({$row['pdffile']['filesize']} bytes)</small><br> -->
    <button class="download-pdf" name="{$row['pdffile']['id']}" data-text="Show PDF"></button>
    <button class="edit_button" name="{$row['id']}" data-text="Edit"></button>
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