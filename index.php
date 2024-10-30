
<!DOCTYPE html>
<html>
<head>
    <?php
        include_once "./php/components/head.php"
    ?>
</head>
<body>
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
                include_once "./php/user.php";
                include_once "./php/database.php";
                $db = new DataBase();
                if (empty($_POST['login']) || empty($_POST["password"])) {
                    echo "has empty fields<br>";
                    return;
                }
                try {
                    $user = $db->getUserBy('login', $_POST['login']);
                } catch (Exception $err) {
                    echo $err->getMessage()."<br>";
                    return;
                }
                if (empty($user)) {
                    echo "login or password is not correct<br>";
                    return;
                }
                $password = sha1($_POST['password']);
                if ($password != $user->getPassword()) {
                    echo "login or password is not correct<br>";
                    return;
                }
                session_start();
                $_SESSION['auth'] = $user->getId();
                header("Location: profile.php");     
            }
            m();
        ?>
        <button type="submit">sign in</button>
        or <a href="./register.php">sign up</a>
    </form>
</body>
</html>