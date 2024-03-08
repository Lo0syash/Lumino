<?php
    require '../db/database.php';
    session_start();

    $url = $_SERVER['REQUEST_URI'];
    $url = explode('?', $url);
    $url = $url[0];

    $idUser = $_SESSION['uid'];

    if (empty($_SESSION['uid'])) {
        header('Location: /lumino');
    } else {
        $checkAdministartor = $connection->query("SELECT * FROM `users` where `id` = '$idUser'")->fetch();
        if ($checkAdministartor['level'] != 1) {
            header('Location: /lumino');
        }
    }

    if (isset($_GET['deleteUser'])) {
        $idDeleteUser = $_GET['id'];
        $deleteSql = $connection->query("DELETE FROM users WHERE `id` = '$idDeleteUser'");
        header('Location: admin?admin_peoples');
    }

    if (isset($_GET['deleteNews'])) {
        $idDeleteNews = $_GET['id'];
        $deleteSql = $connection->query("DELETE FROM news WHERE `id` = '$idDeleteNews'");
        header('Location: admin?admin_news');
    }

    if (isset($_POST['updateText'])) {
        $id = $_POST['id'];
        $someText = $_POST['someText'];
        $updateSql = $connection->query("UPDATE `news` SET `discription` = '$someText' WHERE `id` = '$id'");
    }

    if (isset($_POST['updatePhoto'])) {
        $id = $_POST['id'];
        if ($_FILES['files']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['files']['tmp_name'];
            $fileName = time() . rand(111, 999) . '.jpg';
            $fileDestination = '../../src/assets/images/photo/' . $fileName;
            move_uploaded_file($fileTmpPath, $fileDestination);

            $updateSql = $connection->query("UPDATE `news` SET `photo` = '$fileName' WHERE `id` = '$id'");
        }
    }


