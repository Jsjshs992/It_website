<?php
require_once '../includes/config.php';

// التحقق من تسجيل الدخول
if (!isLoggedIn()) {
    redirect(SITE_URL . '/admin/login.php');
}

$page_title = 'إدارة السلايدر';
require_once __DIR__ . '/includes/admin-header.php';

// معالجة إضافة/تعديل/حذف شرائح السلايدر
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_slide'])) {
        // إضافة شريحة جديدة
        $title = sanitizeInput($_POST['title']);
        $description = sanitizeInput($_POST['description']);
        $button_text = sanitizeInput($_POST['button_text']);
        $button_url = sanitizeInput($_POST['button_url']);
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        // معالجة تحميل الصورة
        $image = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../assets/images/';
            $file_name = basename($_FILES['image']['name']);
            $file_path = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $file_path)) {
                $image = $file_name;
            }
        }
        
        $stmt = $pdo->prepare("INSERT INTO slider (title, description, image, button_text, button_url, is_active) 
                              VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $description, $image, $button_text, $button_url, $is_active]);
        
        $_SESSION['success_message'] = 'تمت إضافة الشريحة بنجاح';
        redirect(SITE_URL . '/admin/slider.php');
    }
    
    if (isset($_POST['update_slide'])) {
        // تحديث شريحة موجودة
        $id = intval($_POST['id']);
        $title = sanitizeInput($_POST['title']);
        $description = sanitizeInput($_POST['description']);
        $button_text = sanitizeInput($_POST['button_text']);
        $button_url = sanitizeInput($_POST['button_url']);
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        // إذا تم تحميل صورة جديدة
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../assets/images/';
            $file_name = basename($_FILES['image']['name']);
            $file_path = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $file_path)) {
                // حذف الصورة القديمة إذا كانت موجودة
                $stmt = $pdo->prepare("SELECT image FROM slider WHERE id = ?");
                $stmt->execute([$id]);
                $old_image = $stmt->fetchColumn();
                
                if ($old_image && file_exists($upload_dir . $old_image)) {
                    unlink($upload_dir . $old_image);
                }
                
                // تحديث الصورة الجديدة
                $stmt = $pdo->prepare("UPDATE slider SET image = ? WHERE id = ?");
                $stmt->execute([$file_name, $id]);
            }
        }
        
        $stmt = $pdo->prepare("UPDATE slider SET title = ?, description = ?, button_text = ?, 
                              button_url = ?, is_active = ? WHERE id = ?");
        $stmt->execute([$title, $description, $button_text, $button_url, $is_active, $id]);
        
        $_SESSION['success_message'] = 'تم تحديث الشريحة بنجاح';
        redirect(SITE_URL . '/admin/slider.php');
    }
}

// معالجة حذف شريحة
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    
    // حذف الصورة المرتبطة بالشريحة
    $stmt = $pdo->prepare("SELECT image FROM slider WHERE id = ?");
    $stmt->execute([$id]);
    $image = $stmt->fetchColumn();
    
    if ($image && file_exists('../assets/images/' . $image)) {
        unlink('../assets/images/' . $image);
    }
    
    // حذف الشريحة من قاعدة البيانات
    $stmt = $pdo->prepare("DELETE FROM slider WHERE id = ?");
    $stmt->execute([$id]);
    
    $_SESSION['success_message'] = 'تم حذف الشريحة بنجاح';
    redirect(SITE_URL . '/admin/slider.php');
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
                <h5>شرائح السلايدر</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>العنوان</th>
                                <th>الصورة</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $pdo->query("SELECT * FROM slider ORDER BY created_at DESC");
                            $slides = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            foreach ($slides as $slide) {
                                echo '<tr>
                                    <td>'.$slide['id'].'</td>
                                    <td>'.$slide['title'].'</td>
                                    <td><img src="../assets/images/'.$slide['image'].'" width="50" height="30" alt="'.$slide['title'].'"></td>
                                    <td>';
                                
                                echo $slide['is_active'] ? 
                                    '<span class="badge bg-success">نشط</span>' : 
                                    '<span class="badge bg-secondary">غير نشط</span>';
                                
                                echo '</td>
                                    <td>
                                        <a href="slider.php?edit='.$slide['id'].'" class="btn btn-sm btn-primary">تعديل</a>
                                        <a href="slider.php?delete='.$slide['id'].'" class="btn btn-sm btn-danger" 
                                           onclick="return confirm(\'هل أنت متأكد من حذف هذه الشريحة؟\')">حذف</a>
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
                <h5><?php echo isset($_GET['edit']) ? 'تعديل شريحة' : 'إضافة شريحة جديدة'; ?></h5>
            </div>
            <div class="card-body">
                <?php
                // إذا كان في وضع التعديل، جلب بيانات الشريحة
                $slide = null;
                if (isset($_GET['edit'])) {
                    $id = intval($_GET['edit']);
                    $stmt = $pdo->prepare("SELECT * FROM slider WHERE id = ?");
                    $stmt->execute([$id]);
                    $slide = $stmt->fetch(PDO::FETCH_ASSOC);
                }
                ?>
                
                <form method="post" enctype="multipart/form-data">
                    <?php if (isset($_GET['edit'])): ?>
                        <input type="hidden" name="id" value="<?php echo $slide['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">العنوان</label>
                        <input type="text" class="form-control" id="title" name="title" 
                               value="<?php echo isset($slide) ? $slide['title'] : ''; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">الوصف</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?php echo isset($slide) ? $slide['description'] : ''; ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="image" class="form-label">الصورة</label>
                        <input type="file" class="form-control" id="image" name="image" <?php echo !isset($slide) ? 'required' : ''; ?>>
                        
                        <?php if (isset($slide) && !empty($slide['image'])): ?>
                            <div class="mt-2">
                                <img src="../assets/images/<?php echo $slide['image']; ?>" width="100" alt="Current Image">
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="button_text" class="form-label">نص الزر (اختياري)</label>
                        <input type="text" class="form-control" id="button_text" name="button_text" 
                               value="<?php echo isset($slide) ? $slide['button_text'] : ''; ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="button_url" class="form-label">رابط الزر (اختياري)</label>
                        <input type="url" class="form-control" id="button_url" name="button_url" 
                               value="<?php echo isset($slide) ? $slide['button_url'] : ''; ?>">
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" 
                               <?php echo (isset($slide) && $slide['is_active']) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="is_active">نشط</label>
                    </div>
                    
                    <div class="d-grid">
                        <?php if (isset($_GET['edit'])): ?>
                            <button type="submit" name="update_slide" class="btn btn-primary">تحديث الشريحة</button>
                            <a href="slider.php" class="btn btn-secondary mt-2">إلغاء</a>
                        <?php else: ?>
                            <button type="submit" name="add_slide" class="btn btn-primary">إضافة شريحة</button>
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