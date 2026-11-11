
<?php
require_once __DIR__ . "/config/koneksi.php";

$bulan = $_GET['bulan'] ?? date('Y-m');

if (!preg_match('/^\d{4}-\d{2}$/', $bulan)) {
    $bulan = date('Y-m');
}

$masuk = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT SUM(t.jumlah) AS total FROM transaksi t
    JOIN kategori k ON t.kategori_id=k.id
    WHERE k.jenis='pemasukan' AND DATE_FORMAT(t.tanggal,'%Y-%m')='$bulan'"
))['total'] ?? 0;

$keluar = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT SUM(t.jumlah) AS total FROM transaksi t
    JOIN kategori k ON t.kategori_id=k.id
    WHERE k.jenis='pengeluaran' AND DATE_FORMAT(t.tanggal,'%Y-%m')='$bulan'"
))['total'] ?? 0;

$saldo = $masuk - $keluar;

$data = mysqli_query($conn,
    "SELECT t.*, k.nama_kategori, k.jenis
    FROM transaksi t JOIN kategori k ON t.kategori_id=k.id
    WHERE DATE_FORMAT(t.tanggal,'%Y-%m')='$bulan'
    ORDER BY t.tanggal DESC, t.id DESC"
);

function rupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Keuangan Keluarga</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h3 class="fw-bold mb-1">Keuangan Keluarga</h3>
            <p class="text-secondary mb-0">Kelola keuangan keluarga dengan mudah.</p>
        </div>
        <a href="tambah.php" class="btn btn-success">+ Tambah Transaksi</a>
    </div>

    <form class="mb-4">
        <input type="month" name="bulan" value="<?= htmlspecialchars($bulan) ?>" class="form-control" style="max-width:220px" onchange="this.form.submit()">
    </form>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card-box">
                <p>Total Saldo</p>
                <h3><?= rupiah($saldo) ?></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-box">
                <p>Total Pemasukan</p>
                <h3 class="text-success"><?= rupiah($masuk) ?></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-box">
                <p>Total Pengeluaran</p>
                <h3 class="text-danger"><?= rupiah($keluar) ?></h3>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 p-3">
        <h5 class="fw-bold mb-3">Riwayat Transaksi</h5>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Keterangan</th>
                        <th>Kategori</th>
                        <th>Jenis</th>
                        <th>Jumlah</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php while($row = mysqli_fetch_assoc($data)): ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                        <td><?= htmlspecialchars($row['keterangan']) ?></td>
                        <td><?= htmlspecialchars($row['nama_kategori']) ?></td>
                        <td>
                            <span class="badge <?= $row['jenis']=='pemasukan'?'bg-success':'bg-danger' ?>">
                                <?= ucfirst($row['jenis']) ?>
                            </span>
                        </td>
                        <td><?= rupiah($row['jumlah']) ?></td>
                        <td>
                            <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="hapus.php?id=<?= $row['id'] ?>" onclick="return confirm('Hapus transaksi?')" class="btn btn-sm btn-danger">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
</body>
</html>
