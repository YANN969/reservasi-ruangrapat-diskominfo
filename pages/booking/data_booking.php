<?php
include "config/koneksi.php";

// Fungsi untuk icon status booking
function getBookingStatusIcon($status) {
    switch($status) {
        case 'Booking': return 'calendar-check';
        case 'Sedang Rapat': return 'users';
        case 'Selesai': return 'check-circle';
        default: return 'calendar';
    }
}

// Search functionality
$search = '';
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = mysqli_real_escape_string($koneksi, $_GET['search']);
}

// Query untuk mengambil data booking dengan JOIN ke tabel ruangan
if (!empty($search)) {
    $query = "SELECT b.*, r.nama_ruangan, r.lantai 
              FROM tbbooking b 
              JOIN tbruangan r ON b.id_ruangan = r.id_ruangan 
              WHERE b.name_booking LIKE '%$search%' 
                 OR b.id_booking LIKE '%$search%'
                 OR r.nama_ruangan LIKE '%$search%'
                 OR b.status LIKE '%$search%'
              ORDER BY b.tanggal DESC, b.jam_rapat DESC";
} else {
    $query = "SELECT b.*, r.nama_ruangan, r.lantai 
              FROM tbbooking b 
              JOIN tbruangan r ON b.id_ruangan = r.id_ruangan 
              ORDER BY b.tanggal DESC, b.jam_rapat DESC";
}

$result = mysqli_query($koneksi, $query);
$total_data = mysqli_num_rows($result);
?>

<div class="main-content">
    <div class="content-wrapper">
        <!-- Page Header yang Diperbaiki -->
        <div class="page-header-improved">
            <div class="header-content-center">
                <div class="header-icon-wrapper">
                    <div class="header-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                </div>
                <div class="header-text-center">
                    <h1>Data Booking Ruangan</h1>
                    <p>Kelola reservasi dan booking ruangan rapat</p>
                </div>
            </div>
        </div>

        <div class="content-card">
            <div class="card-header">
                <h3><i class="fas fa-list"></i> Daftar Booking</h3>
                <a href="index.php?page=tambah_booking" class="btn-primary">
                    <i class="fas fa-plus-circle"></i>
                    <span>Tambah Booking</span>
                </a>
            </div>

            <!-- Search Bar -->
            <div class="search-container">
                <form method="GET" action="" class="search-form">
                    <input type="hidden" name="page" value="data_booking">
                    <div class="search-box">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                               placeholder="Cari booking berdasarkan nama, ID, ruangan, atau status..." class="search-input">
                        <?php if (!empty($search)): ?>
                            <a href="index.php?page=data_booking" class="clear-search">
                                <i class="fas fa-times"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                    <button type="submit" class="btn-search">
                        <i class="fas fa-search"></i>
                        Cari
                    </button>
                </form>
                
                <?php if (!empty($search)): ?>
                    <div class="search-results-info">
                        <p>
                            <i class="fas fa-info-circle"></i>
                            Menampilkan <strong><?php echo $total_data; ?></strong> hasil pencarian untuk "<strong><?php echo htmlspecialchars($search); ?></strong>"
                            <a href="index.php?page=data_booking" class="clear-all-search">
                                <i class="fas fa-times"></i> Hapus pencarian
                            </a>
                        </p>
                    </div>
                <?php endif; ?>
            </div>
            
            <?php if (mysqli_num_rows($result) > 0): ?>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>ID Booking</th>
                                <th>Nama Pemesan</th>
                                <th>No. Telepon</th>
                                <th>Ruangan</th>
                                <th>Tanggal</th>
                                <th>Jam Rapat</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            while ($row = mysqli_fetch_assoc($result)) {
                                $badge_class = 'badge-' . strtolower(str_replace(' ', '-', $row['status']));
                                
                                // Highlight search term in results
                                $name_booking = $row['name_booking'];
                                $id_booking = $row['id_booking'];
                                $nama_ruangan = $row['nama_ruangan'];
                                $status = $row['status'];
                                
                                if (!empty($search)) {
                                    $highlight = '<mark>' . $search . '</mark>';
                                    $name_booking = str_ireplace($search, $highlight, $name_booking);
                                    $id_booking = str_ireplace($search, $highlight, $id_booking);
                                    $nama_ruangan = str_ireplace($search, $highlight, $nama_ruangan);
                                    $status = str_ireplace($search, $highlight, $status);
                                }
                                
                                echo "<tr>";
                                echo "<td>" . $no++ . "</td>";
                                echo "<td><strong>" . $id_booking . "</strong></td>";
                                echo "<td>" . $name_booking . "</td>";
                                echo "<td><strong>" . $row['no_telp'] . "</strong></td>";
                                echo "<td>
                                        <div>
                                            <strong>" . $nama_ruangan . "</strong>
                                            <br><small class='text-muted'>Lantai " . $row['lantai'] . "</small>
                                        </div>
                                      </td>";
                                echo "<td><strong>" . date('d/m/Y', strtotime($row['tanggal'])) . "</strong></td>";
                                echo "<td><strong class='time'>" . date('H:i', strtotime($row['jam_rapat'])) . "</strong></td>";
                                echo "<td>
                                        <span class='badge $badge_class'>
                                            <i class='fas fa-" . getBookingStatusIcon($row['status']) . "'></i>
                                            " . $status . "
                                        </span>
                                      </td>";
                                echo "<td>
                                        <div class='action-buttons'>
                                            <a href='index.php?page=ubah_booking&id=" . $row['id_booking'] . "' class='btn-warning'>
                                                <i class='fas fa-edit'></i>
                                                Ubah
                                            </a>
                                            <a href='index.php?page=hapus_booking&id=" . $row['id_booking'] . "' class='btn-danger' onclick='return confirm(\"Yakin ingin menghapus booking ini?\")'>
                                                <i class='fas fa-trash'></i>
                                                Hapus
                                            </a>
                                        </div>
                                      </td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h3>
                        <?php if (!empty($search)): ?>
                            Pencarian tidak ditemukan
                        <?php else: ?>
                            Belum ada data booking
                        <?php endif; ?>
                    </h3>
                    <p>
                        <?php if (!empty($search)): ?>
                            Tidak ada booking yang cocok dengan "<strong><?php echo htmlspecialchars($search); ?></strong>"
                        <?php else: ?>
                            Silakan tambah booking baru untuk memulai
                        <?php endif; ?>
                    </p>
                    <a href="index.php?page=tambah_booking" class="btn-primary">
                        <i class="fas fa-plus-circle"></i>
                        <span>Tambah Booking Pertama</span>
                    </a>
                    <?php if (!empty($search)): ?>
                        <a href="index.php?page=data_booking" class="btn-secondary" style="margin-top: 10px;">
                            <i class="fas fa-list"></i>
                            <span>Lihat Semua Booking</span>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
/* Improved Page Header */
.page-header-improved {
    margin-bottom: 30px;
    text-align: center;
}

.header-content-center {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 15px;
}

.header-icon-wrapper {
    display: flex;
    justify-content: center;
}

.header-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #3498db, #2980b9);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 25px rgba(52, 152, 219, 0.3);
    position: relative;
    overflow: hidden;
}

