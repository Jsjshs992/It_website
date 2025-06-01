<?php
// دالة لإعادة تنسيق تاريخ MySQL
function formatDate($date) {
    return date('d/m/Y', strtotime($date));
}

// دالة لقص النص
function shortenText($text, $chars = 100) {
    $text = strip_tags($text);
    if (strlen($text) > $chars) {
        $text = substr($text, 0, $chars) . '...';
    }
    return $text;
}

// دالة لتحويل النص إلى رابط
function slugify($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    
    if (empty($text)) {
        return 'n-a';
    }
    
    return $text;
}

// دالة لحماية المدخلات
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// دالة للتحقق من تسجيل الدخول
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// دالة للتحقق من صلاحيات المستخدم
function isAdmin() {
    return (isLoggedIn() && $_SESSION['user_role'] == 'admin');
}

// دالة لإعادة توجيه المستخدم
function redirect($url) {
    if (headers_sent()) {
        die("
            <script>
                window.location.href = '$url';
            </script>
            <noscript>
                <meta http-equiv='refresh' content='0;url=$url'>
            </noscript>
        ");
    } else {
        if (!preg_match("/^https?:\/\//i", $url)) {
            $url = SITE_URL . '/' . ltrim($url, '/');
        }
        header("Location: $url");
        exit();
    }
}

// دالة لعرض الرسائل
function displayMessage($type, $message) {
    return '<div class="alert alert-'.$type.' alert-dismissible fade show" role="alert">
        '.$message.'
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>';
}

// دالة للحصول على اسم نوع الخدمة
function getServiceTypeName($type) {
    $types = [
        'website_design' => 'تصميم مواقع',
        'marketing' => 'تسويق إلكتروني',
        'graphic_design' => 'تصميم جرافيك',
        'programming' => 'برمجة وتطوير',
        'consulting' => 'استشارات',
        'other' => 'أخرى'
    ];
    
    return $types[$type] ?? $type;
}

// دالة لإرسال رسالة واتساب
function sendWhatsAppMessage($phone, $message) {
    $api_url = "https://api.whatsapp.com/send?phone=" . urlencode($phone) . "&text=" . urlencode($message);
    return $api_url;
}

// دالة محسنة لتحميل ملف
function uploadFile($file, $target_dir = UPLOAD_DIR) {
    // التحقق من وجود الملف
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return [
            'success' => false,
            'message' => $this->getUploadError($file['error'] ?? null)
        ];
    }

    // إنشاء المجلد إذا لم يكن موجوداً
    if (!file_exists($target_dir)) {
        if (!mkdir($target_dir, 0755, true)) {
            return [
                'success' => false,
                'message' => 'فشل في إنشاء مجلد التحميل'
            ];
        }
    }

    // التحقق من قابلية الكتابة على المجلد
    if (!is_writable($target_dir)) {
        return [
            'success' => false,
            'message' => 'المجلد الهدف غير قابل للكتابة'
        ];
    }

    // معلومات الملف
    $filename = basename($file['name']);
    $file_size = $file['size'];
    $file_tmp = $file['tmp_name'];
    $file_type = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    // التحقق من حجم الملف
    if ($file_size > MAX_FILE_SIZE) {
        return [
            'success' => false,
            'message' => 'حجم الملف يتجاوز الحد المسموح (' . (MAX_FILE_SIZE / 1024 / 1024) . 'MB)'
        ];
    }

    // التحقق من نوع الملف
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($file_type, $allowed_types)) {
        return [
            'success' => false,
            'message' => 'نوع الملف غير مسموح به. المسموح: ' . implode(', ', $allowed_types)
        ];
    }

    // إنشاء اسم فريد للملف
    $new_filename = uniqid('img_', true) . '.' . $file_type;
    $target_path = rtrim($target_dir, '/') . '/' . $new_filename;

    // التحقق من أن الملف ليس صورة خبيثة
    if (function_exists('exif_imagetype')) {
        $image_type = exif_imagetype($file_tmp);
        if (!$image_type) {
            return [
                'success' => false,
                'message' => 'الملف ليس صورة صالحة'
            ];
        }
    }

    // نقل الملف إلى المجلد الهدف
    if (move_uploaded_file($file_tmp, $target_path)) {
        // تغيير صلاحيات الملف
        chmod($target_path, 0644);
        
        return [
            'success' => true,
            'filename' => $new_filename,
            'full_path' => $target_path,
            'message' => 'تم رفع الملف بنجاح'
        ];
    } else {
        return [
            'success' => false,
            'message' => 'حدث خطأ أثناء محاولة حفظ الملف'
        ];
    }
}

// دالة مساعدة للحصول على رسائل خطأ الرفع
function getUploadError($error_code) {
    $upload_errors = [
        UPLOAD_ERR_OK => 'تم الرفع بنجاح',
        UPLOAD_ERR_INI_SIZE => 'حجم الملف يتجاوز الحد المسموح به في php.ini',
        UPLOAD_ERR_FORM_SIZE => 'حجم الملف يتجاوز الحد المسموح به في النموذج',
        UPLOAD_ERR_PARTIAL => 'تم رفع جزء من الملف فقط',
        UPLOAD_ERR_NO_FILE => 'لم يتم اختيار ملف للرفع',
        UPLOAD_ERR_NO_TMP_DIR => 'مجلد التخزين المؤقت غير موجود',
        UPLOAD_ERR_CANT_WRITE => 'فشل في كتابة الملف على القرص',
        UPLOAD_ERR_EXTENSION => 'تم إيقاف الرفع بواسطة إضافة PHP'
    ];

    return $upload_errors[$error_code] ?? 'خطأ غير معروف في رفع الملف';
}
?>