if (isset($_GET['closesServices'])) {
        $closeServerSql = $connection->query("UPDATE `serverStats` SET `stats` = '1' WHERE `id` = '1'");
        echo '<script>document.location = "?"</script>';
    }

    if (isset($_GET['openServices'])) {
        $closeServerSql = $connection->query("UPDATE `serverStats` SET `stats` = '0' WHERE `id` = '1'");
        echo '<script>document.location = "?"</script>';
    }

    if (isset($_POST['updateUser'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $birth = $_POST['birthDate'];
    $sexs = $_POST['sexs'];
    $email = $_POST['email'];

    if ($_FILES['background_profile']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['background_profile']['tmp_name'];
        $fileName = time() . rand(111, 999) . '.jpg';
        $fileDestination = '../../src/assets/images/photo/' . $fileName;
        move_uploaded_file($fileTmpPath, $fileDestination);

        $updateSql = $connection->query("UPDATE `users` SET `background_profile` = '$fileName' WHERE `id` = '$id'");
    }

    if ($_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['avatar']['tmp_name'];
        $fileName = time() . rand(111, 999) . '.jpg';
        $fileDestination = '../../src/assets/images/avatars/' . $fileName;
        move_uploaded_file($fileTmpPath, $fileDestination);

        $updateSql = $connection->query("UPDATE `users` SET `avatar` = '$fileName' WHERE `id` = '$id'");
    }

        $updateSql1 = $connection->query("UPDATE `users` SET `name` = '$name', `birth` = '$birth', `sexs` = '$sexs', 
                                            `email` = '$email' WHERE `id` = '$id'");
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
    <div class="admin-wrapper">
        <div class="profile-top">
            <p class="profile__title">Админ панель</p>
            <?php
                $checkTech = $connection->query("SELECT * FROM `serverStats` where `id` = '1'")->fetch();
                if ($checkTech['stats'] == 0) {
                    ?>
                        <a href="?closesServices" class="stop-server">Закрыть сайт</a>
                    <?php
                } else {
                    ?>
                        <a href="?openServices" class="stop-server">Открыть сайт</a>
                    <?php
                }
            ?>
        </div>
        <div class="catalog">
            <a href="?admin_peoples" class="catalog-item <?=(isset($_GET['admin_peoples'])) ? 'active' : '' ?>">Пользователи</a>
            <a href="?admin_news" class="catalog-item <?=((isset($_GET['admin_news'])) || (isset($_GET['edite']))) ? 'active' : '' ?>">Новости</a>
            <a href="?application&id=1" class="catalog-item <?=(isset($_GET['application'])) ? 'active' : '' ?>">Заявки</a>
        </div>
        <div class="admin__wrapper-main">
            <?php
            if (isset($_GET['admin_peoples'])) {
                ?>
                    <ul class="peoples__container">
                        <?php
                            $checkPeople = $connection->query("SELECT * FROM `users` where `id` != '$idUser'");
                            while ($result = $checkPeople->fetch()) {
                        ?>
                            <li class="peoples-item">
                                <div class="peoples__content">
                                    <img src="../../src/assets/images/avatars/<?=$result['avatar']?>" alt="avatar" style="margin: 5px 0;
                                                                                                                            border-radius: 50%;
                                                                                                                            height: 70px;
                                                                                                                            width: 70px;
                                                                                                                            object-fit: cover;">
                                    <div class="peoples__name"><?=$result['name']?></div>
                                </div>
                                <div class="peoples__function">
                                    <a href="?editePeople&id=<?=$result['id']?>"><img src="../../src/assets/images/icons/edite.svg" alt="edit"></a>
                                    <a href="?deleteUser&id=<?=$result['id']?>" onclick="return confirm('Вы уверены, что хотите удалить эту новость?');"><img src="../../src/assets/images/icons/trash.svg" alt="trash"></a>
                                </div>
                            </li>
                        <? } ?>
                    </ul>
                <?
            }

            if (isset($_GET['admin_news'])) {
                ?>
                    <ul class="news-list">
                        <?php
                            $checkNews = $connection->query("SELECT * FROM `news`");
                            while ($result = $checkNews->fetch()) {
                                $userCheck = $result['id_user'];
                                $searchUsers = $connection->query("SELECT * FROM `users` where `id` = '$userCheck'")->fetch();
                        ?>
                            <li class="news-item">
                                <div class="news-item__profile">
                                    <div class="container-avatar">
                                        <div class="ellipse-avatar_bck"></div>
                                        <img src="../../src/assets/images/avatars/<?=$searchUsers['avatar']?>" alt="" style="border-radius: 50%">
                                    </div>
                                    <div class="profile-content">
                                        <p class="profile-name"><?=$searchUsers['name']?></p>
                                        <?php
                                        $unixTime = $result['date'];
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
                                    <div class="profile-function">
                                        <a href="?edite&id=<?=$result['id']?>"><img src="../../src/assets/images/icons/edite.svg" alt="edit" class="edite-icon"></a>
                                        <a href="feed-one?id=<?=$result['id']?>"><img src="../../src/assets/images/icons/message.svg" alt="message" class="message-icon"></a>
                                    </div>
                                </div>
                                <p class="news-item__text"><?=$result['discription']?></p>
                                <img src="../../src/assets/images/photo/<?=$result['photo']?>" alt="" style="width: 100%">
                            </li>
                            <?php } ?>
                        </ul>
                <?
            }

            if (isset($_GET['edite'])) {
                $idNews = $_GET['id'];
                $checkNews = $connection->query("SELECT * FROM `news` where `id` = '$idNews'")->fetch();
                $idAuthor = $checkNews['id_user'];
                $checkAuthor = $connection->query("SELECT * FROM `users` where `id` = '$idAuthor'")->fetch();
                ?>
                    <div class="news__more">
                            <div class="news-top">
                                <div class="news-item__profile">
                                    <div class="news-item">
                                        <div class="container-avatar">
                                            <div class="ellipse-avatar_bck"></div>
                                            <img src="../../src/assets/images/avatars/<?=$checkAuthor['avatar']?>" alt="">
                                        </div>
                                        <div class="profile-content">
                                            <p class="profile-name"><?=$checkAuthor['name']?></p>
                                            <?php
                                            $unixTime = $checkNews['date'];
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
                                        <div class="profile-function">
                                            <a href="?deleteNews&id=<?=$checkNews['id']?>" onclick="return confirm('Вы уверены, что хотите удалить эту новость?');"><img src="../../src/assets/images/icons/trash.svg" alt=""></a>
                                        </div>
                                    </div>
                                    <hr class="news-hr">
                                    <form action="" method="post" enctype="multipart/form-data" style="display: flex; align-items: center; gap: 15px">
                                        <input type="hidden" name="id" value="<?=$checkNews['id']?>">
                                        <textarea class="news__text input" name="someText" placeholder="Текст новости" maxlength="450"><?=$checkNews['discription']?></textarea>
                                        <label for="update-news" style="cursor: pointer; " class="label-refresh">
                                            <img src="../../src/assets/images/icons/refresh.png" alt="">
                                        </label>
                                        <input type="submit" name="updateText" id="update-news" class="submit-text" style="display: none">
                                    </form>

                                </div>
                            </div>
                            <form class="news-item__image" enctype="multipart/form-data" method="post">
                                <input type="hidden" name="id" id="id" style="display: none" value="<?=$checkNews['id']?>">
                                <input type="file" id="files" class="news-icons__image" style="display: none" accept="image/*" name="files">
                                <label for="files" class="news-icons__image" style="cursor: pointer;"><img src="../../src/assets/images/icons/edite.svg" alt=""></label>
                                <input type="submit" id="updatePhoto" name="updatePhoto" style="display: none">
                                <label for="updatePhoto" class="updateLabel">
                                    <img src="../../src/assets/images/icons/refresh.png" alt="">
                                </label>
                                <?php
                                    if (!empty($checkNews['photo'])) {
                                        echo '<img src="../../src/assets/images/photo/' . $checkNews['photo'] . '" alt="" class="news-image" style="object-fit:cover">';
                                    } else {
                                        echo 'Изображение для новости отсутствует';
                                    }
                                ?>
                            </form>
                        </div>
                <?
            }

            if (isset($_GET['application'])) {
                ?>
                    <div class="application__content">
                        <div class="application__menu">
                            <h2 class="application__title">Заявки</h2>
                            <ul class="message-list__people">
                                <?php
                                    $supportSql = $connection->query("SELECT * FROM `support`");
                                    $addedUsers = array();
                                    while($result = $supportSql->fetch()) {
                                        $userIdSupport = $result['user'];
                                        if (!in_array($userIdSupport, $addedUsers)) {
                                            $addedUsers[] = $userIdSupport;
                                            $userSqlCheck = $connection->query("SELECT * FROM `users` where `id` = '$userIdSupport'")->fetch();
                                            ?>
                                            <a href="?openSupport&id=<?=$userSqlCheck['id']?>" class="message-people">
                                                <div class="avatar-box online">
                                                    <img src="../../src/assets/images/avatars/<?=$userSqlCheck['avatar']?>" alt="">
                                                </div>
                                                <div class="people-content">
                                                    <div class="people-box">
                                                        <span class="people-lastname"><?=$userSqlCheck['name']?></span>
                                                    </div>
                                                    <p class="people-stats">онлайн</p>
                                                </div>
                                            </a>
                                            <?php
                                        }
                                    }
                                ?>
                            </ul>
                        </div>
                            <div class="application__chat">
                                <?php
                                    $userId = $_GET['id'];
                                    $checkUserSupport = $connection->query("SELECT * FROM `users` where `id` = '$userId'")->fetch();
                                ?>
                                <div class="application__chat-top">
                                    <div class="message-people">
                                        <div class="people-content">
                                            <div class="people-box">
                                                <span class="people-lastname"><?=$checkUserSupport['name']?></span>
                                            </div>
                                            <p class="people-stats">онлайн</p>
                                        </div>
                                        <div class="avatar-box online">
                                            <img src="../../src/assets/images/avatars/<?=$checkUserSupport['avatar']?>" alt="avatar people">
                                        </div>
                                    </div>
                                </div>
                                <div class="message-body">
                                    <div class="message-chat">
                                        <?php
                                        $userId = $_GET['id'];
                                        $avatarUser = $connection->query("SELECT * FROM users where `id` = '$userId'")->fetch();
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
                                                    <div class="message-input">
                                                        <img src="../../src/assets/images/avatars/<?=$avatarUser['avatar']?>" alt="avatar people">
                                                        <p class="message-text"><?= $question ?></p>
                                                    </div>
                                                    <?php
                                                }

                                                if (!empty($answer)) {
                                                    ?>
                                                    <div class="message-output">
                                                        <p class="message-text"><?= $answer ?></p>
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
                                        <textarea value="Напишите..." class="input-message"></textarea>
                                        <a href="#" class="send-box">
                                            <img src="../../src/assets/images/icons/send.svg" alt="send">
                                        </a>
                                    </form>
                                </div>
                            </div>
                    </div>

                <?
            }

            if (isset($_GET['editePeople'])) {
                $idPeople = $_GET['id'];
                $checkPeoples = $connection->query("SELECT * FROM `users` where `id` = '$idPeople'")->fetch();
                ?>

                <form class="profile-wrapper" enctype="multipart/form-data" method="post">
                    <input type="hidden" name="id" value="<?=$checkPeoples['id']?>">
                    <label for="bannerImageInput" class="banner-settings" style="cursor: pointer">
                        <p class="banner-settings__text">Изменить обои</p>
                        <img src="../../src/assets/images/photo/<?=$checkPeoples['background_profile']?>" alt="profile banner" class="banner-profile" id="bannerImage" style="width: 1520px; height: 350px; object-fit: cover">
                        <input type="file" id="bannerImageInput" class="hidden" accept="image/*" name="background_profile" style="display: none" value="<?=$checkPeoples['background_profile']?>">
                    </label>
                    <div class="profile-content settings admin">
                        <div class="profile-content__top">
                            <input type="text" id="full-name" class="profile-name" name="name" placeholder="ФИО" value="<?=$checkPeoples['name']?>">
                            <label for="avatarImageInput" class="avatar-box settings" style="cursor: pointer">
                                <div class="avatar-reverse">
                                    <p class="avatar-text">Изменить аватар</p>
                                </div>
                                <img src="../../src/assets/images/avatars/<?=$checkPeoples['avatar']?>" alt="avatar profile" id="avatarImage" style="object-fit: cover">
                                <input type="file" id="avatarImageInput" class="hidden" accept="image/*" name="avatar" style="display: none" value="<?=$checkPeoples['avatar']?>">
                            </label>
                        </div>
                        <div class="profile-content__body">
                            <div class="profile-block ">
                                <p class="profile-name">Дата рождения:</p>
                                <input type="date" id="dateBirth" class="profile-date date" name="birthDate" placeholder="Дата рождения"
                                       value="<?=$checkPeoples['birth']?>" style="padding-left: 20px; padding-right: 20px;">
                            </div>
                            <div class="profile-block">
                                <p class="profile-name">Пол:</p>
                                <input type="text" id="sex" class="profile-date" name="sexs" placeholder="Пол" value="<?=$checkPeoples['sexs']?>">
                            </div>
                            <div class="profile-block">
                                <p class="profile-name">Электронная почта:</p>
                                <input class="profile-date" id="emailInput" name="email" placeholder="Email" value="<?= $checkPeoples['email'] ?>"
                                       style="max-width: 331px">
                            </div>
                        </div>
                    </div>
                    <input type="submit" id="updateUserSubmit" name="updateUser" style="display: none">
                    <label for="updateUserSubmit" class="updateUserButtons">Обновить</label>
                </form>

                <?php
            }
            ?>
        </div>
    </div>
</div>
</body>
</html>