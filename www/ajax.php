<?php
require_once('core/core.php');
require_once('core/core.db.php');
require_once('frontend.php');

// Здесь собраны функции-ответы на вывод различных данных, передаваемых аяксом.
// в основном это ответы на разные селекторы
$actor = isset($_GET['actor']) ? $_GET['actor'] : '';

$return = '';

$link = ConnectDB();

switch ($actor) {
    case 'get_letters_as_optionlist' : {
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
    case 'load_authors_all' : {
        $lang = isset($_GET['lang']) ? $_GET['lang'] : 'en';
        $letter = isset($_GET['letter']) ? $_GET['letter'] : '';
        $return = DBLoadAuthorsSelectedByLetter($letter,$lang);
        break;
    }

    case 'load_articles_selected_by_query' :{
        $lang = isset($_GET['lang']) ? $_GET['lang'] : 'en';
        $return = DBLoadArticlesList($_GET, $lang);

        break;
    } // case

    case 'load_articles_selected_by_query_with_letter' : {
        $lang = isset($_GET['lang']) ? $_GET['lang'] : 'en';
        $return = DBLoadArticlesListWithLetter($_GET, $lang);

        break;

    }

    case 'load_articles_all' : {
        // запускается на старте, выводит другое сообщение при отсутствии статей
        $lang = isset($_GET['lang']) ? $_GET['lang'] : 'en';
        $return = DBLoadArticlesList($_GET, $lang,'onload');
        break;
    }

    case 'load_articles_with_topic': {
        $topic = $_GET['topic'];
        $book = $_GET['book'];
        $lang = $_GET['lang'];

        $return = DBLoadArticlesList($_GET, $lang);
        break;
    } // case

    case 'get_books_as_optionslist' : {
        // alias to core/ref_books/ref.books.action.getoptionlist.php without id
        $lang = $_GET['lang'];

        $withoutid = 1;
        $q = "SELECT * FROM books WHERE deleted=0";
        $r = mysql_query($q) or die($q);
        $n = @mysql_num_rows($r) ;

        if ($n > 0)
        {
            $data['error'] = 0;
            while ($row = mysql_fetch_assoc($r))
            {
                $data['data'][ $row['id'] ] = returnBooksOptionString($row,$lang,$withoutid); // see core.php
            }
        } else {
            $data['data'][1] = "Добавьте книги (сборники) в базу!!!";
            $data['error'] = 1;
        }

        $return = json_encode($data);
        break;
    } // case

    case 'get_topics_as_optionslist' : {
        // alias to core/ref_topics/ref.topics.action.getoptionlist.php without id
        $lang = $_GET['lang'];
        $withoutid = 1;

        $query = "SELECT * FROM topics WHERE deleted=0";
        $result = mysql_query($query) or die($query);
        $ref_numrows = @mysql_num_rows($result) ;

        if ($ref_numrows > 0)
        {
            $data['error'] = 0;
            while ($row = mysql_fetch_assoc($result))
            {
                $data['data'][ $row['id'] ] = returnTopicsOptionString($row,$lang,$withoutid); // see CORE.PHP
            }
        } else {
            $data['data'][1] = "Добавьте темы (топики) в базу!!!";
            $data['error'] = 1;
        }

        $return = json_encode($data);
        break;
    } // case

    case 'get_authors_as_optionslist' : {
        $lang = $_GET['lang'];
        $withoutid = 1;

        $query = "SELECT * FROM authors WHERE deleted=0";
        if ($result = mysql_query($query)) {
            $ref_numrows = @mysql_num_rows($result) ;

            if ($ref_numrows>0)
            {
                $data['error'] = 0;
                while ($row = mysql_fetch_assoc($result))
                {
                    $data['data'][$row['id']] = returnAuthorsOptionString($row, $lang, $withoutid); // see CORE.PHP
                }
            } else {
                $data['data']['1'] = 'Добавьте авторов в базу!!!';
                $data['error'] = 1;
            }
        } else {
            $data['data']['2'] = "Ошибка работы с базой! [$query]";
            $data['error'] = 2;
        }
        $return = json_encode($data);
        break;
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