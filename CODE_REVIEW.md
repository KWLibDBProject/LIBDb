# Code Review

Этот движок создавался изначально для сайта
<a href="http://etks.opu.ua" target="_blank">«Электротехнические и компьютерные системы»</a>.

Мы хостились на спейсвебе, использовали версию PHP 5.2 (режим slot-compatibility под гентой) и
горя не знали... пока `mysql_connect()` и другие функции не стали DEPRECATED. Впрочем, сайт
работал и необходимости в ремастеринге не было.

В августе 2018 года заказчик обратился с вопросом *"а можно ли создать еще один сайт на этом движке?"*
и предложил денег. После этого отмазываться от ремастеринга сайта было уже как-то неудобно.

Новая версия требует PHP 7+, использует шаблонизатор websun, работает на MySQL 5.7.

В планах - использовать PHPAuth, Monolog, dotenv.

## Структура каталогов

Изначально проект делался под денвером. В денвере каталог виртуального хоста
содержит подкаталог `www` с собственно файлами. По хорошему, от этой структуры
можно было бы избавиться, но я решил этого не делать.

В корень проекта мы можем положить файлы, которым на хосте делать нечего,
но которые могут потребоваться при сборке минифицированных скриптов, файлы документации,
определения БД и прочее.

Для сборки минифицированных скриптов используется grunt.

## Конфигурация

Конфиги проекта движка лежат в `www/core/config`, но из GIT-а исключён
файл с настройками подключения к БД. По понятным причинам.

## База данных

Изначально проект писался под MySQL-extension (`mysql_connect()` и прочее legacy). В августе 2018 года
я перевел его на MySQLi-extension. Вопрос перевода на PDO стоит на повестке дня, но совершенно непонятно
что, кроме прелести подготовленных выражений, он может дать.

## База данных - миграции

Движок миграций... стоило использовать в самом начале проекта, еще в 2013 году, но
тогда я о таких механизмах даже не слышал, а сейчас уже незачем. Тем не менее,
последовательные инструкции на SQL я сделал. Превратить их в миграции несложно.

## Особенности реализации - роутинг URL

Заказчики сайта испытывали и испытывают отвращение к т.н. "человекопонятным URL" и
нынешняя система адресации страниц
```
/?fetch=articles&with=topic&id=2
/?fetch=articles&with=book&id=30
?fetch=articles&with=info&id=979
```
им нравится гораздо больше, чем
```
/articles/topic/2
/articles/book/30
/articles/info/979
```
или даже
```
/articles/info/FUZZY-PID_CONTROLLER_IN_THE_METHOD_PULSE_WIDTH_MODULATION_CONVERTER_ACTIVE_POWER_FILTER/
```

То есть мы не используем "стандартные" механизмы роутинга URL, а разбираем параметры при помощи
системы switch of cases.

Тем не менее, для эксперимента я сделал класс LegacyRouter, который позволяет сделать так:

```
LegacyRouter::bindRouteKeys(['fetch', 'with']);
LegacyRouter::bindCallbackParams([ [
        'mysqli_link'   =>  $mysqli_link,
        'main_template' =>  $main_template
    ]]);
LegacyRouter::bindRule('authors/info', 'Frontend@AuthorInfo');
LegacyRouter::bindRule('authors/estaff', 'Frontend@EStaff');
LegacyRouter::bindRule('foo/bar', function () use ($test1, $test2){
    var_dump($test1);
    var_dump($test2);
});
LegacyRouter::bindDefaultRule('Frontend');
$response = LegacyRouter::start();
```

В результате прекрасно работает и замыкание, и инвоук класса, и вызов метода класса, но... код
становится менее прозрачным. Кроме того, в замыкания или классы нужно пробрасывать доступ к БД
и другие "драйверы". Это легко делается через синглтоны, но.. в некоторых кругах синглтон считается
bad practice ;-)

Короче говоря, если бы заказчик согласился на "красивые" URL-ы, код `index.php`
стал бы намного понятнее, примерно таким:
```
use Pecee\SimpleRouter\SimpleRouter;
SimpleRouter::get   ('/', 'Page@view_frontpage')->name('frontpage');
SimpleRouter::get   ('/authors/info', 'FrontendAuthors@info');
SimpleRouter::get   ('/authors/estaff', 'FrontendAuthors@estaff');

SimpleRouter::get   ('/admin/article/add', 'BackendArticles@form_add');

SimpleRouter::error(function(Pecee\Http\Request $request, \Exception $exception) {
    if($exception instanceof Pecee\SimpleRouter\Exceptions\NotFoundHttpException && $exception->getCode() == 404) {
        response()->redirect('/404');
    }
});
```

Возможно, для другого заказчика я реализую именно эту схему. Или допилю
LegacyRouter до совместимости с `Pecee\SimpleRouter\SimpleRouter` и буду только
менять файл роутинга для разных проектов.

## Особенности реализации - шаблонизатор

... или "почему не смарти".

До меня сайт онлайн журнала для кафедры делал какой-то преподаватель (в 2002 году),
а в 2006 году его допиливал какой-то студент. Скрины этого поделия не сохранились,
скажу только, что там был дизайн из начала нулевых. Студент этот то ли хотел стать
совершенно незаменимым, то ли просто был идиотом - но, используя smarty, он
"размазал" шаблон страницы по 300 файлам. Я не шучу, чуть ли не каждый структурный элемент
страницы лежал в отдельном файле шаблона и собиралось это все вместе непонятно как.

Разумеется, ни о каком смарти при разработке сайта ETKS речи не шло :)

Я написал свой шаблонизатор... совершенно костыльный и кривой, как оказалось позже, но он
хотя бы работал.

В августе 2018, при ремастеринге сайта я выбросил кривую недоделку, взял
шаблонизатор [websun](https://github.com/1234ru/websun) и полностью переписал шаблоны. Их структура
значительно упростилась. Новый шаблонизатор позволил использовать механизмы, ранее недоступные.

В процессе я нашел [багу](https://github.com/1234ru/websun/issues/9) в этом шаблонизаторе, связанную с прекомпиляцией PCRE-регулярок, решил её
немного костыльным способом, но работающим.

Думал о TWIG или BLADE, но сроки поджимали, а Websun прост, удобен и понятен (и я его использую и
в других проектах).











