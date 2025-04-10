<?php
// الاتصال بقاعدة البيانات
$conn = new mysqli("localhost", "root", "", "_____dhiq (3)");
// التأكد من الاتصال
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// التحقق من البيانات المرسلة
if (isset($_POST['bookingId']) && isset($_POST['status'])) {
    $bookingId = $_POST['bookingId'];
    $status = $_POST['status'];

    // تحديث حالة الحجز
    $sql = "UPDATE Booking SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $bookingId);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Missing parameters"]);
}

$conn->close();
?>