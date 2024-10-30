<!DOCTYPE html>
<html>
<head>
    <?php
        include_once "./php/components/head.php"
    ?>
</head>
<body>
    <h1>Registration</h1>
    <form method="post">
        <input type="text" placeholder="login..." name="login" id="login"/>
        <br>
        <input type="email" placeholder="email" name="email" id="email">
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
                if (empty($_POST['login']) || empty($_POST['email']) || empty($_POST["password"])) {
                    echo "has empty fields<br>";
                    return;
                }
                try {
                    $db->addUser(
                        $_POST['login'],
                        $_POST["email"],
                        $_POST["password"]
                    );
                } catch (Exception $err) {
                    echo $err->getMessage()."<br>";
                    return;
                }
                header("Location: ../index.php");
            }
            m();
        ?>

        <button type="submit">sign up</button>
        or <a href="./index.php">sign in</a>
    </form>
</body>
</html>