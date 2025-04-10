<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    die("No session set");
}

$customerID = $_SESSION['user_id'];

$conn = new mysqli("localhost", "root", "", "_____dhiq (3)");

ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// مؤقت لاختبار فقط
//$customerID = 1;

$sql = "SELECT b.id, s.Type AS service_name, s.availability, s.ServiceID as service_id, b.location, b.status
        FROM Booking b
        JOIN Service s ON b.ServiceID = s.ServiceID
        WHERE b.CustomerID = ? AND status = 'Accepted'";


$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customerID);
$stmt->execute();
$result = $stmt->get_result();

$bookings = [];

while ($row = $result->fetch_assoc()) {
    $bookings[] = $row;
}
file_put_contents("debug_bookings.txt", print_r($bookings, true));

echo json_encode($bookings);
?>