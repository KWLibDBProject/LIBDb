<html>
<head>
    <title>Redirect... </title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="refresh" content="{%time%};URL={%target%}">
    <link rel="stylesheet" type="text/css" href="authors.css">
    <style type="text/css"></style>

    <script type="text/javascript">
        var delay = {%time%};
        var pause = step = 0.5;
        var callback = "../ref.authors.show.php";
        function CountDown()
        {
            if (delay > 0) {
                document.getElementById("wait").innerHTML = delay.toFixed(1);
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
