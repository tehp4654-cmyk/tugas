<?php
include "config/koneksi.php";

$id = (int) ($_GET['id'] ?? 0);

$data = mysqli_query($conn, "SELECT * FROM transaksi WHERE id=$id");
$row = mysqli_fetch_assoc($data);

if (!$row) {
    die("Transaksi tidak ditemukan.");
}

$kategori = mysqli_query($conn, "SELECT * FROM kategori");

if (isset($_POST['update'])) {
    $kategori_id = (int) $_POST['kategori_id'];
    $keterangan = $_POST['keterangan'];
    $jumlah = (float) $_POST['jumlah'];
    $tanggal = $_POST['tanggal'];

    $stmt = mysqli_prepare($conn,
        "UPDATE transaksi SET kategori_id=?, keterangan=?, jumlah=?, tanggal=? WHERE id=?"
    );

    mysqli_stmt_bind_param($stmt, "isdsi",
        $kategori_id, $keterangan, $jumlah, $tanggal, $id
    );

    if (mysqli_stmt_execute($stmt)) {
        header("Location: index.php");
        exit;
    }

    echo "Gagal mengubah transaksi.";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Edit Transaksi</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
<div class="card border-0 shadow-sm rounded-4 p-4 mx-auto" style="max-width:550px">

<h4 class="fw-bold mb-4">Edit Transaksi</h4>

<form method="POST">

    <label class="form-label">Kategori</label>
    <select name="kategori_id" class="form-select mb-3" required>
        <?php while($k = mysqli_fetch_assoc($kategori)): ?>
        <option value="<?= $k['id'] ?>" <?= $k['id']==$row['kategori_id']?'selected':'' ?>>
            <?= htmlspecialchars($k['nama_kategori']) ?> (<?= $k['jenis'] ?>)
        </option>
        <?php endwhile; ?>
    </select>

    <label class="form-label">Keterangan</label>
    <input type="text" name="keterangan" class="form-control mb-3"
    value="<?= htmlspecialchars($row['keterangan']) ?>" required>

    <label class="form-label">Jumlah (Rp)</label>
    <input type="number" name="jumlah" class="form-control mb-3"
    value="<?= $row['jumlah'] ?>" min="1" required>

    <label class="form-label">Tanggal</label>
    <input type="date" name="tanggal" class="form-control mb-4"
    value="<?= $row['tanggal'] ?>" required>

    <button name="update" class="btn btn-success w-100">Update Transaksi</button>
    <a href="index.php" class="btn btn-light w-100 mt-2">Kembali</a>

</form>
</div>
</div>
</body>
</html>
