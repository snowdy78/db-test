<header id="header" class="bg-secondary f-light">
    <a href="./index.php">Главная</a>
    <a href="./index.php">вход</a>
    <?php
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            if (isset($_SESSION['session_destroy'])) {
                session_destroy();                
            }
        }
        if (!isset($_SESSION['auth'])) {
            echo "<a href='./register.php'>Регистрация</a>";
        } else {
            echo "<a href=''>Профиль</a>";
            echo '<form method="post"><button type="submit" name="session_destroy">Выход</button></form>';
        }
    ?>
</header>