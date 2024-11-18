<?php
    include_once "database.php";
    if (!empty($_GET['user'])) {
        $db = new DataBase();
        try {
            $db->removeUser($_GET['user']);
            
        } catch (PDOException $err) {
            echo $err->getMessage();
            exit;
        }
    }
?>