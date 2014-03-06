<?php
// Здесь собраны функции-ответы на вывод различных данных, передаваемых аяксом.
// в основном это ответы на разные селекторы
require_once('core/core.php');
require_once('core/core.db.php');
require_once('frontend.php');

$actor = isset($_GET['actor']) ? $_GET['actor'] : '';
$lang = isset($_GET['lang']) ? $_GET['lang'] : 'en';

$return = '';
$link = ConnectDB();

switch ($actor) {
    case 'get_letters_as_optionlist' : {
        /* загрузить первые буквы авторов и отдать JSON-объект для построения селекта */
        $return = json_encode(DB_LoadFirstLettersForSelector($lang));
        break;
    }
    case 'get_books_as_optionlist' : {
        /* загрузить сборники и отдать JSON-объект для построения селекта */
        $withoutid = isset($_GET['withoutid']) ? $_GET['withoutid'] : 1;
        // $withoutid = 1;
        $q = "SELECT * FROM books WHERE deleted=0 AND published=1";
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
    }
    case 'get_topics_as_optionlist' : {
        /* загрузить категории и отдать JSON-объект для построения селекта */
        $withoutid = isset($_GET['withoutid']) ? $_GET['withoutid'] : 1;
        // $withoutid = 1;

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
    }

    case 'load_articles_by_query' : {
        $return = FE_PrintArticlesList_Extended(DB_LoadArticlesByQuery($_GET, $lang, 'no'), $lang);
        break;
    }

    case 'load_authors_selected_by_letter': {
        $return = FE_PrintAuthors_PlainList(DB_LoadAuthors_ByLetter($_GET['letter'], $lang, 'no'), $lang);
        break;
    }


} // switch


CloseDB($link);

if (isAjaxCall()) {
    print($return);
} else {
    printr($return);
}
?>