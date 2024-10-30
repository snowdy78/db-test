<?php
    class DataBase extends mysqli {
        public function __construct()
        {
            mysqli::__construct("localhost", "root", "", "test_x", 3306);
        }
        public function getUserBy($key, $value, $columns = "*") {
            $request = $this->query("SELECT $columns FROM `users` WHERE $key='$value'");
            if (empty($request)) {
                throw new Exception("User not found");
            }
            $user = $request->fetch_assoc();
            if (empty($user)) {
                throw new Exception("User not found");    
            }
            return new User($this, $user["id"]);
        }
        public function addUser(string $login, string $email, string $password) {
            $table_name = User::$table_name;
            $date = getdate();
            $sql_date = $date["mday"]."-".$date["mon"]."-".$date["year"];
            $query = 
                "INSERT INTO 
                $table_name (id, login, email, password, reg_date) 
                VALUES (DEFAULT, '$login', '$email', SHA1('$password'), STR_TO_DATE('$sql_date', '%d-%m-%Y'))";
            $request = $this->query($query);
            if (empty($request)) {
                throw new Exception("Cannot authorise user");
            }
        }
    }
?>