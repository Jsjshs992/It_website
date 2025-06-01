<?php
require_once '../includes/config.php';

// التحقق من تسجيل الدخول
if (!isLoggedIn()) {
    redirect(SITE_URL . '/admin/login.php');
}

$page_title = 'إدارة الخدمات';
require_once __DIR__ . '/includes/admin-header.php';

// معالجة إضافة/تعديل/حذف الخدمات
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_service'])) {
        // إضافة خدمة جديدة
        $title = sanitizeInput($_POST['title']);
        $short_description = sanitizeInput($_POST['short_description']);
        $description = sanitizeInput($_POST['description']);
        $features = sanitizeInput($_POST['features']);
        $steps = sanitizeInput($_POST['steps']);
        $faq = sanitizeInput($_POST['faq']);
        $delivery_time = sanitizeInput($_POST['delivery_time']);
        $price_range = sanitizeInput($_POST['price_range']);
        $support = sanitizeInput($_POST['support']);
        $requirements = sanitizeInput($_POST['requirements']);
        $service_type = sanitizeInput($_POST['service_type']);
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        // معالجة رفع الصورة
        $image = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../uploads/services/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $fileExt = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $fileName = uniqid() . '.' . $fileExt;
            $uploadPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                $image = $fileName;
            }
        }
        
        $stmt = $pdo->prepare("INSERT INTO services (title, short_description, description, features, steps, faq, 
                              delivery_time, price_range, support, requirements, image, is_active, service_type) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $short_description, $description, $features, $steps, $faq, $delivery_time, 
                       $price_range, $support, $requirements, $image, $is_active, $service_type]);
        
        $_SESSION['success_message'] = 'تمت إضافة الخدمة بنجاح';
        redirect(SITE_URL . '/admin/services.php');
    }
    
    if (isset($_POST['update_service'])) {
        // تحديث خدمة موجودة
        $id = intval($_POST['id']);
        $title = sanitizeInput($_POST['title']);
        $short_description = sanitizeInput($_POST['short_description']);
        $description = sanitizeInput($_POST['description']);
        $features = sanitizeInput($_POST['features']);
        $steps = sanitizeInput($_POST['steps']);
        $faq = sanitizeInput($_POST['faq']);
        $delivery_time = sanitizeInput($_POST['delivery_time']);
        $price_range = sanitizeInput($_POST['price_range']);
        $support = sanitizeInput($_POST['support']);
        $requirements = sanitizeInput($_POST['requirements']);
        $service_type = sanitizeInput($_POST['service_type']);
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        // جلب بيانات الخدمة الحالية
        $stmt = $pdo->prepare("SELECT image FROM services WHERE id = ?");
        $stmt->execute([$id]);
        $current_service = $stmt->fetch(PDO::FETCH_ASSOC);
        $image = $current_service['image'];
        
        // معالجة رفع الصورة الجديدة
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../uploads/services/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            // حذف الصورة القديمة إذا كانت موجودة
            if (!empty($current_service['image'])) {
                $oldImagePath = $uploadDir . $current_service['image'];
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
            
            $fileExt = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $fileName = uniqid() . '.' . $fileExt;
            $uploadPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                $image = $fileName;
            }
        }
        
        $stmt = $pdo->prepare("UPDATE services SET title = ?, short_description = ?, description = ?, 
                              features = ?, steps = ?, faq = ?, delivery_time = ?, price_range = ?, 
                              support = ?, requirements = ?, image = ?, is_active = ?, service_type = ? WHERE id = ?");
        $stmt->execute([$title, $short_description, $description, $features, $steps, $faq, $delivery_time, 
                       $price_range, $support, $requirements, $image, $is_active, $service_type, $id]);
        
        $_SESSION['success_message'] = 'تم تحديث الخدمة بنجاح';
        redirect(SITE_URL . '/admin/services.php');
    }
}

// معالجة حذف خدمة
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    
    // حذف الصورة المرتبطة بالخدمة
    $stmt = $pdo->prepare("SELECT image FROM services WHERE id = ?");
    $stmt->execute([$id]);
    $service = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!empty($service['image'])) {
        $imagePath = __DIR__ . '/../uploads/services/' . $service['image'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }
    
    $stmt = $pdo->prepare("DELETE FROM services WHERE id = ?");
    $stmt->execute([$id]);
    
    $_SESSION['success_message'] = 'تم حذف الخدمة بنجاح';
    redirect(SITE_URL . '/admin/services.php');
}

// عرض رسالة النجاح إذا كانت موجودة
if (isset($_SESSION['success_message'])) {
    echo displayMessage('success', $_SESSION['success_message']);
    unset($_SESSION['success_message']);
}
?>

