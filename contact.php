<?php
require_once 'includes/config.php';
require_once 'includes/header.php';

$page_title = 'تواصل معنا';

// معالجة إرسال النموذج
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $subject = sanitizeInput($_POST['subject']);
    $message = sanitizeInput($_POST['message']);
    
    // التحقق من صحة البيانات
    $errors = [];
    
    if (empty($name)) {
        $errors[] = 'الاسم مطلوب';
    }
    
    if (empty($email)) {
        $errors[] = 'البريد الإلكتروني مطلوب';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'البريد الإلكتروني غير صالح';
    }
    
    if (empty($subject)) {
        $errors[] = 'الموضوع مطلوب';
    }
    
    if (empty($message)) {
        $errors[] = 'الرسالة مطلوبة';
    }
    
    // إذا لم تكن هناك أخطاء، أرسل البريد الإلكتروني
    if (empty($errors)) {
        // هنا يمكنك إضافة كود إرسال البريد الإلكتروني
        
        $success_message = 'شكراً لك، تم استلام رسالتك وسيتم الرد عليك قريباً.';
    }
}
?>

<section class="mb-5 mt-4">
    <div class="container">
        <h1 class="text-center mb-5 text-gradient">تواصل معنا</h1>
        
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card h-100 hover-scale">
                    <div class="card-body">
                        <?php
                        // جلب إعدادات الموقع
                        $stmt = $pdo->query("SELECT * FROM settings LIMIT 1");
                        $settings = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        // عرض شعار الموقع إذا كان موجوداً
                        if (!empty($settings['site_logo'])) {
                            echo '<div class="text-center mb-4">';
                            echo '<img src="'.SITE_URL.'/assets/images/'.$settings['site_logo'].'" alt="شعار الموقع" class="img-fluid rounded shadow" style="max-height: 120px;">';
                            echo '</div>';
                        }
                        ?>
                        
                        <h3 class="card-title text-primary mb-4">معلومات التواصل</h3>
                        
                        <ul class="list-unstyled">
                            <li class="mb-3 d-flex align-items-center">
                                <i class="fas fa-envelope me-3 text-primary"></i>
                                <span><?php echo $settings['contact_email']; ?></span>
                            </li>
                            <li class="mb-3 d-flex align-items-center">
                                <i class="fas fa-phone me-3 text-primary"></i>
                                <span><?php echo $settings['contact_phone']; ?></span>
                            </li>
                            <li class="mb-3 d-flex align-items-center">
                                <i class="fas fa-map-marker-alt me-3 text-primary"></i>
                                <span><?php echo $settings['address']; ?></span>
                            </li>
                        </ul>
                        
                        <div class="mt-5">
                            <h5 class="text-primary mb-3">تابعنا على</h5>
                            <div class="social-links">
                                <a href="<?php echo $settings['facebook_url']; ?>" class="btn btn-outline-primary btn-sm rounded-circle me-2">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="<?php echo $settings['twitter_url']; ?>" class="btn btn-outline-info btn-sm rounded-circle me-2">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="<?php echo $settings['instagram_url']; ?>" class="btn btn-outline-danger btn-sm rounded-circle me-2">
                                    <i class="fab fa-instagram"></i>
                                </a>
                                <a href="<?php echo $settings['linkedin_url']; ?>" class="btn btn-outline-primary btn-sm rounded-circle">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6 mb-4">
                <div class="card h-100 hover-scale">
                    <div class="card-body">
                        <h3 class="card-title text-primary mb-4">أرسل رسالة</h3>
                        
                        <?php
                        // عرض رسائل الخطأ
                        if (!empty($errors)) {
                            echo '<div class="alert alert-danger fade-in">';
                            foreach ($errors as $error) {
                                echo '<p class="mb-1"><i class="fas fa-exclamation-circle me-2"></i>'.$error.'</p>';
                            }
                            echo '</div>';
                        }
                        
                        // عرض رسالة النجاح
                        if (isset($success_message)) {
                            echo '<div class="alert alert-success fade-in"><i class="fas fa-check-circle me-2"></i>'.$success_message.'</div>';
                        }
                        ?>
                        
                        <form action="contact.php" method="post" class="needs-validation" novalidate>
                            <div class="mb-4">
                                <label for="name" class="form-label fw-bold">الاسم</label>
                                <input type="text" class="form-control shadow-sm" id="name" name="name" required>
                                <div class="invalid-feedback">
                                    الرجاء إدخال الاسم
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="email" class="form-label fw-bold">البريد الإلكتروني</label>
                                <input type="email" class="form-control shadow-sm" id="email" name="email" required>
                                <div class="invalid-feedback">
                                    الرجاء إدخال بريد إلكتروني صحيح
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="subject" class="form-label fw-bold">الموضوع</label>
                                <input type="text" class="form-control shadow-sm" id="subject" name="subject" required>
                                <div class="invalid-feedback">
                                    الرجاء إدخال الموضوع
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="message" class="form-label fw-bold">الرسالة</label>
                                <textarea class="form-control shadow-sm" id="message" name="message" rows="5" required></textarea>
                                <div class="invalid-feedback">
                                    الرجاء إدخال الرسالة
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-hover-gradient w-100 py-2">
                                <i class="fas fa-paper-plane me-2"></i> إرسال الرسالة
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
require_once 'includes/footer.php';
?>