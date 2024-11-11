
<!DOCTYPE html>
<html>
<head>
    <?php
        include_once "./php/components/head.php"
    ?>
</head>
<body>
    <?php 
        include "./php/components/header.php";
    ?>
    <div class="container">
        <h1>Authorisation</h1>

        <form method="post">
            <input type="text" placeholder="login..." name="login" id="login"/>
            <br>
            <input type="password" placeholder="password..." name="password" id="password"/>
            <br>
            <?php
                function m() {    
                    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
                        return;
                    }
                    include_once "./php/database.php";
                    $db = new DataBase();
                    if (empty($_POST['login']) || empty($_POST["password"])) {
                        throw new Exception("has empty fields");
                    }
                    $user = $db->getUserBy('login', $_POST['login']);                    
                    if (empty($user)) {
                        throw new Exception("login or password is not correct");
                    }
                    $password = sha1($_POST['password']);
                    if ($password != $user->getPassword()) {
                        throw new Exception("login or password is not correct");
                    }
                    session_start();
                    $_SESSION['auth'] = $user->getId();
                    header("Location:./profile.php");
                }
                try {
                    m();
                } catch (Exception $err) {
                    echo "<div class='error'>".$err->getMessage()."</div>";
                }
            ?>
            <button type="submit">sign in</button>
            or <a href="./register.php">sign up</a>
        </form>
    </div>
</body>
</html>