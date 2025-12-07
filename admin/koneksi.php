<?php
session_start();

class Database {
    private static $instance = null;
    private $conn;

    private function __construct() {
        $host = "localhost";
        $user = "root";
        $pass = "";
        $db = "rental_kamera";

        $this->conn = new mysqli($host, $user, $pass, $db);

        if ($this->conn->connect_error) {
            die("Koneksi gagal: " . $this->conn->connect_error);
        }

        // Set charset to UTF-8
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

class Factory {
    private static $models = [];

    /**
     * Create atau ambil model instance
     * @param string $modelName Nama model (contoh: 'customer', 'barang', 'sewa')
     * @return object Instance model
     */
    public static function create($modelName) {
        // Return dari cache jika sudah ada
        if (isset(self::$models[$modelName])) {
            return self::$models[$modelName];
        }

        // Include file model - support folder model/
        $modelFile = __DIR__ . '/model/' . strtolower($modelName) . '.php';
        
        // Fallback ke root folder jika tidak ada di model/
        if (!file_exists($modelFile)) {
            $modelFile = __DIR__ . '/' . strtolower($modelName) . '.php';
        }
        
        if (!file_exists($modelFile)) {
            throw new Exception("Model file untuk '{$modelName}' tidak ditemukan");
        }

        require_once $modelFile;
        $className = ucfirst($modelName);

        // Cek apakah class sudah ter-load
        if (!class_exists($className)) {
            throw new Exception("Class '{$className}' tidak ditemukan");
        }

        // Buat instance dan cache
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

function model($modelName) {
    return Factory::create($modelName);
}
function db() {
    return Database::getInstance()->getConnection();
}
?>