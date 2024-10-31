<?php
    class Success extends Exception {
        public function __construct(string $message = "") {
            Exception::__construct($message, 0, null);
        }
    }
?>