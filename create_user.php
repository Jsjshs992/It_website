<?php
require_once 'includes/config.php';

$username = 'admin';
$password = 'admin123';
$email = 'admin@example.com';

// تشفير كلمة المرور
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
    $stmt->execute([$username, $hashed_password, $email]);
    
    echo "تم إنشاء المستخدم بنجاح!";
} catch (PDOException $e) {
    echo "حدث خطأ: " . $e->getMessage();
}