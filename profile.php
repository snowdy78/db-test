<!DOCTYPE html>
<html lang="en">
<head>
    <?php
        include_once "./php/components/head.php";
    ?>
</head>
<body>
    <?php
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (empty($_SESSION['auth'])) {
            return;
        }
        include "./php/user.php";
        include "./php/database.php";

        m();
        $db = new DataBase();

        $user = $db->getUserBy('id', $_SESSION['auth']);
        $login = $user->getLogin();
        $email = $user->getEmail();
        $reg_date = $user->getRegistrationDate();
        print 
        "
            <form method='post'>
                <table border=0>
                    <tr>
                        <td>
                            Login:
                        </td>
                        <td>
                            <input class='editable' type='text' name='login' value='$login'>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Email:
                        </td>
                        <td>
                            $email
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Registration Date:     
                        </td>
                        <td>
                            $reg_date
                        </td>
                    </tr>
                    <tr>
                        <td colspan=2 align=center>
                            <input id='password-input' name='password' type='password' placeholder='******'><br>
                        </td>
                    </tr>
                    <tr>
                        <td colspan=2 align=center>
                            <input id='password-repeat' name='password-repeat' type='password' placeholder='******'><br>
                        </td>
                    </tr>
                    <tr>
                        <td align=center colspan=2>
                            <button id='password-reset-btn'>
                                Update data
                            </button>
                        </td>
                    </tr>
                </table>
            </form>
        ";
        function m() {
            if ($_SERVER['REQUEST_METHOD'] != 'POST') {
                return;
            }
            include_once "./php/user.php";
            include_once "./php/database.php";
            if (session_status() != PHP_SESSION_ACTIVE) {
                session_start();
            }
            if (empty($_SESSION['auth'])) {
                header('Location: ../index.php');
                return;
            }
            if (empty($_POST['login']) && (empty($_POST['password']) || empty($_POST['password-repeat']))) {
                header('Location: ../profile.php');
                return;
            }
            $db = new DataBase();
            $user = $db->getUserBy('id', $_SESSION['auth']);
            if (!empty($_POST['login'])) {
                if ($user->getLogin() != $_POST['login']) {
                    $user->setLogin($_POST['login']);
                    print "Success login changed!<br>";
                }
            }
            if (!empty($_POST['password']) && !empty($_POST['password-repeat'])) { 
                $encoded_password = sha1($_POST['password']);
    
                if ($_POST['password'] == $_POST['password-repeat'] && $encoded_password != $user->getPassword()) {
                    $user->setPassword($_POST['password']);
                    print "Success password changed!<br>";
                } else if ($encoded_password === $user->getPassword()) {
                    print "current password and new password are the same<br>";
                } else {
                    print "repeated password is not the same<br>";
                }    
            }
        }
        print "go to <a href='../index.php'>authorisation</a><br>";
    ?>
</body>
</html>