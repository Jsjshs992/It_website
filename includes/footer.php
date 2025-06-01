</main>
    
    <!-- Footer -->
    <?php
    // جلب إعدادات الموقع من قاعدة البيانات
    try {
        $stmt = $pdo->query("SELECT * FROM settings LIMIT 1");
        $settings = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // تعيين القيم الافتراضية إذا لم تكن الإعدادات موجودة
        $site_title = isset($settings['site_title']) ? $settings['site_title'] : SITE_TITLE;
        $site_description = isset($settings['site_description']) ? $settings['site_description'] : 'موقع ديناميكي متكامل مع لوحة تحكم لإدارة المحتوى.';
        $contact_email = isset($settings['contact_email']) ? $settings['contact_email'] : 'info@example.com';
        $contact_phone = isset($settings['contact_phone']) ? $settings['contact_phone'] : '+1234567890';
        $address = isset($settings['address']) ? $settings['address'] : '';
        $facebook_url = isset($settings['facebook_url']) ? $settings['facebook_url'] : '';
        $twitter_url = isset($settings['twitter_url']) ? $settings['twitter_url'] : '';
        $instagram_url = isset($settings['instagram_url']) ? $settings['instagram_url'] : '';
        $linkedin_url = isset($settings['linkedin_url']) ? $settings['linkedin_url'] : '';
    } catch (PDOException $e) {
        // في حالة حدوث خطأ، استخدام القيم الافتراضية
        $site_title = SITE_TITLE;
        $site_description = 'موقع ديناميكي متكامل مع لوحة تحكم لإدارة المحتوى.';
        $contact_email = 'info@example.com';
        $contact_phone = '+1234567890';
        $address = '';
        $facebook_url = '';
        $twitter_url = '';
        $instagram_url = '';
        $linkedin_url = '';
    }
    ?>
    
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>عن الموقع</h5>
                    <p><?php echo htmlspecialchars($site_description); ?></p>
                    
                    <?php if (!empty($facebook_url) || !empty($twitter_url) || !empty($instagram_url) || !empty($linkedin_url)): ?>
                        <div class="social-links mt-3">
                            <?php if (!empty($facebook_url)): ?>
                                <a href="<?php echo htmlspecialchars($facebook_url); ?>" target="_blank" class="text-white me-2"><i class="fab fa-facebook-f"></i></a>
                            <?php endif; ?>
                            <?php if (!empty($twitter_url)): ?>
                                <a href="<?php echo htmlspecialchars($twitter_url); ?>" target="_blank" class="text-white me-2"><i class="fab fa-twitter"></i></a>
                            <?php endif; ?>
                            <?php if (!empty($instagram_url)): ?>
                                <a href="<?php echo htmlspecialchars($instagram_url); ?>" target="_blank" class="text-white me-2"><i class="fab fa-instagram"></i></a>
                            <?php endif; ?>
                            <?php if (!empty($linkedin_url)): ?>
                                <a href="<?php echo htmlspecialchars($linkedin_url); ?>" target="_blank" class="text-white me-2"><i class="fab fa-linkedin-in"></i></a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-4">
                    <h5>روابط سريعة</h5>
                    <ul class="list-unstyled">
                        <li><a href="<?php echo SITE_URL; ?>" class="text-white">الرئيسية</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/services.php" class="text-white">الخدمات</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/projects.php" class="text-white">المشاريع</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/blog.php" class="text-white">المدونة</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/contact.php" class="text-white">تواصل معنا</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>تواصل معنا</h5>
                    <ul class="list-unstyled">
                        <?php if (!empty($contact_email)): ?>
                            <li><i class="fas fa-envelope me-2"></i> <?php echo htmlspecialchars($contact_email); ?></li>
                        <?php endif; ?>
                        <?php if (!empty($contact_phone)): ?>
                            <li><i class="fas fa-phone me-2"></i> <?php echo htmlspecialchars($contact_phone); ?></li>
                        <?php endif; ?>
                        <?php if (!empty($address)): ?>
                            <li><i class="fas fa-map-marker-alt me-2"></i> <?php echo htmlspecialchars($address); ?></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            <hr>
            <div class="text-center">
                <p>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($site_title); ?>. جميع الحقوق محفوظة.</p>
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
    <!-- Lightbox لعرض الصور -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>

<!-- Font Awesome للأيقونات -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">

<!-- Animate.css للحركات -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <!-- JS المخصص -->
    <script src="<?php echo SITE_URL; ?>/assets/js/script.js"></script>
</body>
</html>