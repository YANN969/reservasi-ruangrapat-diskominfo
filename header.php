<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Reservasi Ruang Rapat - DISKOMINFO</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- HEADER -->
    <header class="main-header">
        <div class="header-content">
            <div class="header-logo">
                <div class="logo-image-wrapper">
                    <img src="images/diskominfohd.jpg" alt="DISKOMINFO" class="logo-img">
                </div>
                <div class="logo-text">
                    <h1>RESERVASI RUANG RAPAT</h1>
                    <p>Sistem Manajemen Ruang Rapat Digital</p>
                </div>
            </div>
           
        </div>
    </header>

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <!-- User Profile yang bisa diklik -->
        <a href="index.php?page=edit_profile" class="profile-link">
            <div class="user-profile">
                <div class="profile-avatar">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="profile-info">
                    <h3><?php echo $_SESSION['username'] ?? 'Admin Ruangan'; ?></h3>
                
                </div>
            </div>
        </a>

        <!-- Navigation Menu -->
        <nav class="sidebar-nav">
            <ul class="nav-menu">
                <!-- Menu Dashboard -->
                <li class="nav-item">
                    <a href="index.php?page=dashboard" class="nav-link">
                        <div class="nav-icon">
                            <i class="fas fa-home"></i>
                        </div>
                        <span class="nav-text">Dashboard</span>
                        <div class="nav-arrow">
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </a>
                </li>

                <!-- Menu Data Ruangan -->
                <li class="nav-item">
                    <a href="index.php?page=data_ruangan" class="nav-link">
                        <div class="nav-icon">
                            <i class="fas fa-door-open"></i>
                        </div>
                        <span class="nav-text">Data Ruangan</span>
                        <div class="nav-arrow">
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </a>
                </li>

                <!-- Menu Data Booking -->
                <li class="nav-item">
                    <a href="index.php?page=data_booking" class="nav-link">
                        <div class="nav-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <span class="nav-text">Data Booking</span>
                        <div class="nav-arrow">
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Logout Button -->
        <div class="sidebar-footer">
            <a href="logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                <span>Keluar</span>
            </a>
        </div>
    </aside>
</body>
</html>

<style>
/* RESET & VARIABLES */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

:root {
    --primary: #3498db;
    --primary-light: #5dade2;
    --secondary: #2c3e50;
    --secondary-light: #34495e;
    --accent: #e74c3c;
    --light: #f8f9fa;
    --dark: #2c3e50;
    --sidebar-width: 240px;
    --header-height: 70px;
}

body {
    background: #f8f9fa;
    overflow-x: hidden;
}

/* HEADER STYLES */
.main-header {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    padding: 0 30px;
    position: fixed;
    top: 0;
    left: var(--sidebar-width);
    right: 0;
    z-index: 1000;
    height: var(--header-height);
    display: flex;
    align-items: center;
    border-bottom: 1px solid rgba(52, 152, 219, 0.1);
    backdrop-filter: blur(10px);
}

.header-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    max-width: 1200px;
    margin: 0 auto;
}

.header-logo {
    display: flex;
    align-items: center;
    gap: 15px;
}

.logo-image-wrapper {
    padding: 5px;
    background: linear-gradient(135deg, var(--primary), var(--primary-light));
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
}

.logo-img {
    height: 40px;
    width: auto;
    border-radius: 8px;
    display: block;
}

.logo-text {
    text-align: left;
}

.logo-text h1 {
    color: var(--dark);
    font-size: 20px;
    font-weight: 800;
    margin: 0;
    line-height: 1.2;
    letter-spacing: 0.5px;
    background: linear-gradient(135deg, var(--dark), var(--primary));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.logo-text p {
    color: #7f8c8d;
    font-size: 11px;
    margin: 0;
    font-weight: 500;
    letter-spacing: 0.3px;
}

/* Header Decoration */
.header-decoration {
    display: flex;
    gap: 8px;
}

.decoration-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary), var(--primary-light));
    opacity: 0.7;
    animation: pulse-dot 2s infinite;
}

.decoration-dot:nth-child(2) {
    animation-delay: 0.3s;
    background: linear-gradient(135deg, #2ecc71, #27ae60);
}

.decoration-dot:nth-child(3) {
    animation-delay: 0.6s;
    background: linear-gradient(135deg, #e74c3c, #c0392b);
}

@keyframes pulse-dot {
    0%, 100% {
        transform: scale(1);
        opacity: 0.7;
    }
    50% {
        transform: scale(1.2);
        opacity: 1;
    }
}

/* SIDEBAR STYLES */
.sidebar {
    width: var(--sidebar-width);
    height: 100vh;
    background: linear-gradient(180deg, var(--secondary) 0%, var(--secondary-light) 100%);
    position: fixed;
    left: 0;
    top: 0;
    padding: 20px 0;
    display: flex;
    flex-direction: column;
    box-shadow: 4px 0 20px rgba(0, 0, 0, 0.15);
    z-index: 999;
    overflow-y: auto;
    border-right: 1px solid rgba(255, 255, 255, 0.1);
}

/* PROFILE LINK */
.profile-link {
    text-decoration: none;
    display: block;
}

/* USER PROFILE */
.user-profile {
    padding: 25px 20px;
    text-align: center;
    margin-bottom: 20px;
    cursor: pointer;
    transition: all 0.3s ease;
    border-radius: 16px;
    margin: 10px 15px 20px 15px;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    position: relative;
    overflow: hidden;
}

.user-profile::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
    transition: left 0.5s ease;
}

.user-profile:hover::before {
    left: 100%;
}

.user-profile:hover {
    background: rgba(255, 255, 255, 0.08);
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.2);
}

