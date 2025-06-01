<?php
require_once 'includes/config.php';
require_once 'includes/header.php';

$page_title = 'الخدمات';

// عرض تفاصيل الخدمة إذا كان هناك معرف خدمة في الرابط
$service_details = null;
if (isset($_GET['service_id'])) {
    $service_id = intval($_GET['service_id']);
    $stmt = $pdo->prepare("SELECT * FROM services WHERE id = ? AND is_active = 1");
    $stmt->execute([$service_id]);
    $service_details = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<section class="mb-5 mt-4">
    <div class="container">
        <?php if ($service_details): ?>
            <!-- عرض تفاصيل الخدمة -->
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card shadow-sm border-0 mb-5">
                        <div class="card-body p-4">
                            <a href="services.php" class="btn btn-outline-primary mb-4">
                                <i class="fas fa-arrow-right me-2"></i>عودة للخدمات
                            </a>
                            
                            <div class="text-center mb-4">
                                <?php if (!empty($service_details['image'])): ?>
                                    <div class="service-image-main mb-4">
                                        <a href="<?php echo SITE_URL . '/uploads/services/' . $service_details['image']; ?>" data-lightbox="service-image" data-title="<?php echo $service_details['title']; ?>">
                                            <img src="<?php echo SITE_URL . '/uploads/services/' . $service_details['image']; ?>" 
                                                 alt="<?php echo $service_details['title']; ?>" 
                                                 class="img-fluid rounded-3 shadow-lg hover-zoom">
                                        </a>
                                    </div>
                                <?php endif; ?>
                                <h1 class="text-gradient d-inline-block position-relative mb-4"><?php echo $service_details['title']; ?>
                                    <span class="position-absolute bottom-0 start-50 translate-middle-x bg-primary" 
                                          style="height: 3px; width: 80px;"></span>
                                </h1>
                                <p class="lead text-muted"><?php echo $service_details['short_description']; ?></p>
                            </div>
                            
                            <div class="row g-4">
                                <!-- القسم الرئيسي للتفاصيل -->
                                <div class="col-lg-8">
                                    <div class="service-content mb-4">
                                        <h3 class="text-primary mb-3"><i class="fas fa-info-circle me-2"></i>وصف الخدمة</h3>
                                        <div class="text-muted"><?php echo nl2br($service_details['description']); ?></div>
                                    </div>
                                    
                                    <?php if (!empty($service_details['features'])): ?>
                                        <div class="service-features mb-4">
                                            <h3 class="text-primary mb-3"><i class="fas fa-star me-2"></i>مميزات الخدمة</h3>
                                            <div class="text-muted"><?php echo nl2br($service_details['features']); ?></div>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($service_details['steps'])): ?>
                                        <div class="service-steps mb-4">
                                            <h3 class="text-primary mb-3"><i class="fas fa-list-ol me-2"></i>خطوات العمل</h3>
                                            <div class="text-muted"><?php echo nl2br($service_details['steps']); ?></div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- الجانب الأيمن للمعلومات الإضافية -->
                                <div class="col-lg-4">
                                    <div class="card bg-light mb-4">
                                        <div class="card-body">
                                            <?php if (!empty($service_details['delivery_time'])): ?>
                                                <div class="d-flex align-items-center mb-3">
                                                    <i class="fas fa-clock fa-lg text-primary me-3"></i>
                                                    <div>
                                                        <h6 class="mb-0">مدة التسليم</h6>
                                                        <p class="text-muted mb-0"><?php echo $service_details['delivery_time']; ?></p>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <?php if (!empty($service_details['price_range'])): ?>
                                                <div class="d-flex align-items-center mb-3">
                                                    <i class="fas fa-tags fa-lg text-primary me-3"></i>
                                                    <div>
                                                        <h6 class="mb-0">نطاق الأسعار</h6>
                                                        <p class="text-muted mb-0"><?php echo $service_details['price_range']; ?></p>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <?php if (!empty($service_details['support'])): ?>
                                                <div class="d-flex align-items-center mb-3">
                                                    <i class="fas fa-headset fa-lg text-primary me-3"></i>
                                                    <div>
                                                        <h6 class="mb-0">الدعم الفني</h6>
                                                        <p class="text-muted mb-0"><?php echo $service_details['support']; ?></p>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <?php if (!empty($service_details['requirements'])): ?>
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-clipboard-list fa-lg text-primary me-3"></i>
                                                    <div>
                                                        <h6 class="mb-0">متطلبات الخدمة</h6>
                                                        <p class="text-muted mb-0"><?php echo $service_details['requirements']; ?></p>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="text-center">
    <?php if (isset($service_details['id'])): ?>
        <a href="order.php?service_id=<?php echo (int)$service_details['id']; ?>" class="btn btn-primary btn-hover-gradient px-5">
            <i class="fas fa-shopping-cart me-2"></i>اطلب الخدمة الآن
        </a>
    <?php else: ?>
        <a href="services.php" class="btn btn-secondary px-5">
            <i class="fas fa-exclamation-circle me-2"></i>الخدمة غير متاحة
        </a>
    <?php endif; ?>
