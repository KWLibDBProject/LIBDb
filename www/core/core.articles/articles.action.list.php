<?php
require_once '../__required.php'; // $mysqli_link

/* файл вызывается черех аякс лоадер
Варианты аргументов:
author - показать статьи АВТОРА
topic - показать статьи в топике
book - показать статьи в сборнике
*/

/* этот же запрос существует в frontend.php (DB_BuildQuery)*/

// ttitle -> topic_title
// btitle -> book_title
// DATE_FORMAT(date_add, '%d.%m.%Y') as date_add

$query = "
SELECT 
DISTINCT articles.id, 
articles.title_en AS title_en, 
articles.title_ru AS title_ru, 
articles.title_uk AS title_uk, 
udc, 
pdfid, 
DATE_FORMAT(date_add, '%d.%m.%Y') as date_add, 
pages,
topics.title_ru AS ttitle,
books.title AS btitle

FROM articles, cross_aa, topics, books
WHERE
cross_aa.article=articles.id
AND
topics.id=articles.topic
AND
books.id=articles.book";

$query .= (IsSet($_GET['author'])   && $_GET['author']!=0)  ? " AND cross_aa.author = " . intval($_GET['author'])    : "";
$query .= (IsSet($_GET['book'])     && $_GET['book']!=0 )   ? " AND articles.book = "   . intval($_GET['book'])      : "";
$query .= (IsSet($_GET['topic'])    && $_GET['topic'] !=0 ) ? " AND articles.topic = "  . intval($_GET['topic'])     : "";

$query .= " ORDER BY articles.id";

$res = mysqli_query($mysqli_link, $query) or die("Death on : $query");
$articles_count = mysqli_num_rows($res);

$articles_list = array();
if ($articles_count>0) {
    while ($an_article = mysqli_fetch_assoc($res))
    {
        $id = $an_article['id']; // айди статьи

        $an_article['pdffile'] = FileStorage::getFileInfo($an_article['pdfid']);

        // получить информацию об авторах
        $r_auths = mysqli_query($mysqli_link, "SELECT authors.name_ru, authors.title_ru, authors.id FROM authors, cross_aa WHERE authors.id=cross_aa.author AND cross_aa.article=$id ORDER BY cross_aa.id");
        $r_auths_count = @mysqli_num_rows($r_auths);

        $an_article['authors_list'] = [];

        if ($r_auths_count>0)
        {
            $an_article['authors'] = '';

            while ($an_author = mysqli_fetch_assoc($r_auths))
            {
                $an_article['authors_list'][ /* $an_author['id']  */ ] = $an_author;

                /*$an_article['authors'] .= <<<ArticlesAL_AuthorsEach
<li> <a href="/?fetch=authors&with=info&id={$an_author['id']}&lang=ru" target="_blank">{$an_author['name_ru']}</a> ({$an_author['title_ru']})</li>
ArticlesAL_AuthorsEach;*/
            }
        }

        $articles_list[$id] = $an_article; // ВСЯ статья
    }
}

/*
по идее, следует использовать оптимизированный запрос для построения списка авторов, примерно такой:

SELECT

cross_aa.`article` AS article_id,
authors.name_ru AS author_name_ru,
authors.title_ru AS author_title_ru,
authors.id AS author_id
FROM `authors`, `cross_aa`
WHERE authors.id=cross_aa.author AND cross_aa.article IN (1, 2, 3) ORDER BY article_id, cross_aa.id

Здесь в IN() перечислены айдишники статей, попавших в выборку

Вывод будет примерно такой:

"article_id","author_name_ru","author_title_ru","author_id"
"1","Лебедев Владимир Александрович","доктор технических наук, профессор","1"
"2","Лимонов Леонид Григорьевич","канд. техн. наук","2"
"3","Акчебаш Наталья Викторовна","","4"
"3","Бойко Андрей Александрович","доктор технических наук, доцент","3"

Этот массив данных мы можем перебрать, ориентируясь на article_id (выше по коду: $articles_list[$id] ) и в

$an_article['authors_list'] записывать собранный массив из оставшихся трех полей этой множественной выборки

Этот метод будет более оптимален, но... требуется отладка и немного волшебства.

*/

$template_dir = '$/core/core.articles';
$template_file = "_template.articles.list.html";

$template_data = array(
    'articles_list' =>  $articles_list
);

echo \Websun\websun::websun_parse_template_path($template_data, $template_file, $template_dir);

/*
$return = <<<ArticlesAL_Start
<table border="1" width="100%">
ArticlesAL_Start;

$return .= <<<ArticlesAL_TH
    <tr>
        <th width="3%">id</th>
        <th>Тематический<br> раздел</th>
        <th>Сборник</th>
        <th width="7%">УДК</th>
        <th>Авторы</th>
        <th>Название</th>
        <th width="7%">Дата</th>
        <th width="105" colspan="1">Control<br><small>PDF info</small></th>
    </tr>
ArticlesAL_TH;

if ($articles_count > 0) {
    foreach ($articles_list as $a_id => $a_article)
    {
        $row = $a_article;
        $return .= <<<ArticlesAL_Each
<tr>
<td>{$row['id']}</td>
<td>{$row['ttitle']}</td>
<td><nobr>{$row['btitle']}</nobr></td>
<td>{$row['udc']}</td>
<td>
<ol class="articles-list-table-authors-list">
{$row['authors']}
</ol>
</td>
<td><small>{$row['title_ru']}</small></td>
<td class="center_cell"><small>{$row['date_add']}</small></td>
<td>
    <button class="action-download-pdf button-download-icon" name="{$row['pdffile']['id']}" data-text="Show PDF"></button>
    <button class="action-edit button-edit-icon" name="{$row['id']}" data-text="Edit"></button>
</td>
</tr>
ArticlesAL_Each;
    }
} else {
    $return .= <<<ArticlesAL_Nothing
<tr><td colspan="8">Статей не найдено</td></tr>

ArticlesAL_Nothing;
}

$return .= <<<ArticlesAL_End
</table>
ArticlesAL_End;
*/
// print($return);
