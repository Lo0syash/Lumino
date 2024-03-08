<?php
    require '../db/database.php';
    session_start();

    $userId = $_SESSION['uid'];

    if (empty($_SESSION['uid'])) {
        header('Location: /lumino');
        exit;
    }

    $checkTech = $connection->query("SELECT * FROM `serverStats` where `id` = '1'")->fetch();
    $checkAdmin = $connection->query("SELECT * FROM `users` where `id` = '$userId'")->fetch();

    if ($checkTech['stats'] == 1 && $checkAdmin['level'] == 0) {
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
    <div class="support-window">
        <div class="support-top">
            <div class="support-profile">
                <h1 class="support__title open-burger"><span><</span>Помощь</h1>
                <div class="support-people">
                    <div class="support-content">
                        <div class="people-box">
                            <span class="people-lastname">Lumino</span>
                        </div>
                        <p class="people-stats">онлайн</p>
                    </div>
                    <div class="avatar-box">
                        <img src="../../src/assets/images/icons/support-avatar.svg" alt="avatar supports">
                    </div>
                </div>
            </div>
        </div>
        <div class="support-body">

            <div class="support-chat">
                <?php
                $query = "SELECT * FROM support WHERE user = :userId ORDER BY data ASC";
                $stmt = $connection->prepare($query);

                $stmt->bindParam(':userId', $userId);
                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $id = $row['id'];
                        $answer = $row['answer'];
                        $question = $row['question'];
                        $user = $row['user'];
                        $data = $row['data'];
                        if (!empty($answer)) {
                            ?>
                            <div class="support-output">
                                <p class="support-text"><?= $question ?></p>
                            </div>
                            <?php
                        }

                        if (!empty($question)) {
                            ?>
                            <div class="support-input">
                                <img src="../../src/assets/images/icons/support-avatar.svg" alt="avatar people">
                                <p class="support-text"><?= $answer ?></p>
                            </div>
                            <?php
                        }
                    }
                }
                ?>


            </div>



            <form action="" class="form">
                <a href="#" class="clip-box">
                    <img src="../../src/assets/images/icons/clip.svg" alt="clip">
                </a>
                <textarea class="input-support"></textarea>
                <a href="#" class="send-box">
                    <img src="../../src/assets/images/icons/send.svg" alt="send">
                </a>
            </form>
        </div>
    </div>
</div>
<script src="../../src/assets/js/main.js"></script>
</body>
</html>