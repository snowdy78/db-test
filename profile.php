<!DOCTYPE html>
<html lang="en">
<head>
    <?php
        include_once "./php/components/head.php";
    ?>
</head>
<body>
    
    <?php 
        include "./php/components/header.php";
    ?>
    <div class="container">
        <?php
            if (session_status() != PHP_SESSION_ACTIVE) {
                session_start();
            }
            if (empty($_SESSION['auth']) && empty($_GET['user'])) {
                echo "<script>history.back();</script>";
                return;
            }
            include_once "./php/database.php";
            $db = new DataBase();
            if (empty($_GET['user'])) {
                $user = $db->getUserBy('id', $_SESSION['auth']);
            } else {
                $user = $db->getUserBy('id', $_GET['user']);
            }
            try {
                $success = m();
            }
            catch (Exception $err) {} 

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
            ";
            
            if (isset($err) || isset($success)) {
                $obj = $err ?? $success;
                $message = gettype($obj) == 'string' ? $obj : $obj->getMessage();
                print "
                        <tr>

                            <td colspan=2>
                                <div class='".(isset($err) ? 'error' : 'success')."'>
                                    $message
                                </div>
                            </td>
                        </tr>

                ";
            }
            echo "
                    </table>
                </form>
            ";
            function m() {
                if ($_SERVER['REQUEST_METHOD'] != 'POST') {
                    return;
                }
                if (empty($_POST['login']) && (empty($_POST['password']) || empty($_POST['password-repeat']))) {
                    throw new Exception("incorrect value");
                }
                global $user;
                if (!empty($_POST['login'])) {
                    if ($user->getLogin() != $_POST['login']) {
                        $user->setLogin($_POST['login']);
                        return "Login successfully changed!";
                    }
                }
                if (!empty($_POST['password']) && !empty($_POST['password-repeat'])) { 
                    $encoded_password = sha1($_POST['password']);
        
                    if ($_POST['password'] == $_POST['password-repeat'] && $encoded_password != $user->getPassword()) {
                        $user->setPassword($_POST['password']);
                        return "Password successfully changed!";
                    } else if ($encoded_password === $user->getPassword()) {
                        throw new Exception("current password and new password are the same<br>");
                    } else {
                        throw new Exception("repeated password is not the same<br>");
                    }    
                }
            }
        ?>
    </div>
</body>
</html>