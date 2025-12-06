<?php

class Barang extends Database
{
    public function tampil()
    {
        $query = $this->conn->query("SELECT * FROM tb_barang");
        $results = [];
        while ($row = $query->fetch_assoc()) {
            $results[] = $row;
        }
        return $results;
    }

    public function uploadFiles()
    {
        $targetDir = "uploads/";
        $fileName = basename($_FILES["foto"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        // Verifikasi Format File
        $allowTypes = array('jpg', 'png', 'jpeg');
        if (in_array($fileType, $allowTypes)) {
            if (move_uploaded_file($_FILES["foto"]["tmp_name"], $targetFilePath)) {
                return $fileName;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }


    public function tambah($data) {}
    public function edit($id, $data) {}
    public function hapus($id) {}
    public function getById($id) {}
}
