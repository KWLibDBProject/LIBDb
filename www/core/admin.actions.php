<?php
/**
 * User: Arris
 * Date: 03.08.2018, time: 4:18
 */
// prepare data
require_once '__required.php'; // $mysqli_link
$SID = session_id();
if(empty($SID)) session_start();

$action = $_GET['try:action'] ?? 'form';

$_GET=[]; unset($_GET);

switch ($action) {
    case 'try:login': {
        $result = DBLoginCheck($_POST['login'], $_POST['md5password']);

        if ($result['error']==0) {
            $_SESSION['u_id']           = $result['id'];
            $_SESSION['u_permissions']  = $result['permissions'];

            setcookie('u_libdb_logged', $result['id'], 0, '/');
            setcookie('u_libdb_permissions', $result['permissions'], 0, '/'); //ENCRYPT COOKIE

            Redirect('/core/admin.php');
        } else {

            die(<<<ERRORMESSAGE
Error: {$result['message']} <br>
<a href="/core/admin.actions.php">Return to login form</a>
ERRORMESSAGE

            );
        }

        break;
    }

    case 'try:logout': {
        kwLogger::logEvent('login', 'userlist', $_SESSION['u_username'], 'User logged out');

        setcookie('u_libdb_logged',FALSE,-1, '/');
        unset($_COOKIE['u_libdb_logged']);
        $_SESSION['u_libdb_logged'] = -1;

        setcookie('u_libdb_permissions',FALSE,-1, '/');
        unset($_COOKIE['u_libdb_permissions']);
        $_SESSION['u_libdb_permissions'] = -1;

        setcookie('u_id',FALSE,-1, '/');
        unset($_COOKIE['u_id']);
        $_SESSION['u_id'] = -1;

        Redirect('/core/admin.php');

        break;
    }
    case 'form':
    default: {

        $template_dir = '$/core/_templates';
        $template_file = "admin.form.login.html";
        $template_data = [
            'action'    =>  $action,
        ];
        echo \Websun\websun::websun_parse_template_path($template_data, $template_file, $template_dir);

        break;
    }
}



