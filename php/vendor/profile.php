<?php
    require '../db/database.php';
    session_start();

    $idUser = $_SESSION['uid'];
    $dateUser = $connection->query("SELECT * FROM `users` where `id` = '$idUser'")->fetch();

    if (empty($_SESSION['uid'])) {
        header('Location: /lumino');
    }

    $checkTech = $connection->query("SELECT * FROM `serverStats` where `id` = '1'")->fetch();

    if ($checkTech['stats'] == 1 && $dateUser['level'] == 0) {
        header('Location: tech');
    }
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="../../src/assets/css/main.css">
    <title>Lumino</title>
</head>
<body>
    <div class="wrapper">
        <?php (include '../include/aside.php') ?>
        <div class="profile-wrapper">
            <h1 class="profile__title open-burger"><span><</span>Профиль</h1>
            <img src="../../src/assets/images/photo/<?=$dateUser['background_profile']?>" alt="profile banner" class="banner-profile">
            <div class="profile-content">
                <div class="mobile-content">
                    <div class="profile-content__top">
                        <div class="profile-date">
                            <p class="profile-name"><?=$dateUser['name']?></p>
                            <p class="profile-status">онлайн</p>
                        </div>
                        <div class="avatar-box">
                            <div class="avatar-bck"></div>
                            <img src="../../src/assets/images/avatars/<?=$dateUser['avatar']?>" alt="avatar profile">
                        </div>
                    </div>
                    <div class="profile-content__body">
                        <div class="profile-block">
                            <p class="profile-name">Дата рождения:</p>
                            <p class="profile-date"><?=(empty($dateUser['birth']) ? 'Не заполнено' : $dateUser['birth'])?></p>
                        </div>
                        <div class="profile-block">
                            <p class="profile-name">Пол:</p>
                            <p class="profile-date"><?=(empty($dateUser['sexs']) ? 'Не заполнено' : $dateUser['sexs'])?></p>
                        </div>
                        <div class="profile-block">
                            <p class="profile-name">Электронная почта:</p>
                            <p class="profile-date"><?=$dateUser['email']?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../../src/assets/js/main.js"></script>
</body>
</html>