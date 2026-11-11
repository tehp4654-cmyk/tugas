<?php
include "config/koneksi.php";

$kategori = mysqli_query($conn, "SELECT * FROM kategori");

if (isset($_POST['simpan'])) {
    $kategori_id = (int) $_POST['kategori_id'];
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
    $jumlah = (float) $_POST['jumlah'];
    $tanggal = $_POST['tanggal'];

    $query = mysqli_prepare($conn,
        "INSERT INTO transaksi (kategori_id,keterangan,jumlah,tanggal)
         VALUES (?, ?, ?, ?)"
    );

    mysqli_stmt_bind_param($query, "isds",
        $kategori_id, $keterangan, $jumlah, $tanggal
    );

    if (mysqli_stmt_execute($query)) {
        header("Location: index.php");
        exit;
    }

    echo "Gagal menyimpan transaksi.";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Tambah Transaksi</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
<div class="card border-0 shadow-sm rounded-4 p-4 mx-auto" style="max-width:550px">

<h4 class="fw-bold mb-4">Tambah Transaksi</h4>

<form method="POST">

    <label class="form-label">Kategori</label>
    <select name="kategori_id" class="form-select mb-3" required>
        <option value="">Pilih kategori</option>
        <?php while($k = mysqli_fetch_assoc($kategori)): ?>
        <option value="<?= $k['id'] ?>">
            <?= htmlspecialchars($k['nama_kategori']) ?> (<?= $k['jenis'] ?>)
        </option>
        <?php endwhile; ?>
    </select>

    <label class="form-label">Keterangan</label>
    <input type="text" name="keterangan" class="form-control mb-3" required>

    <label class="form-label">Jumlah (Rp)</label>
    <input type="number" name="jumlah" class="form-control mb-3" min="1" required>

    <label class="form-label">Tanggal</label>
    <input type="date" name="tanggal" class="form-control mb-4" value="<?= date('Y-m-d') ?>" required>

    <button name="simpan" class="btn btn-success w-100">Simpan Transaksi</button>
    <a href="index.php" class="btn btn-light w-100 mt-2">Kembali</a>

</form>
</div>
</div>
</body>
</html>
