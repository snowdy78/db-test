<?php
    include_once "database.php";
    if (!empty($_GET['user'])) {
        $db = new DataBase();
        try {
            $db->removeUser($_GET['user']);
        } catch (Exception $err) {
            echo $err->getMessage();
        }
    }
    echo "<script>history.back();</script>";

?>