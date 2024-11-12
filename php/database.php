<?php
    class IncorrectFile extends Exception {}
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
        public function setAvatar(mixed $file) {
            if (empty($file["tmp_name"])) {
                throw new IncorrectFile("File wasn't choosen");
            }
            $size = getimagesize($file["tmp_name"]);
            $clear_file_type = str_replace("image/", "", $size['mime']);
            $filename = $this->getLogin()."_".$this->getRegistrationDate('-').".$clear_file_type";
            $sizex = $size[0];
            $sizey = $size[1];
            if (!($size[0] == 800 && $size[1] == 800 || $size[0] == 100 || $size == 100)) {
                throw new Exception("File size '$sizex"."x"."$sizey' is too much");
            }
            $encoded_file = 'data:'.$size['mime'].';base64,'.base64_encode(file_get_contents($file['tmp_name']));

            $table_name = self::$table_name;
            $this->database->query("UPDATE $table_name 
                SET enc_image='$encoded_file', image_width=$sizex, image_height=$sizey, image_name='$filename' 
                WHERE id=$this->id");
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
        public function getAvatarImageDataUrl(): string | null {
            $val = htmlspecialchars($this->get('enc_image'));
            $val = trim($val);
            return $val;
        }
        public function getAvatarWidth(): int | null {
            return $this->get('image_width');
        }
        public function getAvatarHeight(): int | null {
            return $this->get('image_height');
        }
        public function getAvatarFileName(): string | null {
            return $this->get('image_name');
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
        public function getRegistrationDate($sep = '.'): mixed {
            $sql_date = $this->get("reg_date");
            $reg_date = date("d$sep"."m$sep"."Y", strtotime($sql_date));
            return $reg_date;
        }
    }
    class DataBase extends mysqli {
        public function __construct()
        {
            mysqli::__construct("localhost", "root", "", "obvp", 3306);
        }
        public function getUserBy($key, $value) {
            $request = $this->query("SELECT * FROM `users` WHERE $key='$value'");
            if (empty($request)) {
                throw new Exception("User not found");
            }
            $user = $request->fetch_assoc();
            if (empty($user)) {
                throw new Exception("User not found");    
            }
            return new User($this, $user["id"]);
        }
        public function getAllUsersBy($condition = null, $columns = "*") {
            $request = $this->query("SELECT * FROM `users` ".(empty($condition) ? "" : "WHERE $condition"));
            if (empty($request)) {
                throw new Exception("Error request");
            }
            $users = $request->fetch_all(MYSQLI_ASSOC);
            if (empty($users)) {
                throw new Exception("User not found");    
            }
            $user_arr = array();
            $i = 0;
            foreach ($users as $user) {
                $user_arr[$i] = new User($this, $user['id']);
                $i++;
            }
            return $user_arr;
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
        public function removeUser($id) {
            $query = "DELETE FROM `users` WHERE id=$id";
            $this->query($query);
        }
    }
?>