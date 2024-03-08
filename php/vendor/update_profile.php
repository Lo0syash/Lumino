<?php
    require '../db/database.php';
    session_start();

    $userId = $_SESSION['uid'];
    $fullName = $_POST['fullName'];
    $birthDate = $_POST['birthDate'];
    $sex = $_POST['sex'];
    $email = $_POST['email'];

    $updateQuery = "UPDATE `users` SET `name`=:fullName, `birth`=NULLIF(:birthDate, ''), `sexs`=:sex, `email`=:email WHERE `id`=:userId";
    $statement = $connection->prepare($updateQuery);
    $statement->bindParam(':fullName', $fullName);
    $statement->bindParam(':birthDate', $birthDate);
    $statement->bindParam(':sex', $sex);
    $statement->bindParam(':email', $email);
    $statement->bindParam(':userId', $userId);

    $result = $statement->execute();

    if ($result) {
        echo "Профиль успешно обновлен";
    } else {
        echo "Ошибка при обновлении профиля: " . $statement->errorInfo()[2];
    }
?>
