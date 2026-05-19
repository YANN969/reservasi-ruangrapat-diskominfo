<?php
// PASTIKAN session_start() HANYA SEKALI di awal
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'koneksi.php';

// Debug: cek session
error_log("Page.php - Session user_id: " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'NOT SET'));

// Redirect ke login jika belum login
if (!isset($_SESSION['user_id'])) {
    error_log("Redirecting to login - no user_id in session");
    header("Location: login.php");
    exit();
}

$base_path = '/kominfo-reservasi';

// Include header
include 'header.php';

// Routing content
if (isset($_GET['page'])) {
    $page = $_GET['page'];

    switch ($page) {
        case '':
        case 'dashboard':
            include "dashboard.php";
            break;

        case 'edit_profile':
            include "pages/profile/edit_profile.php";
            break;

   case 'data_ruangan':
    include "pages/ruangan/data_ruangan.php";
    break;

case 'tambah_ruangan':
    include "pages/ruangan/tambah_ruangan.php";
    break;

case 'ubah_ruangan':
    include "pages/ruangan/ubah_ruangan.php";
    break;

case 'hapus_ruangan':
    include "pages/ruangan/hapus_ruangan.php";
    break;

case 'data_booking':
    include "pages/booking/data_booking.php";
    break;

case 'tambah_booking':
    include "pages/booking/tambah_booking.php";
    break;

case 'ubah_booking':
    include "pages/booking/ubah_booking.php";
    break;

case 'hapus_booking':
    include "pages/booking/hapus_booking.php";
    break;

        default:
            include "pages/dashboard.php";
            break;
    }
} else {
    include "dashboard.php";
}

// Include footer penutup
echo '</main>';
echo '</body>';
echo '</html>';