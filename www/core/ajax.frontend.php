<?php
require_once('core.frontend.php');

// Здесь собраны функции-ответы на вывод различных данных, передаваемых аяксом.
// в основном это ответы на разные селекторы
$actor = isset($_GET['actor']) ? $_GET['actor'] : '';

$return = '';

$link = ConnectDB();

switch ($actor) {
    case 'load_letters_optionlist' : {
        $lang = isset($_GET['lang']) ? $_GET['lang'] : 'en';

        $return = json_encode(DBLoadFirstLettersForSelector($lang));
        break;
    }

    case 'load_authors_selected_by_letter': {
        $lang = isset($_GET['lang']) ? $_GET['lang'] : 'en';
        $letter = isset($_GET['letter']) ? $_GET['letter'] : '';

        $return = DBLoadAuthorsSelectedByLetter($letter,$lang);

        break;
    } // case
    case 'load_articles_selected_by_query' :{
        $lang = isset($_GET['lang']) ? $_GET['lang'] : 'en';
        $return = DBLoadArticlesList($_GET, $lang);

        break;
    } // case;
    case 'load_articles_with_topic': {
        $topic = $_GET['topic'];
        $book = $_GET['book'];
        $lang = $_GET['lang'];

        $return = DBLoadArticlesList($_GET, $lang);
    } // case



    default: {

    }
} // switch

CloseDB($link);

if (isAjaxCall()) {
    print($return);
} else {
    printr($return);
}
?>