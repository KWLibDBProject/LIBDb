<?php
require_once '../__required.php'; // $mysqli_link

// выводит в виде таблицы содержимое справочника 'books' в админку

$ref_name = 'books';

//@todo:date - GROUP BY books.year => GROUP BY YEAR(books.published_date)

$query = "
SELECT 
books.id AS book_id, 
books.title, 
books.date, 
contentpages, 
published_status, 
file_cover, file_title_ru, file_title_en, file_toc_ru, file_toc_en,
 COUNT(articles.book) AS book_articles_count
 FROM books 
 
 LEFT JOIN articles ON books.id=articles.book
 
 GROUP BY books.id, books.title, books.year
 ORDER BY books.title DESC";

$res = mysqli_query($mysqli_link, $query) or die("Невозможно получить содержимое справочника! ".$query);
$books_count = @mysqli_num_rows($res) ;

$books_list = [];

if ($books_count > 0) {
    while($book_record = mysqli_fetch_assoc($res)) {
        $books_list[$book_record['book_id']] = $book_record;
    }
}


$template_dir = '$/core/core.books';
$template_file = "_template.books.list.html";

$template_data = array(
    'books_list' =>  $books_list
);

echo \Websun\websun::websun_parse_template_path($template_data, $template_file, $template_dir);
