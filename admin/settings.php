<?php
require_once '../includes/config.php';

// التحقق من تسجيل الدخول
if (!isLoggedIn()) {
    redirect(SITE_URL . '/admin/login.php');
}

$page_title = 'إعدادات الموقع';
require_once __DIR__ . '/includes/admin-header.php';

// معالجة تحديث الإعدادات
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $site_title = sanitizeInput($_POST['site_title']);
    $site_description = sanitizeInput($_POST['site_description']);
    $contact_email = sanitizeInput($_POST['contact_email']);
    $contact_phone = sanitizeInput($_POST['contact_phone']);
    $address = sanitizeInput($_POST['address']);
    $facebook_url = sanitizeInput($_POST['facebook_url']);
    $twitter_url = sanitizeInput($_POST['twitter_url']);
    $instagram_url = sanitizeInput($_POST['instagram_url']);
    $linkedin_url = sanitizeInput($_POST['linkedin_url']);
    
    // معالجة تحميل الشعار
    $site_logo = '';
    if (isset($_FILES['site_logo']) && $_FILES['site_logo']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../assets/images/';
        $file_name = basename($_FILES['site_logo']['name']);
        $file_path = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['site_logo']['tmp_name'], $file_path)) {
            // حذف الشعار القديم إذا كان موجوداً
            $stmt = $pdo->query("SELECT site_logo FROM settings LIMIT 1");
            $old_logo = $stmt->fetchColumn();
            
            if ($old_logo && file_exists($upload_dir . $old_logo)) {
                unlink($upload_dir . $old_logo);
            }
            
            $site_logo = $file_name;
        }
    }
    
    // التحقق مما إذا كانت هناك إعدادات موجودة بالفعل
    $stmt = $pdo->query("SELECT COUNT(*) FROM settings");
    $settings_count = $stmt->fetchColumn();
    
    if ($settings_count > 0) {
        // تحديث الإعدادات الموجودة
        if (!empty($site_logo)) {
            $stmt = $pdo->prepare("UPDATE settings SET site_title = ?, site_description = ?, site_logo = ?, 
                                  contact_email = ?, contact_phone = ?, address = ?, facebook_url = ?, 
                                  twitter_url = ?, instagram_url = ?, linkedin_url = ?");
            $stmt->execute([$site_title, $site_description, $site_logo, $contact_email, $contact_phone, 
                          $address, $facebook_url, $twitter_url, $instagram_url, $linkedin_url]);
        } else {
            $stmt = $pdo->prepare("UPDATE settings SET site_title = ?, site_description = ?, 
                                  contact_email = ?, contact_phone = ?, address = ?, facebook_url = ?, 
                                  twitter_url = ?, instagram_url = ?, linkedin_url = ?");
            $stmt->execute([$site_title, $site_description, $contact_email, $contact_phone, 
                          $address, $facebook_url, $twitter_url, $instagram_url, $linkedin_url]);
        }
    } else {
        // إضافة إعدادات جديدة
        $stmt = $pdo->prepare("INSERT INTO settings (site_title, site_description, site_logo, 
                              contact_email, contact_phone, address, facebook_url, twitter_url, 
                              instagram_url, linkedin_url) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$site_title, $site_description, $site_logo, $contact_email, $contact_phone, 
                       $address, $facebook_url, $twitter_url, $instagram_url, $linkedin_url]);
    }
    
    $_SESSION['success_message'] = 'تم تحديث إعدادات الموقع بنجاح';
    redirect(SITE_URL . '/admin/settings.php');
}

// جلب إعدادات الموقع
$stmt = $pdo->query("SELECT * FROM settings LIMIT 1");
$settings = $stmt->fetch(PDO::FETCH_ASSOC);

// عرض رسالة النجاح إذا كانت موجودة
if (isset($_SESSION['success_message'])) {
    echo displayMessage('success', $_SESSION['success_message']);
    unset($_SESSION['success_message']);
}
?>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5>إعدادات الموقع</h5>
            </div>
            <div class="card-body">
                <form method="post" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="site_title" class="form-label">عنوان الموقع</label>
                            <input type="text" class="form-control" id="site_title" name="site_title" 
                                   value="<?php echo isset($settings['site_title']) ? $settings['site_title'] : ''; ?>" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="site_logo" class="form-label">شعار الموقع</label>
                            <input type="file" class="form-control" id="site_logo" name="site_logo">
                            
                            <?php if (isset($settings['site_logo']) && !empty($settings['site_logo'])): ?>
                                <div class="mt-2">
                                    <img src="../assets/images/<?php echo $settings['site_logo']; ?>" width="100" alt="Current Logo">
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="site_description" class="form-label">وصف الموقع</label>
                        <textarea class="form-control" id="site_description" name="site_description" rows="3"><?php echo isset($settings['site_description']) ? $settings['site_description'] : ''; ?></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="contact_email" class="form-label">البريد الإلكتروني للتواصل</label>
                            <input type="email" class="form-control" id="contact_email" name="contact_email" 
                                   value="<?php echo isset($settings['contact_email']) ? $settings['contact_email'] : ''; ?>" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="contact_phone" class="form-label">هاتف التواصل</label>
                            <input type="text" class="form-control" id="contact_phone" name="contact_phone" 
                                   value="<?php echo isset($settings['contact_phone']) ? $settings['contact_phone'] : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="address" class="form-label">العنوان</label>
                        <textarea class="form-control" id="address" name="address" rows="2"><?php echo isset($settings['address']) ? $settings['address'] : ''; ?></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="facebook_url" class="form-label">رابط فيسبوك</label>
                            <input type="url" class="form-control" id="facebook_url" name="facebook_url" 
                                   value="<?php echo isset($settings['facebook_url']) ? $settings['facebook_url'] : ''; ?>">
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label for="twitter_url" class="form-label">رابط تويتر</label>
                            <input type="url" class="form-control" id="twitter_url" name="twitter_url" 
                                   value="<?php echo isset($settings['twitter_url']) ? $settings['twitter_url'] : ''; ?>">
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label for="instagram_url" class="form-label">رابط إنستجرام</label>
                            <input type="url" class="form-control" id="instagram_url" name="instagram_url" 
                                   value="<?php echo isset($settings['instagram_url']) ? $settings['instagram_url'] : ''; ?>">
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label for="linkedin_url" class="form-label">رابط لينكد إن</label>
                            <input type="url" class="form-control" id="linkedin_url" name="linkedin_url" 
                                   value="<?php echo isset($settings['linkedin_url']) ? $settings['linkedin_url'] : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">حفظ الإعدادات</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/admin-footer.php';
?>