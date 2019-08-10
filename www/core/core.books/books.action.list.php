<?php
define('__ACCESS_MODE__', 'admin');
require_once '../__required.php'; // $mysqli_link

// выводит в виде таблицы содержимое справочника 'books' в админку
$sql_query = "
SELECT 
    books.id AS book_id, 
    books.title_en AS title_en,
    books.title_ru AS title_ru,
    books.title_ua AS title_ua, 
    DATE_FORMAT(books.published_date, '%d.%m.%Y') as date, 
    contentpages, 
    published_status, 
    file_cover, file_title_ru, file_title_en, file_toc_ru, file_toc_en,
    COUNT(articles.book) AS book_articles_count
FROM 
    books 
LEFT JOIN 
    articles 
ON 
    books.id = articles.book
GROUP BY 
    books.id, books.title_en, YEAR(books.published_date)
ORDER BY 
    books.published_date DESC";

$sql_query_result = mysqli_query($mysqli_link, $sql_query) or die("Невозможно получить содержимое таблицы: {$sql_query}");
$books_count = @mysqli_num_rows($sql_query_result);

$books_list = [];

if ($books_count > 0) {
    while ($book_record = mysqli_fetch_assoc($sql_query_result)) {
        $book_id = $book_record['book_id']; 
        
        $books_list[ $book_id ] = $book_record;
        $books_list[ $book_id ]['imploded_titles'] = implode(', <br>', [ $book_record['title_en'], $book_record['title_ru'], $book_record['title_ua'] ]); 
    }
}


$template_dir = '$/core/core.books';
$template_file = "_template.books.list.html";

$template_data = array(
    'books_list' => $books_list,
    'books_use_lang_depended_title' =>  Config::get('frontend/theme/book:use_lang_depended_title', false),
);

echo websun_parse_template_path($template_data, $template_file, $template_dir);
