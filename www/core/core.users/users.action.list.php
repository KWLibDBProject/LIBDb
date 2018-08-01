<?php
require_once '../__required.php'; // $mysqli_link

$ref_name = 'users';

$ref_prompt = IsSet($_GET["prompt"]) ? ($_GET["prompt"]) : 'Работа с пользователем';

$query = "SELECT * FROM $ref_name ";

$res = mysqli_query($mysqli_link, $query); // or die("Невозможно получить содержимое справочника! ".$ref_name);
$ref_numrows = @mysqli_num_rows($res) ;

if ($ref_numrows > 0) {
    while ($ref_record = mysqli_fetch_assoc($res))
    {
        foreach($ref_record as $rid => $rfield)
        {
            $new_record[ $rid ] = (empty($rfield)) ? '&nbsp;' : $rfield; // пустые поля превратить в неразрывные пробелы
        }
        $ref_list[ $ref_record['id'] ] = $new_record;
    }
} else {
    $ref_message = 'Пока не ввели ни одного пользователя!';
}

$return = <<<RUA_Start
<table border="1" width="100%">
RUA_Start;

$return .= <<<RUA_TH
<tr>
    <th width="5%">№</th>
    <th>Ф.И.О.</th>
    <th>E-Mail</th>
    <th>Телефон</th>
    <th>Уровень<br>доступа</th>
    <th>Login</th>
    <th width="10%">Управление</th>
</tr>
RUA_TH;

if ($ref_numrows > 0) {
    foreach ($ref_list as $r_id => $r_value)
    {
        $row = $r_value;
        $is_disabled = ($row['permissions'] == 255) ? ' disabled' : '';
        $return .=<<<RUA_EACH
<tr>
    <td>{$row['id']}</td>
    <td>{$row['name']}</td>
    <td>{$row['email']}</td>
    <td>{$row['phone']}</td>
    <td>{$row['permissions']}</td>
    <td>{$row['login']}</td>
    <td class="centred_cell"><button class="actor-edit button-edit" name="{$row['id']}" $is_disabled>Edit</button></td>
</tr>
RUA_EACH;
    }
} else {
    $return .= <<<RUA_NOTHING
<tr><td colspan="7">Пока не добавили ни одного пользователя!</td></tr>
RUA_NOTHING;
}

print($return);