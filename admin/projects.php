<?php
require_once '../includes/config.php';

// التحقق من تسجيل الدخول
if (!isLoggedIn()) {
    redirect(SITE_URL . '/admin/login.php');
}

$page_title = 'إدارة المشاريع';
require_once __DIR__ . '/includes/admin-header.php';

// تمكين عرض الأخطاء للتصحيح
error_reporting(E_ALL);
ini_set('display_errors', 1);

// معالجة إضافة/تعديل/حذف المشاريع
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_project'])) {
        // إضافة مشروع جديد
        $title = sanitizeInput($_POST['title']);
        $description = sanitizeInput($_POST['description']);
        $category = sanitizeInput($_POST['category']);
        $client = sanitizeInput($_POST['client']);
        $project_date = sanitizeInput($_POST['project_date']);
        $technologies = sanitizeInput($_POST['technologies']);
        $features = sanitizeInput($_POST['features']);
        $challenges = sanitizeInput($_POST['challenges']);
        $external_url = sanitizeInput($_POST['external_url']);
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        // معالجة تحميل الصورة الرئيسية
        $image = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../assets/images/';
            
            // إنشاء المجلد إذا لم يكن موجوداً
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            // التحقق من نوع الملف
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $file_info = finfo_open(FILEINFO_MIME_TYPE);
            $file_type = finfo_file($file_info, $_FILES['image']['tmp_name']);
            finfo_close($file_info);
            
            if (!in_array($file_type, $allowed_types)) {
                $_SESSION['error_message'] = 'نوع الملف غير مسموح به. يرجى اختيار صورة (JPEG, PNG, GIF, WEBP)';
                redirect(SITE_URL . '/admin/projects.php');
            }
            
            // إنشاء اسم فريد للملف
            $file_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $new_filename = uniqid('project_', true) . '.' . $file_ext;
            $target_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                $image = $new_filename;
            } else {
                $_SESSION['error_message'] = 'حدث خطأ أثناء رفع الصورة الرئيسية';
                redirect(SITE_URL . '/admin/projects.php');
            }
        } else {
            $_SESSION['error_message'] = 'يجب اختيار صورة رئيسية للمشروع';
            redirect(SITE_URL . '/admin/projects.php');
        }
        
        // معالجة تحميل معرض الصور
        $gallery_images = [];
        if (!empty($_FILES['gallery_images']['name'][0])) {
            foreach ($_FILES['gallery_images']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['gallery_images']['error'][$key] === UPLOAD_ERR_OK) {
                    $file_info = finfo_open(FILEINFO_MIME_TYPE);
                    $file_type = finfo_file($file_info, $tmp_name);
                    finfo_close($file_info);
                    
                    if (in_array($file_type, $allowed_types)) {
                        $file_ext = pathinfo($_FILES['gallery_images']['name'][$key], PATHINFO_EXTENSION);
                        $new_filename = uniqid('gallery_', true) . '.' . $file_ext;
                        $target_path = $upload_dir . $new_filename;
                        
                        if (move_uploaded_file($tmp_name, $target_path)) {
                            $gallery_images[] = $new_filename;
                        }
                    }
                }
            }
        }
        
        try {
            $stmt = $pdo->prepare("INSERT INTO projects 
                                  (title, description, image, category, client, project_date, 
                                   technologies, features, challenges, external_url, gallery_images, is_active) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            $gallery_images_str = implode(',', $gallery_images);
            $stmt->execute([
                $title, $description, $image, $category, $client, $project_date,
                $technologies, $features, $challenges, $external_url, $gallery_images_str, $is_active
            ]);
            
            $_SESSION['success_message'] = 'تمت إضافة المشروع بنجاح';
            redirect(SITE_URL . '/admin/projects.php');
        } catch (PDOException $e) {
            // حذف الصور التي تم رفعها في حالة فشل الإدراج
            if (!empty($image)) {
                @unlink($upload_dir . $image);
            }
            foreach ($gallery_images as $img) {
                @unlink($upload_dir . $img);
            }
            
            $_SESSION['error_message'] = 'حدث خطأ في إضافة المشروع: ' . $e->getMessage();
            redirect(SITE_URL . '/admin/projects.php');
        }
    }
    
    if (isset($_POST['update_project'])) {
        // تحديث مشروع موجود
        $id = intval($_POST['id']);
        $title = sanitizeInput($_POST['title']);
        $description = sanitizeInput($_POST['description']);
        $category = sanitizeInput($_POST['category']);
        $client = sanitizeInput($_POST['client']);
        $project_date = sanitizeInput($_POST['project_date']);
        $technologies = sanitizeInput($_POST['technologies']);
        $features = sanitizeInput($_POST['features']);
        $challenges = sanitizeInput($_POST['challenges']);
        $external_url = sanitizeInput($_POST['external_url']);
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        $upload_dir = '../assets/images/';
        
        // الحصول على بيانات المشروع الحالية
        $stmt = $pdo->prepare("SELECT image, gallery_images FROM projects WHERE id = ?");
        $stmt->execute([$id]);
        $current_project = $stmt->fetch(PDO::FETCH_ASSOC);
        $current_image = $current_project['image'];
        $current_gallery = $current_project['gallery_images'];
        
        // معالجة الصورة الرئيسية
        $image = $current_image;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $file_info = finfo_open(FILEINFO_MIME_TYPE);
            $file_type = finfo_file($file_info, $_FILES['image']['tmp_name']);
            finfo_close($file_info);
            
            if (in_array($file_type, $allowed_types)) {
                $file_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $new_filename = uniqid('project_', true) . '.' . $file_ext;
                $target_path = $upload_dir . $new_filename;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                    // حذف الصورة القديمة إذا كانت موجودة
                    if (!empty($current_image) && file_exists($upload_dir . $current_image)) {
                        @unlink($upload_dir . $current_image);
                    }
                    $image = $new_filename;
                } else {
                    $_SESSION['error_message'] = 'حدث خطأ أثناء رفع الصورة الرئيسية';
                    redirect(SITE_URL . '/admin/projects.php?edit=' . $id);
                }
            } else {
                $_SESSION['error_message'] = 'نوع الملف غير مسموح به';
                redirect(SITE_URL . '/admin/projects.php?edit=' . $id);
            }
        }
        
        // معالجة معرض الصور
        $gallery_images = isset($_POST['existing_gallery_images']) ? $_POST['existing_gallery_images'] : [];
        $deleted_images = isset($_POST['deleted_gallery_images']) ? json_decode($_POST['deleted_gallery_images']) : [];
        
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        // حذف الصور المحددة للإزالة
        if (!empty($deleted_images)) {
            foreach ($deleted_images as $deleted_img) {
                if (file_exists($upload_dir . $deleted_img)) {
                    @unlink($upload_dir . $deleted_img);
                }
                // إزالة الصورة من المصفوفة
                $gallery_images = array_diff($gallery_images, [$deleted_img]);
            }
        }
        
        // إضافة الصور الجديدة
        if (!empty($_FILES['gallery_images']['name'][0])) {
            foreach ($_FILES['gallery_images']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['gallery_images']['error'][$key] === UPLOAD_ERR_OK) {
                    $file_info = finfo_open(FILEINFO_MIME_TYPE);
                    $file_type = finfo_file($file_info, $tmp_name);
                    finfo_close($file_info);
                    
                    if (in_array($file_type, $allowed_types)) {
                        $file_ext = pathinfo($_FILES['gallery_images']['name'][$key], PATHINFO_EXTENSION);
                        $new_filename = uniqid('gallery_', true) . '.' . $file_ext;
                        $target_path = $upload_dir . $new_filename;
                        
                        if (move_uploaded_file($tmp_name, $target_path)) {
                            $gallery_images[] = $new_filename;
                        }
                    }
                }
            }
        }
        
        $gallery_images_str = implode(',', $gallery_images);
        
        try {
            $stmt = $pdo->prepare("UPDATE projects SET 
                                  title = ?, description = ?, image = ?, category = ?, 
                                  client = ?, project_date = ?, technologies = ?,
                                  features = ?, challenges = ?, external_url = ?,
                                  gallery_images = ?, is_active = ? 
                                  WHERE id = ?");
            
            $stmt->execute([
                $title, $description, $image, $category, $client, $project_date,
                $technologies, $features, $challenges, $external_url,
                $gallery_images_str, $is_active, $id
            ]);
            
            $_SESSION['success_message'] = 'تم تحديث المشروع بنجاح';
            redirect(SITE_URL . '/admin/projects.php');
        } catch (PDOException $e) {
            // في حالة فشل التحديث، نعيد الصورة القديمة إذا تم تغييرها
            if ($image !== $current_image && !empty($current_image)) {
                @rename($upload_dir . $image, $upload_dir . $current_image);
            }
            $_SESSION['error_message'] = 'حدث خطأ في تحديث المشروع: ' . $e->getMessage();
            redirect(SITE_URL . '/admin/projects.php?edit=' . $id);
        }
    }
}

