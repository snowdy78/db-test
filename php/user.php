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
?>