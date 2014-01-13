<?php
// Здесь собраны функции-ответы на вывод различных данных, передаваемых аяксом.
// в основном это ответы на разные селекторы
$actor = isset($_GET['actor']) ? $_GET['actor'] : '';

$return = '';

$link = ConnectDB();

switch ($actor) {
    case 'load_letters_optionlist' : {
        //@todo: возврат массива "первых букв" для списка авторов
        $return['error'] = 0;
        $return['data']['1'] = '1';
        $return['data']['2'] = '2';
        $return = json_encode($return);
        break;
    }
    case 'load_authors_selected_by_letter': {
        // показать список авторов ссылками с отбором по букве
        $firstletter = isset($_GET['letter']) ? $_GET['letter'] : '';
        $q = "SELECT * FROM authors WHERE `deleted`=0";
        $r = mysql_query($q) or Die(0);
        $n = mysql_num_rows($r);
        // ФИО, научное звание/ученая степень
        //@todo: форматная строка вывода @ load_authors_selected_by_letter
        $return = <<<LASBL_START
        <ul>
LASBL_START;
        if ($n > 0) {
            while ($i = mysql_fetch_assoc($r)){
                //@todo: эктор-ссылка на /authors/info/(id) - работает, якорь для замены в модреврайт
                $return .= <<<LASBL_EACH
<li>
<label>
<a href="?fetch=authors&with=info&id={$i['id']}">{$i['name_rus']} , {$i['title_rus']}, {$i['email']}</a>
</label>
</li>
LASBL_EACH;
            } // while
        } else {
        $ret .= <<<LASBL_EMPTY
</ul>
LASBL_EMPTY;
        } // else
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