// معالجة حذف مشروع
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    
    try {
        // حذف الصور المرتبطة بالمشروع
        $stmt = $pdo->prepare("SELECT image, gallery_images FROM projects WHERE id = ?");
        $stmt->execute([$id]);
        $project = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $upload_dir = '../assets/images/';
        
        // حذف الصورة الرئيسية
        if (!empty($project['image']) && file_exists($upload_dir . $project['image'])) {
            @unlink($upload_dir . $project['image']);
        }
        
        // حذف صور المعرض
        if (!empty($project['gallery_images'])) {
            $gallery_images = explode(',', $project['gallery_images']);
            foreach ($gallery_images as $image) {
                if (file_exists($upload_dir . $image)) {
                    @unlink($upload_dir . $image);
                }
            }
        }
        
        // حذف المشروع من قاعدة البيانات
        $stmt = $pdo->prepare("DELETE FROM projects WHERE id = ?");
        $stmt->execute([$id]);
        
        $_SESSION['success_message'] = 'تم حذف المشروع بنجاح';
    } catch (PDOException $e) {
        $_SESSION['error_message'] = 'حدث خطأ في حذف المشروع: ' . $e->getMessage();
    }
    
    redirect(SITE_URL . '/admin/projects.php');
}

// عرض رسائل النظام
if (isset($_SESSION['error_message'])) {
    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>' . $_SESSION['error_message'] . '
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>';
    unset($_SESSION['error_message']);
}

