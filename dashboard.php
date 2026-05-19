<?php
include 'config/koneksi.php';

// Cek session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Query untuk statistik dashboard
$total_booking = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tbbooking");
$total_booking = mysqli_fetch_assoc($total_booking)['total'];

// Booking hari ini dari tbruangan yang statusnya 'Sedang Dipakai' atau 'Sudah Dibooking'
$booking_hari_ini = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tbruangan WHERE status IN ('Sedang Dipakai', 'Sudah Dibooking')");
$booking_hari_ini = mysqli_fetch_assoc($booking_hari_ini)['total'];

$ruangan_tersedia = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tbruangan WHERE status = 'Kosong'");
$ruangan_tersedia = mysqli_fetch_assoc($ruangan_tersedia)['total'];

$total_ruangan = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tbruangan");
$total_ruangan = mysqli_fetch_assoc($total_ruangan)['total'];

// PERBAIKAN: Rapat Aktif sekarang dihitung dari tbruangan yang statusnya 'Sedang Dipakai'
$rapat_aktif = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tbruangan WHERE status = 'Sedang Dipakai'");
$rapat_aktif = mysqli_fetch_assoc($rapat_aktif)['total'];

// PERBAIKAN: Hindari division by zero
$persen_tersedia = 0;
if ($total_ruangan > 0) {
    $persen_tersedia = round(($ruangan_tersedia / $total_ruangan) * 100);
}

// Hitung detail ruangan untuk informasi
$ruangan_dipakai = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tbruangan WHERE status = 'Sedang Dipakai'");
$ruangan_dipakai = mysqli_fetch_assoc($ruangan_dipakai)['total'];

$ruangan_dibooking = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tbruangan WHERE status = 'Sudah Dibooking'");
$ruangan_dibooking = mysqli_fetch_assoc($ruangan_dibooking)['total'];

// Data untuk chart (booking 7 hari terakhir)
$query_chart = "SELECT tanggal, COUNT(*) as jumlah 
                FROM tbbooking 
                WHERE tanggal >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) 
                GROUP BY tanggal 
                ORDER BY tanggal";
$result_chart = mysqli_query($koneksi, $query_chart);

$chart_labels = [];
$chart_data = [];

while ($row = mysqli_fetch_assoc($result_chart)) {
    $chart_labels[] = date('d M', strtotime($row['tanggal']));
    $chart_data[] = $row['jumlah'];
}

// PERBAIKAN: Booking aktif hari ini dihitung dari ruangan yang statusnya 'Sedang Dipakai'
$query_booking_aktif = "SELECT b.*, r.nama_ruangan, r.lantai, r.status as status_ruangan
                       FROM tbbooking b 
                       JOIN tbruangan r ON b.id_ruangan = r.id_ruangan 
                       WHERE b.tanggal = CURDATE() 
                       AND r.status = 'Sedang Dipakai'
                       AND b.status IN ('Sedang Rapat', 'Booking')
                       ORDER BY b.jam_rapat ASC 
                       LIMIT 5";
$booking_aktif = mysqli_query($koneksi, $query_booking_aktif);

// PERBAIKAN: Query untuk menampilkan semua booking hari ini 
$query_semua_booking_hari_ini = "SELECT b.*, r.nama_ruangan, r.lantai, r.status as status_ruangan
                                 FROM tbbooking b 
                                 JOIN tbruangan r ON b.id_ruangan = r.id_ruangan 
                                 WHERE b.tanggal = CURDATE() 
                                 AND b.status IN ('Booking', 'Sedang Rapat', 'Selesai')
                                 ORDER BY 
                                     CASE r.status 
                                         WHEN 'Sedang Dipakai' THEN 1
                                         WHEN 'Sudah Dibooking' THEN 2
                                         ELSE 3
                                     END,
                                     b.jam_rapat ASC 
                                 LIMIT 5";
$semua_booking_hari_ini = mysqli_query($koneksi, $query_semua_booking_hari_ini);

// Query untuk menampilkan ruangan yang sedang dipakai/dibooking
$query_ruangan_digunakan = "SELECT * FROM tbruangan 
                            WHERE status IN ('Sedang Dipakai', 'Sudah Dibooking')
                            ORDER BY 
                                CASE status 
                                    WHEN 'Sedang Dipakai' THEN 1
                                    WHEN 'Sudah Dibooking' THEN 2
                                END,
                                nama_ruangan ASC";
