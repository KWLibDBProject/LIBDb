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
            $message = $('#message');

            $("#loginform").on('click','#logintry',function(event){
                event.preventDefault();
                $message.empty().hide();
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
                            document.location.href = result['url'];
                        } else {
                            // error message
                            $message.html(result['message']).fadeIn(500);
                        }
                    });

                } else {
                    // логин пуст
                    $message.fadeIn(500).html('Логин-то введите, ок? ');
                }
                event.preventDefault();
            });
        });
    </script>
    <style type="text/css">
        #message {
            border: 1px solid red;
            display: none;
        }
        dt {
            float: left;
            width: 100px;
            text-align: right;
            padding-right: 5px;
            min-height: 1px;
        }
        dd {
            position: relative;
            top: -1px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
<div id="loginform">
    <form action="_admin.login.php" method="post">
        <dl>
            <dt>Login: </dt>
            <dd><input type="text" name="login" required></dd>
            <dt>Password: </dt>
            <dd><input type="password" name="password"></dd>
            <dt></dt>
            <dd><button type="submit" id="logintry">Вход</button></dd>
        </dl>
    </form>
</div>
<div id="message"></div>
<a href="/index.php">Переход к основному сайту</a>
</body>
</html>