if (isset($_SESSION['success_message'])) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>' . $_SESSION['success_message'] . '
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>';
    unset($_SESSION['success_message']);
}
?>

<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">المشاريع</h5>
                <div>
                    <a href="projects.php" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-sync-alt me-1"></i> تحديث
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>العنوان</th>
                                <th>الصورة</th>
                                <th>الفئة</th>
                                <th>التقنيات</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $pdo->query("SELECT * FROM projects ORDER BY created_at DESC");
                            $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            foreach ($projects as $project) {
                                $image_path = '../assets/images/' . $project['image'];
                                $image_exists = !empty($project['image']) && file_exists($image_path);
                                
                                echo '<tr>
                                    <td>'.$project['id'].'</td>
                                    <td>'.$project['title'].'</td>
                                    <td>';
                                
                                if ($image_exists) {
                                    echo '<img src="'.SITE_URL.'/assets/images/'.$project['image'].'" width="50" height="50" alt="'.$project['title'].'" class="img-thumbnail object-fit-cover">';
                                } else {
                                    echo '<span class="badge bg-warning text-dark">لا توجد صورة</span>';
                                }
                                
                                echo '</td>
                                    <td>'.$project['category'].'</td>
                                    <td>';
                                
                                if (!empty($project['technologies'])) {
                                    $techs = explode(',', $project['technologies']);
                                    foreach ($techs as $tech) {
                                        echo '<span class="badge bg-light text-dark border me-1 mb-1">'.trim($tech).'</span>';
                                    }
                                }
                                
                                echo '</td>
                                    <td>';
                                
                                echo $project['is_active'] ? 
                                    '<span class="badge bg-success">نشط</span>' : 
                                    '<span class="badge bg-secondary">غير نشط</span>';
                                
                                echo '</td>
                                    <td>
                                        <a href="projects.php?edit='.$project['id'].'" class="btn btn-sm btn-primary" title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="projects.php?delete='.$project['id'].'" class="btn btn-sm btn-danger" 
                                           onclick="return confirm(\'هل أنت متأكد من حذف هذا المشروع؟\')" title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </a>
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
                <h5><?php echo isset($_GET['edit']) ? 'تعديل مشروع' : 'إضافة مشروع جديد'; ?></h5>
            </div>
            <div class="card-body">
                <?php
                // إذا كان في وضع التعديل، جلب بيانات المشروع
                $project = null;
                if (isset($_GET['edit'])) {
                    $id = intval($_GET['edit']);
                    $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
                    $stmt->execute([$id]);
                    $project = $stmt->fetch(PDO::FETCH_ASSOC);
                }
                ?>
                
                <form method="post" enctype="multipart/form-data" id="projectForm">
                    <?php if (isset($_GET['edit'])): ?>
                        <input type="hidden" name="id" value="<?php echo $project['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">عنوان المشروع</label>
                        <input type="text" class="form-control" id="title" name="title" 
                               value="<?php echo isset($project) ? $project['title'] : ''; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">وصف المشروع</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required><?php echo isset($project) ? $project['description'] : ''; ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="image" class="form-label">صورة المشروع الرئيسية</label>
                        <input type="file" class="form-control" id="image" name="image" <?php echo !isset($project) ? 'required' : ''; ?> accept="image/jpeg, image/png, image/gif, image/webp">
                        
                        <?php if (isset($project) && !empty($project['image'])): ?>
                            <div class="mt-2">
                                <?php 
                                $image_path = '../assets/images/' . $project['image'];
                                if (file_exists($image_path)): ?>
                                    <img src="<?php echo SITE_URL; ?>/assets/images/<?php echo $project['image']; ?>" width="100" alt="Current Image" class="img-thumbnail" id="imagePreview">
                                <?php else: ?>
                                    <div class="alert alert-warning py-2">ملف الصورة غير موجود على الخادم</div>
                                <?php endif; ?>
                                <input type="hidden" name="existing_image" value="<?php echo $project['image']; ?>">
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="gallery_images" class="form-label">معرض الصور (يمكن اختيار أكثر من صورة)</label>
                        <input type="file" class="form-control" id="gallery_images" name="gallery_images[]" multiple accept="image/jpeg, image/png, image/gif, image/webp">
                        
                        <?php if (isset($project) && !empty($project['gallery_images'])): 
                            $gallery_images = explode(',', $project['gallery_images']);
                        ?>
                            <div class="mt-2">
                                <h6>الصور الحالية:</h6>
                                <div class="d-flex flex-wrap gap-2" id="galleryContainer">
                                    <?php foreach ($gallery_images as $image): ?>
                                        <?php 
                                        $image_path = '../assets/images/' . $image;
                                        if (file_exists($image_path)): ?>
                                            <div class="position-relative gallery-item">
                                                <img src="<?php echo SITE_URL; ?>/assets/images/<?php echo $image; ?>" width="60" height="60" class="img-thumbnail object-fit-cover">
                                                <input type="hidden" name="existing_gallery_images[]" value="<?php echo $image; ?>">
                                                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 start-0 p-0 remove-gallery-image" 
                                                        style="width: 20px; height: 20px; font-size: 10px;"
                                                        data-image="<?php echo $image; ?>">
                                                    ×
                                                </button>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                                <input type="hidden" name="deleted_gallery_images" id="deletedGalleryImages" value="">
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="category" class="form-label">الفئة</label>
                        <input type="text" class="form-control" id="category" name="category" 
                               value="<?php echo isset($project) ? $project['category'] : ''; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="client" class="form-label">العميل</label>
                        <input type="text" class="form-control" id="client" name="client" 
                               value="<?php echo isset($project) ? $project['client'] : ''; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="project_date" class="form-label">تاريخ المشروع</label>
                        <input type="date" class="form-control" id="project_date" name="project_date" 
                               value="<?php echo isset($project) ? $project['project_date'] : ''; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="technologies" class="form-label">التقنيات المستخدمة (مفصولة بفواصل)</label>
                        <textarea class="form-control" id="technologies" name="technologies" rows="2"><?php echo isset($project) ? $project['technologies'] : ''; ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="features" class="form-label">الميزات الرئيسية (سطر لكل ميزة)</label>
                        <textarea class="form-control" id="features" name="features" rows="3"><?php echo isset($project) ? $project['features'] : ''; ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="challenges" class="form-label">التحديات والحلول (سطر لكل تحدٍ)</label>
                        <textarea class="form-control" id="challenges" name="challenges" rows="3"><?php echo isset($project) ? $project['challenges'] : ''; ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="external_url" class="form-label">رابط خارجي (اختياري)</label>
                        <input type="url" class="form-control" id="external_url" name="external_url" 
                               value="<?php echo isset($project) ? $project['external_url'] : ''; ?>">
                    </div>
                    
                    <div class="mb-3 form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" 
                               <?php echo (isset($project) && $project['is_active']) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="is_active">نشط</label>
                    </div>
                    
                    <div class="d-grid">
                        <?php if (isset($_GET['edit'])): ?>
                            <button type="submit" name="update_project" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>تحديث المشروع
                            </button>
                            <a href="projects.php" class="btn btn-secondary mt-2">
                                <i class="fas fa-times me-2"></i>إلغاء
                            </a>
                        <?php else: ?>
                            <button type="submit" name="add_project" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>إضافة مشروع
                            </button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// معاينة الصورة الرئيسية قبل الرفع
document.getElementById('image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            let preview = document.getElementById('imagePreview');
            if (!preview) {
                const container = document.createElement('div');
                container.className = 'mt-2';
                preview = document.createElement('img');
                preview.id = 'imagePreview';
                preview.className = 'img-thumbnail';
                preview.width = 100;
                container.appendChild(preview);
                e.target.parentNode.appendChild(container);
            }
            preview.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
});

