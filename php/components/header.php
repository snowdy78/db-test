<header id="header" class="bg-secondary f-light">
    <a href="./index.php">Главная</a>
    <a href="./index.php">вход</a>
    <?php
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (!isset($_SESSION['auth'])) {
            echo "<a href='./register.php'>Регистрация</a>";
        } else {
            echo "<a href=''>Профиль</a>";
            echo '<form method="post" action="./php/logout.php"><button type="submit">Выход</button></form>';
        }
    ?>
</header>