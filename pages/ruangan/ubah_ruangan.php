<?php
include "config/koneksi.php";

$id_ruangan = $_GET['id'];

// Ambil data ruangan untuk form
$query = "SELECT * FROM tbruangan WHERE id_ruangan='$id_ruangan'";
$result = mysqli_query($koneksi, $query);
$ruangan = mysqli_fetch_assoc($result);

// Proses form jika ada POST
if (isset($_POST['ubah'])) {
    $nama_ruangan = $_POST['nama_ruangan'];
    $lantai = $_POST['lantai'];
    $kapasitas_ruangan = $_POST['kapasitas_ruangan'];
    $status = $_POST['status'];
    
    // Validasi data
    if (empty($nama_ruangan) || empty($lantai) || empty($kapasitas_ruangan) || empty($status)) {
        $error = "Semua field harus diisi!";
    } else {
        $ubah = mysqli_query($koneksi, "UPDATE tbruangan SET nama_ruangan='$nama_ruangan', lantai='$lantai', kapasitas_ruangan='$kapasitas_ruangan', status='$status' WHERE id_ruangan='$id_ruangan'");
        if ($ubah) {
            echo "<script>alert('Data ruangan berhasil diubah'); window.location.href='index.php?page=data_ruangan';</script>";
            exit();
        } else {
            $error = "Gagal mengubah data ruangan: " . mysqli_error($koneksi);
        }
    }
}
?>

<div class="main-content">
    <div class="content-wrapper">
        <div class="page-header">
            <div class="welcome-section">
                <h1>Ubah Data Ruangan <i class="fas fa-edit"></i></h1>
                <p>Perbarui informasi ruangan yang sudah ada</p>
            </div>
        </div>

        <div class="content-card">
            <div class="card-header">
                <h3><i class="fas fa-door-open"></i> Form Ubah Ruangan</h3>
                <a href="index.php?page=data_ruangan" class="btn-secondary">
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
                        <label for="id_ruangan">ID Ruangan</label>
                        <input type="text" id="id_ruangan" value="<?php echo $ruangan['id_ruangan']; ?>" readonly class="form-input">
                        <small class="form-help">ID Ruangan tidak dapat diubah</small>
                    </div>

                    <div class="form-group">
                        <label for="nama_ruangan">Nama Ruangan</label>
                        <input type="text" id="nama_ruangan" name="nama_ruangan" value="<?php echo isset($_POST['nama_ruangan']) ? $_POST['nama_ruangan'] : $ruangan['nama_ruangan']; ?>" required class="form-input" placeholder="Masukkan nama ruangan">
                    </div>

                    <div class="form-group">
                        <label for="lantai">Lantai</label>
                        <input type="number" id="lantai" name="lantai" value="<?php echo isset($_POST['lantai']) ? $_POST['lantai'] : $ruangan['lantai']; ?>" required class="form-input" placeholder="Masukkan nomor lantai" min="1" max="10">
                    </div>

                    <div class="form-group">
                        <label for="kapasitas_ruangan">Kapasitas Ruangan</label>
                        <input type="number" id="kapasitas_ruangan" name="kapasitas_ruangan" value="<?php echo isset($_POST['kapasitas_ruangan']) ? $_POST['kapasitas_ruangan'] : $ruangan['kapasitas_ruangan']; ?>" required class="form-input" placeholder="Masukkan kapasitas ruangan" min="1">
                    </div>

                    <div class="form-group">
                        <label for="status">Status Ruangan</label>
                        <select id="status" name="status" required class="form-input">
                            <option value="">Pilih Status Ruangan</option>
                            <option value="Kosong" <?php echo ((isset($_POST['status']) && $_POST['status'] == 'Kosong') || $ruangan['status'] == 'Kosong') ? 'selected' : ''; ?>>Kosong</option>
                            <option value="Sedang Dipakai" <?php echo ((isset($_POST['status']) && $_POST['status'] == 'Sedang Dipakai') || $ruangan['status'] == 'Sedang Dipakai') ? 'selected' : ''; ?>>Sedang Dipakai</option>
                            <option value="Sudah Dibooking" <?php echo ((isset($_POST['status']) && $_POST['status'] == 'Sudah Dibooking') || $ruangan['status'] == 'Sudah Dibooking') ? 'selected' : ''; ?>>Sudah Dibooking</option>
                        </select>
                    </div>

                    <div class="form-actions">
                        <button type="submit" name="ubah" class="btn-warning">
                            <i class="fas fa-save"></i>
                            Update Ruangan
                        </button>
                        <a href="index.php?page=data_ruangan" class="btn-secondary">
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
/* Styles sama dengan tambah_ruangan.php */
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