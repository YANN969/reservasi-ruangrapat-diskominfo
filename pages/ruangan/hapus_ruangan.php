<?php
include "config/koneksi.php";

$id_ruangan = $_GET['id'];

$hapus = mysqli_query($koneksi, "DELETE FROM tbruangan WHERE id_ruangan='$id_ruangan'");
if ($hapus) {
    echo "<script>alert('Data ruangan berhasil dihapus'); window.location.href='index.php?page=data_ruangan';</script>";
} else {
    echo "<script>alert('Gagal menghapus data ruangan'); window.location.href='index.php?page=data_ruangan';</script>";
}
?>