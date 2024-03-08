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
    <div class="message-wrapper">
        <div class="message-top">
            <h2 class="message__title">Сообщения</h2>
            <div class="open-burger">
                <img src="../../src/assets/images/icons/left-arrow.svg" alt="">
            </div>
            <a href="?friends" class="new-message">Создать чат</a>
            <a href="?friends" class="new-message mobile">
                <img src="../../src/assets/images/icons/plus.svg" alt="">
            </a>
        </div>
        <?php
        $dialogUsers = $connection->query("SELECT DISTINCT `id_send` FROM `message` WHERE (`id_send` = $idUser OR `id_eqipt` = $idUser) AND `id_send` != $idUser")->fetchAll(PDO::FETCH_COLUMN);
        $dialogUsers = array_flip($dialogUsers);
        ?>

        <ul class="message-list__people">
            <?php
            foreach ($dialogUsers as $userId => $index) {
                $user = $connection->query("SELECT * FROM `users` WHERE `id` = $userId")->fetch();

                if ($user) {
                    $isOnline = ($user['online'] == 1) ? 'online' : '';
                    $avatar = $user['avatar'];
                    $name = $user['name'];

                    echo '<a href="?id=' . $userId . '" class="message-people">';
                    echo '<div class="avatar-box ' . $isOnline . '">';
                    echo '<img src="../../src/assets/images/avatars/' . $avatar . '" alt="avatar people">';
                    echo '</div>';
                    echo '<div class="people-content">';
                    echo '<div class="people-box">';
                    echo '<span class="people-name">' . $name . '</span>';
                    echo '</div>';
                    echo '<p class="people-stats">' . ($isOnline ? 'онлайн' : 'оффлайн') . '</p>';
                    echo '</div>';
                    echo '</a>';
                }
            }
            ?>
        </ul>
    </div>
    <?php if (!empty($_GET)) { ?>
        <div class="message-window">
            <div class="message-top">
                <div class="message-profile">
                    <div class="message-people">
                        <?php
                        $checkId = $_GET['id'];
                        $checkPeople = $connection->query("SELECT * FROM `users` where `id` = '$checkId'")->fetch();
                        ?>
                        <div class="people-content">
                            <div class="people-box">
                                <span class="people-name"><?= $checkPeople['name'] ?></span>
                            </div>
                            <p class="people-stats">онлайн</p>
                        </div>
                        <div class="avatar-box online">
                            <img src="../../src/assets/images/avatars/<?= $checkPeople['avatar'] ?>"
                                 alt="avatar people">
                        </div>
                    </div>
                </div>
            </div>
            <div class="message-body">
                <div class="message-chat">
                    <?php
                    $checkMessage = $connection->query("SELECT * FROM `message` where (`id_send` = '$idUser' AND `id_eqipt` = '$checkId') OR (`id_send` = '$checkId' AND `id_eqipt` = '$idUser')");
                    $messages = [];

                    while ($result = $checkMessage->fetch()) {
                        $user = $result['id_send'];
                        $message = $result['message'];
                        $timestamp = strtotime($result['timestamp']);

                        $messages[] = array('timestamp' => $timestamp, 'user' => $user, 'message' => $message);
                    }

                    usort($messages, function ($a, $b) {
                        return $a['timestamp'] - $b['timestamp'];
                    });

                    foreach ($messages as $messageData) {
                        $timestamp = $messageData['timestamp'];
                        $user = $messageData['user'];
                        $message = $messageData['message'];
                        $timeString = date('Y-m-d H:i:s', $timestamp);

                        if ($user == $idUser) {
                            ?>

                            <div class="message-input">
                                <img src="../../src/assets/images/avatars/<?= $checkPeople['avatar'] ?>"
                                     alt="avatar people">
                                <p class="message-text">
                                    <?= $message ?>
                                </p>
                            </div>

                            <?php
                        } else {
                            ?>

                            <div class="message-output">
                                <p class="message-text">
                                    <?= $message ?>
                                </p>
                            </div>

                            <?php
                        }
                    }
                    ?>

                </div>
                <form action="" class="form">
                    <a href="#" class="clip-box">
                        <img src="../../src/assets/images/icons/clip.svg" alt="clip">
                    </a>
                    <textarea placeholder="Напишите..." class="input-message"></textarea>
                    <a href="#" class="send-box">
                        <img src="../../src/assets/images/icons/send.svg" alt="send">
                    </a>
                </form>
            </div>
        </div>
    <?php } ?>
</div>
<script src="../../src/assets/js/main.js"></script>
</body>
</html>
