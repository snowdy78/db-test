<?php
    include_once "database.php";
    if (!empty($_GET['user'])) {
        $db = new DataBase('../obvp.db');
        try {
            $db->removeUser($_GET['user']);
            
        } catch (PDOException $err) {
            echo $err->getMessage();
            exit;
        }
    }
?>