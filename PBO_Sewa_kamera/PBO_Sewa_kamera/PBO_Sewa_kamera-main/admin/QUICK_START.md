# Simple Design Patterns dengan Interface - Quick Start

## 📁 Struktur

```
admin/
├── koneksi.php    # Interface + Singleton Database + Factory
└── sewa.php       # Contoh Class yang implement Interface
```

## 🎯 3 Patterns di koneksi.php

### 1. Interface - ModelInterface
Standardisasi semua model harus punya method:
```php
interface ModelInterface {
    public function tampil();
    public function getById($id);
    public function tambah($data);
    public function edit($id, $data);
    public function hapus($id);
}
```

### 2. Singleton - Database
```php
$db = Database::getInstance();
$conn = $db->getConnection();
```
- Hanya 1 koneksi untuk seluruh aplikasi

### 3. Factory - Models
```php
$sewa = Factory::create('sewa');
```
- Buat model dengan mudah
- Auto caching

## 📝 Template Model (Lihat sewa.php)

Class mengimplementasikan ModelInterface:
```php
class Sewa implements ModelInterface {
    public function tampil() { ... }
    public function getById($id) { ... }
    public function tambah($data) { ... }
    public function edit($id, $data) { ... }
    public function hapus($id) { ... }
}
```

## 🚀 Cara Buat Model Baru

1. Copy struktur dari sewa.php
2. Ubah nama class ke nama model baru (misal `class Barang implements ModelInterface`)
3. Ubah `$table` dan `$primaryKey`
4. Sesuaikan method dengan kolom tabel
5. Update `generateId()` untuk format ID yang berbeda

**Contoh untuk Barang:**
```php
class Barang implements ModelInterface {
    private $table = 'tb_barang';
    private $primaryKey = 'id_barang';
    // ... implement semua method dari interface
}
```

## ✨ Auto ID Generation

Setiap model auto generate ID dengan format:
- Sewa: SWA001, SWA002, dst
- Barang: BRNG001, BRNG002, dst (tinggal ubah di generateId)

## 💡 Keuntungan Interface

✅ Semua model punya method yang sama  
✅ Konsistensi code  
✅ Mudah maintenance  
✅ Type safety  

Selesai! 🎉

