<?php 
$query = $conn->query("SELECT * FROM tb_barang");
?>

<div class="container mt-4">
  <div class="row row-cols-1 row-cols-md-3 g-4">

  <?php while($k = $query->fetch_assoc()): ?>
    <div class="col">
      <div class="card h-100">
        <img src="uploads/<?= $k['foto'] ?>" class="card-img-top" alt="<?= $k['nama_kamera'] ?>">
        <div class="card-body">
          <h5 class="card-title"><?= $k['nama_kamera'] ?></h5>
          <p class="card-text"><?= $k['deskripsi'] ?></p>
        </div>
        <div class="card-footer">
          <a href="detail.php?id=<?= $k['id_kamera'] ?>" class="btn btn-primary w-100">Detail</a>
        </div>
      </div>
    </div>
  <?php endwhile; ?>

  </div>
</div>
