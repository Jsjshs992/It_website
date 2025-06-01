<?php
// admin/includes/admin-header.php
require_once __DIR__ . '/../../includes/config.php';

// التحقق من تسجيل الدخول
if (!isLoggedIn()) {
    header('Location: ' . SITE_URL . '/admin/login.php');
    exit;
}

$page_title = isset($page_title) ? $page_title : 'لوحة التحكم';

// تحديد الصفحة الحالية لتحديد العنصر النشط
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> | لوحة التحكم</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- خط Tajawal -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;900&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap RTL -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-rtl@5.3.0/dist/css/bootstrap-rtl.min.css" rel="stylesheet">
    
    <!-- CSS المخصص للوحة التحكم -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/admin.css?v=<?php echo time(); ?>">
    
    <style>
        /* حل مؤقت لمشكلة المحتوى في الأسفل */
        body.admin-body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .admin-main-content {
            flex: 1;
            margin-right: 280px; /* يتناسب مع عرض الشريط الجانبي */
            transition: var(--transition-slow);
        }
        @media (max-width: 992px) {
            .admin-main-content {
                margin-right: 0;
            }
        }
        
        /* إضافة أيقونة القائمة */
        .sidebar-toggle {
            cursor: pointer;
            margin-left: 15px;
            font-size: 1.25rem;
        }
        
        /* حالة الشريط الجانبي المخفي */
        body.sidebar-collapsed .admin-sidebar {
            transform: translateX(100%);
        }
        body.sidebar-collapsed .admin-main-content {
            margin-right: 0;
        }
        
        /* العنصر النشط في القائمة الجانبية */
        .admin-sidebar-link.active {
            background-color: rgba(255, 255, 255, 0.1);
            border-right: 3px solid #fff;
        }
        
        /* العنصر النشط في شريط التنقل العلوي */
        .admin-navbar-nav .nav-link.active {
            color: #fff;
            font-weight: bold;
        }
    </style>
