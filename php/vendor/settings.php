<?php
    require '../db/database.php';
    session_start();

    $userId = $_SESSION['uid'];

    $checkTech = $connection->query("SELECT * FROM `serverStats` where `id` = '1'")->fetch();
    $checkAdmin = $connection->query("SELECT * FROM `users` where `id` = '$userId'")->fetch();

    if ($checkTech['stats'] == 1 && $checkAdmin['level'] == 0) {
        header('Location: tech');
    }

    if (empty($_SESSION['uid'])) {
        header('Location: /lumino');
        exit;
    }

    if (isset($_GET['exit'])) {
        session_destroy();
        header('Location: /lumino');
        exit;
    }

    if (isset($_POST['updateWallpaper'])) {
        $fileTmpPath = $_FILES['files']['tmp_name'];
        $fileName = time() . rand(111, 999) . '.jpg';
        $fileDestination = '../../src/assets/images/photo/' . $fileName;

        if (move_uploaded_file($fileTmpPath, $fileDestination)) {
            $updateSql = $connection->query("UPDATE `users` SET `background_profile` = '$fileName' WHERE `id` = '$userId'");
        }
    }

    if (isset($_POST['updateAvatar'])) {
        $fileTmpPath = $_FILES['files']['tmp_name'];
        $fileName = time() . rand(111, 999) . '.jpg';
        $fileDestination = '../../src/assets/images/avatars/' . $fileName;

        if (move_uploaded_file($fileTmpPath, $fileDestination)) {
            $updateSql = $connection->query("UPDATE `users` SET `avatar` = '$fileName' WHERE `id` = '$userId'");
        }
    }


    $statement = $connection->prepare("SELECT * FROM `users` WHERE `id` = :id");
    $statement->bindParam(':id', $userId);
    $statement->execute();
    $user = $statement->fetch(PDO::FETCH_ASSOC);

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
    <?php include '../include/aside.php'; ?>
    <div class="profile-wrapper">
        <div class="profile-top">
            <div class="profile-top--box">
                <p class="profile__title profile__title-setting open-burger"><span><</span> Настройки профиля</p>
                <a href="support.php" class="btn-help">
                    <img src="../../src/assets/images/icons/help.svg" alt="help">
                </a>
            </div>
            <a href="?exit" class="delete-profile settings">
                <img src="../../src/assets/images/icons/exit.svg" alt="trash" style="width: 35px; height: 35px;">
                <span>Выйти с аккаунта</span>
            </a>
        </div>
        <label for="updateWallpaper" class="buttonUpdate">Обновить</label>
        <label for="bannerImageInput" class="banner-settings" style="cursor: pointer">
            <form action="" method="post" enctype="multipart/form-data">
                <p class="banner-settings__text">Изменить обои</p>
                <img src="../../src/assets/images/photo/<?= $user['background_profile'] ?>" alt="" class="banner-profile" id="bannerImage">
                <input type="file" id="bannerImageInput" class="hidden" accept="image/*" style="display: none" name="files">
                <input type="submit" id="updateWallpaper" name="updateWallpaper" class="buttonUpdate" style="display: none">
            </form>
        </label>
        <div class="profile-content settings">
            <div class="profile-content__top">
                <form action="" method="post" class="profile-date">
                    <input type="text" id="full-name" class="profile-name" name="fullName" placeholder="ФИО" value="<?=$user['name']?>" oninput="updateProfile(this)">
                </form>
                <div class="box-avatar">
                    <label for="updateAvatar" class="updateAvatar"><img src="../../src/assets/images/icons/photocamera.svg" alt=""></label>
                    <label for="avatarImageInput" class="avatar-box settings" style="cursor: pointer">
                        <form action="" method="post" enctype="multipart/form-data">
                            <div class="avatar-reverse">
                                <p class="avatar-text">Изменить аватар</p>
                            </div>
                            <img src="../../src/assets/images/avatars/<?= $user['avatar'] ?>" alt="avatar profile" id="avatarImage">
                            <input type="file" id="avatarImageInput" class="hidden" name="files" accept="image/*" style="display: none">
                            <input type="submit" id="updateAvatar" name="updateAvatar" style="display: none">
                        </form>
                    </label>
                </div>

            </div>
            <div class="profile-content__body">
                <form action="" method="post" class="profile-block">
                    <p class="profile-name">Дата рождения:</p>
                    <input type="date" id="dateBirth" class="profile-date" name="birthDate" placeholder="Дата рождения"
                           value="<?= $user['birth'] ?>" oninput="updateProfile(this)" style="padding-left: 20px; padding-right: 20px;">
                </form>
                <div class="profile-block">
                    <p class="profile-name">Пол:</p>
                    <input type="text" id="sex" class="profile-date" name="sex" placeholder="Пол" value="<?= $user['sexs'] ?>"
                           oninput="updateProfile(this)">
                </div>
                <div class="profile-block">
                    <p class="profile-name">Электронная почта:</p>
                    <input class="profile-date" id="emailInput" name="email" placeholder="Email" value="<?= $user['email'] ?>"
                           style="max-width: 331px" oninput="updateProfile(this)">
                </div>
            </div>
        </div>
    </div>
</div>
<script src="../../src/assets/js/main.js"></script>
-
<script>

    let fullNameInput = document.getElementById('full-name');
    let birthDateInput = document.getElementById('dateBirth');
    let sexInput = document.getElementById('sex');
    let emailInput = document.getElementById('emailInput');

    fullNameInput.addEventListener('change', updateProfile);
    birthDateInput.addEventListener('change', updateProfile);
    sexInput.addEventListener('change', updateProfile);
    emailInput.addEventListener('change', updateProfile);

    function updateProfile() {
        let fullNameValue = fullNameInput.value;
        let birthDateValue = birthDateInput.value;
        let sexValue = sexInput.value;
        let emailValue = emailInput.value;

        let formData = new FormData();
        formData.append('userId', <?=$_SESSION['uid']?>);
        formData.append('fullName', fullNameValue);
        formData.append('birthDate', birthDateValue);
        formData.append('sex', sexValue);
        formData.append('email', emailValue);
        formData.append('bannerImage', bannerImageInput.files[0]);
        formData.append('avatarImage', avatarImageInput.files[0]);

        let xhr = new XMLHttpRequest();

        xhr.open('POST', 'update_profile.php', true);

        xhr.setRequestHeader('enctype', 'multipart/form-data');

        xhr.send(formData);

        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    console.log('Запрос выполнен успешно.');
                    console.log(xhr.responseText);
                } else {
                    console.error('Ошибка выполнения запроса:', xhr.status);
                }
            }
        };
    }
</script>
</body>
</html>
