<?php
// إعدادات قاعدة البيانات
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'it_website');

// إعدادات الموقع
define('SITE_TITLE', 'موقعنا الديناميكي');
define('SITE_URL', 'http://localhost/it_website');

// إعدادات البريد الإلكتروني
define('SITE_EMAIL', 'kinganwr2016@gmail.com');
define('ADMIN_EMAIL', 'kinganwr2016@gmail.com');
define('CONTACT_EMAIL', 'kinganwr2016@gmail.com');

// إعدادات الواتساب
define('WHATSAPP_NUMBER', '967780267990'); // استبدله برقمك مع رمز الدولة

// إعدادات رفع الملفات
define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('MAX_FILE_SIZE', 2 * 1024 * 1024); // 2MB
define('ALLOWED_FILE_TYPES', ['image/jpeg', 'image/png', 'application/pdf']);

// الاتصال بقاعدة البيانات
try {
    $pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec('SET NAMES utf8');
} catch (PDOException $e) {
    die('Connection failed: ' . $e->getMessage());
}

// بدء الجلسة
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// تضمين ملف الدوال
require_once 'functions.php';
?>