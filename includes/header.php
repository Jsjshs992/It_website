<?php
// includes/header.php
require_once 'config.php';

// جلب إعدادات الموقع من قاعدة البيانات
try {
    $stmt = $pdo->query("SELECT * FROM settings LIMIT 1");
    $settings = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // تعيين القيم الافتراضية إذا لم تكن الإعدادات موجودة
    $site_title = isset($settings['site_title']) ? $settings['site_title'] : SITE_TITLE;
    $site_logo = isset($settings['site_logo']) ? $settings['site_logo'] : '';
    $site_description = isset($settings['site_description']) ? $settings['site_description'] : '';
} catch (PDOException $e) {
    // في حالة حدوث خطأ، استخدام القيم الافتراضية
    $site_title = SITE_TITLE;
    $site_logo = '';
    $site_description = '';
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' | ' . $site_title : $site_title; ?></title>
    
    <!-- Meta Description -->
    <meta name="description" content="<?php echo htmlspecialchars($site_description); ?>">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;900&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">

    <!-- Bootstrap RTL -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-rtl@5.3.0/dist/css/bootstrap-rtl.min.css" rel="stylesheet">
    
    <!-- CSS المخصص -->
    <link href="<?php echo SITE_URL; ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- شريط التنقل الرئيسي المحسّن -->
    <header>
        <nav class="navbar navbar-expand-lg navbar-custom shadow-sm fixed-top">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="<?php echo SITE_URL; ?>">
                    <?php if (!empty($site_logo)): ?>
                        <img src="<?php echo SITE_URL; ?>/assets/images/<?php echo $site_logo; ?>" alt="<?php echo $site_title; ?>" class="me-2" style="height: 40px;">
                    <?php endif; ?>
                    <span class="fw-bold fs-3 text-white"><?php echo $site_title; ?></span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="تبديل التنقل">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="mainNav">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link px-3 py-2 rounded <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>">
                                <i class="fas fa-home me-1"></i> الرئيسية
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-3 py-2 rounded <?php echo (basename($_SERVER['PHP_SELF']) == 'services.php') ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/services.php">
                                <i class="fas fa-concierge-bell me-1"></i> الخدمات
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-3 py-2 rounded <?php echo (basename($_SERVER['PHP_SELF']) == 'projects.php') ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/projects.php">
                                <i class="fas fa-project-diagram me-1"></i> المشاريع
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-3 py-2 rounded <?php echo (basename($_SERVER['PHP_SELF']) == 'blog.php') ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/blog.php">
                                <i class="fas fa-blog me-1"></i> المدونة
                            </a>
                        </li>
                    </ul>
                    <div class="d-flex ms-3">
                        <a href="<?php echo SITE_URL; ?>/contact.php" class="btn btn-light btn-hover-gradient rounded-pill px-4 py-2">
                            <i class="fas fa-envelope me-1"></i> تواصل معنا
                        </a>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <main class="container py-5 mt-5">