.profile-avatar {
    font-size: 50px;
    color: var(--primary-light);
    margin-bottom: 12px;
    transition: all 0.3s ease;
    position: relative;
    z-index: 2;
}

.user-profile:hover .profile-avatar {
    transform: scale(1.1);
    color: #2ecc71;
}

.profile-info {
    color: white;
    position: relative;
    z-index: 2;
}

.profile-info h3 {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 5px;
}

.profile-info span {
    font-size: 12px;
    color: #bdc3c7;
    font-weight: 400;
}

/* NAVIGATION MENU */
.sidebar-nav {
    flex: 1;
    padding: 0 15px;
}

.nav-menu {
    list-style: none;
}

.nav-item {
    margin-bottom: 8px;
}

.nav-link {
    display: flex;
    align-items: center;
    padding: 14px 16px;
    color: #ecf0f1;
    text-decoration: none;
    border-radius: 12px;
    transition: all 0.3s ease;
    position: relative;
    border: 1px solid transparent;
}

.nav-link:hover {
    background: rgba(52, 152, 219, 0.15);
    color: white;
    transform: translateX(8px);
    border-color: rgba(52, 152, 219, 0.3);
}

.nav-link.active {
    background: var(--primary);
    color: white;
    box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
}

.nav-icon {
    width: 30px;
    font-size: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.3s ease;
}

.nav-link:hover .nav-icon {
    transform: scale(1.1);
}

.nav-text {
    font-size: 14px;
    font-weight: 500;
    flex: 1;
    margin-left: 10px;
}

.nav-arrow {
    opacity: 0;
    transform: translateX(-5px);
    transition: all 0.3s ease;
    font-size: 12px;
}

.nav-link:hover .nav-arrow {
    opacity: 1;
    transform: translateX(0);
}

/* SIDEBAR FOOTER */
.sidebar-footer {
    padding: 20px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.logout-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 12px 16px;
    color: var(--accent);
    text-decoration: none;
    border-radius: 10px;
    transition: all 0.3s ease;
    background: rgba(231, 76, 60, 0.1);
    gap: 10px;
    font-size: 14px;
    font-weight: 500;
    border: 1px solid transparent;
}

.logout-btn:hover {
    background: var(--accent);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(231, 76, 60, 0.3);
    border-color: rgba(231, 76, 60, 0.3);
}

/* ==================== */
/* MAIN CONTENT STYLES */
/* ==================== */

.main-content {
    margin-left: var(--sidebar-width);
    margin-top: var(--header-height);
    padding: 30px;
    min-height: calc(100vh - var(--header-height));
    background: #f8f9fa;
    position: relative;
    z-index: 1;
}

.content-wrapper {
    max-width: 1200px;
    margin: 0 auto;
}

/* Content Card Styles */
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

/* Button Styles */
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
    cursor: pointer;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #2980b9, #21618c);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(52, 152, 219, 0.4);
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
    cursor: pointer;
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
    cursor: pointer;
}

.btn-danger:hover {
    background: linear-gradient(135deg, #c0392b, #a93226);
    color: white;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(231, 76, 60, 0.3);
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

/* Table Styles */
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

/* Badge Styles */
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

.badge-available {
    background: linear-gradient(135deg, #27ae60, #219a52);
}

.badge-occupied {
    background: linear-gradient(135deg, #e74c3c, #c0392b);
}

.badge-maintenance {
    background: linear-gradient(135deg, #f39c12, #e67e22);
}

/* Empty State */
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

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: 8px;
    flex-wrap: nowrap;
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

.form-actions {
    display: flex;
    gap: 15px;
    margin-top: 30px;
}

/* RESPONSIVE */
@media (max-width: 768px) {
    .main-header {
        left: 0;
        padding: 0 20px;
        height: 60px;
    }
    
    .main-content {
        margin-left: 0;
        margin-top: 60px;
        padding: 20px;
    }
    
    .header-logo {
        gap: 10px;
    }
    
    .logo-img {
        height: 35px;
    }
    
    .logo-text h1 {
        font-size: 16px;
    }
    
    .logo-text p {
        font-size: 10px;
    }
    
    .header-decoration {
        display: none;
    }
    
    .sidebar {
        width: 220px;
        transform: translateX(-100%);
        transition: transform 0.3s ease;
    }
    
    .sidebar.active {
        transform: translateX(0);
    }
    
    .content-card {
        padding: 20px;
    }
    
    .card-header {
        flex-direction: column;
        align-items: flex-start;
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
}

@media (max-width: 480px) {
    .sidebar {
        width: 200px;
    }
    
    .main-content {
        padding: 15px;
    }
    
    .user-profile {
        padding: 20px 15px;
    }
    
    .profile-avatar {
        font-size: 45px;
    }
    
    .nav-link {
        padding: 12px 14px;
    }
    
    .nav-text {
        font-size: 13px;
    }
    
    .empty-state {
        padding: 40px 20px;
    }
    
    .empty-icon {
        font-size: 48px;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .btn-primary, .btn-secondary {
        width: 100%;
        justify-content: center;
    }
    
    .data-form {
        padding: 20px;
    }
}
</style>