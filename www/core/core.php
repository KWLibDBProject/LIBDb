<?php
// функции ядра
// аналог plural
function GetHumanFriendlyCounter($num,$str1,$str2,$str3)
{
    $ret = '';
    if ($num==0) $ret = $str3;
    if ($num==1) $ret = $str1;
    if ($num<21)
    {
        if ($num == 1) $ret = $str1;
        if (($num>1)&&($num<5)) $ret = $str2;
        if (($num>4)&&($num<21)) $ret = $str3;
    }
    else
    {
        $residue = ($num%10);
        if ($residue == 1) $ret = $str1;
        if (($residue>1)&&($residue<5)) $ret = $str2;
        if (($residue>4)&&($residue<=9)) $ret = $str3;
        if ($residue == 0) $ret = $str3;
    }
    return $ret;
}

function floadpdf($filename)
{
    $fh = fopen($filename,"rb");
    $real_filesize = filesize($filename);
    $blobdata = fread($fh, $real_filesize);
    fclose($fh);
    return $blobdata;
}

function isAjaxCall($debugmode=false)
{
    $debug = (isset($debugmode)) ? $debugmode : false;
    return ((!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) && (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) || ($debug);
}
function Redirect($url)
{
    if (headers_sent() === false) header('Location: '.$url);
}

function isLogged()
{
    $we_are_logged = !empty($_SESSION);
    $we_are_logged = $we_are_logged && isset($_SESSION['u_id']);
    $we_are_logged = $we_are_logged && $_SESSION['u_id'] !== -1;
    // вот тут мы проверямем куки и сессию на предмет "залогинились ли мы"
    // return $we_are_logged ? 1 : 0;
    return (int) $we_are_logged ;
}

function printr($str)
{
    echo '<pre>'.print_r($str,true).'</pre>';
}
?>