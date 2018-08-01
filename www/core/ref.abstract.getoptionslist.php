<?php
require_once '__required.php'; // $mysqli_link

// отдает JSON объект для построения selector/options list на основе абстрактного справочника
$ref = (isset($_GET['ref'])) ? $_GET['ref'] : '';

$ref = getAllowedValue( $ref, $CONFIG['allowed_abstract_refs'] );
//@todo: Config::get('allowed', 'allowed_abstract_ref');

if (!empty($ref))
{
    $query = "SELECT * FROM $ref";
    $result = mysqli_query($mysqli_link, $query) or die($query);
    $ref_numrows = @mysqli_num_rows($result) ;

    if ($ref_numrows>0)
    {
        $data['error'] = 0;
        while ($row = mysqli_fetch_assoc($result))
        {
            $data['data'][ $row['id'] ] = "[{$row['id']}] {$row['data_str']}";
        }
        $data['count'] = $ref_numrows;
    } else {
        $data['data'][1] = "Справочник $ref пуст!";
        $data['error'] = 1;
        $data['count'] = 0;
    }

} else {
    $data['data'][1] = "Справочник $ref не существует!";
    $data['error'] = 2;
    $data['count'] = 0;
}

print(json_encode($data));