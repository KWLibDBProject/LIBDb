<!DOCTYPE HTML>
<html>
<head>
    <title>Вход на сайт</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <script src="js/jquery-1.10.2.min.js"></script>
    <script src="js/md5.js"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $.ajaxSetup({cache: false});

            $("#loginform").on('click','#logintry',function(event){
                $('#message').empty().hide();
                var $form = $("#loginform").find('form');
                fValid = true;
                fValid = fValid && $form.find("input[name='login']").val()!='';

                if (fValid) {
                    // проверяем связку по базе
                    url = $form.attr("action");
                    var posting = $.post(url,{
                        login: $form.find("input[name='login']").val(),
                        password: md5($form.find("input[name='password']").val())
                    });
                    posting.done(function(data){
                        result = $.parseJSON(data);
                        if (result['error'] == 0) {
                            // default
                            md5 = md5($form.find("input[name='password']").val());
                            $form.find("input[name='timestamp']").val( parseInt(new Date().getTime()/1000) );
                            $form.find("input[name='password']").val(md5);
                            $form.find("input[name='md5password']").val(md5);
                            $form.trigger('submit');
                        } else {
                            // error message
                            $('#message').html(result['message']).fadeIn(500);
                            event.preventDefault();
                        }
                    });

                } else {
                    // логин пуст
                    $('#message').fadeIn(500).html('Логин-то введите, ок? ');
                }
            });
        });
    </script>
    <style type="text/css">
        #message {
            border: 1px solid red;
            display: none;
        }
    </style>
</head>
<body>
<div id="loginform">
    <form action="admin.login.php" method="post">
        <label>Login: <input type="text" name="login" required></label><br>
        <label>Password: <input type="password" name="password"></label><br>
        <input type="hidden" name="md5password">
        <input type="hidden" name="timestamp">
    </form>
    <button id="logintry">Вход</button>
</div>
<div id="message"></div>
<a href="/index.php">Переход к основному сайту</a>
</body>
</html>