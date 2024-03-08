<?php
    global $connection;
    require 'php/db/database.php';

    $ip = $_SERVER['REMOTE_ADDR'];
    // $country = geoip_country_name_by_name($ip);

    if ($country == 'Russian Federation') {
        $language = include_once('php/db/language-ru.php');
    } else {
        $language = include_once('php/db/language-en.php');
    }

    session_start();
    if (!empty($_SESSION['uid'])) {
        header('Location: php/vendor/feed');
    }

    if (isset($_POST['registration'])) {
        $name = $_POST['name'];
        $lastname = $_POST['lastname'];
        $email = $_POST['email'];
        $password = $_POST['f-password'];
        $re_password = $_POST['s-password'];

        foreach($_POST as $key=>$value){
            $date[$key] = $value;
            if (empty($value)){
                $errors = "Заполните все поля";
                break;
            }
        }

        if (empty($errors)){
            if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
                $errors = "Неверный формат почты";
            }
        }

        $result = $connection->query("SELECT * FROM `users` WHERE `email` = '$email'");
        if ($result->rowCount() > 0) {
            $errors = "Вы уже зарегистрированы";
        }

        if (empty($errors)){
            if (strlen($password) >= 6){
                if (strlen($name) >= 2 && strlen($lastname) >= 2){
                        if ($password == $re_password) {
                            $password = md5($password);
                            $name = $lastname . ' ' . $name;
                            $regPeople = $connection->query("INSERT INTO `users` (`id`,`background_profile`, `avatar`, `name`, `birth`, `sexs`, `email`, `password`,`level`) 
                                                VALUES (NULL, 'default-banner.png', 'default.png', '$name', NULL, NULL, '$email', '$password','0')");
                            $sessionUser = $connection->query("SELECT * FROM `users` where `email` = '$email'")->fetch();
                            $_SESSION['uid'] = $sessionUser['id'];
                            header('Location: php/vendor/feed');
                        } 
                        else {
                        $errors = "Пароли не совпадают!";
                    }
                } else {
                    $errors = "Введите действительную фамилию и имя!";
                }
            } else {
                $errors = "Пароль должен быть больше 5 символов!";
            }
        }
    }
    if (isset($_POST['login'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];
        foreach ($_POST as $key => $value) {
            $date[$key] = $value;
            if (empty($value)) {
                $errorsLogin = "Заполните все поля";
                break;
            }
        }

        if (empty($errorsLogin)) {
            if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                $errorsLogin = "Неверный формат почты";
            }
        }

        if (empty($errorsLogin)) {
            if (strlen($password) >= 6) {
                $result = $connection->query("SELECT * FROM `users` WHERE `email` = '$email'");
                foreach($result as $value){
                    if (!$value){
                        $errorsLogin = "Неверная почта";
                        break;
                    }
                }

                $password = md5($password);
                $authorizeUser = $connection->query("SELECT * FROM `users` where `email` = '$email' AND `password` = '$password'")->fetch();
                if (empty($authorizeUser)) {
                    $errorsLogin = "Неверный пароль";
                } else {
                    $_SESSION['uid'] = $authorizeUser['id'];
                    header('Location: php/vendor/feed');
                }
            } else {
                $errors = "Пароль должен быть больше 5 символов!";
            }
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
    <link rel="stylesheet" href="src/assets/css/main.css">
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <title>Lumino</title>
</head>

<body class="body <?=((isset($_GET['reg'])) || (!isset($_GET['']))) ? 'auth' : '' ?>">
    <div class="wrapper wrapper-authorize">
        <?php if (isset($_GET['reg'])) {
        ?>
        <div class="container">
            <img src="src/assets/images/icons/logotype.svg" alt="logotype" class="lgootype">
            <h1 class="form__title reg-title"><?=$language['reg-title']?></h1>
            <form class="form" method="post">
                <div class="form-top">
                    <input type="text" class="form-input" name="name" placeholder="<?=$language['name']?>"
                        value="<?=$_POST['name']?>">
                    <input type="text" class="form-input" name="lastname" placeholder="<?=$language['lastname']?>"
                        value="<?=$_POST['lastname']?>">
                </div>
                <input type="email" class="form-input" name="email" placeholder="Email" value="<?=$_POST['email']?>">
                <input type="password" class="form-input" name="f-password" placeholder="<?=$language['cr-password']?>">
                <input type="password" class="form-input" name="s-password" placeholder="<?=$language['re-password']?>">
                <input type="submit" class="form-input submit" name="registration"
                    value="<?=$language['registration']?>">
                <p style="color: red; font-weight: 600;"><?=$errors?></p>
            </form>
            <a href="?" class="auth-link"><?=$language['auth-nowReg']?></a>
        </div>
        <?
    }
    else {
        ?>
        <div class="container">
            <img src="src/assets/images/icons/logotype.svg" alt="logotype" class="lgootype">
            <h1 class="form__title"><?=$language['auth-title']?></h1>
            <p class="form__subtitle"><?=$language['auth-subtitle']?></p>
            <form class="form" method="post">
                <input type="email" class="form-input" name="email" placeholder="Email" value="<?=$_POST['email']?>">
                <input type="password" class="form-input" name="password" placeholder="<?=$language['password']?>">
                <input type="submit" class="form-input submit" name="login" value="<?=$language['login']?>">
                <p style="color: red; font-weight: 600;"><?=$errorsLogin?></p>
            </form>
            <a href="?reg" class="auth-link"><?=$language['auth-dontReg']?></a>
        </div>
        <?
    }
    ?>
    </div>
    <script src="src/assets/js/main.js"></script>
</body>

</html>