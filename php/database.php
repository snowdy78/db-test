<?php
    class IncorrectFile extends Exception {}
    enum UserAccessLevel {
        case User; 
        case Admin;
    }
    class User {
        public static string $table_name = "users";
        private DataBase $database;
        private int $id;
        
        private function get($key) {
            $table_name = self::$table_name;
            $stmt = $this->database->prepare("SELECT * FROM $table_name WHERE id=:id");
            $stmt->execute(['id' => $this->id]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (empty($rows)) {
                return null;
            }
            return $rows[0][$key];
        }
        private function set($operations) {
            $table_name = self::$table_name;
            $this->database->query("UPDATE $table_name SET $operations WHERE id=$this->id");
        }
        public function __construct(DataBase $db, int $id) {
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

            $this->set("enc_image='$encoded_file', image_width=$sizex, image_height=$sizey, image_name='$filename'");
        }
        public function setLogin(string $login) {
            $this->set("login='$login'");
        }
        public function setEmail(string $email) {
            $this->set("email='$email'");
        }
        public function setPassword(string $password) {
            $sha1_password = sha1($password);
            $this->set("password='$sha1_password'");
        }
        public function getAvatarImageDataUrl(): string | null {
            $val = htmlspecialchars($this->get('enc_image'));
            $val = trim($val);
            return $val;
        }
        public function setAccessLevel(UserAccessLevel $level) {
            if ($level == UserAccessLevel::User) {
                $this->set("access_level='user'");
            }
            else if ($level == UserAccessLevel::Admin) {
                $this->set("access_level='admin'");
            }
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
        public function getAccessLevel(): string {
            return $this->get('access_level');
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
    class DataBase extends \PDO {
        public function __construct()
        {
            try {
                \PDO::__construct("sqlite:obvp.db");

                $this->exec("CREATE TABLE IF NOT EXISTS users (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    login TEXT NOT NULL,
                    password TEXT NOT NULL,
                    email TEXT NOT NULL,
                    reg_date DATE NOT NULL,
                    access_level TEXT CHECK(access_level IN ('user', 'admin')) NOT NULL DEFAULT('user'),
                    enc_image TEXT DEFAULT(NULL),
                    image_width INTEGER DEFAULT(NULL),
                    image_height INTEGER DEFAULT(NULL),
                    image_name TEXT DEFAULT(NULL)
                )");
                $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (Exception $err) {
                echo $err->getMessage();
            }
        }
        private function executeQuery(string $query, array $keys = [], array $values = [], string $operation = '=') {
            if (!empty($keys)) {
                $query .= " WHERE ";
            }
            $i = 0;
            foreach ($keys as $key) {
                $value = '?';
                if ($key === 'reg_date') {
                    
                    $query .= "reg_date = ?";
                }
                else 
                    $query .= "$key $operation $value";
                if ($i != sizeof($keys) - 1) {
                    $query .= " AND ";
                }
                $i++;
            }
            $request = $this->prepare($query);
            if (empty($request)) {
                throw new Exception("User not found");
            }
            $request->execute($values);
            return $request;
        }
        public function getUserBy(array $keys, array $values, $operation = '=') {
            $request = $this->executeQuery("SELECT * FROM users", $keys, $values, $operation);
            $rows = $request->fetchAll(PDO::FETCH_ASSOC);
            if (empty($rows)) {
                throw new Exception("User not found");
            }
            $user = $rows[0];
            return new User($this, $user["id"]);
        }
        public function getAllUsersBy(array $keys, array $values, $operation = '=') {
            $request = $this->executeQuery("SELECT * FROM users", $keys, $values, $operation);
            $users = $request->fetchAll();
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
            try {
                $user = $this->getUserBy(['login', 'email'], [$login, $email]);
                throw new Exception('User is already defined');
            } catch (Exception $err) {}
            $sha1_password = sha1($password);

            $table_name = User::$table_name;
            $date = getdate();
            $str_date = $date["mday"]."-".$date["mon"]."-".$date["year"];
            var_dump($str_date);
            $query = 
                "INSERT INTO 
                $table_name (login, email, password, reg_date) 
                VALUES (:login, :email, :sha1_password, :reg_date)";
            $request = $this->prepare($query);
            return $request->execute(['login' => $login, 'email' => $email, 'sha1_password' => $sha1_password, "reg_date" => $str_date]);
        }
        public function removeUser($id) {
            $this->beginTransaction();
            $table_name = User::$table_name;
            $request = $this->prepare("DELETE FROM $table_name WHERE id=:id");
            $iid = intval($id);
            $request->bindParam(':id', $iid, PDO::PARAM_INT);
            return;
        }
    }
?>