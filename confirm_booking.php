<?php
session_start();  // ✅ Line 1: Start session

include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customerName = $_POST['customerName'];
    $phone = $_POST['phoneNumber'];
    $location = $_POST['location'];
    $serviceID = $_POST['serviceID'];
    
   
   $customerID = $_SESSION['user_id'];  // if your login uses this instead

    $status = 'Accepted'; // Or 'Pending' if needed

    // Insert into booking table
    $stmt = $conn->prepare("INSERT INTO booking (PhonNo, Location, ServiceID, CustomerID, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiis", $phone, $location, $serviceID, $customerID, $status);

    if ($stmt->execute()) {
        header("Location: MyBooking.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request";
}
?>
