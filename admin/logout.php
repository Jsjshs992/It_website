<?php
require_once '../includes/config.php';

// إنهاء الجلسة
session_unset();
session_destroy();

// توجيه المستخدم إلى صفحة تسجيل الدخول
redirect(SITE_URL . '/admin/login.php');
?>