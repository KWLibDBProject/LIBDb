<?php
require_once '../__required.php'; // $mysqli_link

// отдает JSON объект для селектора 'books'
// this script is duplicated with books.action.getoptionlist.php,
// differences is returned data format. @todo: optimise .js & remove this

$lang = isset($_GET['lang']) ? $_GET['lang'] : 'ru';

$lang = getAllowedValue( $lang, array(
    'ru', 'en', 'ua', 'uk'
));

$withoutid = isset($_GET['withoutid']) ? 1 : 0;

$query = "SELECT * FROM books ORDER BY SUBSTRING(title, 6, 2)";
$result = mysqli_query($mysqli_link, $query) or die($query);
$ref_numrows = @mysqli_num_rows($result) ;

if ($ref_numrows>0)
{
    $data['error'] = 0;
    while ($row = mysqli_fetch_assoc($result))
    {
        $data['data'][ $row['id'] ] = returnBooksOptionString($row,$lang,$withoutid); // see core.php
    }
} else {
    $data['data'][1] = "Добавьте книги (сборники) в базу!!!";
    $data['error'] = 1;
}

print(json_encode($data));
