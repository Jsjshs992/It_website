<?php
require_once '../includes/config.php';

// التحقق من تسجيل الدخول
if (!isLoggedIn()) {
    redirect(SITE_URL . '/admin/login.php');
}

$page_title = 'إدارة المقالات';
require_once __DIR__ . '/includes/admin-header.php';

// معالجة إضافة/تعديل/حذف المقالات
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_article'])) {
        // إضافة مقال جديد
        $title = sanitizeInput($_POST['title']);
        $content = sanitizeInput($_POST['content'], false);
        $author = sanitizeInput($_POST['author']);
        $tags = sanitizeInput($_POST['tags']);
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
        
        $stmt = $pdo->prepare("INSERT INTO blog (title, content, image, author, tags, is_active) 
                              VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $content, $image, $author, $tags, $is_active]);
        
        $_SESSION['success_message'] = 'تمت إضافة المقال بنجاح';
        redirect(SITE_URL . '/admin/blog.php');
    }
    
    if (isset($_POST['update_article'])) {
        // تحديث مقال موجود
        $id = intval($_POST['id']);
        $title = sanitizeInput($_POST['title']);
        $content = sanitizeInput($_POST['content'], false);
        $author = sanitizeInput($_POST['author']);
        $tags = sanitizeInput($_POST['tags']);
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        // إذا تم تحميل صورة جديدة
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../assets/images/';
            $file_name = basename($_FILES['image']['name']);
            $file_path = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $file_path)) {
                // حذف الصورة القديمة إذا كانت موجودة
                $stmt = $pdo->prepare("SELECT image FROM blog WHERE id = ?");
                $stmt->execute([$id]);
                $old_image = $stmt->fetchColumn();
                
                if ($old_image && file_exists($upload_dir . $old_image)) {
                    unlink($upload_dir . $old_image);
                }
                
                // تحديث الصورة الجديدة
                $stmt = $pdo->prepare("UPDATE blog SET image = ? WHERE id = ?");
                $stmt->execute([$file_name, $id]);
            }
        }
        
        $stmt = $pdo->prepare("UPDATE blog SET title = ?, content = ?, author = ?, 
                              tags = ?, is_active = ? WHERE id = ?");
        $stmt->execute([$title, $content, $author, $tags, $is_active, $id]);
        
        $_SESSION['success_message'] = 'تم تحديث المقال بنجاح';
        redirect(SITE_URL . '/admin/blog.php');
    }
}

// معالجة حذف مقال
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    
    // حذف الصورة المرتبطة بالمقال
    $stmt = $pdo->prepare("SELECT image FROM blog WHERE id = ?");
    $stmt->execute([$id]);
    $image = $stmt->fetchColumn();
    
    if ($image && file_exists('../assets/images/' . $image)) {
        unlink('../assets/images/' . $image);
    }
    
    // حذف المقال من قاعدة البيانات
    $stmt = $pdo->prepare("DELETE FROM blog WHERE id = ?");
    $stmt->execute([$id]);
    
    $_SESSION['success_message'] = 'تم حذف المقال بنجاح';
    redirect(SITE_URL . '/admin/blog.php');
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
                <h5>المقالات</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>العنوان</th>
                                <th>المؤلف</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $pdo->query("SELECT * FROM blog ORDER BY created_at DESC");
                            $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            foreach ($articles as $article) {
                                echo '<tr>
                                    <td>'.$article['id'].'</td>
                                    <td>'.$article['title'].'</td>
                                    <td>'.$article['author'].'</td>
                                    <td>';
                                
                                echo $article['is_active'] ? 
                                    '<span class="badge bg-success">نشط</span>' : 
                                    '<span class="badge bg-secondary">غير نشط</span>';
                                
                                echo '</td>
                                    <td>
                                        <a href="blog.php?edit='.$article['id'].'" class="btn btn-sm btn-primary">تعديل</a>
                                        <a href="blog.php?delete='.$article['id'].'" class="btn btn-sm btn-danger" 
                                           onclick="return confirm(\'هل أنت متأكد من حذف هذا المقال؟\')">حذف</a>
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
                <h5><?php echo isset($_GET['edit']) ? 'تعديل مقال' : 'إضافة مقال جديد'; ?></h5>
            </div>
            <div class="card-body">
                <?php
                // إذا كان في وضع التعديل، جلب بيانات المقال
                $article = null;
                if (isset($_GET['edit'])) {
                    $id = intval($_GET['edit']);
                    $stmt = $pdo->prepare("SELECT * FROM blog WHERE id = ?");
                    $stmt->execute([$id]);
                    $article = $stmt->fetch(PDO::FETCH_ASSOC);
                }
                ?>
                
                <form method="post" enctype="multipart/form-data">
                    <?php if (isset($_GET['edit'])): ?>
                        <input type="hidden" name="id" value="<?php echo $article['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">عنوان المقال</label>
                        <input type="text" class="form-control" id="title" name="title" 
                               value="<?php echo isset($article) ? $article['title'] : ''; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="content" class="form-label">محتوى المقال</label>
                        <textarea class="form-control" id="content" name="content" rows="6" required><?php echo isset($article) ? $article['content'] : ''; ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="image" class="form-label">صورة المقال</label>
                        <input type="file" class="form-control" id="image" name="image" <?php echo !isset($article) ? 'required' : ''; ?>>
                        
                        <?php if (isset($article) && !empty($article['image'])): ?>
                            <div class="mt-2">
                                <img src="../assets/images/<?php echo $article['image']; ?>" width="100" alt="Current Image">
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="author" class="form-label">المؤلف</label>
                        <input type="text" class="form-control" id="author" name="author" 
                               value="<?php echo isset($article) ? $article['author'] : ''; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="tags" class="form-label">الكلمات المفتاحية (مفصولة بفواصل)</label>
                        <input type="text" class="form-control" id="tags" name="tags" 
                               value="<?php echo isset($article) ? $article['tags'] : ''; ?>">
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" 
                               <?php echo (isset($article) && $article['is_active']) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="is_active">نشط</label>
                    </div>
                    
                    <div class="d-grid">
                        <?php if (isset($_GET['edit'])): ?>
                            <button type="submit" name="update_article" class="btn btn-primary">تحديث المقال</button>
                            <a href="blog.php" class="btn btn-secondary mt-2">إلغاء</a>
                        <?php else: ?>
                            <button type="submit" name="add_article" class="btn btn-primary">إضافة مقال</button>
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