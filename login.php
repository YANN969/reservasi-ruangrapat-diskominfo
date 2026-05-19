<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'config/koneksi.php';

$base_path = '/kominfo-reservasi';
$error = '';

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = md5($_POST['password']); 
    
    $query = "SELECT * FROM tbpetugas WHERE email='$email' AND passwd='$password'";
    $result = mysqli_query($koneksi, $query);
    
    if ($result && mysqli_num_rows($result) == 1) { 
        $user = mysqli_fetch_assoc($result);
        
        $_SESSION['user_id'] = $user['email'];
        $_SESSION['username'] = $user['username'];
        
        header("Location: index.php"); 
        exit();
    } else {
        $error = "Email atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Reservasi DISKOMINFO</title>
</head>
<body>
    <div class="container">
        <div class="login-section">
            <div class="logo">
                <h1>RESERVASI RUANG RAPAT DISKOMINFO</h1>
                <p>Sistem Manajemen Reservasi Ruang Rapat Digital</p>
            </div>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Masukkan email Anda" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password Anda" required>
                </div>
                
                <button type="submit" name="login" class="btn-login">Masuk</button>
                
                <?php if (!empty($error)): ?>
                    <div class="error-message">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
            </form>
        </div>
        
        <div class="graphic-section">
            <div class="floating-elements">
                <div class="floating-element"></div>
                <div class="floating-element"></div>
                <div class="floating-element"></div>
            </div>
            
            <div class="photo-container">
                <img src="images/diskominfohd.jpg" alt="DISKOMINFO" class="brand-photo">
            </div>
            
            <div class="graphic-content">
                <h2>Selamat Datang</h2>
                <p><span class="pulse"></span> Sistem Reservasi Digital</p>
                <p>DISKOMINFO</p>
            </div>
        </div>
    </div>
</body>
</html>
<style>
/* VARIABEL WARNA */
:root {
    --primary: #3498db;
    --secondary: #2ecc71;
    --accent: #e74c3c;
    --light: #f8f9fa;
    --dark: #343a40;
    --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

/* RESET DAN STYLE DASAR */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

body {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    overflow: hidden;
}

/* CONTAINER UTAMA */
.container {
    display: flex;
    width: 900px;
    height: 550px;
    background: white;
    border-radius: 20px;
    box-shadow: var(--shadow);
    overflow: hidden;
    position: relative;
}

/* BAGIAN LOGIN */
.login-section {
    flex: 1;
    padding: 40px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    position: relative;
    z-index: 2;
}

/* BAGIAN GRAFIS */
.graphic-section {
    flex: 1;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* LOGO DAN JUDUL */
.logo {
    text-align: center;
    margin-bottom: 30px;
}

.logo h1 {
    color: var(--primary);
    font-size: 28px;
    margin-bottom: 5px;
}

.logo p {
    color: var(--dark);
    font-size: 14px;
}

/* FORM ELEMENTS */
.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    color: var(--dark);
    font-weight: 500;
}

.form-control {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 16px;
    transition: all 0.3s;
}

.form-control:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
    outline: none;
}

.btn-login {
    width: 100%;
    padding: 12px;
    background: var(--primary);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    margin-top: 10px;
}

.btn-login:hover {
    background: #2980b9;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.error-message {
    color: var(--accent);
    text-align: center;
    margin-top: 15px;
    padding: 10px;
    background: rgba(231, 76, 60, 0.1);
    border-radius: 5px;
    animation: shake 0.5s ease-in-out;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}

/* ANIMASI ELEMEN MELAYANG */
.floating-elements {
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
}

.floating-element {
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    animation: float 15s infinite linear;
}

.floating-element:nth-child(1) {
    width: 80px;
    height: 80px;
    top: 10%;
    left: 20%;
    animation-delay: 0s;
}

.floating-element:nth-child(2) {
    width: 120px;
    height: 120px;
    top: 60%;
    left: 70%;
    animation-delay: -5s;
}

.floating-element:nth-child(3) {
    width: 60px;
    height: 60px;
    top: 30%;
    left: 80%;
    animation-delay: -10s;
}

@keyframes float {
    0% {
        transform: translate(0, 0) rotate(0deg);
    }
    25% {
        transform: translate(20px, 20px) rotate(90deg);
    }
    50% {
        transform: translate(0, 40px) rotate(180deg);
    }
    75% {
        transform: translate(-20px, 20px) rotate(270deg);
    }
    100% {
        transform: translate(0, 0) rotate(360deg);
    }
}

/* FOTO BRAND */
.photo-container {
    position: relative;
    z-index: 2;
    text-align: center;
    margin-bottom: 20px;
}

.brand-photo {
    max-width: 200px;
    max-height: 120px;
    width: auto;
    height: auto;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    border: 3px solid white;
    transition: transform 0.3s ease;
}

.brand-photo:hover {
    transform: scale(1.05);
}

/* KONTEN GRAFIS */
.graphic-content {
    position: absolute;
    bottom: 30px;
    left: 50%;
    transform: translateX(-50%);
    text-align: center;
    color: white;
    z-index: 2;
    width: 100%;
}

.graphic-content h2 {
    font-size: 24px;
    margin-bottom: 10px;
}

.graphic-content p {
    font-size: 14px;
    opacity: 0.9;
}

.pulse {
    display: inline-block;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: white;
    margin-right: 8px;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        transform: scale(0.9);
        box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.7);
    }
    70% {
        transform: scale(1);
        box-shadow: 0 0 0 10px rgba(255, 255, 255, 0);
    }
    100% {
        transform: scale(0.9);
        box-shadow: 0 0 0 0 rgba(255, 255, 255, 0);
    }
}

/* RESPONSIVE DESIGN */
@media (max-width: 768px) {
    .container {
        flex-direction: column;
        width: 90%;
        height: auto;
    }
    
    .graphic-section {
        height: 200px;
        padding: 20px;
    }
    
    .brand-photo {
        max-width: 150px;
        max-height: 80px;
    }
    
    .graphic-content {
        position: relative;
        bottom: auto;
        transform: none;
        margin-top: 15px;
    }
    
    .graphic-content h2 {
        font-size: 20px;
    }
}
</style>