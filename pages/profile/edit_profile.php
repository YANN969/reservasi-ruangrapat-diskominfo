<?php
include 'config/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['user_id'];
$error = '';
$success = '';

// Ambil data user saat ini
$query = "SELECT * FROM tbpetugas WHERE email='$email'";
$result = mysqli_query($koneksi, $query);
$user = mysqli_fetch_assoc($result);

if (isset($_POST['update'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    
    // Validasi
    if (empty($username)) {
        $error = "Username tidak boleh kosong!";
    } else {
        // Jika ingin ganti password
        if (!empty($current_password) || !empty($new_password)) {
            if (empty($current_password)) {
                $error = "Password saat ini harus diisi!";
            } elseif (empty($new_password)) {
                $error = "Password baru harus diisi!";
            } elseif (md5($current_password) !== $user['passwd']) {
                $error = "Password saat ini salah!";
            } else {
                // Update password
                $hashed_password = md5($new_password);
                $update_query = "UPDATE tbpetugas SET username='$username', passwd='$hashed_password' WHERE email='$email'";
            }
        } else {
            // Hanya update username
            $update_query = "UPDATE tbpetugas SET username='$username' WHERE email='$email'";
        }
        
        if (empty($error)) {
            if (mysqli_query($koneksi, $update_query)) {
                $_SESSION['username'] = $username;
                $success = "Profile berhasil diperbarui!";
                // Refresh data user
                $result = mysqli_query($koneksi, $query);
                $user = mysqli_fetch_assoc($result);
            } else {
                $error = "Error: " . mysqli_error($koneksi);
            }
        }
    }
}
?>

<div class="main-content">
    <div class="content-wrapper">
        <!-- Page Header -->
        <div class="page-header-improved">
            <div class="header-content-center">
                <div class="header-icon-wrapper">
                    <div class="header-icon">
                        <i class="fas fa-user-edit"></i>
                    </div>
                </div>
                <div class="header-text-center">
                    <h1>Edit Profile</h1>
                    <p>Kelola informasi akun dan keamanan Anda</p>
                </div>
            </div>
        </div>

        <div class="content-card">
            <!-- User Info Card -->
            <div class="user-info-card">
                <div class="user-avatar">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="user-details">
                    <h3><?php echo htmlspecialchars($user['username']); ?></h3>
                    <p><?php echo htmlspecialchars($user['email']); ?></p>
                </div>
            </div>

            <?php if ($error): ?>
                <div class="alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <div class="form-container">
                <form method="POST" class="data-form">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly class="form-input">
                        <small class="form-help">Email tidak dapat diubah</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required class="form-input" placeholder="Masukkan username baru">
                    </div>

                    <div class="password-section">
                        <h4><i class="fas fa-lock"></i> Ganti Password (Opsional)</h4>
                        
                        <div class="form-group">
                            <label for="current_password">Password Saat Ini</label>
                            <input type="password" id="current_password" name="current_password" class="form-input" placeholder="Masukkan password saat ini">
                        </div>
                        
                        <div class="form-group">
                            <label for="new_password">Password Baru</label>
                            <input type="password" id="new_password" name="new_password" class="form-input" placeholder="Masukkan password baru">
                            <div class="password-strength" id="password-strength"></div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" name="update" class="btn-success">
                            <i class="fas fa-save"></i>
                            Update Profile
                        </button>
                        <a href="index.php?page=dashboard" class="btn-secondary">
                            <i class="fas fa-times"></i>
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Password strength indicator
    document.getElementById('new_password').addEventListener('input', function() {
        const password = this.value;
        const strengthText = document.getElementById('password-strength');
        
        if (password.length === 0) {
            strengthText.textContent = '';
            strengthText.className = 'password-strength';
            return;
        }
        
        let strength = 0;
        if (password.length >= 8) strength++;
        if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
        if (password.match(/\d/)) strength++;
        if (password.match(/[^a-zA-Z\d]/)) strength++;
        
        if (strength <= 1) {
            strengthText.textContent = 'Kekuatan password: Lemah';
            strengthText.className = 'password-strength strength-weak';
        } else if (strength <= 3) {
            strengthText.textContent = 'Kekuatan password: Sedang';
            strengthText.className = 'password-strength strength-medium';
        } else {
            strengthText.textContent = 'Kekuatan password: Kuat';
            strengthText.className = 'password-strength strength-strong';
        }
    });
</script>

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

/* User Info Card */
.user-info-card {
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    border: 1px solid #e9ecef;
    border-radius: 12px;
    padding: 30px;
    margin-bottom: 30px;
    display: flex;
    align-items: center;
    gap: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.user-avatar {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #3498db, #2980b9);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    color: white;
    box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
}

.user-details h3 {
    color: #2c3e50;
    font-size: 24px;
    font-weight: 700;
    margin: 0 0 5px 0;
}

.user-details p {
    color: #6c757d;
    margin: 0;
    font-size: 16px;
}

/* Form Styles */
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

.alert-success {
    background: rgba(39, 174, 96, 0.1);
    border: 1px solid #27ae60;
    color: #219a52;
    padding: 12px 16px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 500;
}

/* Password Section */
.password-section {
    background: #e8f5e8;
    border: 1px solid #27ae60;
    border-radius: 8px;
    padding: 20px;
    margin: 30px 0;
}

.password-section h4 {
    color: #2c3e50;
    margin-bottom: 20px;
    font-size: 16px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
}

.password-strength {
    margin-top: 8px;
    font-size: 12px;
    font-weight: 500;
}

.strength-weak {
    color: #e74c3c;
}

.strength-medium {
    color: #f39c12;
}

.strength-strong {
    color: #27ae60;
}

/* Form Actions */
.form-actions {
    display: flex;
    gap: 15px;
    margin-top: 30px;
}

.btn-success {
    background: linear-gradient(135deg, #27ae60, #219a52);
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
    box-shadow: 0 2px 10px rgba(39, 174, 96, 0.3);
}

.btn-success:hover {
    background: linear-gradient(135deg, #219a52, #1e8449);
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(39, 174, 96, 0.4);
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
    
    .user-info-card {
        flex-direction: column;
        text-align: center;
        padding: 25px;
    }
    
    .user-avatar {
        width: 70px;
        height: 70px;
        font-size: 28px;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .btn-success, .btn-secondary {
        width: 100%;
        justify-content: center;
    }
    
    .data-form {
        padding: 20px;
    }
    
    .password-section {
        padding: 15px;
    }
}

@media (max-width: 480px) {
    .main-content {
        padding: 15px;
    }
    
    .header-text-center h1 {
        font-size: 22px;
    }
    
    .user-details h3 {
        font-size: 20px;
    }
    
    .user-avatar {
        width: 60px;
        height: 60px;
        font-size: 24px;
    }
}
</style>