
<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once '../admin/koneksi.php';
$cart = $_SESSION['cart'] ?? [];
?>

<div class="card">
    <div class="card-header">
        <h5>Keranjang Anda</h5>
    </div>
    <div class="card-body">
        <?php if (empty($cart)): ?>
            <div class="alert alert-info">Keranjang kosong</div>
        <?php else: ?>
            <form method="POST" action="proses_sewa.php" id="cartForm">
                <table class="table">
                    <thead><tr><th>Nama</th><th>Harga/hari</th><th>Jumlah</th><th>Subtotal</th><th>Aksi</th></tr></thead>
                    <tbody id="cartTableBody">
                        <?php foreach ($cart as $c): ?>
                            <tr data-id="<?php echo $c['id_barang']; ?>">
                                <td><?php echo htmlspecialchars($c['nama']); ?></td>
                                <td>Rp <?php echo number_format($c['harga_sewa_hari'],0,',','.'); ?>
                                    <input type="hidden" name="harga_per_hari[]" value="<?php echo $c['harga_sewa_hari']; ?>">
                                </td>
                                <td style="width:140px;">
                                    <input type="number" name="jumlah[]" value="<?php echo $c['jumlah']; ?>" min="1" class="form-control cart-qty" required>
                                    <input type="hidden" name="id_barang[]" value="<?php echo $c['id_barang']; ?>">
                                </td>
                                <td class="text-end">
                                    <span class="subtotal-text">-</span>
                                    <input type="hidden" name="subtotal[]" class="subtotal-input" value="0">
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-danger" type="button" onclick="removeItem('<?php echo $c['id_barang']; ?>')">Hapus</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Tanggal Mulai</label>
                        <input type="date" name="tgl_sewa" id="tgl_sewa" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label>Tanggal Kembali Estimasi</label>
                        <input type="date" name="tgl_kembali_est" id="tgl_kembali_est" class="form-control" required>
                    </div>
                </div>

                <div class="mt-3">
                    <label>Catatan</label>
                    <textarea name="catatan" class="form-control"></textarea>
                </div>

                <div class="mt-3 d-flex justify-content-between align-items-center">
                    <div>
                        <button type="button" class="btn btn-outline-danger" onclick="clearCart()">Kosongkan Keranjang</button>
                    </div>
                    <div class="text-end">
                        <div class="mb-2">Total Estimasi: <strong id="cartTotalDisplay">Rp 0</strong></div>
                        <input type="hidden" name="total_harga" id="total_harga_input" value="0">
                        <button type="submit" class="btn btn-success">Checkout & Buat Sewa</button>
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<script>
function removeItem(id) {
    fetch('cart_action.php', {
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body: new URLSearchParams({action:'remove', id_barang: id})
    }).then(r=>r.json()).then(res=>{
        if (res.success) location.reload();
    });
}

function clearCart() {
    if (!confirm('Kosongkan seluruh keranjang?')) return;
    fetch('cart_action.php', {
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body: new URLSearchParams({action:'clear'})
    }).then(r=>r.json()).then(res=>{
        if (res.success) location.reload();
    });
}

function parseNumber(v){ return parseInt(v) || 0; }

function calculateCartTotals(){
    const tglMulai = new Date(document.getElementById('tgl_sewa').value);
    const tglKembali = new Date(document.getElementById('tgl_kembali_est').value);
    let hari = 1;
    if (!isNaN(tglMulai) && !isNaN(tglKembali) && tglKembali >= tglMulai) {
        hari = Math.floor((tglKembali - tglMulai) / (1000*60*60*24)) + 1;
        if (hari < 1) hari = 1;
    }

    let total = 0;
    const rows = document.querySelectorAll('#cartTableBody tr');
    rows.forEach(r=>{
        const harga = parseNumber(r.querySelector('input[name="harga_per_hari[]"]').value);
        const qty = parseNumber(r.querySelector('input[name="jumlah[]"]').value);
        const subtotal = harga * hari * qty;
        r.querySelector('.subtotal-text').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(subtotal);
        r.querySelector('.subtotal-input').value = subtotal;
        total += subtotal;
    });

    document.getElementById('cartTotalDisplay').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
    document.getElementById('total_harga_input').value = total;
}

document.addEventListener('DOMContentLoaded', function(){
    // attach listeners to qty inputs and date inputs
    document.querySelectorAll('.cart-qty').forEach(inp=> inp.addEventListener('change', calculateCartTotals));
    const d1 = document.getElementById('tgl_sewa');
    const d2 = document.getElementById('tgl_kembali_est');
    if (d1) d1.addEventListener('change', calculateCartTotals);
    if (d2) d2.addEventListener('change', calculateCartTotals);
    // initial calc
    calculateCartTotals();
});
</script>

<?php
?>
