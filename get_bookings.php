<?php
session_start();
$conn = new mysqli("localhost", "root", "", "_____dhiq (3)");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "Not logged in"]);
    exit();
}

$providerID = $_SESSION['user_id'];

$sql = "SELECT 
            Booking.*, 
            Customer.Name AS CustomerName, 
            Customer.Email AS CustomerEmail,
            Service.Type AS ServiceType,
            Service.Description, 
            Service.availability
        FROM Booking 
        JOIN Customer ON Booking.CustomerID = Customer.CustomerID 
        JOIN Service ON Booking.ServiceID = Service.ServiceID 
        WHERE Service.ProviderID = ? AND Booking.status IN ('Pending', 'Accepted')";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $providerID);
$stmt->execute();
$result = $stmt->get_result();

$bookings = [];
while ($row = $result->fetch_assoc()) {
    $bookings[] = $row;
}

header('Content-Type: application/json');
echo json_encode($bookings);
?>