<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5>الخدمات</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>العنوان</th>
                                <th>الصورة</th>
                                <th>النوع</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $pdo->query("SELECT * FROM services ORDER BY created_at DESC");
                            $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            foreach ($services as $service) {
                                echo '<tr>
                                    <td>'.$service['id'].'</td>
                                    <td>'.$service['title'].'</td>
                                    <td>';
                                
                                if (!empty($service['image'])) {
                                    echo '<img src="'.SITE_URL.'/uploads/services/'.$service['image'].'" 
                                          alt="'.$service['title'].'" 
                                          style="width: 50px; height: 50px; object-fit: cover;">';
                                } else {
                                    echo '---';
                                }
                                
                                echo '</td>
                                    <td>'.getServiceTypeName($service['service_type']).'</td>
                                    <td>';
                                
                                echo $service['is_active'] ? 
                                    '<span class="badge bg-success">نشط</span>' : 
                                    '<span class="badge bg-secondary">غير نشط</span>';
                                
                                echo '</td>
                                    <td>
                                        <a href="services.php?edit='.$service['id'].'" class="btn btn-sm btn-primary">تعديل</a>
                                        <a href="services.php?delete='.$service['id'].'" class="btn btn-sm btn-danger" 
                                           onclick="return confirm(\'هل أنت متأكد من حذف هذه الخدمة؟\')">حذف</a>
                                    </td>
                                </tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5><?php echo isset($_GET['edit']) ? 'تعديل خدمة' : 'إضافة خدمة جديدة'; ?></h5>
            </div>
            <div class="card-body">
                <?php
                // إذا كان في وضع التعديل، جلب بيانات الخدمة
                $service = null;
                if (isset($_GET['edit'])) {
                    $id = intval($_GET['edit']);
                    $stmt = $pdo->prepare("SELECT * FROM services WHERE id = ?");
                    $stmt->execute([$id]);
                    $service = $stmt->fetch(PDO::FETCH_ASSOC);
                }
                ?>
                
                <form method="post" enctype="multipart/form-data">
                    <?php if (isset($_GET['edit'])): ?>
                        <input type="hidden" name="id" value="<?php echo $service['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">عنوان الخدمة</label>
                        <input type="text" class="form-control" id="title" name="title" 
                               value="<?php echo isset($service) ? $service['title'] : ''; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="short_description" class="form-label">وصف مختصر</label>
                        <textarea class="form-control" id="short_description" name="short_description" rows="2" required><?php echo isset($service) ? $service['short_description'] : ''; ?></textarea>
                        <small class="text-muted">سيظهر هذا الوصف في بطاقة الخدمة الرئيسية</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="service_type" class="form-label">نوع الخدمة</label>
                        <select class="form-select" id="service_type" name="service_type" required>
                            <option value="">اختر نوع الخدمة</option>
                            <option value="website_design" <?php echo (isset($service) && $service['service_type'] == 'website_design') ? 'selected' : ''; ?>>تصميم مواقع</option>
                            <option value="marketing" <?php echo (isset($service) && $service['service_type'] == 'marketing') ? 'selected' : ''; ?>>تسويق إلكتروني</option>
                            <option value="graphic_design" <?php echo (isset($service) && $service['service_type'] == 'graphic_design') ? 'selected' : ''; ?>>تصميم جرافيك</option>
                            <option value="programming" <?php echo (isset($service) && $service['service_type'] == 'programming') ? 'selected' : ''; ?>>برمجة وتطوير</option>
                            <option value="consulting" <?php echo (isset($service) && $service['service_type'] == 'consulting') ? 'selected' : ''; ?>>استشارات</option>
                            <option value="other" <?php echo (isset($service) && $service['service_type'] == 'other') ? 'selected' : ''; ?>>أخرى</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="image" class="form-label">صورة الخدمة</label>
                        <input type="file" class="form-control" id="image" name="image">
                        <?php if (isset($service) && !empty($service['image'])): ?>
                            <div class="mt-2">
                                <img src="<?php echo SITE_URL . '/uploads/services/' . $service['image']; ?>" 
                                     alt="صورة الخدمة الحالية" 
                                     style="max-width: 100px; max-height: 100px;">
                                <input type="hidden" name="current_image" value="<?php echo $service['image']; ?>">
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">وصف الخدمة الكامل</label>
                        <textarea class="form-control" id="description" name="description" rows="4" required><?php echo isset($service) ? $service['description'] : ''; ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="features" class="form-label">مميزات الخدمة</label>
                        <textarea class="form-control" id="features" name="features" rows="4"><?php echo isset($service) ? $service['features'] : ''; ?></textarea>
                        <small class="text-muted">اذكر أهم مميزات الخدمة (سطر لكل ميزة)</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="steps" class="form-label">خطوات العمل</label>
                        <textarea class="form-control" id="steps" name="steps" rows="4"><?php echo isset($service) ? $service['steps'] : ''; ?></textarea>
                        <small class="text-muted">صف خطوات تنفيذ الخدمة للعميل</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="faq" class="form-label">الأسئلة الشائعة</label>
                        <textarea class="form-control" id="faq" name="faq" rows="4"><?php echo isset($service) ? $service['faq'] : ''; ?></textarea>
                        <small class="text-muted">صيغة الإدخال: السؤال: الجواب (سطر لكل سؤال)</small>
                    </div>
                    
                    <div class="row g-2">
                        <div class="col-md-6 mb-3">
                            <label for="delivery_time" class="form-label">مدة التسليم</label>
                            <input type="text" class="form-control" id="delivery_time" name="delivery_time" 
                                   value="<?php echo isset($service) ? $service['delivery_time'] : ''; ?>">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="price_range" class="form-label">نطاق الأسعار</label>
                            <input type="text" class="form-control" id="price_range" name="price_range" 
                                   value="<?php echo isset($service) ? $service['price_range'] : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="row g-2">
                        <div class="col-md-6 mb-3">
                            <label for="support" class="form-label">الدعم الفني</label>
                            <input type="text" class="form-control" id="support" name="support" 
                                   value="<?php echo isset($service) ? $service['support'] : ''; ?>">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="requirements" class="form-label">متطلبات الخدمة</label>
                            <input type="text" class="form-control" id="requirements" name="requirements" 
                                   value="<?php echo isset($service) ? $service['requirements'] : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" 
                               <?php echo (isset($service) && $service['is_active']) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="is_active">نشط</label>
                    </div>
                    
                    <div class="d-grid">
                        <?php if (isset($_GET['edit'])): ?>
                            <button type="submit" name="update_service" class="btn btn-primary">تحديث الخدمة</button>
                            <a href="services.php" class="btn btn-secondary mt-2">إلغاء</a>
                        <?php else: ?>
                            <button type="submit" name="add_service" class="btn btn-primary">إضافة خدمة</button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/admin-footer.php';
?>