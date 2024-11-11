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
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $login = $_POST["login"];
            $login_condition = empty($login) ? "" : "login LIKE '$login%'";
            $email = $_POST["email"];
            $email_condition = empty($email) ? "" : "email LIKE '$email%'";
            $date_condition = "";
            if (!empty($_POST["date"])) {
                $date = getdate(strtotime($_POST["date"]));
                $str_date = $date["mday"].".".$date["mon"].".".$date["year"];
                $date_condition = "reg_date=STR_TO_DATE('$str_date', '%d.%m.%Y')";
            }
            $search_condition = "";
            $conditions = array($login_condition, $email_condition, $date_condition);
            $last_exist = false;
            for ($i = 0; $i < sizeof($conditions); $i++) {
                if (!empty($conditions[$i])) {
                    $search_condition = $search_condition.($last_exist ? " and " : "").$conditions[$i];
                    $last_exist = true;
                } else {
                    $last_exist = false;
                }
            }
        }
        try {
            $users = $db->getAllUsersBy($search_condition ?? null);
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