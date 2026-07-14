<?php
// إعلام المتصفح أن الرد سيكون دائماً بصيغة JSON ليفهمه الجافا سكريبت
header('Content-Type: application/json; charset=utf-8');

// 1. الاتصال بقاعدة البيانات
$host = "localhost";
$username = "root";
$password = "";
$dbname = "invoice_system";

$conn = new mysqli($host, $username, $password, $dbname);
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "فشل الاتصال بقاعدة البيانات"]);
    exit;
}

// 2. قراءة البيانات القادمة من طلب الـ Fetch (JSON body)
$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);

if (!$data) {
    echo json_encode(["status" => "error", "message" => "لم يتم استلام أي بيانات صالحة."]);
    exit;
}

// 3. حماية وتجهيز البيانات الأساسية
$buyer_name = $conn->real_escape_string($data['buyer_name']);
$buyer_phone = $conn->real_escape_string($data['buyer_phone']);
$buyer_address = $conn->real_escape_string($data['buyer_address']);
$total_amount = intval($data['total_amount']);
$items = $data['items'];

// 4. بدء "المعاملة الآمنة" (Transaction)
// لضمان أنه في حال فشلت إضافة أي مادة يتم إلغاء العملية كاملة ولا تُخزن فاتورة مشوهة
$conn->begin_transaction();

try {
    // إدخال رأس الفاتورة
    $stmt = $conn->prepare("INSERT INTO invoices (buyer_name, buyer_phone, buyer_address, total_amount) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssd", $buyer_name, $buyer_phone, $buyer_address, $total_amount);
    $stmt->execute();
    $invoice_id = $conn->insert_id; // جلب المعرف التلقائي للفاتورة المدخلة فوراً

    // إدخال مواد الفاتورة بالتفصيل
    $stmt_item = $conn->prepare("INSERT INTO invoice_items (invoice_id, item_name, quantity, price, specifications, serial_number) VALUES (?, ?, ?, ?, ?, ?)");
    
    foreach ($items as $item) {
        $item_name = $item['name'];
        $quantity = intval($item['qty']);
        $price = floatval($item['price']);
        $spec = $item['spec'];
        $sn = $item['sn'];

        $stmt_item->bind_param("isidss", $invoice_id, $item_name, $quantity, $price, $spec, $sn);
        $stmt_item->execute();
    }

    // إذا تمت جميع العمليات بنجاح، نعتمد التغييرات نهائياً بقاعدة البيانات
    $conn->commit();

    echo json_encode([
        "status" => "success", 
        "invoice_id" => $invoice_id, 
        "message" => "تم حفظ الفاتورة بنجاح وتوليد الرقم التعريفي!"
    ]);

} catch (Exception $e) {
    // في حال حدوث أي خطأ نقوم بالتراجع عن كل شيء لإبقاء قاعدة البيانات سليمة
    $conn->rollback();
    echo json_encode([
        "status" => "error", 
        "message" => "حدث فشل غير متوقع: " . $e->getMessage()
    ]);
}

$conn->close();
?>