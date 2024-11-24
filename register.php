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
        <script>
            var n = Math.floor(Math.random() * 9);
        </script>
        <h1>Registration</h1>
        <form class="form-with-captcha" method="post" onsubmit="validateCaptcha(event, n);">
            <input type="text" placeholder="login..." name="login" id="login" required/>
            <br>
            <input type="email" placeholder="email" name="email" id="email" required/>
            <br>
            <input type="password" placeholder="password..." name="password" id="password" required/>
            <br>
            <?php 
                function m() {
                    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
                        return;
                    }
                    include_once "./php/database.php";
                    $db = new DataBase();
                    if (empty($_POST['login']) || empty($_POST['email']) || empty($_POST["password"])) {
                        throw new Exception("has empty fields<br>");
                    }
                    $state = $db->addUser(
                        $_POST['login'],
                        $_POST["email"],
                        $_POST["password"]
                    );
                    if (!$state) {
                        throw new Exception("User is not registered. Try again");
                    }
                    return "Successfully registred!";
                }
                try {
                    $message = m();
                    if (!empty($message)) {
                        echo "<div class='success'>$message</div>";
                    } 
                } catch(Exception $err) {
                    echo "<div class='error'>".$err->getMessage()."</div>";
                }
            ?>
            <div class="captcha">
                <div class="captcha-container">
                    <div class="captcha-image">
                        <script>
                            window.onload = () => {
                                generateCaptcha(n);
                            };
                        </script>
                    </div>
                </div>
            </div>
            Type a number of circles<br>
            <input type="number" placeholder="captcha..." name="captcha" class="captcha-input" pattern="0-9" required/><br>
            <input type="submit" class="captcha-submition" value="sign up"/>
            or <a href="./index.php">sign in</a>
            
        </form>
    </div>
</body>
</html>