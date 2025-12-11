<?php

// =====================================
// CEGAH SESSION_START DUPLIKAT
// =====================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// =====================================
// CLASS DATABASE
// =====================================
if (!class_exists('Database')) {
    class Database {
        private static $instance = null;
        private $conn;

        private function __construct() {
            $host = "localhost";
            $user = "root";
            $pass = "";
            $db = "sewa_kamera";

            $this->conn = new mysqli($host, $user, $pass, $db);

            if ($this->conn->connect_error) {
                die("Koneksi gagal: " . $this->conn->connect_error);
            }

            $this->conn->set_charset("utf8");
        }

        private function __clone() {}

        public static function getInstance() {
            if (self::$instance === null) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function getConnection() {
            return $this->conn;
        }
    }
}

// =====================================
// CLASS FACTORY
// =====================================
if (!class_exists('Factory')) {
    class Factory {
        private static $models = [];

        public static function create($modelName) {
            if (isset(self::$models[$modelName])) {
                return self::$models[$modelName];
            }

            $adminDir = __DIR__;
            $modelFile = $adminDir . '/model/' . strtolower($modelName) . '.php';

            if (!file_exists($modelFile)) {
                $modelFile = $adminDir . '/' . strtolower($modelName) . '.php';
            }

            if (!file_exists($modelFile)) {
                throw new Exception("Model '{$modelName}' tidak ditemukan: {$modelFile}");
            }

            require_once $modelFile;

            $className = ucfirst($modelName);
            if (!class_exists($className)) {
                throw new Exception("Class '{$className}' tidak ditemukan");
            }

            self::$models[$modelName] = new $className();
            return self::$models[$modelName];
        }

        public static function get($modelName) {
            return self::$models[$modelName] ?? null;
        }

        public static function clearCache($modelName = null) {
            if ($modelName === null) {
                self::$models = [];
            } else {
                unset(self::$models[$modelName]);
            }
        }
    }
}

// =====================================
// HELPER FUNCTIONS
// =====================================
if (!function_exists('model')) {
    function model($modelName) {
        return Factory::create($modelName);
    }
}

if (!function_exists('db')) {
    function db() {
        return Database::getInstance()->getConnection();
    }
}

?>