$ruangan_digunakan = mysqli_query($koneksi, $query_ruangan_digunakan);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistem Reservasi Ruang Rapat</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
    /* Reset dasar untuk mencegah zoom */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        overflow-x: hidden;
        background: #f8f9fa;
    }

    /* MAIN CONTENT STYLES */
    .main-content {
        margin-left: 220px;
        margin-top: 65px;
        padding: 20px;
        min-height: calc(100vh - 65px);
        background: #f8f9fa;
        width: calc(100% - 220px);
    }

    .content-wrapper {
        max-width: 1400px;
        margin: 0 auto;
        width: 100%;
    }

    .page-header {
        margin-bottom: 30px;
        text-align: center;
        background: white;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.08);
    }

    .welcome-section h1 {
        color: #2c3e50;
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .welcome-section p {
        color: #7f8c8d;
        font-size: 16px;
        margin: 0;
    }

    /* STATS CARDS */
    .stats-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        border: 1px solid #e9ecef;
        position: relative;
        overflow: hidden;
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
    }

    .stat-card:nth-child(1)::before { background: linear-gradient(90deg, #3498db, #2980b9); }
    .stat-card:nth-child(2)::before { background: linear-gradient(90deg, #e74c3c, #c0392b); }
    .stat-card:nth-child(3)::before { background: linear-gradient(90deg, #2ecc71, #27ae60); }
    .stat-card:nth-child(4)::before { background: linear-gradient(90deg, #f39c12, #e67e22); }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 15px;
        font-size: 20px;
        color: white;
    }

    .stat-icon.total-booking { background: linear-gradient(135deg, #3498db, #2980b9); }
    .stat-icon.today-booking { background: linear-gradient(135deg, #e74c3c, #c0392b); }
    .stat-icon.available-rooms { background: linear-gradient(135deg, #2ecc71, #27ae60); }
    .stat-icon.active-meetings { background: linear-gradient(135deg, #f39c12, #e67e22); }

    .stat-content h3 {
        color: #2c3e50;
        font-size: 28px;
        font-weight: 800;
        margin-bottom: 5px;
    }

    .stat-content p {
        color: #7f8c8d;
        font-size: 14px;
        margin-bottom: 10px;
        font-weight: 600;
    }

    .stat-trend {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 12px;
        font-weight: 600;
        padding: 4px 8px;
        border-radius: 20px;
    }

    .stat-trend.up {
        background: rgba(46, 204, 113, 0.1);
        color: #27ae60;
    }

    .stat-trend.down {
        background: rgba(231, 76, 60, 0.1);
        color: #c0392b;
    }

    .stat-trend.neutral {
        background: rgba(149, 165, 166, 0.1);
        color: #7f8c8d;
    }

    .stat-info {
        font-size: 12px;
        color: #95a5a6;
        margin-top: 5px;
        font-style: italic;
    }

    /* DASHBOARD CONTENT */
    .dashboard-content {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 20px;
        margin-bottom: 30px;
    }

    .chart-card, .actions-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        border: 1px solid #e9ecef;
    }

    .chart-header, .actions-header {
        margin-bottom: 15px;
    }

    .chart-header h3, .actions-header h3 {
        color: #2c3e50;
        font-size: 18px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 0;
    }

    .chart-container {
        height: 250px;
        position: relative;
    }

    /* QUICK ACTIONS */
    .actions-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 12px;
    }

    .action-btn {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 15px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }

    .action-btn.primary {
        background: rgba(52, 152, 219, 0.1);
        color: #3498db;
        border-color: rgba(52, 152, 219, 0.2);
    }

    .action-btn.secondary {
        background: rgba(108, 117, 125, 0.1);
        color: #6c757d;
        border-color: rgba(108, 117, 125, 0.2);
    }

    .action-btn.success {
        background: rgba(46, 204, 113, 0.1);
        color: #27ae60;
        border-color: rgba(46, 204, 113, 0.2);
    }

    .action-btn.warning {
        background: rgba(243, 156, 18, 0.1);
        color: #f39c12;
        border-color: rgba(243, 156, 18, 0.2);
    }

    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .action-btn.primary:hover {
        background: #3498db;
        color: white;
        border-color: #3498db;
    }

    .action-btn.secondary:hover {
        background: #6c757d;
        color: white;
        border-color: #6c757d;
    }

    .action-btn.success:hover {
        background: #27ae60;
        color: white;
        border-color: #27ae60;
    }

    .action-btn.warning:hover {
        background: #f39c12;
        color: white;
        border-color: #f39c12;
    }

    /* RECENT BOOKINGS */
    .bookings-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        border: 1px solid #e9ecef;
    }

    .bookings-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .bookings-header h3 {
        color: #2c3e50;
        font-size: 18px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 0;
    }

    .view-all {
        color: #3498db;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 5px;
        transition: all 0.3s ease;
    }

    .view-all:hover {
        color: #2980b9;
        transform: translateX(3px);
    }

    .bookings-list {
        max-height: 400px;
        overflow-y: auto;
    }

    .booking-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 0;
        border-bottom: 1px solid #e9ecef;
    }

    .booking-item:last-child {
        border-bottom: none;
    }

    .booking-title {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 8px;
    }

    .booking-title h4 {
        color: #2c3e50;
        font-size: 16px;
        font-weight: 600;
        margin: 0;
        max-width: 200px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .booking-status {
        padding: 4px 8px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .booking-status.booking {
        background: rgba(52, 152, 219, 0.1);
        color: #3498db;
    }

    .booking-status.sedang-rapat {
        background: rgba(243, 156, 18, 0.1);
        color: #f39c12;
    }

    .booking-status.selesai {
        background: rgba(149, 165, 166, 0.1);
        color: #7f8c8d;
    }
    
    .booking-status.aktif {
        background: rgba(231, 76, 60, 0.1);
        color: #c0392b;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.7; }
        100% { opacity: 1; }
    }

    .room-status {
        padding: 4px 8px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .room-status.sedang-dipakai {
        background: rgba(231, 76, 60, 0.1);
        color: #c0392b;
    }

    .room-status.sudah-dibooking {
        background: rgba(52, 152, 219, 0.1);
        color: #3498db;
    }

    .room-status.kosong {
        background: rgba(46, 204, 113, 0.1);
        color: #27ae60;
    }

    .booking-details {
        display: flex;
        gap: 15px;
        font-size: 13px;
        color: #7f8c8d;
    }

    .booking-details span {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .btn-sm {
        padding: 6px 10px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 12px;
        font-weight: 500;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 36px;
    }

    .btn-sm.warning {
        background: rgba(243, 156, 18, 0.1);
        color: #f39c12;
        border: 1px solid rgba(243, 156, 18, 0.2);
    }

    .btn-sm.warning:hover {
        background: #f39c12;
        color: white;
        border-color: #f39c12;
    }

    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #bdc3c7;
    }

    .empty-state i {
        font-size: 48px;
        margin-bottom: 15px;
    }

    .empty-state p {
        margin: 0;
        font-size: 14px;
    }

    /* RUANGAN DIGUNAKAN SECTION */
    .rooms-section {
        margin-top: 30px;
    }

    .rooms-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 15px;
        margin-top: 15px;
    }

    .room-card {
        background: white;
        border-radius: 8px;
        padding: 15px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        border: 1px solid #e9ecef;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .room-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        color: white;
    }

    .room-icon.sedang-dipakai {
        background: linear-gradient(135deg, #e74c3c, #c0392b);
    }

    .room-icon.sudah-dibooking {
        background: linear-gradient(135deg, #3498db, #2980b9);
    }

    .room-info h4 {
        color: #2c3e50;
        font-size: 16px;
        margin: 0 0 5px 0;
    }

    .room-info p {
        color: #7f8c8d;
        font-size: 12px;
        margin: 0;
    }

    /* RESPONSIVE DESIGN */
    @media (max-width: 1024px) {
        .dashboard-content {
            grid-template-columns: 1fr;
        }
        
        .main-content {
            margin-left: 0;
            width: 100%;
        }
    }

    @media (max-width: 768px) {
        .main-content {
            margin-left: 0;
            margin-top: 55px;
            padding: 15px;
            width: 100%;
        }
        
        .stats-cards {
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .page-header {
            padding: 20px;
        }
        
        .welcome-section h1 {
            font-size: 24px;
        }
        
        .booking-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }
        
        .booking-actions {
            align-self: flex-end;
        }
        
        .booking-details {
            flex-direction: column;
            gap: 8px;
        }
        
        .booking-title h4 {
            max-width: 150px;
        }
        
        .rooms-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 480px) {
        .main-content {
            padding: 10px;
        }
        
        .stats-cards {
            grid-template-columns: 1fr;
        }
        
        .stat-content h3 {
            font-size: 24px;
        }
        
        .chart-card, .actions-card, .bookings-card {
            padding: 15px;
        }
        
        .chart-container {
            height: 220px;
        }
        
        .welcome-section h1 {
            font-size: 20px;
        }
        
        .welcome-section p {
            font-size: 14px;
        }
    }
    
    /* Scrollbar styling */
    .bookings-list::-webkit-scrollbar {
        width: 6px;
    }
    
    .bookings-list::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .bookings-list::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 10px;
    }
    
    .bookings-list::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <div class="content-wrapper">
            <div class="page-header">
                <div class="welcome-section">
                    <h1>Selamat datang, <?php echo htmlspecialchars($_SESSION['username']); ?>! 🏢</h1>
                    <p>Kelola reservasi ruang rapat dengan mudah dan efisien</p>
                </div>
            </div>
            
            <!-- Statistik Cards -->
            <div class="stats-cards">
                <div class="stat-card">
                    <div class="stat-icon total-booking">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $total_booking; ?></h3>
                        <p>Total Booking</p>
                        <span class="stat-trend up">
                            <i class="fas fa-arrow-up"></i>
                            Semua waktu
                        </span>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon today-booking">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $booking_hari_ini; ?></h3>
                        <p>Booking Hari Ini</p>
                      
                        <span class="stat-trend <?php echo $booking_hari_ini > 0 ? 'up' : 'neutral'; ?>">
                            <i class="fas fa-<?php echo $booking_hari_ini > 0 ? 'arrow-up' : 'minus'; ?>"></i>
                            Hari ini
                        </span>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon available-rooms">
                        <i class="fas fa-door-open"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $ruangan_tersedia; ?>/<?php echo $total_ruangan; ?></h3>
                        <p>Ruangan Tersedia</p>
                        <div class="stat-info">
                            (Sedang dipakai: <?php echo $ruangan_dipakai; ?>, 
                            Sudah dibooking: <?php echo $ruangan_dibooking; ?>)
                        </div>
                        <span class="stat-trend <?php echo $ruangan_tersedia > 0 ? 'up' : 'down'; ?>">
                            <i class="fas fa-<?php echo $ruangan_tersedia > 0 ? 'check' : 'times'; ?>"></i>
                            <?php echo $persen_tersedia; ?>% tersedia
                        </span>
                    </div>
                </div>

                <!-- PERBAIKAN: Card Rapat Aktif -->
                <div class="stat-card">
                    <div class="stat-icon active-meetings">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <!-- PERBAIKAN: Menggunakan $rapat_aktif dari tbruangan -->
                        <h3><?php echo $rapat_aktif; ?></h3>
                        <p>Rapat Aktif</p>
                        <span class="stat-trend <?php echo $rapat_aktif > 0 ? 'up' : 'neutral'; ?>">
                            <i class="fas fa-<?php echo $rapat_aktif > 0 ? 'arrow-up' : 'minus'; ?>"></i>
                            Sedang berlangsung
                        </span>
                    </div>
                </div>
            </div>

            <!-- Charts and Quick Actions -->
            <div class="dashboard-content">
                <!-- Chart Section -->
                <div class="chart-section">
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3><i class="fas fa-chart-line"></i> Statistik Booking 7 Hari Terakhir</h3>
                        </div>
                        <div class="chart-container">
                            <canvas id="bookingChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="quick-actions-section">
                    <div class="actions-card">
                        <div class="actions-header">
                            <h3><i class="fas fa-bolt"></i> Akses Cepat</h3>
                        </div>
                        <div class="actions-grid">
                            <a href="index.php?page=tambah_booking" class="action-btn primary">
                                <i class="fas fa-plus-circle"></i>
                                <span>Tambah Booking</span>
                            </a>
                            <a href="index.php?page=data_booking" class="action-btn secondary">
                                <i class="fas fa-list"></i>
                                <span>Lihat Semua Booking</span>
                            </a>
                            <a href="index.php?page=data_ruangan" class="action-btn success">
                                <i class="fas fa-building"></i>
                                <span>Kelola Ruangan</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Bookings -->
            <div class="recent-bookings">
                <div class="bookings-card">
                    <div class="bookings-header">
                        <h3><i class="fas fa-history"></i> Booking Hari Ini</h3>
                        <a href="index.php?page=data_booking" class="view-all">Lihat Semua <i class="fas fa-arrow-right"></i></a>
                    </div>
                    <div class="bookings-list">
                        <?php if (mysqli_num_rows($semua_booking_hari_ini) > 0): ?>
                            <?php 
                            // Reset pointer hasil query
                            mysqli_data_seek($semua_booking_hari_ini, 0);
                            while ($booking = mysqli_fetch_assoc($semua_booking_hari_ini)): 
                                // Tentukan status yang ditampilkan berdasarkan status ruangan
                                $display_status = $booking['status_ruangan'] == 'Sedang Dipakai' ? 'Aktif' : $booking['status'];
                                $status_class = $booking['status_ruangan'] == 'Sedang Dipakai' ? 'aktif' : strtolower(str_replace(' ', '-', $booking['status']));
                            ?>
                                <div class="booking-item">
                                    <div class="booking-info">
                                        <div class="booking-title">
                                            <h4><?php echo htmlspecialchars($booking['name_booking']); ?></h4>
                                            <span class="booking-status <?php echo $status_class; ?>">
                                                <?php echo htmlspecialchars($display_status); ?>
                                            </span>
                                        </div>
                                        <div class="booking-details">
                                            <span class="room-name">
                                                <i class="fas fa-building"></i>
                                                <?php echo htmlspecialchars($booking['nama_ruangan']); ?> (Lantai <?php echo htmlspecialchars($booking['lantai']); ?>)
                                            </span>
                                            <span class="booking-time">
                                                <i class="fas fa-clock"></i>
                                                <?php echo date('H:i', strtotime($booking['jam_rapat'])); ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="booking-actions">
                                        <a href="index.php?page=ubah_booking&id=<?php echo $booking['id_booking']; ?>" class="btn-sm warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-calendar-times"></i>
                                <p>Tidak ada booking untuk hari ini</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Ruangan yang Sedang Digunakan -->
            <?php if (mysqli_num_rows($ruangan_digunakan) > 0): ?>
            <div class="rooms-section">
                <div class="bookings-card">
                    <div class="bookings-header">
                        <h3><i class="fas fa-building"></i> Ruangan yang Sedang Digunakan</h3>
                    </div>
                    <div class="rooms-grid">
                        <?php 
                        mysqli_data_seek($ruangan_digunakan, 0);
                        while ($ruangan = mysqli_fetch_assoc($ruangan_digunakan)): 
                            $status_class = strtolower(str_replace(' ', '-', $ruangan['status']));
                            $icon_class = strtolower(str_replace(' ', '-', $ruangan['status']));
                        ?>
                            <div class="room-card">
                                <div class="room-icon <?php echo $icon_class; ?>">
                                    <i class="fas fa-<?php echo $ruangan['status'] == 'Sedang Dipakai' ? 'users' : 'calendar-check'; ?>"></i>
                                </div>
                                <div class="room-info">
                                    <h4><?php echo htmlspecialchars($ruangan['nama_ruangan']); ?></h4>
                                    <p>Lantai <?php echo htmlspecialchars($ruangan['lantai']); ?> • Kapasitas: <?php echo htmlspecialchars($ruangan['kapasitas_ruangan']); ?> orang</p>
                                    <span class="room-status <?php echo $status_class; ?>">
                                        <?php echo htmlspecialchars($ruangan['status']); ?>
                                    </span>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>

<script>
// Chart.js implementation
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('bookingChart');
    if (ctx) {
        const bookingChart = new Chart(ctx.getContext('2d'), {
            type: 'line',
            data: {
                labels: <?php echo json_encode($chart_labels); ?>,
                datasets: [{
                    label: 'Jumlah Booking',
                    data: <?php echo json_encode($chart_data); ?>,
                    backgroundColor: 'rgba(52, 152, 219, 0.1)',
                    borderColor: '#3498db',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#3498db',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 1,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleFont: {
                            size: 12
                        },
                        bodyFont: {
                            size: 11
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            stepSize: 1,
                            font: {
                                size: 11
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 11
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>