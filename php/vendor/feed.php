<?php
global $connection;
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

    $sortByDate = isset($_GET['todate']);

    $sortByDate = isset($_GET['todate']);
    $sortByPopular = isset($_GET['popular']);

    if ($sortByDate) {
        $checkNews = $connection->query("SELECT * FROM `news` ORDER BY `date` DESC");
    } elseif ($sortByPopular) {
        $checkNews = $connection->query("SELECT n.*, COUNT(c.id) AS comment_count 
                                             FROM `news` AS n
                                             LEFT JOIN `comments` AS c ON n.id = c.id_news
                                             GROUP BY n.id
                                             ORDER BY comment_count ASC");
    } else {
        $checkNews = $connection->query("SELECT * FROM `news`");
    }

    if (isset($_POST['addSubmit'])) {
        $textFeed = $_POST['textFeed'];
        if (!empty($textFeed)) {
            $time = time();
            $photo = '';

            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $photo = $_FILES['photo']['name'];
                $photo = time() . rand(111, 999) . '.jpg';
                $file = '../../src/assets/images/photo/' . $photo;

                if (move_uploaded_file($_FILES['photo']['tmp_name'], $file)) {
                    $addNews = $connection->query("INSERT INTO `news` (`id`, `id_user`, `discription`, `date`, `photo`) 
                                            VALUES (NULL, '$idUser', '$textFeed', '$time','$photo') ");
                }
            } else {
                $addNews = $connection->query("INSERT INTO `news` (`id`, `id_user`, `discription`, `date`, `photo`) 
                                        VALUES (NULL, '$idUser', '$textFeed', '$time', NULL) ");
            }

            echo '<script>document.location = "?"</script>';
        } else {
            ?><script>alert('Новость не может быть пустой!'); document.location = "?"</script><?php
        }

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
            <form action="" class="form" method="post" enctype="multipart/form-data">
                <textarea name="textFeed" class="new-feed" placeholder="Новый пост..." maxlength="249"></textarea>
                <div class="form-bottom">
                    <input type="file" name="photo" id="photo" class="photo" accept="image/*">
                    <label for="photo" class="label-photo">
                        <img src="../../src/assets/images/icons/photocamera.svg" alt="photo camera">
                    </label>
                    <input name="addSubmit" type="submit" class="submit-feed">
                </div>
            </form>
            <div class="feed-filter">
                <?php if ($sortByPopular): ?>
                    <a href="?" class="filter-link">По популярности</a>
                <?php else: ?>
                    <a href="?popular" class="filter-link">По популярности</a>
                <?php endif; ?>

                <?php if ($sortByDate): ?>
                    <a href="?" class="filter-link">По дате добавления</a>
                <?php else: ?>
                    <a href="?todate" class="filter-link">По дате добавления</a>
                <?php endif; ?>
            </div>
            <ul class="news-list">
                <?php
                    $sortByDate = isset($_GET['todate']);
                    $sortByPopular = isset($_GET['popular']);

                    if ($sortByDate) {
                        $checkNews = $connection->query("SELECT * FROM `news` ORDER BY `date` DESC");
                    } elseif ($sortByPopular) {
                        $checkNews = $connection->query("SELECT n.*, COUNT(c.id) AS comment_count 
                                             FROM `news` AS n
                                             LEFT JOIN `comments` AS c ON n.id = c.id_news
                                             GROUP BY n.id
                                             ORDER BY comment_count DESC");
                    } else {
                        $checkNews = $connection->query("SELECT * FROM `news`");
                    }
                    while ($resultNews = $checkNews->fetch()) {
                        $idUser = $resultNews['id_user'];
                        $userCheck = $connection->query("SELECT * FROM `users` where `id` = '$idUser'")->fetch();
                ?>
                <li class="news-item">
                    <div class="news-item__profile">
                        <div class="container-avatar">
                            <div class="ellipse-avatar_bck"></div>
                            <img src="../../src/assets/images/avatars/<?=$userCheck['avatar']?>" alt="avatar" style="border-radius: 50%">
                        </div>
                        <div class="profile-content">
                            <p class="profile-name"><?=$userCheck['name']?></p>
                            <?php
                                $unixTime = $resultNews['date'];
                                $timeAgo = time() - $unixTime;

                                if ($timeAgo <= 60) {
                                    $minutesAgo = floor($timeAgo / 60);
                                    echo "<p class='profile-time'>" . " только что</p>";
                                }
                                elseif (($timeAgo < 3600) && ($timeAgo > 60)) {
                                    $minutesAgo = floor($timeAgo / 60);
                                    if ($minutesAgo == 1) {
                                        echo "<p class='profile-time'>" . $minutesAgo . " минуту назад</p>";
                                    }
                                    elseif ($minutesAgo >= 5) {
                                        echo "<p class='profile-time'>" . $minutesAgo . " минут назад</p>";
                                    }
                                    elseif (($minutesAgo < 10) && ($minutesAgo > 1)) {
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
                    <a href="feed-one?id=<?=$resultNews['id']?>" class="news-comments"><img src="../../src/assets/images/icons/commenticons.svg" alt="comment icons"></a>
                    <p class="news-item__text"><?=$resultNews['discription']?></p>
                    <?php
                        if (!empty($resultNews['photo'])) {
                            ?>
                                <img src="../../src/assets/images/photo/<?=$resultNews['photo']?>" alt="" style="width: 100%;">
                            <?
                        }
                    ?>
                </li>
                <? } ?>
            </ul>
        </div>
    </div>
    <script src="../../src/assets/js/main.js"></script>
</body>
</html>