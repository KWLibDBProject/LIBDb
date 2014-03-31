<?php
require_once('core.db.php');
$file_storage = 'filestorage';

// base64-строка для плашки "нет изображения" 240х180
$noimg_240x180 = 'R0lGODlh8AC0ALMPAIiIiLu7u0RERBEREe7u7iIiIszMzN3d3TMzM2ZmZqqqqlVVVZmZmXd3dwAA';
$noimg_240x180.= 'AP///yH5BAEAAA8ALAAAAADwALQAAAT/8MlJq7046827/2AojmRpnmiqrmzrvnAsz3Rt33iu73zv';
$noimg_240x180.= '/8CgcEgsGo/IpHLJbDqf0Kh0Sq1ar9isdsvter/gsHhMLpvP6LR6zW673/C4fE6v2+/4vH7P7/v/';
$noimg_240x180.= 'gIGCg4SFhoeIiYqLjI2Oj5CRkpOUlZaXmJmam5ydnp+goaKjpKWmp6ipqqusra6vsLGys7S1tre4';
$noimg_240x180.= 'ubq7vL2+v8DBwsPExcbHyMnKy8zNzs/QVAqUBAHWBxYG1gFnBgANAgMOlAEO5gAWAubjZAwF6+vk';
$noimg_240x180.= '6+gV6uZkCfDrA/Ln6fFiGMBDkABAAAL9HNCjYI9dmHcOBhjIVM5fPYAWqh3ksQ2hBm0b/y0oWMcg';
$noimg_240x180.= 'hDZsGUDGuFalosJ/9yoYaOgggccHAPTBW3ghpwMBFBqs4/bgAAJ9BaZVIABA3LoFE4OuI8BggYAE';
$noimg_240x180.= 'JSvMMwDxZ9QJARI4jcjT588J9oBKaMiTQL51Ar4+cEkUJ0a7ZycINVf3AdOj+tSKcMlz7d0HBsau';
$noimg_240x180.= 'Q+DRrM7CFXwKdumAKGV4SiUQAKwz8wN7BTibW6B1tOKINz/rdNCAwoJ1StNKGDmawmadEsEOnWCW';
$noimg_240x180.= 'gmTduyeIhic4xOXVMTVDHNCggVN6jvVB9m1OsGjLAxYAANCVNNp9AroOuNl1desJ+hDQTMDQAYLt';
$noimg_240x180.= 'Y28ScDpe9dn55upPeG2uIMTJweHlEP9e1sFTF20OFLDdfyQchxx15kQlUIIV0NXBbwLypZl8cE1g';
$noimg_240x180.= 'wGIevfWSBPAsgI1jN8GT1Yf5BfUViw545lJrsu1VGQUw0nPAOihZKEFvvFX3oz51mYWSbCM4uFp7';
$noimg_240x180.= 'NVHAIwU+bvDbAeKM1ZdmZi1kVmYELIaeOQVQIGJmHe73ZDYIFmbWTNXlGBmYTGZF2DYiQggUlREF';
$noimg_240x180.= 'aJZHSA4G13aAcnfXOgUIYGhDfUWpwW/8mdWXPgPw1FBqDX2Z12zzWFpWgAc0UJ5FFACmXnX2IADT';
$noimg_240x180.= 'AIeGY5GSdzF6DqdPWQNYcSAQBtOAyAU4l64ZSEZbAV1qaGl1WRmWnLEOlSmBrSSCKiD/UXjuk+mb';
$noimg_240x180.= '+YE5LZPI0cPqsb6CGeyNEzBwmpBJXvvdsYSmeqhcivZaHUTc6ApoVwtViu25YU6QpqZSRSgBfwgo';
$noimg_240x180.= 'gJC5iK2TQFo2XmkPquoKIKfBgNo75E/wPsArgsQ1SLB9AwLG3gbtYhCdd7xOsFyQ5nCprI0oPZCw';
$noimg_240x180.= 'pR9L0NUETin1bWGAjZcWfgmmtle+GEQJ5MRPNQsuptUFMKvGziIrZn5yHdDysiX3BI9+wU39wMxU';
$noimg_240x180.= 'm8OYBCIu5JJaibU4bFRmeWexOVllCeFLsiEY8wMTOlCsX18J3erVHulKH0J9GrexxEXBUxAA6lwZ';
$noimg_240x180.= 'stXXBsfd4ZyZem5E4RFKabo7OUlc/3l1LQeAiCMWDKd9ajXkWVcCbLdApMAdPXSGCwVYNz2B1zr4';
$noimg_240x180.= 'YXUTWWHVFphVnOPIeXYbbnIhNm6Tmieo09zRsSUcbMaqtWN+N5X9WOtF7k2u0Z2fWfsHzN5bgQLD';
$noimg_240x180.= '2bS7sFKuI1dw4qbnmWZiGa61BAfwByZk85jFegUiCnAz0YLpk+9wZL82YQ9lA1pT8ohSt5h9rwYH';
$noimg_240x180.= 'sEbxbBBBa3BgGxzQyPyMhg6NZCCCG1yBRq4UjI1FowYmPOEMUqjCGLCwhS94IQxnSMMa2vCGOMyh';
$noimg_240x180.= 'DnfIwx768IdADKIQh0jEIhrxiEhMohKXyMQmOvGJUIyiFKdIxSpa8YpYzKIWt8jFLjJ68YtgDKMY';
$noimg_240x180.= 'x0jGMprxjGhMoxrXyMY2uvGNcIyjHOdIxzra8Y54zKMe98jHPvrxj0SMAAA7';

$id = isset($_GET['id']) ? $_GET['id'] : -1;
if ($id != -1) {

    $link = ConnectDB() or Die("Не удается соединиться с базой данных!");
    $q = "SELECT content, filetype, username FROM $file_storage WHERE (id=$id)";
    $result = mysql_query($q);// or Die("Не удается выполнить запрос к БД, строка запроса: [$q]");

    if ($result !== FALSE) {
        $fetch = mysql_fetch_array($result) or Die("Не удается получить результат запроса ($q)");

        header ("HTTP/1.1 200 OK");
        header ("X-Powered-By: PHP/" . phpversion());
        header ("Expires: Thu, 19 Nov 1981 08:52:00 GMT");
        header ("Cache-Control: None");
        header ("Pragma: no-cache");
        header ("Accept-Ranges: bytes");
        header ("Content-Disposition: inline; filename=\"" . $fetch['username'] . "\"");
        header ("Content-Type: application/octet-stream");
        // header ("Content-Length: " . $filesize);
        header ("Age: 0");
        header ("Proxy-Connection: close");

        print($fetch['content']);
        flush();
        CloseDB($link);
    } else {
        header("Content-type: image/gif");
        print(base64_decode($noimg_240x180));
        flush();
    }
} else
{
    header("Content-type: image/gif");
    print(base64_decode($noimg_240x180));
    flush();
};
?>
