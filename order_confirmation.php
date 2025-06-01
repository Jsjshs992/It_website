<?php
require_once 'includes/config.php';
require_once 'includes/header.php';

// التحقق من وجود معرف الطلب
if (!isset($_GET['order_id'])) {
    header("Location: services.php");
    exit();
}

$order_id = intval($_GET['order_id']);

// جلب معلومات الطلب
$stmt = $pdo->prepare("SELECT o.*, s.title as service_title 
                      FROM orders o 
                      JOIN services s ON o.service_id = s.id 
                      WHERE o.id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header("Location: services.php");
    exit();
}

$page_title = 'تأكيد الطلب #' . $order_id;
?>

<section class="mb-5 mt-4">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 mb-5">
                    <div class="card-body p-4 text-center">
                        <div class="icon-success mb-4">
                            <i class="fas fa-check-circle fa-5x text-success"></i>
                        </div>
                        
                        <h1 class="text-gradient d-inline-block position-relative mb-4">شكراً لك!
                            <span class="position-absolute bottom-0 start-50 translate-middle-x bg-primary" 
                                  style="height: 3px; width: 80px;"></span>
                        </h1>
                        
                        <div class="alert alert-success mb-4">
                            <h4 class="alert-heading">تم استلام طلبك بنجاح!</h4>
                            <p>رقم الطلب: <strong>#<?php echo $order_id; ?></strong></p>
                            <p>الخدمة: <strong><?php echo htmlspecialchars($order['service_title']); ?></strong></p>
                        </div>
                        
                        <p class="lead mb-4">لقد تم استلام طلبك وسنتواصل معك في أقرب وقت لتأكيد التفاصيل.</p>
                        
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">معلومات الطلب</h5>
                            </div>
                            <div class="card-body text-start">
                                <p><strong>اسم العميل:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
                                <p><strong>البريد الإلكتروني:</strong> <?php echo htmlspecialchars($order['customer_email']); ?></p>
                                <p><strong>رقم الهاتف:</strong> <?php echo htmlspecialchars($order['customer_phone']); ?></p>
                                
                                <?php if (!empty($order['deadline'])): ?>
                                    <p><strong>الموعد النهائي المطلوب:</strong> <?php echo htmlspecialchars($order['deadline']); ?></p>
                                <?php endif; ?>
                                
                                <?php if (!empty($order['notes'])): ?>
                                    <p><strong>ملاحظات إضافية:</strong> <?php echo nl2br(htmlspecialchars($order['notes'])); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-center gap-3">
                            <a href="services.php" class="btn btn-outline-primary">
                                <i class="fas fa-arrow-right me-2"></i> تصفح المزيد من الخدمات
                            </a>
                            <a href="index.php" class="btn btn-primary">
                                <i class="fas fa-home me-2"></i> العودة للرئيسية
                            </a>
                        </div>
                        <div class="text-center mt-4">
    <a href="<?php echo $_SESSION['whatsapp_link']; ?>" 
       class="btn btn-success" 
       target="_blank">
        <i class="fab fa-whatsapp me-2"></i> التواصل عبر واتساب
    </a>
    <?php unset($_SESSION['whatsapp_link']); ?>
</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
require_once 'includes/footer.php';
?>