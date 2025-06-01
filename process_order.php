<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// تضمين مكتبة PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'includes/PHPMailer/src/Exception.php';
require_once 'includes/PHPMailer/src/PHPMailer.php';
require_once 'includes/PHPMailer/src/SMTP.php';
// التحقق من أن الطلب من نوع POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: services.php");
    exit();
}

// التحقق من وجود معرف الخدمة
if (!isset($_POST['service_id'])) {
    header("Location: services.php");
    exit();
}

$service_id = intval($_POST['service_id']);

// جلب معلومات الخدمة
$stmt = $pdo->prepare("SELECT * FROM services WHERE id = ?");
$stmt->execute([$service_id]);
$service = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$service) {
    header("Location: services.php");
    exit();
}

// تنظيف بيانات الإدخال
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$customer_name = clean_input($_POST['customer_name']);
$customer_email = clean_input($_POST['customer_email']);
$customer_phone = clean_input($_POST['customer_phone']);
$deadline = !empty($_POST['deadline']) ? clean_input($_POST['deadline']) : null;
$notes = !empty($_POST['notes']) ? clean_input($_POST['notes']) : null;

// معالجة البيانات الخاصة بالخدمة
$service_data = [];
$service_type = $service['service_type'] ?? 'default';

switch ($service_type) {
    case 'website_design':
        $service_data['website_type'] = clean_input($_POST['website_type']);
        $service_data['pages_count'] = intval($_POST['pages_count']);
        $service_data['preferred_colors'] = clean_input($_POST['preferred_colors']);
        $service_data['features'] = isset($_POST['features']) ? array_map('clean_input', $_POST['features']) : [];
        $service_data['website_examples'] = clean_input($_POST['website_examples']);
        break;
        
    case 'marketing':
        $service_data['platforms'] = isset($_POST['platforms']) ? array_map('clean_input', $_POST['platforms']) : [];
        $service_data['monthly_budget'] = intval($_POST['monthly_budget']);
        $service_data['goal'] = clean_input($_POST['goal']);
        break;
        
    default:
        $service_data['details'] = clean_input($_POST['service_details']);
}

// معالجة الملفات المرفوعة
$uploaded_files = [];
if (!empty($_FILES['attachments']['name'][0])) {
    $upload_dir = 'uploads/orders/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    for ($i = 0; $i < count($_FILES['attachments']['name']); $i++) {
        if ($_FILES['attachments']['error'][$i] === UPLOAD_ERR_OK) {
            $file_name = time() . '_' . basename($_FILES['attachments']['name'][$i]);
            $file_path = $upload_dir . $file_name;
            
            $allowed_types = ['image/jpeg', 'image/png', 'application/pdf'];
            $file_type = $_FILES['attachments']['type'][$i];
            $file_size = $_FILES['attachments']['size'][$i];
            
            if (in_array($file_type, $allowed_types) && $file_size <= 2 * 1024 * 1024) {
                if (move_uploaded_file($_FILES['attachments']['tmp_name'][$i], $file_path)) {
                    $uploaded_files[] = $file_name;
                }
            }
        }
    }
}

