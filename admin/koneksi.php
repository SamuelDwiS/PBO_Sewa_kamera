<?php
abstract class Database {
    protected $conn;  

    public function __construct() {
        $host = "localhost";
        $user = "root";
        $pass = "";   // 
        $db   = "rental_kamera"; 

        
        $this->conn = new mysqli($host, $user, $pass, $db);

    
        if ($this->conn->connect_error) {
            die("Koneksi gagal: " . $this->conn->connect_error);
        }
    }

    // Getter untuk ambil koneksi
    public function getConnection() {
        return $this->conn;
    }

    abstract public function tampil();
    abstract public function tambah($data);
    abstract public function edit($id,$data);
    abstract public function hapus($id);
    abstract public function getById($id);

}
?>
