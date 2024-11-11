<?php
    class User {
        public static string $table_name = "`users`";
        private mysqli $database;
        private int $id;
        private function get($key) {
            $table_name = self::$table_name;
            $users = $this->database->query("SELECT $key FROM $table_name WHERE id=$this->id");
            return $users->fetch_assoc()[$key];
        }
        public function __construct(mysqli $db, int $id) {
            $this->database = $db;
            $this->id = $id;
        }
        public function setLogin(string $login) {
            $table_name = self::$table_name;
            $this->database->query("UPDATE $table_name SET login='$login' WHERE id=$this->id");
        }
        public function setEmail(string $email) {
            $table_name = self::$table_name;
            $this->database->query("UPDATE $table_name SET email='$email' WHERE id=$this->id");
        }
        public function setPassword(string $password) {
            $table_name = self::$table_name;
            $sha1_password = sha1($password);
            $this->database->query("UPDATE $table_name SET password='$sha1_password' WHERE id=$this->id");
        }
        public function getId(): int {
            return $this->id;
        }
        public function getLogin(): mixed {
            return $this->get("login");
        }
        public function getPassword(): mixed {
            return $this->get("password");
        }
        public function getEmail(): mixed {
            return $this->get("email");
        }
        public function getRegistrationDate(): mixed {
            $sql_date = $this->get("reg_date");
            $reg_date = date('d.m.Y', strtotime($sql_date));
            return $reg_date;
        }
    }
    class DataBase extends mysqli {
        public function __construct()
        {
            mysqli::__construct("localhost", "root", "", "obvp", 3306);
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