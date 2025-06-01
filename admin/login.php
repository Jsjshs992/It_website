<?php
require_once '../includes/config.php';

// إذا كان المستخدم مسجل الدخول بالفعل، توجيهه إلى لوحة التحكم
if (isLoggedIn()) {
    redirect(SITE_URL . '/admin/dashboard.php');
}

// تهيئة متغيرات
$error_message = '';
$username = '';

// معالجة تسجيل الدخول
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // التحقق من وجود البيانات المطلوبة
    if (empty($_POST['username']) || empty($_POST['password'])) {
        $error_message = 'اسم المستخدم وكلمة المرور مطلوبان';
    } else {
        $username = sanitizeInput($_POST['username']);
        $password = $_POST['password']; // لا نستخدم sanitizeInput على كلمة المرور
        
        // التحقق من بيانات المستخدم
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                // التحقق من كلمة المرور باستخدام password_verify
                if (password_verify($password, $user['password'])) {
                    // تسجيل الدخول الناجح
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['user_role'] = 'admin';
                    
                    // إنشاء رمز CSRF للحماية
                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                    
                    // توجيه المستخدم
                    redirect(SITE_URL . '/admin/dashboard.php');
                } else {
                    $error_message = 'اسم المستخدم أو كلمة المرور غير صحيحة';
                }
            } else {
                $error_message = 'اسم المستخدم أو كلمة المرور غير صحيحة';
            }
        } catch (PDOException $e) {
            $error_message = 'حدث خطأ في النظام، يرجى المحاولة لاحقاً';
            // يمكنك تسجيل الخطأ في ملف log هنا
            error_log('Login Error: ' . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول | لوحة التحكم</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap RTL -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-rtl@5.3.0/dist/css/bootstrap-rtl.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Tajawal', sans-serif;
        }
        .login-card {
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        .form-control:focus {
            border-color: #4a6cf7;
            box-shadow: 0 0 0 0.25rem rgba(74, 108, 247, 0.25);
        }
        .btn-primary {
            background-color: #4a6cf7;
            border-color: #4a6cf7;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card login-card">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <h2 class="fw-bold">تسجيل الدخول</h2>
                            <p class="text-muted">لوحة تحكم الموقع</p>
                        </div>
                        
                        <?php if ($error_message): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <?php echo $error_message; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form method="post" autocomplete="off">
                            <div class="mb-3">
                                <label for="username" class="form-label">اسم المستخدم</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" class="form-control" id="username" name="username" 
                                           value="<?php echo htmlspecialchars($username); ?>" required autofocus>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">كلمة المرور</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                            </div>
                            
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember">تذكرني</label>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">تسجيل الدخول</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>