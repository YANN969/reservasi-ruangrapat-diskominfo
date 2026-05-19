<?php
include "config/koneksi.php";

$id_booking = $_GET['id'];

$hapus = mysqli_query($koneksi, "DELETE FROM tbbooking WHERE id_booking='$id_booking'");
if ($hapus) {
    echo "<script>alert('Data booking berhasil dihapus'); window.location.href='index.php?page=data_booking';</script>";
} else {
    echo "<script>alert('Gagal menghapus data booking'); window.location.href='index.php?page=data_booking';</script>";
}
?>