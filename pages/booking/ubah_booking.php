<?php
include "config/koneksi.php";

$id_booking = $_GET['id'];

// Ambil data booking untuk form
$query = "SELECT b.*, r.nama_ruangan, r.lantai, r.kapasitas_ruangan 
          FROM tbbooking b 
          JOIN tbruangan r ON b.id_ruangan = r.id_ruangan 
          WHERE b.id_booking='$id_booking'";
$result = mysqli_query($koneksi, $query);
$booking = mysqli_fetch_assoc($result);

// Ambil data ruangan untuk dropdown
$query_ruangan = "SELECT * FROM tbruangan ORDER BY nama_ruangan";
$result_ruangan = mysqli_query($koneksi, $query_ruangan);

// Proses form jika ada POST
if (isset($_POST['ubah'])) {
    $id_ruangan = $_POST['id_ruangan'];
    $name_booking = $_POST['name_booking'];
    $no_telp = $_POST['no_telp'];
    $jam_rapat = $_POST['jam_rapat'];
    $tanggal = $_POST['tanggal'];
    $status = $_POST['status'];
    $old_id_ruangan = $booking['id_ruangan'];
    $old_status = $booking['status'];
    
    // Validasi data
    if (empty($name_booking) || empty($no_telp) || empty($jam_rapat) || empty($tanggal) || empty($status)) {
        $error = "Semua field harus diisi!";
    } else {
        // Mulai transaction
        mysqli_begin_transaction($koneksi);
        
        try {
            // Update data booking
            $ubah = mysqli_query($koneksi, "UPDATE tbbooking SET id_ruangan='$id_ruangan', name_booking='$name_booking', no_telp='$no_telp', jam_rapat='$jam_rapat', status='$status', tanggal='$tanggal' WHERE id_booking='$id_booking'");
            
            if ($ubah) {
                // Jika ruangan berubah, kembalikan status ruangan lama
                if ($old_id_ruangan != $id_ruangan) {
                    $reset_ruangan_lama = mysqli_query($koneksi, "UPDATE tbruangan SET status = 'Kosong' WHERE id_ruangan = '$old_id_ruangan'");
                    if (!$reset_ruangan_lama) {
                        throw new Exception("Gagal reset status ruangan lama");
                    }
                }
                
                // Update status ruangan baru berdasarkan status booking
                $status_ruangan = '';
                if ($status == 'Booking') {
                    $status_ruangan = 'Sudah Dibooking';
                } elseif ($status == 'Sedang Rapat') {
                    $status_ruangan = 'Sedang Dipakai';
                } elseif ($status == 'Selesai') {
                    $status_ruangan = 'Kosong';
                }
                
                if (!empty($status_ruangan)) {
                    $update_ruangan = mysqli_query($koneksi, "UPDATE tbruangan SET status = '$status_ruangan' WHERE id_ruangan = '$id_ruangan'");
                    if (!$update_ruangan) {
                        throw new Exception("Gagal update status ruangan baru");
                    }
                }
                
                // Commit transaction
                mysqli_commit($koneksi);
                echo "<script>alert('Data booking berhasil diubah'); window.location.href='index.php?page=data_booking';</script>";
                exit();
            } else {
                throw new Exception("Gagal mengubah data booking: " . mysqli_error($koneksi));
            }
        } catch (Exception $e) {
            // Rollback transaction
            mysqli_rollback($koneksi);
            $error = $e->getMessage();
        }
    }
}
?>

