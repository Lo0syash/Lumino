<?php
    require '../db/database.php';
    session_start();
    $idUser = $_SESSION['uid'];

    $checkTech = $connection->query("SELECT * FROM `serverStats` where `id` = '1'")->fetch();
    $checkAdmin = $connection->query("SELECT * FROM `users` where `id` = '$idUser'")->fetch();

    if ($checkTech['stats'] == 1 && $checkAdmin['level'] == 0) {
        header('Location: tech');
    }

    if (empty($_SESSION['uid'])) {
        header('Location: /lumino');
    }

    $idNews = $_GET['id'];
    $checkNewsGet = $connection->query("SELECT * FROM `news` where `id` = '$idNews'")->fetch();
    $idUserNews = $checkNewsGet['id_user'];
    $checkAuthor = $connection->query("SELECT * FROM `users` where `id` = '$idUserNews'")->fetch();


    if (isset($_POST['send-comments'])) {
        $textComments = $_POST['text-comments'];
        if (empty($textComments)) {
            echo '<script>alert("Комментарий не может быть пустым")</script>';
            echo '<script>
                    let currentURL = window.location.href;

                    let url = new URL(currentURL);
                    let id = url.searchParams.get("id");
                    
                    window.location.href = "feed-one?id=" + id;
                    </script>';
        } else {
            $sendComments = $connection->query("INSERT INTO `comments` (`id`, `id_user`, `id_news`, `commetsText`) 
                                                VALUES (NULL, '$idUser', '$idNews', '$textComments')");
            echo '<script>
                    let currentURL = window.location.href;

                    let url = new URL(currentURL);
                    let id = url.searchParams.get("id");
                    
                    window.location.href = "feed-one?id=" + id;
                    </script>';
        }
    }

    if (isset($_GET['deleteCommets'])) {
        $idComments = $_GET['id'];
        $idNews = $_GET['news'];
        $deleteCommentsSql = $connection->query("DELETE FROM comments WHERE `comments`.`id` = '$idComments'");
        header('Location: feed-one?id='.$idNews);
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
    <?php (include '../include/aside.php'); ?>
    <div class="feed-wrapper">
        <h1 class="feed__title open-burger"><span><</span>Главная</h1>
        <ul class="news-list comments" >
                <li class="news-item">
                    <div class="news-item__profile">
                        <div class="container-avatar">
                            <div class="ellipse-avatar_bck"></div>
                            <img src="../../src/assets/images/avatars/<?= $checkAuthor['avatar'] ?>" alt="avatar" style="border-radius: 50%">
                        </div>
                        <div class="profile-content">
                            <p class="profile-name"><?=$checkAuthor['name'] ?></p>
                            <?php
                            $unixTime = $checkNewsGet['date'];
                            $timeAgo = time() - $unixTime;

                            if ($timeAgo <= 60) {
                                $minutesAgo = floor($timeAgo / 60);
                                echo "<p class='profile-time'>" . " только что</p>";
                            } elseif (($timeAgo < 3600) && ($timeAgo > 60)) {
                                $minutesAgo = floor($timeAgo / 60);
                                if ($minutesAgo == 1) {
                                    echo "<p class='profile-time'>" . $minutesAgo . " минуту назад</p>";
                                } elseif ($minutesAgo >= 5) {
                                    echo "<p class='profile-time'>" . $minutesAgo . " минут назад</p>";
                                } elseif (($minutesAgo < 10) && ($minutesAgo > 1)) {
                                    echo "<p class='profile-time'>" . $minutesAgo . " минуты назад</p>";
                                } else {
                                    echo "<p class='profile-time'>" . $minutesAgo . " минут назад</p>";
                                }
                            } elseif ($timeAgo < 86400) {
                                $hoursAgo = floor($timeAgo / 3600);
                                if ($hoursAgo >= 5) {
                                    $checkHours = 'часов';
                                } else {
                                    $checkHours = 'часа';
                                }
                                echo "<p class='profile-time'>" . $hoursAgo . ' ' . $checkHours . "  назад</p>";
                            } else {
                                $formattedDate = date('d.m.Y', $unixTime);
                                echo "<p class='profile-time'>" . $formattedDate . "</p>";
                            }
                            ?>
                        </div>
                    </div>
                    <p class="news-item__text"><?= $checkNewsGet['discription'] ?></p>
                    <img src="../../src/assets/images/photo/<?= $checkNewsGet['photo'] ?>" alt="" style="width: 100%;">
                    <form action="" class="form form-feed--one" method="post">
                        <textarea placeholder="Оставить комментарий.." name="text-comments" class="input-message comments" maxlength="150"></textarea>
                        <input type="submit" name="send-comments" id="send-message" style="display: none">
                        <label for="send-message" class="send-box" style="cursor: pointer">
                            <img src="../../src/assets/images/icons/send.svg" alt="send" class="send-images">
                        </label>
                    </form>
                    <ul class="list-comments">
                        <?php
                            $newsId = $checkNewsGet['id'];
                            $commentsCheck = $connection->query("SELECT * FROM `comments` where `id_news` = '$newsId'");
                            while($result = $commentsCheck->fetch()) {
                                $checkUserAdd = $result['id_user'];
                                $checkPeople = $connection->query("SELECT * FROM `users` where `id` = '$checkUserAdd'")->fetch()?>
                               <li class="item-comments">
                                   <div class="item-comments--box">
                                       <div class="container-avatar">
                                           <div class="ellipse-avatar_bck"></div>
                                           <img src="../../src/assets/images/avatars/<?= $checkPeople['avatar'] ?>" alt="avatar" style="border-radius: 50%">
                                       </div>
                                       <p class="profile-name"><?=$checkPeople['name'] ?></p>
                                   </div>
                                   <p class="text-comments"><?=$result['commetsText']?></p>
                                   <?php if ($checkAdmin['level'] == 1) { ?><a href="?deleteCommets&id=<?=$result['id']?>&news=<?=$newsId?>" class="deleteButton" onclick="return confirm('Вы уверены, что хотите удалить этот комментарий?');"><img src="../../src/assets/images/icons/trash.svg" alt=""></a> <?php } ?>
                               </li>
                        <?php } ?>

                    </ul>
                </li>

        </ul>
    </div>
</div>
<script src="../../src/assets/js/main.js"></script>
</body>
</html>