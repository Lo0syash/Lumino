<?php
    require '../db/database.php';
    session_start();
    if (!$_SESSION['uid']) {
        header('Location: /lumino');
    }

    $idUser = $_SESSION['uid'];

    $checkTech = $connection->query("SELECT * FROM `serverStats` where `id` = '1'")->fetch();
    $checkAdmin = $connection->query("SELECT * FROM `users` where `id` = '$idUser'")->fetch();

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
        <div class="music-wrapper">
            <h1 class="music__title-wrapp open-burger"><span><</span>Музыка</h1>
            <div class="banner-music">
                <p class="num-banner">01</p>
                <h2 class="music__title">BRING ME THE HORIZON</h2>
                <p class="music__subtitle">C новым хитом!</p>
                <a href="#" class="music-banner__play" onclick="toggleMusic(3, 'imageId3', '../../src/assets/images/icons/player.svg', '../../src/assets/images/icons/pause.svg')">Play</a>
            </div>
            <div class="music-content">
                <ul class="music-charts">
                    <?php
                    $dateMusicDataBase = $connection->query("SELECT * FROM `music`");
                    while ($resultMusic = $dateMusicDataBase->fetch()) {
                        ?>
                        <li class="music-album" onclick="toggleMusic(<?=$resultMusic['id']?>, 'imageId<?=$resultMusic['id']?>', '../../src/assets/images/icons/player.svg', '../../src/assets/images/icons/pause.svg')">
                            <img src="../../src/assets/images/music/<?=$resultMusic['photo']?>" alt="<?=$resultMusic['name']?>" style="max-width: 147px; border-radius: 15px;">
                            <p class="music-album__title"><?=$resultMusic['name']?></p>
                        </li>
                    <?php } ?>
                </ul>
                <div class="music-list">
                    <?php
                        $dateMusicDataBase = $connection->query("SELECT * FROM `music`");
                        while ($resultMusic = $dateMusicDataBase->fetch()) {
                            ?>
                            <li class="music-item">
                                <audio id="<?=$resultMusic['id']?>" src="../../src/assets/music/<?=$resultMusic['name']?>.mp3"></audio>
                                <img id="imageId<?=$resultMusic['id']?>" src="../../src/assets/images/icons/player.svg" alt="player" onclick="toggleMusic(<?=$resultMusic['id']?>, 'imageId<?=$resultMusic['id']?>', '../../src/assets/images/icons/player.svg', '../../src/assets/images/icons/pause.svg')">
                                <p class="music-box">
                                    <span class="music-author"><?=$resultMusic['author']?></span> - <span class="music-name"><?=$resultMusic['name']?></span>
                                </p>
                                <p class="music-time"><?=$resultMusic['duration']?></p>
                            </li>

                            <?
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <script src="../../src/assets/js/main.js"></script>
    <script>
        let activeAudio = null;
        let activeImage = null;
        let previousAudio = null;

        function toggleMusic(audioID, imageId, playImageSrc, pauseImageSrc) {
            let audio = document.getElementById(audioID);
            let image = document.getElementById(imageId);

            if (audio.paused) {
                if (activeAudio && activeAudio !== audio) {
                    previousAudio = activeAudio;
                    activeAudio.pause();
                    activeImage.src = "../../src/assets/images/icons/player.svg";
                }

                audio.play();
                image.src = pauseImageSrc;

                activeAudio = audio;
                activeImage = image;
            } else {
                audio.pause();
                image.src = playImageSrc;

                activeAudio = null;
                activeImage = null;
            }

            // Обнуление времени предыдущего трека
            if (previousAudio && previousAudio !== audio) {
                previousAudio.currentTime = 0;
            }
        }
    </script>
</body>
</html>