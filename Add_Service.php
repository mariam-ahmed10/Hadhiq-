<?php
session_start();
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $targetDir = "uploads/";
    $imageName = basename($_FILES["serviceImage"]["name"]);
    $uniqueName = time() . "_" . $imageName;
    $targetFile = $targetDir . $uniqueName;

    if (!move_uploaded_file($_FILES["serviceImage"]["tmp_name"], $targetFile)) {
        die("Image upload failed.");
    }

    $Type = $_POST['serviceType'];
    $description = $_POST['Description'];
    $availability = $_POST['availability'];
    $imagePath = $targetFile;
    $provider_id = $_SESSION['user_id'];  // ✅ must have session_start() above

    $sql = "INSERT INTO service (Type, description, availability, ImagePath, ProviderID)
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $Type, $description, $availability, $imagePath, $provider_id);

    if ($stmt->execute()) {
        echo "<script>alert('Service added successfully!'); window.location.href='Provider_Homepage.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Add a Service</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/png" href="img/Hadhiq2.png" sizes="32x32" />
    <link rel="icon" type="image/png" href="img/Hadhiq2.png" sizes="16x16" />
    <style>
        .button {
            background-color:#081833 !important;
            border: 0.125rem solid #004369 !important;
            border-radius: 0.625rem;
            color:white !important;
            cursor: pointer;
            transition-duration: 0.2s;
            text-decoration: none; 
            padding:2%;
            margin: 20px;   
            text-align: center;
        }
        .button:hover {
            background-color: #004369 !important;
            border: 0.125rem solid  #004369 !important;
            color: #081833;
        }
    </style>
</head>
<body>
    <nav> 
        <a href="Provider_Homepage.php"><img id="logo" src="img/HadhiqBG.png"></a>
        <div class="nav-links">
            <ul>
                <li><a href="Provider_Homepage.php" class="active">Home</a></li>
                <li><a href="UpCoomingBooking.php">Upcoming Bookings</a></li>
                <li><a href="profileS.php">Profile</a></li>
               <li><a href="logout.php">Log out</a></li>
            </ul>
        </div>
    </nav>

    <br><br>
    <div class="breadcrumb">
        <a href="Provider_Homepage.php" accesskey="h">Home</a><span> &gt;&gt;</span> <span id="thispage">Add a Service</span>
    </div>
    <h1 class="company"> Company X Services</h1>
    <br><br>

    <form id="addServiceForm" action="Add_Service.php" method="POST" enctype="multipart/form-data">
        <fieldset class="group">
            <legend>Add a New Service</legend>
            
            <label for="serviceType">Service Type:</label>
            <input type="text" id="serviceType" name="serviceType" required><br><br>

            <label for="Description">Description:</label>
            <input type="text" id="Description" name="Description" required><br><br>

            <label for="availability">Availability:</label>
            <input type="text" id="availability" name="availability" required><br><br>

            <label for="serviceImage">Service Image:</label>
            <input type="file" id="serviceImage" name="serviceImage" accept="image/*" required><br><br>

            <input type="submit" class="button" value="Add Service">
        </fieldset>
    </form>

    <footer>
        <hr>
        <h4>Contact Us At: </h4>
        <h4>support@Hadhiq.com</h4>
        <br>
        <div class="footer-bottom">
            © 2025 Hadhiq - All Rights Reserved
        </div>
    </footer>
</body>
</html>