</div>
                                </div>
                            </div>
                            
                            <?php if (!empty($service_details['faq'])): ?>
    <div class="service-faq mt-5">
        <h3 class="text-primary mb-4"><i class="fas fa-question-circle me-2"></i>الأسئلة الشائعة</h3>
        <div class="accordion" id="faqAccordion">
            <?php 
            // تقسيم النص إلى أسئلة منفصلة
            $faq_entries = explode("\n\n", trim($service_details['faq']));
            $counter = 0;
            
            foreach ($faq_entries as $entry) {
                if (empty(trim($entry))) continue;
                
                // تقسيم كل سؤال إلى سؤال وجواب
                $parts = explode("\n", $entry, 2);
                
                if (count($parts) >= 2) {
                    $question = trim(str_replace(['س:', 'Q:'], '', $parts[0]));
                    $answer = trim($parts[1]);
                    ?>
                    <div class="accordion-item mb-2 border-0 shadow-sm">
                        <h4 class="accordion-header" id="heading<?php echo $counter; ?>">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                    data-bs-target="#collapse<?php echo $counter; ?>" aria-expanded="false" 
                                    aria-controls="collapse<?php echo $counter; ?>">
                                <?php echo htmlspecialchars($question); ?>
                            </button>
                        </h4>
                        <div id="collapse<?php echo $counter; ?>" class="accordion-collapse collapse" 
                             aria-labelledby="heading<?php echo $counter; ?>" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted">
                                <?php echo nl2br(htmlspecialchars($answer)); ?>
                            </div>
                        </div>
                    </div>
                    <?php
                    $counter++;
                }
            }
            ?>
        </div>
    </div>
<?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- عرض قائمة الخدمات -->
            <div class="text-center mb-5">
                <h1 class="text-gradient d-inline-block position-relative mb-4">خدماتنا
                    <span class="position-absolute bottom-0 start-50 translate-middle-x bg-primary" 
                          style="height: 3px; width: 80px;"></span>
                </h1>
                <p class="lead text-muted mx-auto" style="max-width: 700px;">نقدم لكم مجموعة متكاملة من الخدمات المتميزة التي تلبي جميع احتياجاتكم بأعلى معايير الجودة والاحترافية</p>
            </div>
            
            <div class="row g-4">
                <?php
                $stmt = $pdo->query("SELECT * FROM services WHERE is_active = 1 ORDER BY created_at DESC");
                $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                foreach ($services as $service) {
                    echo '<div class="col-lg-4 col-md-6" id="service-'.$service['id'].'">
                        <div class="card h-100 hover-scale shadow-sm border-0">
                            <div class="card-body text-center p-4">';
                    
                    if (!empty($service['image'])) {
                        echo '<div class="service-image-wrapper mb-4">
                                <img src="'.SITE_URL.'/uploads/services/'.$service['image'].'" 
                                     alt="'.$service['title'].'" 
                                     class="img-fluid rounded service-thumbnail">
                              </div>';
                    } else {
                        echo '<div class="service-icon-wrapper mb-4">
                                <i class="fas fa-image service-default-icon"></i>
                              </div>';
                    }
                    
                    echo '<h3 class="card-title text-primary mb-3">'.$service['title'].'</h3>
                                <p class="card-text text-muted">'.$service['short_description'].'</p>
                                <a href="services.php?service_id='.$service['id'].'" class="btn btn-outline-primary btn-hover-gradient mt-3">
                                    <i class="fas fa-arrow-left me-2"></i>عرض التفاصيل
                                </a>
                            </div>
                        </div>
                    </div>';
                }
                
                if (empty($services)) {
                    echo '<div class="col-12">
                        <div class="alert alert-info text-center fade-in">
                            <i class="fas fa-info-circle me-2"></i>لا توجد خدمات متاحة حالياً.
                        </div>
                    </div>';
                }
                ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
require_once 'includes/footer.php';
?>