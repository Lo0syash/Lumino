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


    if (isset($_GET['addfriend'])) {
        $idAdd = $_GET['id'];
        $addFriend = $connection->query("INSERT INTO `friends` (`id`, `id_user`, `id_friend`, `stats`) 
                                         VALUES (NULL, '$idUser', '$idAdd', '0')");
        echo '<script>document.location = "?"</script>';

    }

    if (isset($_GET['request'])) {
        $idRequest = $_GET['id'];
        $requstQuery = $connection->query("DELETE FROM friends WHERE `id` = '$idRequest'");
        echo '<script>document.location = "?"</script>';
    }

    if (isset($_GET['completeRequest'])) {
        $idComplete = $_GET['id'];
        $completeFriends = $connection->query("UPDATE `friends` SET `stats` = '1' WHERE `id` = '$idComplete'");
        echo '<script>document.location = "?"</script>';
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
    <div class="friend-wrapper">
        <h2 class="friend__title open-burger"><span><</span> Друзья</h2>
        <form action="" class="form" method="post">
            <img src="../../src/assets/images/icons/search.svg" alt="search" class="loup">
            <input type="text" class="search-input" name="search_input" placeholder="Поиск друзей" maxlength="89" value="<?=$_POST['search_input']?>" >
            <input type="submit" name="search" id="search-btn" class="search-submit" value="search">
            <label for="search-btn">
                <img src="../../src/assets/images/icons/send.svg" alt="send">
            </label>
        </form>
        <div class="friend-filter">
            <a href="?" class="friend-btn <?= (empty($_GET)) ? 'active' : '' ?>">Мои друзья</a>
            <a href="?allpeople" class="friend-btn <?= (isset($_GET['allpeople'])) ? 'active' : '' ?>">Другие люди</a>
            <?php
            $checkRequest = $connection->query("SELECT * FROM `friends` WHERE `id_friend` = '$idUser' AND `stats` = '0'")->fetch();
            if (!empty($checkRequest)) {
                ?>
                <a href="?requestFriends" class="friend-btn <?= (isset($_GET['requestFriends'])) ? 'active' : '' ?>">Запрос</a>
                <?php
            }
            ?>
        </div>
        <ul class="friend-list">
            <?php
            if (isset($_GET['requestFriends'])) {
                $addRequest = $connection->query("SELECT * FROM `friends` WHERE `id_friend` = '$idUser' AND `stats` = '0'");
                while ($result = $addRequest->fetch()) {
                    $idPeople = $result['id_user'];
                    $contentSql = $connection->query("SELECT * FROM `users` WHERE `id` = '$idPeople'")->fetch();
                    ?>
                    <li class="friend-item">
                        <img src="../../src/assets/images/avatars/<?= $contentSql['avatar']?>" alt="photo avatar" style="border-radius: 50%">
                        <div class="friend-content">
                            <h3 class="friend-name"><?= $contentSql['lastname'] . ' ' . $contentSql['name'] ?></h3>
                            <div class="friend-function">
                                <a href="?completeRequest&id=<?= $result['id'] ?>" class="friend-btn">Добавить в друзья</a>
                                <a href="?request&id=<?= $result['id'] ?>" class="friend-btn">Отклонить заявку</a>
                            </div>
                        </div>
                    </li>
                    <?php
                }
            }
            elseif (isset($_POST['search'])) {
                $searchInput = $_POST['search_input'];
                $contentSql = $connection->query("SELECT * FROM `users` WHERE name LIKE '%".$searchInput."%' ");

                if ($contentSql->rowCount() === 0) {
                    echo "Ничего не найдено.";
                } else {
                    while ($result = $contentSql->fetch()) {
                        ?>
                        <li class="friend-item">
                            <img src="../../src/assets/images/avatars/<?= $result['avatar']?>" alt="photo avatar" style="border-radius: 50%">
                            <div class="friend-content">
                                <h3 class="friend-name"><?= $result['lastname'] . ' ' . $result['name'] ?></h3>
                                <div class="friend-function">
                                    <?php
                                    $authorizedUser = $idUser;
                                    $friendId = $result['id'];

                                    $checkFriend = $connection->query("SELECT * FROM `friends` WHERE (`id_user` = '$authorizedUser' AND `id_friend` = '$friendId' AND `stats` = '1') OR 
                                                                        (`id_friend` = '$authorizedUser' AND `id_user` = '$friendId' AND `stats` = '1')")->fetch();
                                    $checkRequest = $connection->query("SELECT * FROM `friends` WHERE `id_friend` = '$friendId' AND `stats` = '0'")->fetch();

                                    if (!empty($checkFriend)) {
                                        ?>
                                        <a href="?message" class="friend-btn">Написать сообщение</a>
                                        <a href="?request&id=<?= $checkFriend['id'] ?>" class="friend-btn">Удалить из друзей</a>
                                        <?php
                                    } elseif (!empty($checkRequest) && $checkRequest['id_user'] == $authorizedUser) {
                                        ?>
                                        <a href="?request&id=<?= $checkRequest['id'] ?>" class="friend-btn">Заявка отправлена (отменить)</a>
                                        <?php
                                    } else {
                                        ?>
                                        <a href="?addfriend&id=<?= $result['id'] ?>" class="friend-btn">Добавить в друзья</a>
                                        <?php
                                    }

                                    ?>
                                </div>
                            </div>
                        </li>
                        <?php
                    }
                }

            } elseif (isset($_GET['allpeople'])) {
                $checkPeople = $connection->query("SELECT * FROM `users` WHERE `id` != '$idUser'");
                while ($result = $checkPeople->fetch()) {
                    ?>
                    <li class="friend-item">
                        <img src="../../src/assets/images/avatars/<?=$result['avatar']?>" alt="photo avatar" style="border-radius: 50%">
                        <div class="friend-content">
                            <h3 class="friend-name"><?= $result['lastname'] . ' ' . $result['name'] ?></h3>
                            <div class="friend-function">
                                <?php
                                    $authorizedUser = $idUser;
                                    $friendId = $result['id'];

                                    $checkFriend = $connection->query("SELECT * FROM `friends` WHERE (`id_user` = '$authorizedUser' AND `id_friend` = '$friendId' AND `stats` = '1') OR (`id_friend` = '$authorizedUser' AND `id_user` = '$friendId' AND `stats` = '1')")->fetch();
                                    $checkRequest = $connection->query("SELECT * FROM `friends` WHERE `id_friend` = '$friendId' AND `stats` = '0'")->fetch();

                                    if (!empty($checkFriend)) {
                                        ?>
                                        <a href="?message" class="friend-btn">Написать сообщение</a>
                                        <a href="?request&id=<?= $checkFriend['id'] ?>" class="friend-btn">Удалить из друзей</a>
                                        <?php
                                    } elseif (!empty($checkRequest) && $checkRequest['id_user'] == $authorizedUser) {
                                        ?>
                                        <a href="?request&id=<?= $checkRequest['id'] ?>" class="friend-btn">Заявка отправлена (отменить)</a>
                                        <?php
                                    } else {
                                        ?>
                                        <a href="?addfriend&id=<?= $result['id'] ?>" class="friend-btn">Добавить в друзья</a>
                                        <?php
                                    }

                                ?>
                            </div>
                        </div>
                    </li>
                    <?php
                }
            } else {
                $checkPeople = $connection->query("SELECT * FROM `friends` WHERE (`id_user` = '$idUser' OR `id_friend` = '$idUser') AND `stats` = '1'");
                $hasFriend = false;
                while ($result = $checkPeople->fetch()) {
                    if ($result['id_user'] == $_SESSION['uid']) {
                        $friendId = $result['id_friend'];
                    } else {
                        $friendId = $result['id_user'];
                    }
                    $hasFriend = true;
                    $checkFriend = $connection->query("SELECT * FROM `users` WHERE `id` = '$friendId'")->fetch();
                    ?>
                    <li class="friend-item">
                        <img src="../../src/assets/images/avatars/<?=$checkFriend['avatar']?>" alt="photo avatar" style="border-radius: 50%">
                        <div class="friend-content">
                            <h3 class="friend-name"><?= $checkFriend['lastname'] . ' ' . $checkFriend['name'] ?></h3>
                            <div class="friend-function">
                                <?php
                                if ($result['stats'] == 0) {
                                    ?>
                                    <a href="?request&id=<?= $result['id'] ?>" class="friend-btn">Заявка отправлена
                                        (отменить)</a>
                                    <?php
                                } else {
                                    ?>
                                    <a href="?message" class="friend-btn">Написать сообщение</a>
                                    <a href="?request&id=<?=$result['id'] ?>" class="friend-btn">Удалить из
                                        друзей</a>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                    </li>
                    <?php
                }
                if (!$hasFriend) {
                    ?>
                        <p class="error-friends">У вас пока нету друзей</p>
                    <?php
                }

            }
            ?>

        </ul>
    </div>
</div>
<script src="../../src/assets/js/main.js"></script>
</body>
</html>