.header-icon::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
    transform: rotate(45deg);
    animation: shine 3s infinite;
}

@keyframes shine {
    0% {
        transform: translateX(-100%) translateY(-100%) rotate(45deg);
    }
    100% {
        transform: translateX(100%) translateY(100%) rotate(45deg);
    }
}

.header-icon i {
    font-size: 32px;
    color: white;
    z-index: 2;
    position: relative;
}

.header-text-center {
    text-align: center;
}

.header-text-center h1 {
    color: #2c3e50;
    font-size: 32px;
    font-weight: 800;
    margin: 0 0 8px 0;
    background: linear-gradient(135deg, #2c3e50, #3498db);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    line-height: 1.2;
}

.header-text-center p {
    color: #7f8c8d;
    font-size: 16px;
    margin: 0;
    font-weight: 500;
    line-height: 1.4;
}

.content-card {
    background: white;
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    border: 1px solid #e9ecef;
    margin-bottom: 30px;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
    flex-wrap: wrap;
    gap: 15px;
}

.card-header h3 {
    color: #2c3e50;
    font-size: 22px;
    font-weight: 700;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

/* Search Container */
.search-container {
    margin-bottom: 25px;
}

.search-form {
    display: flex;
    gap: 12px;
    align-items: center;
    flex-wrap: wrap;
}

.search-box {
    position: relative;
    flex: 1;
    min-width: 300px;
}

.search-icon {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #6c757d;
    font-size: 14px;
}

.search-input {
    width: 100%;
    padding: 12px 45px 12px 40px;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    font-size: 14px;
    background: white;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
}

.search-input:focus {
    outline: none;
    border-color: #3498db;
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
}

.clear-search {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #6c757d;
    text-decoration: none;
    font-size: 14px;
    padding: 4px;
    border-radius: 50%;
    transition: all 0.3s ease;
}

.clear-search:hover {
    background: #f8f9fa;
    color: #e74c3c;
}

.btn-search {
    background: linear-gradient(135deg, #27ae60, #219a52);
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    box-shadow: 0 2px 8px rgba(39, 174, 96, 0.3);
    white-space: nowrap;
}

.btn-search:hover {
    background: linear-gradient(135deg, #219a52, #1e8449);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(39, 174, 96, 0.4);
}

/* Search Results Info */
.search-results-info {
    background: #e8f5e8;
    border: 1px solid #27ae60;
    border-radius: 8px;
    padding: 12px 16px;
    margin-top: 15px;
    font-size: 14px;
    color: #2c3e50;
}

.search-results-info p {
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.search-results-info i {
    color: #27ae60;
}

.clear-all-search {
    color: #e74c3c;
    text-decoration: none;
    font-weight: 500;
    margin-left: auto;
    padding: 4px 8px;
    border-radius: 4px;
    transition: all 0.3s ease;
    font-size: 13px;
}

.clear-all-search:hover {
    background: rgba(231, 76, 60, 0.1);
}

/* Highlight search term */
mark {
    background: #fff3cd;
    color: #856404;
    padding: 2px 4px;
    border-radius: 3px;
    font-weight: 600;
}

.btn-primary {
    background: linear-gradient(135deg, #3498db, #2980b9);
    color: white;
    padding: 12px 20px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    box-shadow: 0 2px 10px rgba(52, 152, 219, 0.3);
}

.btn-primary:hover {
    background: linear-gradient(135deg, #2980b9, #21618c);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(52, 152, 219, 0.4);
}

.btn-secondary {
    background: linear-gradient(135deg, #6c757d, #5a6268);
    color: white;
    padding: 12px 20px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    box-shadow: 0 2px 10px rgba(108, 117, 125, 0.3);
}

.btn-secondary:hover {
    background: linear-gradient(135deg, #5a6268, #495057);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(108, 117, 125, 0.4);
}

.table-container {
    overflow-x: auto;
    border-radius: 10px;
    border: 1px solid #e9ecef;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius: 10px;
    overflow: hidden;
}

.data-table th {
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    color: #2c3e50;
    padding: 16px 20px;
    text-align: left;
    font-weight: 600;
    font-size: 14px;
    border-bottom: 2px solid #e9ecef;
}

.data-table td {
    padding: 16px 20px;
    border-bottom: 1px solid #e9ecef;
    color: #495057;
    font-size: 14px;
}

.data-table tr:hover {
    background: #f8f9fa;
}

.data-table tr:last-child td {
    border-bottom: none;
}

.btn-warning {
    background: linear-gradient(135deg, #f39c12, #e67e22);
    color: white;
    padding: 8px 12px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 12px;
    font-weight: 500;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    border: none;
}

.btn-warning:hover {
    background: linear-gradient(135deg, #e67e22, #d35400);
    color: white;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(243, 156, 18, 0.3);
}

.btn-danger {
    background: linear-gradient(135deg, #e74c3c, #c0392b);
    color: white;
    padding: 8px 12px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 12px;
    font-weight: 500;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    border: none;
}

.btn-danger:hover {
    background: linear-gradient(135deg, #c0392b, #a93226);
    color: white;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(231, 76, 60, 0.3);
}

.badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    border: none;
    color: white;
}

.badge-booking {
    background: linear-gradient(135deg, #3498db, #2980b9);
}

.badge-sedang-rapat {
    background: linear-gradient(135deg, #f39c12, #e67e22);
}

.badge-selesai {
    background: linear-gradient(135deg, #27ae60, #219a52);
}

.time {
    color: #e74c3c;
    font-weight: 700;
}

.text-muted {
    color: #6c757d !important;
    font-size: 12px;
}

.empty-state {
    text-align: center;
    padding: 60px 40px;
    color: #6c757d;
}

.empty-icon {
    font-size: 64px;
    margin-bottom: 20px;
    color: #3498db;
    opacity: 0.7;
}

.empty-state h3 {
    color: #2c3e50;
    margin-bottom: 10px;
    font-size: 20px;
    font-weight: 600;
}

.empty-state p {
    margin-bottom: 25px;
    font-size: 14px;
    color: #6c757d;
}

.action-buttons {
    display: flex;
    gap: 8px;
    flex-wrap: nowrap;
}

@media (max-width: 768px) {
    .main-content {
        margin-left: 0;
        margin-top: 55px;
        padding: 20px;
    }
    
    .header-text-center h1 {
        font-size: 26px;
    }
    
    .header-text-center p {
        font-size: 14px;
    }
    
    .header-icon {
        width: 60px;
        height: 60px;
    }
    
    .header-icon i {
        font-size: 24px;
    }
    
    .content-card {
        padding: 20px;
    }
    
    .card-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .search-form {
        flex-direction: column;
        width: 100%;
    }
    
    .search-box {
        min-width: 100%;
    }
    
    .btn-search {
        width: 100%;
        justify-content: center;
    }
    
    .btn-primary {
        width: 100%;
        justify-content: center;
    }
    
    .action-buttons {
        flex-direction: column;
        gap: 6px;
    }
    
    .btn-warning, .btn-danger {
        text-align: center;
        padding: 8px 12px;
        font-size: 11px;
    }
    
    .data-table th,
    .data-table td {
        padding: 12px 8px;
        font-size: 13px;
    }
    
    .search-results-info p {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    
    .clear-all-search {
        margin-left: 0;
    }
}

@media (max-width: 480px) {
    .main-content {
        padding: 15px;
    }
    
    .header-text-center h1 {
        font-size: 22px;
    }
    
    .empty-state {
        padding: 40px 20px;
    }
    
    .empty-icon {
        font-size: 48px;
    }
}
</style>