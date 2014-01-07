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

function isAjaxCall()
{
    return ((!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) && (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) || ($GLOBALS['debugmode']);
}

?>