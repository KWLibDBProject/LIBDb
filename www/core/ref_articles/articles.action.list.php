<?php
/* файл вызывается черех аякс лоадер
Варианты аргументов:

author - показать статьи АВТОРА
topic - показать статьи в топике
book - показать статьи в сборнике
? объединение этих вариантов (правка морды и тут)
*/
$_author = (IsSet($_GET['author'])) ? $_GET['author'] : -1;

require_once('../core.php');
require_once('../db.php');

$link = ConnectDB();

$query = "
select distinct articles.id,title_eng,topics.title as ttitle,books.title as btitle
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
$query .= (IsSet($_GET['topic'])    && $_GET['topic'] !=0 ) ? " AND articles.topic = $_GET[topic] "     : "";

/*
 * new request is:
select articles.id,title_eng,title_rus,title_ukr,udc,pdfid,add_date,topics.title
from articles, cross_aa,topics,books
WHERE
cross_aa.article=articles.id AND
articles.deleted=0 AND
topics.id=articles.topic AND
books.id=articles.book
[ AND
cross_aa.author=$_author ]
[ AND
articles.book = $_book ]
[ AND
articles.topic = $_topic ]
*/
/*
$query = ($_author != -1)
    ? "select articles.id,title_eng,title_rus,title_ukr,udc,pdfid,add_date,topics.title
       from articles, cross_aa,topics WHERE cross_aa.author=$_author AND cross_aa.article=articles.id AND articles.deleted=0 AND topics.id=articles.topic"
    : "SELECT articles.id,title_rus,title_eng,title_ukr,udc,pdfid,add_date,topics.title as ttitle,books.title as btitle
FROM articles,topics,books
WHERE articles.deleted=0 AND topics.id=articles.topic AND books.id=articles.book";
*/
// получаем ВСЕ статьи, кроме удаленных @todo: это опция
$res = mysql_query($query) or die("Death on : $query");
$articles_count = @mysql_num_rows($res);

if ($articles_count>0) {
    while ($an_article = mysql_fetch_assoc($res))
    {
        $id = $an_article['id']; // айди статьи
        $all_articles[$id]['article'] = $an_article; // ВСЯ статья
        // $all_articles[$id]['article']['id'] = $id;

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
<table border="1" width="100%">
    <tr>
        <th width="3%">id</th>
        <th>Топик</th>
        <th>Сборник</th>
        <th width="7%">УДК</th>
        <th>Авторы</th>
        <th>Название</th>
        <th width="10%">Дата</th>
        <th width="8%">PDF</th>
        <th width="10%" colspan="1">Control</th>
    </tr>
    <?php
    if ($articles_count > 0) {
        foreach ($all_articles as $a_id => $a_article)
        {
            $row = $a_article;
            echo <<<REF_ANYARTICLE
<tr>
<td>{$row['article']['id']}</td>
<td>{$row['article']['ttitle']}</td>
<td>{$row['article']['btitle']}</td>
<td>{$row['article']['udc']}</td>
<td><small>{$row['authors']}</small></td>
<td><small>Eng: {$row['article']['title_eng']}<br>Рус: {$row['article']['title_rus']}<br>Укр: {$row['article']['title_ukr']}</small></td>
<td>{$row['article']['add_date']}</td>
<td>{$row['pdffile']['username']}<br><small>({$row['pdffile']['filesize']} bytes)<small></td>
<td>
    <button class="download-pdf" name="{$row['pdffile']['id']}" data-text="Show PDF"></button>
    <button class="edit_button" name="{$row['article']['id']}" data-text="Edit"></button>
</td>
</tr>
REF_ANYARTICLE;
        }
    } else {
        echo <<<REF_NUMROWS_ZERO
<tr><td colspan="7">$ref_message</td></tr>
REF_NUMROWS_ZERO;
    }

    ?>

</table>