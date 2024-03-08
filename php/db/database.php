<?php
    try {
        return $connection = new PDO('mysql:host=localhost;dbname=z577;charset=utf8', 'z577', 'dCb7xJbFLehsEEaR');
    } catch (PDOException $error) {
        die('Ошибка подключения к бд: '.$error->getMessage());
    }
?>