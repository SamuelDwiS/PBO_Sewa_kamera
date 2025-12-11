# Dokumentasi Penggunaan Factory di Project

## 🎯 Cara Menggunakan Factory di File Lain

### 1. **Di File Admin (`/admin/`)**
File di folder admin sudah ada koneksi, jadi tinggal gunakan langsung:

```php
<?php
require 'koneksi.php';  // Include koneksi di awal file

// Cara 1: Menggunakan Factory::create()
$barang = Factory::create('barang');
$data = $barang->tampil();

// Cara 2: Menggunakan helper function model() (lebih singkat)
$barang = model('barang');
$data = $barang->tampil();

// Cara 3: Menggunakan database connection
$conn = db();
$result = $conn->query("SELECT * FROM tb_barang");
?>
```

### 2. **Di File Customer Page (`/page/`)**
File di folder page perlu include koneksi dari folder admin:

```php
<?php
require '../admin/koneksi.php';  // Path relatif ke koneksi

// Langsung gunakan Factory
$sewa = Factory::create('sewa');
$myRentals = $sewa->tampil();

// Atau dengan helper function
$sewa = model('sewa');
?>
```

### 3. **Struktur File Include yang Benar**
```
pbo_rental_kamera/
├── admin/
│   ├── koneksi.php          ← Tempat Factory, Database, Interface
│   ├── barang.php           ← require 'koneksi.php'
│   ├── sewa.php             ← require 'koneksi.php'
│   ├── customer.php         ← require 'koneksi.php'
│   └── index.php            ← require 'koneksi.php'
└── page/
    ├── index.php            ← require '../admin/koneksi.php'
    ├── login.php            ← require '../admin/koneksi.php'
    └── daftar_kamera.php    ← require '../admin/koneksi.php'
```

## 📝 Contoh Penggunaan Factory di berbagai skenario

### Contoh 1: Mengambil data di file customer
```php
<?php
require '../admin/koneksi.php';

// Gunakan Factory untuk ambil model Sewa
$sewa = Factory::create('sewa');

// Panggil method dari interface ModelInterface
$allRentals = $sewa->tampil();
$rentalById = $sewa->getById('SWA001');

foreach($allRentals as $rental) {
    echo $rental['id_sewa'];
}
?>
```

### Contoh 2: Tambah data di file admin
```php
<?php
require 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Gunakan helper function model() untuk singkat
    $barang = model('barang');
    
    $data = [
        'nama_barang' => $_POST['nama'],
        'harga_sewa_hari' => $_POST['harga'],
        'stok' => $_POST['stok'],
        'merk' => $_POST['merk'],
        'kategori' => $_POST['kategori'],
        'deskripsi' => $_POST['deskripsi'],
        'gambar' => $_POST['gambar']
    ];
    
    if ($barang->tambah($data)) {
        echo "Data berhasil ditambahkan";
    }
}
?>
```

### Contoh 3: Update data
```php
<?php
require 'koneksi.php';

$barang = Factory::create('barang');

$data = [
    'nama_barang' => $_POST['nama'],
    'harga_sewa_hari' => $_POST['harga'],
    // ... field lainnya
];

$barang->edit('BRNG001', $data);
?>
```

### Contoh 4: Delete data
```php
<?php
require 'koneksi.php';

$barang = model('barang');
$barang->hapus('BRNG001');
?>
```

## 🔧 Fitur Factory yang Bisa Digunakan

### 1. Create/Get Model
```php
$model = Factory::create('barang');  // Buat atau ambil dari cache
```

### 2. Get Model dari Cache (tidak membuat instance baru)
```php
$model = Factory::get('barang');     // null jika belum di-cache
```

### 3. Clear Cache Model
```php
Factory::clearCache('barang');       // Clear cache barang
Factory::clearCache();               // Clear semua cache
```

### 4. Get Cached Models
```php
$cached = Factory::getCachedModels();
// Output: ['barang', 'sewa', 'customer']
```

## ⚠️ Penting!

**Jangan Lupa Include koneksi.php di awal file sebelum menggunakan Factory!**

```php
<?php
require 'koneksi.php';  // ← WAJIB di awal

// Baru bisa gunakan Factory
$barang = Factory::create('barang');
?>
```

## 🎨 Best Practice

1. **Selalu include koneksi.php di awal file**
   ```php
   <?php
   require 'koneksi.php';
   // code lainnya
   ```

2. **Gunakan helper function `model()` untuk lebih singkat**
   ```php
   // Baik
   $barang = model('barang');
   
   // Juga bisa, tapi lebih panjang
   $barang = Factory::create('barang');
   ```

3. **Jangan lupa nama model sesuai dengan nama class (case-sensitive pada class name)**
   ```php
   // Nama file: barang.php, Class: Barang
   model('barang');  // ✅ Benar
   
   // Nama file: customer.php, Class: Customer
   model('customer');  // ✅ Benar
   ```

4. **Gunakan database singleton untuk query custom**
   ```php
   $conn = db();
   $result = $conn->query("SELECT * FROM tb_barang WHERE stok > 0");
   ```
