<?php
require_once '../includes/config.php';

// التحقق من تسجيل الدخول
if (!isLoggedIn()) {
    redirect(SITE_URL . '/admin/login.php');
}

$page_title = 'لوحة التحكم';
require_once __DIR__ . '/includes/admin-header.php';
?>

<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5 class="card-title">شرائح السلايدر</h5>
                <p class="card-text display-6">
                    <?php
                    $stmt = $pdo->query("SELECT COUNT(*) FROM slider");
                    echo $stmt->fetchColumn();
                    ?>
                </p>
                <a href="slider.php" class="text-white">إدارة السلايدر <i class="fas fa-arrow-left"></i></a>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5 class="card-title">الخدمات</h5>
                <p class="card-text display-6">
                    <?php
                    $stmt = $pdo->query("SELECT COUNT(*) FROM services");
                    echo $stmt->fetchColumn();
                    ?>
                </p>
                <a href="services.php" class="text-white">إدارة الخدمات <i class="fas fa-arrow-left"></i></a>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h5 class="card-title">المشاريع</h5>
                <p class="card-text display-6">
                    <?php
                    $stmt = $pdo->query("SELECT COUNT(*) FROM projects");
                    echo $stmt->fetchColumn();
                    ?>
                </p>
                <a href="projects.php" class="text-white">إدارة المشاريع <i class="fas fa-arrow-left"></i></a>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card bg-warning text-dark">
            <div class="card-body">
                <h5 class="card-title">المقالات</h5>
                <p class="card-text display-6">
                    <?php
                    $stmt = $pdo->query("SELECT COUNT(*) FROM blog");
                    echo $stmt->fetchColumn();
                    ?>
                </p>
                <a href="blog.php" class="text-dark">إدارة المقالات <i class="fas fa-arrow-left"></i></a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5>أحدث المقالات</h5>
            </div>
            <div class="card-body">
                <ul class="list-group">
                    <?php
                    $stmt = $pdo->query("SELECT id, title FROM blog ORDER BY created_at DESC LIMIT 5");
                    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    foreach ($articles as $article) {
                        echo '<li class="list-group-item d-flex justify-content-between align-items-center">
                            <a href="../blog.php?id='.$article['id'].'" target="_blank">'.$article['title'].'</a>
                            <a href="blog.php?action=edit&id='.$article['id'].'" class="btn btn-sm btn-outline-primary">تعديل</a>
                        </li>';
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5>إعدادات الموقع</h5>
            </div>
            <div class="card-body">
                <?php
                $stmt = $pdo->query("SELECT * FROM settings LIMIT 1");
                $settings = $stmt->fetch(PDO::FETCH_ASSOC);
                ?>
                
                <ul class="list-group">
                    <li class="list-group-item"><strong>عنوان الموقع:</strong> <?php echo $settings['site_title']; ?></li>
                    <li class="list-group-item"><strong>البريد الإلكتروني:</strong> <?php echo $settings['contact_email']; ?></li>
                    <li class="list-group-item"><strong>الهاتف:</strong> <?php echo $settings['contact_phone']; ?></li>
                </ul>
                
                <div class="mt-3">
                    <a href="settings.php" class="btn btn-primary">تعديل الإعدادات</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/admin-footer.php';
?>