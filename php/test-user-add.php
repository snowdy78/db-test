<?php
    include_once "database.php";

    $db = new DataBase();
    $login = "my-login";
    $email = "my.mail@mail.ru";
    $password = "dog27";
    try {
        $db->addUser($login, $email, $password);
        $user = $db->getUserBy('login', 'my-login');
        $user->setLogin("new-login");
    } catch (Exception $err) {
        echo $err->getMessage();
        exit;
    }
?>