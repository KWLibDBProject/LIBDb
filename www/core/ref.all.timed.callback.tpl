<html>
<head>
    <title>Redirect... </title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="refresh" content="{%time%};URL={%target%}">
    <style type="text/css">
        #wait {  /* класс для области вывода таймера */
            color: red;
            font-weight: bold;
        }
        .button-huge { /* огромная кнопка для таймера*/
            height: 120px;
            width:400px;
        }
        .info { /* класс для информационного сообщения */
            color: #7b68ee;
            font-weight: bold;
        }
    </style>

    <script type="text/javascript">
        var delay = {%time%};
        var pause = step = 0.5;
        var callback = "{%target%}";
        var dtf;
        function CountDown()
        {
            if (delay > 0) {
                dtf = delay.toFixed(1);
                document.getElementById("wait").innerHTML = dtf;
                document.title = '...осталось '+ dtf + ' секунд...';
                delay -= step;
                setTimeout("CountDown('wait')", pause*1000);
            } else {
                document.location.href = callback;
            }
        }
    </script>

</head>
<body onLoad="CountDown()">
<div class="info">{%message%}</div>
<hr>
<button class="button-huge" onclick="window.location.href='{%target%}'">{%buttonmessage%}
<br><br>До перехода осталось <span id="wait">{%time%}</span> секунд
</button>
</body>
</html>