// إدارة معرض الصور
document.addEventListener('DOMContentLoaded', function() {
    const galleryContainer = document.getElementById('galleryContainer');
    const deletedImagesInput = document.getElementById('deletedGalleryImages');
    let deletedImages = [];
    
    if (galleryContainer) {
        galleryContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-gallery-image')) {
                const imageName = e.target.getAttribute('data-image');
                deletedImages.push(imageName);
                deletedImagesInput.value = JSON.stringify(deletedImages);
                e.target.closest('.gallery-item').remove();
            }
        });
    }
    
    // معاينة صور المعرض قبل الرفع
    const galleryInput = document.getElementById('gallery_images');
    if (galleryInput) {
        galleryInput.addEventListener('change', function(e) {
            const files = e.target.files;
            const previewContainer = document.getElementById('galleryContainer') || document.createElement('div');
            previewContainer.id = 'galleryContainer';
            previewContainer.className = 'd-flex flex-wrap gap-2 mt-2';
            
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                if (file.type.match('image.*')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const preview = document.createElement('img');
                        preview.src = e.target.result;
                        preview.width = 60;
                        preview.height = 60;
                        preview.className = 'img-thumbnail object-fit-cover';
                        previewContainer.appendChild(preview);
                    };
                    reader.readAsDataURL(file);
                }
            }
            
            if (!document.getElementById('galleryContainer')) {
                this.parentNode.appendChild(previewContainer);
            }
        });
    }
});

// التحقق من النموذج قبل الإرسال
document.getElementById('projectForm').addEventListener('submit', function(e) {
    const title = document.getElementById('title').value.trim();
    if (!title) {
        e.preventDefault();
        alert('يرجى إدخال عنوان المشروع');
        document.getElementById('title').focus();
    }
});
</script>

<?php
require_once __DIR__ . '/includes/admin-footer.php';
?>