<?php
    session_start();
    if (isset($_SESSION['auth'])) {
        $_SESSION['auth'] = null;
    }
    header('Location: ../index.php');
?>