<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Байки из склепа</title>
    <style>
        body {
            font: 11pt Arial, Helvetica, sans-serif; /* Рубленый шрифт текста */
            margin: 0; /* Отступы на странице */
        }
        h1 {
            font-size: 36px; /* Размер шрифта */
            margin: 0; /* Убираем отступы */
            color: #fc6; /* Цвет текста */
        }
        h2 {
            margin-top: 0; /* Убираем отступ сверху */
        }
        #header { /* Верхний блок */
            background: #0080c0; /* Цвет фона */
            padding: 10px; /* Поля вокруг текста */
        }
        #sidebar { /* Левая колонка */
            float: left; /* Обтекание справа */
            border: 1px solid #333; /* Параметры рамки вокруг */
            width: 20%; /* Ширина колонки */
            padding: 5px; /* Поля вокруг текста */
            margin: 10px 10px 20px 5px; /* Значения отступов */
        }
        #content { /* Правая колонка */
            margin: 10px 5px 20px 25%; /* Значения отступов */
            padding: 5px; /* Поля вокруг текста */
            border: 1px solid #333; /* Параметры рамки */
        }
        #footer { /* Нижний блок */
            background: #333; /* Цвет фона */
            padding: 5px; /* Поля вокруг текста */
            color: #fff; /* Цвет текста */
            clear: left; /* Отменяем действие float */
        }
    </style>
    <style>
        .api table {
            border: 1px solid grey;
        }

        .api th {
            border: 1px solid grey;
        }

        .api td {
            border: 1px solid grey;
        }

    </style>
</head>
<body>
<div id="header"><h1>Тестовая работа AMOCRM</h1></div>
<div id="sidebar">
    <p><a href="/index">Главная</a></p>
    <p><a href="/article">Задание 3.1</a></p>
    <p><a href="/createleads">Задание 3.2</a></p>
    <p><a href="/error">Страница ошибки</a></p>
    <p><a href="/amocrm">AmoCrm</a></p>
    <?php
     if(!\vendor\classes\Core::$user->getIsGuest()) echo '<p><a href="/logout">Выйти</a></p>';
     else  echo '<p><a href="/login">Войти</a></p>';
    ?>

</div>

<?= $content  ?>

</body>
</html>


