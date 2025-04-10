<?php
$conn = new mysqli("localhost", "root", "", "_____dhiq (3)");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if(isset($_POST['service_id']) && isset($_POST['customer_id']) && isset($_POST['review'])) {
    $service_id = $_POST['service_id'];
    $customer_id = $_POST['customer_id'];
    $review = $_POST['review'];

    $stmt = $conn->prepare("INSERT INTO review (Description, ServiceID, CustomerID) VALUES (?, ?, ?)");
    $stmt->bind_param("sii", $review, $service_id, $customer_id);

    if ($stmt->execute()) {
        echo "Review submitted successfully!";
    } else {
        echo "Error saving review.";
    }

    $stmt->close();
} else {
    echo "Invalid input.";
}

$conn->close();
?>
