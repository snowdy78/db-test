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
            function m($user) {
                if ($_SERVER['REQUEST_METHOD'] != 'POST') {
                    return;
                }
                if (
                    empty($_POST['avatar'])
                    && empty($_POST['login']) 
                    && (empty($_POST['password']) || empty($_POST['password-repeat']))
                ) {
                    throw new Exception("incorrect value");
                }
                if (!empty($_FILES['avatar'])) {
                    try {
                        $user->setAvatar($_FILES['avatar']);
                        return "Avatar successfuly changed!";
                    } catch (IncorrectFile $err) {}
                    catch(Exception $err) {
                        throw $err;
                    }
                }
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
                $success = m($user);
            }
            catch (Exception $err) {} 
            $login = $user->getLogin();
            $email = $user->getEmail();
            $reg_date = $user->getRegistrationDate();
        ?>
        <form method='post' enctype="multipart/form-data">
            <table border=0>
                <tr>
                    <td colspan="2" align="center">
                        <?php
                            $avatar_bytes = $user->getAvatarImageDataUrl();
                            $avatar_width = $user->getAvatarWidth();
                            $avatar_height = $user->getAvatarHeight();
                            $avatar_name = $user->getAvatarFileName();
                            if (!empty($avatar_bytes) && !empty($avatar_width) && !empty($avatar_height) && !empty($avatar_name)) {
                                echo "<img class='avatar' src='$avatar_bytes' style='width: $avatar_width; height: $avatar_height;' alt='$avatar_name' />";
                            } 
                        ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" align="center">
                        <input type="file" name="avatar" id="#user-avatar-upload" />
                    </td>
                </tr>
                <tr>
                    <td>
                        Login:
                    </td>
                    <td>
                        <input class='editable' type='text' name='login' value='<?php echo $login; ?>'>
                    </td>
                </tr>
                <tr>
                    <td>
                        Email:
                    </td>
                    <td>
                        <?php echo $email; ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        Registration Date:     
                    </td>
                    <td>
                        <?php echo $reg_date; ?>
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
<?php            
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
            
?>
            </table>
        </form>

    </div>
</body>
</html>