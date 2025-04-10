<?php
$conn = new mysqli("localhost", "root", "", "_____dhiq (3)");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['booking_id']) && isset($_POST['new_location'])) {
    $booking_id = intval($_POST['booking_id']);
    $new_location = trim($_POST['new_location']);

    if (!empty($new_location)) {
        // ✅ Use prepared statement to avoid SQL injection and syntax errors
        $stmt = $conn->prepare("UPDATE Booking SET location = ? WHERE id = ?");
        $stmt->bind_param("si", $new_location, $booking_id);

        if ($stmt->execute()) {
            echo "Booking updated successfully!";
        } else {
            echo "Error updating booking: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Location cannot be empty.";
    }
} else {
    echo "Invalid input.";
}

$conn->close();
