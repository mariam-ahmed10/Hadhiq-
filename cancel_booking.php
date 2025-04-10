<?php
$conn = new mysqli("localhost", "root", "", "_____dhiq (3)");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['booking_id'])) {
    $booking_id = $_POST['booking_id'];

    $stmt = $conn->prepare("UPDATE Booking SET status = 'Canceled' WHERE id = ?");
    $stmt->bind_param("i", $booking_id);

    if ($stmt->execute()) {
        echo "Booking canceled successfully!";
    } else {
        echo "Error canceling booking.";
    }

    $stmt->close();
} else {
    echo "Invalid input.";
}

$conn->close();
?>