<?php
require_once 'includes/config.php';
require_once 'includes/header.php';

// التحقق من وجود معرف الخدمة
if (!isset($_GET['service_id'])) {
    header("Location: services.php");
    exit();
}

$service_id = intval($_GET['service_id']);
$stmt = $pdo->prepare("SELECT * FROM services WHERE id = ? AND is_active = 1");
$stmt->execute([$service_id]);
$service = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$service) {
    header("Location: services.php");
    exit();
}

$page_title = 'طلب خدمة: ' . $service['title'];
?>

<section class="mb-5 mt-4">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-sm border-0 mb-5">
                    <div class="card-body p-4">
                        <a href="services.php?service_id=<?php echo $service_id; ?>" class="btn btn-outline-primary mb-4">
                            <i class="fas fa-arrow-right me-2"></i>عودة إلى الخدمة
                        </a>
                        
                        <div class="text-center mb-4">
                            <h1 class="text-gradient d-inline-block position-relative mb-4">طلب خدمة: <?php echo htmlspecialchars($service['title']); ?>
                                <span class="position-absolute bottom-0 start-50 translate-middle-x bg-primary" 
                                      style="height: 3px; width: 80px;"></span>
                            </h1>
                            <p class="lead text-muted"><?php echo htmlspecialchars($service['short_description']); ?></p>
                            
                            <?php if (!empty($service['price_range'])): ?>
                                <div class="alert alert-info d-inline-block">
                                    <i class="fas fa-tags me-2"></i> نطاق السعر: <?php echo htmlspecialchars($service['price_range']); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <form id="orderForm" action="process_order.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="service_id" value="<?php echo $service_id; ?>">
                            
                            <div class="row g-4">
                                <!-- القسم الخاص بالخدمة -->
                                <div class="col-lg-8">
                                    <div class="card mb-4">
                                        <div class="card-header bg-primary text-white">
                                            <i class="fas fa-cogs me-2"></i> تفاصيل الطلب
                                        </div>
                                        <div class="card-body">
                                            <?php
                                            // تحديد الحقول حسب نوع الخدمة
                                            switch ($service['service_type']) {
                                                case 'website_design':
                                                    ?>
                                                    <div class="mb-3">
                                                        <label class="form-label">نوع الموقع</label>
                                                        <select name="website_type" class="form-select" required>
                                                            <option value="">اختر نوع الموقع</option>
                                                            <option value="company">موقع شركة</option>
                                                            <option value="ecommerce">متجر إلكتروني</option>
                                                            <option value="blog">مدونة</option>
                                                            <option value="portfolio">بورتفوليو</option>
                                                            <option value="other">أخرى</option>
                                                        </select>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <label class="form-label">عدد الصفحات التقريبي</label>
                                                        <input type="number" name="pages_count" class="form-control" min="1" required>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <label class="form-label">الألوان المفضلة</label>
                                                        <input type="text" name="preferred_colors" class="form-control" placeholder="مثال: أزرق، أبيض، رمادي">
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <label class="form-label">الميزات المطلوبة</label>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="features[]" value="booking" id="feature1">
                                                            <label class="form-check-label" for="feature1">نظام حجز</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="features[]" value="payment" id="feature2">
                                                            <label class="form-check-label" for="feature2">دفع إلكتروني</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="features[]" value="multilingual" id="feature3">
                                                            <label class="form-check-label" for="feature3">متعدد اللغات</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="features[]" value="responsive" id="feature4">
                                                            <label class="form-check-label" for="feature4">متوافق مع الجوال</label>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <label class="form-label">أمثلة لمواقع أعجبتك (روابط)</label>
                                                        <textarea name="website_examples" class="form-control" rows="3" placeholder="ضع كل رابط في سطر"></textarea>
                                                    </div>
                                                    <?php
                                                    break;
                                                    
                                                case 'marketing':
                                                    ?>
                                                    <div class="mb-3">
                                                        <label class="form-label">منصات التواصل المطلوبة</label>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="platforms[]" value="instagram" id="platform1">
                                                            <label class="form-check-label" for="platform1">إنستغرام</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="platforms[]" value="tiktok" id="platform2">
                                                            <label class="form-check-label" for="platform2">تيك توك</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="platforms[]" value="twitter" id="platform3">
                                                            <label class="form-check-label" for="platform3">تويتر</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="platforms[]" value="facebook" id="platform4">
                                                            <label class="form-check-label" for="platform4">فيسبوك</label>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <label class="form-label">الميزانية الشهرية (USD)</label>
                                                        <input type="number" name="monthly_budget" class="form-control" min="100" required>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <label class="form-label">الهدف الرئيسي</label>
                                                        <select name="goal" class="form-select" required>
                                                            <option value="">اختر الهدف</option>
                                                            <option value="followers">زيادة المتابعين</option>
                                                            <option value="sales">زيادة المبيعات</option>
                                                            <option value="awareness">زيادة وعي العلامة</option>
                                                            <option value="engagement">زيادة التفاعل</option>
                                                        </select>
                                                    </div>
                                                    <?php
                                                    break;
                                                    
                                                default:
                                                    ?>
                                                    <div class="mb-3">
                                                        <label class="form-label">تفاصيل الطلب</label>
                                                        <textarea name="service_details" class="form-control" rows="5" required></textarea>
                                                    </div>
                                                    <?php
                                            }
                                            ?>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">ملفات مرفقة (اختياري)</label>
                                                <input type="file" name="attachments[]" class="form-control" multiple>
                                                <small class="text-muted">يمكنك رفع أكثر من ملف (الحد الأقصى 5 ملفات، 2MB لكل ملف)</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- معلومات العميل -->
                                <div class="col-lg-4">
                                    <div class="card mb-4">
                                        <div class="card-header bg-primary text-white">
                                            <i class="fas fa-user me-2"></i> معلومات العميل
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label class="form-label">الاسم الكامل</label>
                                                <input type="text" name="customer_name" class="form-control" required>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">البريد الإلكتروني</label>
                                                <input type="email" name="customer_email" class="form-control" required>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">رقم الهاتف</label>
                                                <input type="tel" name="customer_phone" class="form-control" required>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">الموعد النهائي المطلوب (اختياري)</label>
                                                <input type="date" name="deadline" class="form-control" min="<?php echo date('Y-m-d', strtotime('+1 week')); ?>">
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">ملاحظات إضافية (اختياري)</label>
                                                <textarea name="notes" class="form-control" rows="3"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary btn-hover-gradient">
                                            <i class="fas fa-paper-plane me-2"></i> إرسال الطلب
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.getElementById('orderForm').addEventListener('submit', function(e) {
    // التحقق من صحة البيانات قبل الإرسال
    const phoneInput = document.querySelector('input[name="customer_phone"]');
    const emailInput = document.querySelector('input[name="customer_email"]');
    const filesInput = document.querySelector('input[name="attachments[]"]');
    
    // التحقق من رقم الهاتف
    const phoneRegex = /^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/;
    if (!phoneRegex.test(phoneInput.value)) {
        alert('الرجاء إدخال رقم هاتف صحيح');
        phoneInput.focus();
        e.preventDefault();
        return false;
    }
    
    // التحقق من البريد الإلكتروني
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(emailInput.value)) {
        alert('الرجاء إدخال بريد إلكتروني صحيح');
        emailInput.focus();
        e.preventDefault();
        return false;
    }
    
    // التحقق من الملفات
    if (filesInput.files.length > 5) {
        alert('الحد الأقصى للملفات هو 5 ملفات');
        e.preventDefault();
        return false;
    }
    
    for (let i = 0; i < filesInput.files.length; i++) {
        if (filesInput.files[i].size > 2 * 1024 * 1024) {
            alert(`الملف ${filesInput.files[i].name} يتجاوز الحد الأقصى للحجم (2MB)`);
            e.preventDefault();
            return false;
        }
    }
    
    return true;
});
</script>

<?php
require_once 'includes/footer.php';
?>