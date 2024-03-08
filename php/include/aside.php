<?php
    global $connection;
    require '../db/database.php';
    session_start();
    $idSession = $_SESSION['uid'];
    $checkUser = $connection->query("SELECT * FROM `users` where `id` = '$idSession'")->fetch();
    $url = $_SERVER['REQUEST_URI'];
    $url = explode('?', $url);
    $url = $url[0];

    if (isset($_GET['feed'])) {
        header('Location: ../vendor/feed');
    }
    if (isset($_GET['profile'])) {
        header('Location: ../vendor/profile');
    }
    if (isset($_GET['music'])) {
        header('Location: ../vendor/music');
    }
    if (isset($_GET['friends'])) {
        header('Location: ../vendor/friends');
    }
    if (isset($_GET['message'])) {
        header('Location: ../vendor/message');
    }
    if (isset($_GET['settings'])) {
        header('Location: ../vendor/settings');
    }
    if (isset($_GET['admin'])) {
        header('Location: ../vendor/admin?admin_peoples');
    }
?>
<link rel="shortcut icon" href="/lumino/src/assets/favicon.ico" type="image/x-icon">
<section class="aside">
    <div class="aside__inner">
        <div class="mobile-aside">
            <a href="/lumino" class="aside-top">
                <img src="../../src/assets/images/icons/logotype.svg" alt="logotype">
                <h1 class="aside__title-logotype">Lumino</h1>
            </a>
            <span class="close close-btn">
                <svg height="25px" width="25px" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                    viewBox="0 0 26 26" xml:space="preserve">
                    <g>
                    <path style="fill:#16CF8D" d="M21.125,0H4.875C2.182,0,0,2.182,0,4.875v16.25C0,23.818,2.182,26,4.875,26h16.25
                        C23.818,26,26,23.818,26,21.125V4.875C26,2.182,23.818,0,21.125,0z M18.78,17.394l-1.388,1.387c-0.254,0.255-0.67,0.255-0.924,0
                        L13,15.313L9.533,18.78c-0.255,0.255-0.67,0.255-0.925-0.002L7.22,17.394c-0.253-0.256-0.253-0.669,0-0.926l3.468-3.467
                        L7.221,9.534c-0.254-0.256-0.254-0.672,0-0.925l1.388-1.388c0.255-0.257,0.671-0.257,0.925,0L13,10.689l3.468-3.468
                        c0.255-0.257,0.671-0.257,0.924,0l1.388,1.386c0.254,0.255,0.254,0.671,0.001,0.927l-3.468,3.467l3.468,3.467
                        C19.033,16.725,19.033,17.138,18.78,17.394z"/>
                    </g>
                </svg>
            </span>
        </div>
        <ul class="aside-pages">
            <li><a href="?feed" class="aside-page <?= ($url == '/lumino/php/vendor/feed') ? 'active' : '' ?> <?= ($url == '/lumino/php/vendor/feed-one') ? 'active' : '' ?> feed">Главная</a></li>
            <li><a href="?profile" class="aside-page <?= ($url == '/lumino/php/vendor/profile') ? 'active' : '' ?> profile">Профиль</a></li>
            <li><a href="?music" class="aside-page <?= ($url == '/lumino/php/vendor/music') ? 'active' : '' ?> music">Музыка</a></li>
            <li><a href="?friends" class="aside-page <?= ($url == '/lumino/php/vendor/friends') ? 'active' : '' ?> friends">Друзья</a></li>
            <li><a href="?message" class="aside-page <?= ($url == '/lumino/php/vendor/message') ? 'active' : '' ?> message">Сообщения</a></li>
        </ul>
        <ul class="aside-settings">
            <li><a href="?settings" class="aside-setting">Настройки</a></li>
            <?php if ($checkUser['level'] == 1) { ?><li><a href="?admin" class="aside-setting <?= ($url == '/lumino/php/vendor/admin') ? 'active' : '' ?>">Админ панель</a></li> <?php } ?>
        </ul>
    </div>
</section>

<div class="outline"></div>

