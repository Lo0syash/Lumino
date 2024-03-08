<?php
    require '../db/database.php';
    $checkTech = $connection->query("SELECT * FROM `serverStats` where `id` = '1'")->fetch();
    if ($checkTech['stats'] == 0){
        header('Location: /lumino');
    }
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" type="image/x-icon" href="../../src/assets/favicon.ico" >
    <link rel="stylesheet" href="../../src/assets/css/main.css">
    <? /* <title>Технические работы</title> */ ?>
    <title>Сайт на ремонте</title>
</head>
<body class="body-tech">
    <div class="wrapper-tech">
        <img src="../../src/assets/images/icons/tech.png" alt="birds" class="image-tech">
        <hr>
        <div class="tech__content">
            <? /* <h1 class="tech__title">Технические работы</h1> */?>
            <h1 class="tech__title">Сайт на ремонте</h1>
            <p class="tech__paragraph">В течении некоторого времени сервис будет недоступен</p>
            <? /* <p class="tech__paragraph">Скоро всё заработает - обязательно
                возвращайтесь!</p> */ ?>
        </div>
    </div>
</body>
</html>