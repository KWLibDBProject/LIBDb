<?php
define('__ACCESS_MODE__', 'frontend');

require_once '../__required.php'; // $mysqli_link
// отдает JSON объект для селектора 'books'

//@todo: input param 'lang' is useless

$lang = isset($_GET['lang']) ? $_GET['lang'] : 'ru';

$lang = getAllowedValue( $lang, array(
    'ru', 'en', 'ua'
), 'en');

$withoutid = isset($_GET['withoutid']) ? 1 : 0;

$flag_with_id = isset($_GET['id']) ? 1 : 0;

$query = "SELECT * FROM books ";
$result = mysqli_query($mysqli_link, $query) or die($query);
$ref_numrows = @mysqli_num_rows($result) ;

if ($ref_numrows>0)
{
    $data['error']  = 0;
    $data['state']  = 'ok';
    while ($row = mysqli_fetch_assoc($result))
    {
        $ov_id = ($withoutid == 1) ? '' : "[{$row['id']}]" ;
        $option_value = "{$ov_id} {$row['title']}";

        $data['data'][ $row['id'] ] = array(
            'type'      => 'option',
            'value'     => $row['id'],
            'text'      => $option_value
        );
    }
} else {
    $data['data'][1] = "Добавьте книги (сборники) в базу!!!";
    $data['error'] = 1;
}

print(json_encode($data));