// حفظ الطلب في قاعدة البيانات
try {
    $pdo->beginTransaction();
    
    $stmt = $pdo->prepare("INSERT INTO orders (service_id, customer_name, customer_email, customer_phone, deadline, notes, service_data, files) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    $files_str = !empty($uploaded_files) ? implode(',', $uploaded_files) : null;
    $service_data_json = json_encode($service_data, JSON_UNESCAPED_UNICODE);
    
    $stmt->execute([
        $service_id,
        $customer_name,
        $customer_email,
        $customer_phone,
        $deadline,
        $notes,
        $service_data_json,
        $files_str
    ]);
    
    $order_id = $pdo->lastInsertId();
    $pdo->commit();
    
    // إنشاء رسالة الواتساب
    $whatsapp_message = "📌 *طلب خدمة جديد* #$order_id\n\n";
    $whatsapp_message .= "🛠 *الخدمة:* " . $service['title'] . "\n";
    $whatsapp_message .= "👤 *العميل:* " . $customer_name . "\n";
    $whatsapp_message .= "📞 *الهاتف:* " . $customer_phone . "\n";
    $whatsapp_message .= "📧 *البريد:* " . $customer_email . "\n\n";
    
    if ($service_type == 'website_design') {
        $whatsapp_message .= "🌐 *نوع الموقع:* " . $service_data['website_type'] . "\n";
        $whatsapp_message .= "📑 *عدد الصفحات:* " . $service_data['pages_count'] . "\n";
        $whatsapp_message .= "🎨 *الألوان:* " . $service_data['preferred_colors'] . "\n";
        
        if (!empty($service_data['features'])) {
            $whatsapp_message .= "✨ *الميزات:* " . implode(', ', $service_data['features']) . "\n";
        }
        
        if (!empty($service_data['website_examples'])) {
            $whatsapp_message .= "🔗 *مواقع مثالية:* " . str_replace("\n", ', ', $service_data['website_examples']) . "\n";
        }
    } 
    elseif ($service_type == 'marketing') {
        $whatsapp_message .= "📱 *منصات التواصل:* " . implode(', ', $service_data['platforms']) . "\n";
        $whatsapp_message .= "💰 *الميزانية:* " . $service_data['monthly_budget'] . " USD\n";
        $whatsapp_message .= "🎯 *الهدف:* " . $service_data['goal'] . "\n";
    }
    
    if ($deadline) {
        $whatsapp_message .= "⏳ *الموعد النهائي:* " . $deadline . "\n";
    }
    if ($notes) {
        $whatsapp_message .= "📝 *ملاحظات إضافية:* " . $notes . "\n";
    }
    
    $your_whatsapp_number = "967780267990";
    $whatsapp_link = "https://wa.me/$your_whatsapp_number?text=" . urlencode($whatsapp_message);
    $_SESSION['whatsapp_link'] = $whatsapp_link;
    
    // إرسال الإيميل باستخدام PHPMailer
    $mail = new PHPMailer(true);
    try {
        // إعدادات السيرفر
        $mail->isSMTP();
        $mail->Host = 'localhost';
        $mail->SMTPAuth = false;
        $mail->Username = 'kinganwr2016@gmail.com'; // بريدك الإلكتروني
        $mail->Password = '3280'; // كلمة سر التطبيق
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        // المرسل والمستقبل
        $mail->setFrom(SITE_EMAIL, SITE_TITLE);
        $mail->addAddress(ADMIN_EMAIL);
        $mail->addReplyTo($customer_email, $customer_name);

        // محتوى الإيميل
        $mail->isHTML(true);
        $mail->Subject = "طلب خدمة جديد #$order_id - " . $service['title'];
        
        $email_content = "
        <html dir='rtl'>
        <head>
            <title>طلب خدمة جديد</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; }
                .header { background-color: #f8f9fa; padding: 20px; text-align: center; }
                .content { padding: 20px; }
                .details { background-color: #f1f1f1; padding: 15px; border-radius: 5px; }
            </style>
        </head>
        <body>
            <div class='header'>
                <h2>طلب خدمة جديد #$order_id</h2>
                <h3>" . htmlspecialchars($service['title']) . "</h3>
            </div>
            
            <div class='content'>
                <h4>معلومات العميل:</h4>
                <p><strong>الاسم:</strong> " . htmlspecialchars($customer_name) . "</p>
                <p><strong>البريد الإلكتروني:</strong> " . htmlspecialchars($customer_email) . "</p>
                <p><strong>الهاتف:</strong> " . htmlspecialchars($customer_phone) . "</p>
                
                <h4>تفاصيل الطلب:</h4>
                <div class='details'>
                    " . nl2br(htmlspecialchars($whatsapp_message)) . "
                </div>
                
                <p>يمكنك الرد على هذا الطلب عبر البريد أو الواتساب.</p>
                <p><a href='$whatsapp_link' style='background-color: #25D366; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>فتح محادثة واتساب</a></p>
            </div>
        </body>
        </html>
        ";
        
        $mail->Body = $email_content;
        $mail->AltBody = strip_tags($whatsapp_message);

        $mail->send();
    } catch (Exception $e) {
        error_log("فشل إرسال الإيميل: {$mail->ErrorInfo}");
    }
    
    // توجيه المستخدم إلى صفحة التأكيد
    header("Location: order_confirmation.php?order_id=$order_id");
    exit();
    
} catch (PDOException $e) {
    $pdo->rollBack();
    die("حدث خطأ أثناء حفظ الطلب: " . $e->getMessage());
}
?>