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
    <form class='search-form' method="post" action="">
        <input type='text' name='login' placeholder='Login...'/>
        <input type="text" name="email" placeholder="Email..."/>
        <input type="date" name="date" placeholder="Register Date..."/>
        <button type="submit">Search</button>
    </form>

    <?php 
        include_once "php/database.php";
        $db = new DataBase();
        $keys = [];
        $values = [];
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $login = empty($_POST["login"]) ? null : '%'.$_POST["login"].'%';
            $email = empty($_POST["email"]) ? null : '%'.$_POST['email'].'%';
            $date = empty($_POST["date"]) ? null : getdate(strtotime($_POST["date"]));
            $str_date = null;
            if (!empty($date)) {
                $str_date = $date["mday"]."-".$date["mon"]."-".$date["year"];
            }
            $k = ['login', 'email', 'reg_date'];
            $v = [$login, $email, $str_date];
            for ($i = 0; $i < min(sizeof($k), sizeof($v)); $i++) {
                if (!empty($v[$i])) {
                    $keys[] = $k[$i];
                    $values[] = $v[$i];
                }
            }
        }
        try {
            $users = $db->getAllUsersBy($keys, $values, 'LIKE');
        } catch (Exception $err) {
            echo $err->getMessage();
            return;
        }
        echo "<table class='users'>";
        foreach ($users as $user) {
            $id = $user->getId();
            $login = $user->getLogin();
            $email = $user->getEmail();
            $reg_date = $user->getRegistrationDate();
            echo "<tr>";
            echo "<td>$id</td>";
            echo "<td>$login</td>";
            echo "<td>$email</td>";
            echo "<td>$reg_date</td>";
            echo "<td><a href='./profile.php?user=$id'>edit</a></td>";
            echo "<td><a href='./php/remove.php?user=$id'>remove</a></td>";
            echo "</tr>";
        }
        echo "</table>";
    ?>
</body>
</html>