<?php
// Здесь собраны функции-ответы на вывод различных данных, передаваемых аяксом.
// в основном это ответы на разные селекторы
require_once('core/core.php');
require_once('core/core.db.php');
require_once('frontend.php');
require_once('template.bootstrap24.php');

$actor = isset($_GET['actor']) ? $_GET['actor'] : '';
$lang = isset($_GET['lang']) ? GetRequestLanguage($_GET['lang']) : 'en';

$return = '';
$link = ConnectDB();

$engine = new Template($tpl_path, $lang);

switch ($actor) {
    case 'get_letters_as_optionlist' : {
        /* загрузить первые буквы авторов и отдать JSON-объект для построения селекта */
        $data = LoadFirstLettersForSelector($lang);
        $return = json_encode($data);
        break;
    }
    case 'get_books_as_optionlist' : {
        /* загрузить сборники и отдать JSON-объект для построения селекта */
        $withoutid = isset($_GET['withoutid']) ? $_GET['withoutid'] : 1;
        $q = "SELECT * FROM books WHERE published = 1 ORDER BY SUBSTRING(title, 6, 2)";
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
    case 'get_topics_as_option_list' : {
        /* загрузить категории и отдать JSON-объект для построения селекта */
        $withoutid = isset($_GET['withoutid']) ? $_GET['withoutid'] : 1;

        $data['data'] = LoadTopics($lang);

        if (sizeof($data['data'])) {
            $data['error'] = 0;
            $data['state'] = 'ok';
        } else {
            $data['data'][1] = "Добавьте тематические разделы в базу!!!";
            $data['error'] = 1;
        }
        $return = json_encode($data);
        break;
    }
    case 'get_topics_as_optgroup_list' : {
        /* загрузить категории и отдать JSON-объект для построения селекта c группировкой */
        $withoutid = isset($_GET['withoutid']) ? $_GET['withoutid'] : 1;

        $data = LoadTopicsTree($lang, $withoutid);
        if ($data['data'][1]['value'] != -1 ) {
            $data['state'] = 'ok';
            $data['error'] = 0;
        }

        $return = json_encode($data);
        break;
    }

    case 'load_articles_by_query' : {
        // Поиск статей - расширенный (/articles/extended/)
        // called by:     f_articles+w_extended.en.js ->
        $return = $engine -> getArticlesList($_GET);
        break;
    }

    case 'load_authors_selected_by_letter': {
        $return = $engine -> getAuthors_PlainList($_GET['letter']);
        break;
    }
    case 'load_articles_expert_search': {
        // Поиск статей - экспертный ( в keywords может быть склеенная плюсом строчка )
        $return = $engine -> getArticlesList($_GET);
        break;
    }
    default: {
        $return = 'Unknown request method!';
        break;
    }

} // switch


CloseDB($link);

if (isAjaxCall()) {
    print($return);
} else {
    print_r($return);
}
?>