</head>
<body class="admin-body">
    <!-- شريط التنقل العلوي -->
    <nav class="admin-navbar navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="admin-navbar-brand navbar-brand" href="<?php echo SITE_URL; ?>/admin/dashboard.php">
                <i class="fas fa-tachometer-alt me-2"></i> لوحة التحكم
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="adminNav">
                <ul class="admin-navbar-nav navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'services.php') ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/admin/services.php">
                            <i class="fas fa-concierge-bell me-2"></i> الخدمات
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'projects.php') ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/admin/projects.php">
                            <i class="fas fa-project-diagram me-2"></i> المشاريع
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'blog.php') ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/admin/blog.php">
                            <i class="fas fa-blog me-2"></i> المدونة
                        </a>
                    </li>
                </ul>
                <div class="d-flex">
                    <i class="fas fa-bars sidebar-toggle d-lg-none me-3" id="mobileSidebarToggle"></i>
                    <a href="<?php echo SITE_URL; ?>" class="admin-btn admin-btn-outline-primary me-2" target="_blank">
                        <i class="fas fa-external-link-alt me-2"></i> عرض الموقع
                    </a>
                    <a href="<?php echo SITE_URL; ?>/admin/logout.php" class="admin-btn admin-btn-danger">
                        <i class="fas fa-sign-out-alt me-2"></i> تسجيل الخروج
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- الشريط الجانبي -->
            <div class="admin-sidebar col-lg-2 d-none d-lg-block" id="desktopSidebar">
                <div class="admin-sidebar-header">
                    <h5 class="text-white">القائمة الرئيسية</h5>
                    <i class="fas fa-times sidebar-toggle" id="desktopSidebarToggle"></i>
                </div>
                <div class="admin-sidebar-menu">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="admin-sidebar-link nav-link <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/admin/dashboard.php">
                                <i class="fas fa-tachometer-alt"></i> لوحة التحكم
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="admin-sidebar-link nav-link <?php echo ($current_page == 'services.php') ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/admin/services.php">
                                <i class="fas fa-concierge-bell"></i> الخدمات
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="admin-sidebar-link nav-link <?php echo ($current_page == 'projects.php') ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/admin/projects.php">
                                <i class="fas fa-project-diagram"></i> المشاريع
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="admin-sidebar-link nav-link <?php echo ($current_page == 'blog.php') ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/admin/blog.php">
                                <i class="fas fa-blog"></i> المدونة
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="admin-sidebar-link nav-link <?php echo ($current_page == 'settings.php') ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/admin/settings.php">
                                <i class="fas fa-cog"></i> الإعدادات
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- المحتوى الرئيسي -->
            <main class="admin-main-content col-lg-10">
                <div class="container-fluid py-4">
                    <?php if (isset($_SESSION['success_message'])): ?>
                        <div class="admin-alert admin-alert-success alert-dismissible fade show">
                            <?php echo $_SESSION['success_message']; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['success_message']); ?>
                    <?php endif; ?>

                    <!-- إضافة زر لإظهار/إخفاء الشريط الجانبي على الأجهزة الكبيرة -->
                    <button class="admin-btn admin-btn-secondary mb-3 d-none d-lg-inline-block" id="toggleSidebar">
                        <i class="fas fa-bars me-2"></i> إظهار/إخفاء القائمة
                    </button>

                    <!-- إضافة شريط جانبي للجوال -->
                    <div class="admin-mobile-sidebar d-lg-none" id="mobileSidebar">
                        <div class="admin-mobile-sidebar-header">
                            <h5 class="text-white">القائمة الرئيسية</h5>
                            <i class="fas fa-times" id="closeMobileSidebar"></i>
                        </div>
                        <div class="admin-mobile-sidebar-menu">
                            <ul class="nav flex-column">
                                <li class="nav-item">
                                    <a class="admin-sidebar-link nav-link <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/admin/dashboard.php">
                                        <i class="fas fa-tachometer-alt"></i> لوحة التحكم
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="admin-sidebar-link nav-link <?php echo ($current_page == 'services.php') ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/admin/services.php">
                                        <i class="fas fa-concierge-bell"></i> الخدمات
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="admin-sidebar-link nav-link <?php echo ($current_page == 'projects.php') ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/admin/projects.php">
                                        <i class="fas fa-project-diagram"></i> المشاريع
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="admin-sidebar-link nav-link <?php echo ($current_page == 'blog.php') ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/admin/blog.php">
                                        <i class="fas fa-blog"></i> المدونة
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="admin-sidebar-link nav-link <?php echo ($current_page == 'settings.php') ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/admin/settings.php">
                                        <i class="fas fa-cog"></i> الإعدادات
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

    <script>
        // وظيفة إظهار/إخفاء الشريط الجانبي
        document.addEventListener('DOMContentLoaded', function() {
            // للشاشات الكبيرة
            const desktopSidebarToggle = document.getElementById('desktopSidebarToggle');
            const toggleSidebarBtn = document.getElementById('toggleSidebar');
            const desktopSidebar = document.getElementById('desktopSidebar');
            
            if (desktopSidebarToggle && toggleSidebarBtn && desktopSidebar) {
                const toggleDesktopSidebar = () => {
                    document.body.classList.toggle('sidebar-collapsed');
                    localStorage.setItem('sidebarCollapsed', document.body.classList.contains('sidebar-collapsed'));
                };
                
                desktopSidebarToggle.addEventListener('click', toggleDesktopSidebar);
                toggleSidebarBtn.addEventListener('click', toggleDesktopSidebar);
                
                // استعادة حالة الشريط الجانبي من localStorage
                if (localStorage.getItem('sidebarCollapsed') === 'true') {
                    document.body.classList.add('sidebar-collapsed');
                }
            }
            
            // للجوال
            const mobileSidebarToggle = document.getElementById('mobileSidebarToggle');
            const closeMobileSidebar = document.getElementById('closeMobileSidebar');
            const mobileSidebar = document.getElementById('mobileSidebar');
            
            if (mobileSidebarToggle && closeMobileSidebar && mobileSidebar) {
                mobileSidebarToggle.addEventListener('click', function() {
                    mobileSidebar.style.transform = 'translateX(0)';
                });
                
                closeMobileSidebar.addEventListener('click', function() {
                    mobileSidebar.style.transform = 'translateX(100%)';
                });
            }
        });
    </script>