<div class="main-content">
    <div class="content-wrapper">
        <div class="page-header">
            <div class="welcome-section">
                <h1>Ubah Data Booking <i class="fas fa-edit"></i></h1>
                <p>Perbarui informasi booking ruangan</p>
            </div>
        </div>

        <div class="content-card">
            <div class="card-header">
                <h3><i class="fas fa-calendar-check"></i> Form Ubah Booking</h3>
                <a href="index.php?page=data_booking" class="btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali</span>
                </a>
            </div>

            <div class="form-container">
                <form method="POST" class="data-form">
                    <?php if (isset($error)): ?>
                        <div class="alert-error">
                            <i class="fas fa-exclamation-circle"></i>
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="id_booking">ID Booking</label>
                        <input type="text" id="id_booking" value="<?php echo $booking['id_booking']; ?>" readonly class="form-input">
                        <small class="form-help">ID Booking tidak dapat diubah</small>
                    </div>

                    <div class="form-group">
                        <label for="id_ruangan">Ruangan</label>
                        <select id="id_ruangan" name="id_ruangan" required class="form-input">
                            <option value="">Pilih Ruangan</option>
                            <?php 
                            mysqli_data_seek($result_ruangan, 0);
                            while ($ruangan = mysqli_fetch_assoc($result_ruangan)): 
                            ?>
                                <option value="<?php echo $ruangan['id_ruangan']; ?>" <?php echo ((isset($_POST['id_ruangan']) && $_POST['id_ruangan'] == $ruangan['id_ruangan']) || $booking['id_ruangan'] == $ruangan['id_ruangan']) ? 'selected' : ''; ?>>
                                    <?php echo $ruangan['nama_ruangan'] . ' (Lantai ' . $ruangan['lantai'] . ') - Kapasitas: ' . $ruangan['kapasitas_ruangan'] . ' orang - Status: ' . $ruangan['status']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="name_booking">Nama Pemesan</label>
                        <input type="text" id="name_booking" name="name_booking" value="<?php echo isset($_POST['name_booking']) ? $_POST['name_booking'] : $booking['name_booking']; ?>" required class="form-input" placeholder="Masukkan nama pemesan">
                    </div>

                    <div class="form-group">
                        <label for="no_telp">No. Telepon</label>
                        <input type="text" id="no_telp" name="no_telp" value="<?php echo isset($_POST['no_telp']) ? $_POST['no_telp'] : $booking['no_telp']; ?>" required class="form-input" placeholder="Masukkan nomor telepon">
                    </div>

                    <div class="form-group">
                        <label for="tanggal">Tanggal Rapat</label>
                        <input type="date" id="tanggal" name="tanggal" value="<?php echo isset($_POST['tanggal']) ? $_POST['tanggal'] : $booking['tanggal']; ?>" required class="form-input">
                    </div>

                    <div class="form-group">
                        <label for="jam_rapat">Jam Rapat</label>
                        <input type="time" id="jam_rapat" name="jam_rapat" value="<?php echo isset($_POST['jam_rapat']) ? $_POST['jam_rapat'] : $booking['jam_rapat']; ?>" required class="form-input">
                    </div>

                    <div class="form-group">
                        <label for="status">Status Booking</label>
                        <select id="status" name="status" required class="form-input">
                            <option value="">Pilih Status Booking</option>
                            <option value="Booking" <?php echo ((isset($_POST['status']) && $_POST['status'] == 'Booking') || $booking['status'] == 'Booking') ? 'selected' : ''; ?>>Booking</option>
                            <option value="Sedang Rapat" <?php echo ((isset($_POST['status']) && $_POST['status'] == 'Sedang Rapat') || $booking['status'] == 'Sedang Rapat') ? 'selected' : ''; ?>>Sedang Rapat</option>
                            <option value="Selesai" <?php echo ((isset($_POST['status']) && $_POST['status'] == 'Selesai') || $booking['status'] == 'Selesai') ? 'selected' : ''; ?>>Selesai</option>
                        </select>
                      
                    </div>

                    <div class="form-actions">
                        <button type="submit" name="ubah" class="btn-warning">
                            <i class="fas fa-save"></i>
                            Update Booking
                        </button>
                        <a href="index.php?page=data_booking" class="btn-secondary">
                            <i class="fas fa-times"></i>
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
/* Styles sama dengan tambah_booking.php */
.form-container {
    max-width: 600px;
    margin: 0 auto;
}

.data-form {
    background: #f8f9fa;
    padding: 30px;
    border-radius: 10px;
    border: 1px solid #e9ecef;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    color: #2c3e50;
    font-weight: 600;
    font-size: 14px;
}

.form-input {
    width: 100%;
    padding: 12px 16px;
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    color: #495057;
    font-size: 14px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.form-input:focus {
    outline: none;
    border-color: #3498db;
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
}

.form-input:read-only {
    background: #e9ecef;
    color: #6c757d;
}

.form-help {
    display: block;
    margin-top: 5px;
    color: #6c757d;
    font-size: 12px;
}

.alert-error {
    background: rgba(231, 76, 60, 0.1);
    border: 1px solid #e74c3c;
    color: #c0392b;
    padding: 12px 16px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 500;
}

.form-actions {
    display: flex;
    gap: 15px;
    margin-top: 30px;
}

.btn-warning {
    background: linear-gradient(135deg, #f39c12, #e67e22);
    color: white;
    border: none;
    padding: 14px 24px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    box-shadow: 0 2px 10px rgba(243, 156, 18, 0.3);
}

.btn-warning:hover {
    background: linear-gradient(135deg, #e67e22, #d35400);
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(243, 156, 18, 0.4);
}

.btn-secondary {
    background: linear-gradient(135deg, #6c757d, #5a6268);
    color: white;
    padding: 14px 24px;
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

@media (max-width: 768px) {
    .form-actions {
        flex-direction: column;
    }
    
    .btn-warning, .btn-secondary {
        width: 100%;
        justify-content: center;
    }
    
    .data-form {
        padding: 20px;
    }
}
</style>