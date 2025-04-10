<?php
session_start();
include("db_connection.php");

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'provider') {
    header("Location: index.php");
    exit();
}

$provider_id = $_SESSION['user_id'];

// Now fetch only the services that belong to this provider
$sql = "SELECT * FROM service WHERE ProviderID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $provider_id);
$stmt->execute();
$result = $stmt->get_result();
$services = $result->fetch_all(MYSQLI_ASSOC);
?>
<a href="index.php"></a>


<?php

$message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['edit_id'])) {
    $editID = intval($_POST['edit_id']);
    $Type = $_POST['serviceType'];
    $description = $_POST['Description'];
    $availability = $_POST['availability'];

    // Handle optional image upload
    if (!empty($_FILES['newImage']['name'])) {
        $targetDir = "uploads/";
        $imageName = basename($_FILES["newImage"]["name"]);
        $uniqueName = time() . "_" . $imageName;
        $targetFile = $targetDir . $uniqueName;

        if (move_uploaded_file($_FILES["newImage"]["tmp_name"], $targetFile)) {
            $sql = "UPDATE service SET Type=?, Description=?, availability=?, ImagePath=? WHERE ServiceID=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssi", $Type, $description, $availability, $targetFile, $editID);
        } else {
            $message = "Image upload failed.";
        }
    } else {
        // No new image selected
        $sql = "UPDATE service SET Type=?, Description=?, availability=? WHERE ServiceID=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $Type, $description, $availability, $editID);
    }

    if (isset($stmt) && $stmt->execute()) {
        $message = "Service updated successfully!";
    } elseif (isset($stmt)) {
        $message = "Error: " . $stmt->error;
    }

    if (isset($stmt)) {
        $stmt->close();
    }
}

// Get all services

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Service Provider Home</title>
  <link rel="stylesheet" href="style.css">
  <link rel="icon" type="image/png" href="img/Hadhiq2.png" sizes="32x32" />
  <link rel="icon" type="image/png" href="img/Hadhiq2.png" sizes="16x16" />
  <style>
    .button {
      background-color: #081833;
      border-radius: 0.625rem;
      color: white;
      cursor: pointer;
      transition-duration: 0.2s;
      text-decoration: none;
      padding: 2%;
      margin: 20px;
      text-align: center;
    }
    .buttons {
      display: flex;
      justify-content: center;
      align-items: center;
      text-align: center;
    }
    .button:hover {
      background-color: #004369;
      border: 0.125rem solid #004369;
      color: white;
    }
    .single-service {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      border: 1px solid #ccc;
      
      padding: 10px;
      border-radius: 8px;
      background: white;
    }
    .service-left img {
      width: 100px;
      height: 100px;
      object-fit: cover;
      border-radius: 10px;
    }
    .service-info {
      flex-grow: 1;
      padding: 0 20px;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }
    .service-info div {
      margin: 4px 0;
      font-size: 14px;
    }
    .edit-button {
      background-color: #004369;
      color: white;
      border: none;
      border-radius: 6px;
      padding: 10px 15px;
      cursor: pointer;
      align-self: start;
      margin-left: 10px;
      text-decoration: none;
    }
    .edit-button:hover {
      background-color: #081833;
    }
    input[type="text"], input[type="file"] {
      padding: 5px;
      width: 100%;
      margin-bottom: 5px;
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
  <h1 class="company">Available Services</h1>

  <?php if ($message): ?>
    <p style="text-align:center; color:green;"><strong><?= $message ?></strong></p>
  <?php endif; ?>

  <div id="servicesList">
    <?php foreach ($services as $row): ?>
      <div class="single-service">
        <div class="service-left">
          <img src="<?= htmlspecialchars($row['ImagePath']) ?>" alt="<?= htmlspecialchars($row['Type']) ?>">
        </div>
        <div class="service-info">
          <?php if (isset($_GET['edit']) && $_GET['edit'] == $row['ServiceID']): ?>
            <form method="POST" enctype="multipart/form-data">
              <input type="hidden" name="edit_id" value="<?= $row['ServiceID'] ?>">
              <label>Service Type:</label>
              <input type="text" name="serviceType" value="<?= htmlspecialchars($row['Type']) ?>" required>
              <label>Availability:</label>
              <input type="text" name="availability" value="<?= htmlspecialchars($row['availability']) ?>" required>
              <label>Description:</label>
              <input type="text" name="Description" value="<?= htmlspecialchars($row['Description']) ?>" required>
              <label>Change Image:</label>
              <input type="file" name="newImage" accept="image/*">
              <button type="submit" class="edit-button">Save</button>
              <a href="Provider_Homepage.php" class="edit-button" style="background-color:#999;">Cancel</a>
            </form>
          <?php else: ?>
            <div class="service-name"><strong><?= htmlspecialchars($row['Type']) ?></strong></div>
            <div class="service-availability"><?= htmlspecialchars($row['availability']) ?></div>
            <div class="service-description"><?= htmlspecialchars($row['Description']) ?></div>
          <?php endif; ?>
        </div>
        <?php if (!isset($_GET['edit'])): ?>
          <a href="Provider_Homepage.php?edit=<?= $row['ServiceID'] ?>" class="edit-button">Edit</a>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="buttons">
    <a href="Add_Service.php" class="button">Add a Service</a>
  </div>

  <footer>
    <hr>
    <h4>Contact Us At:</h4>
    <h4>support@Hadhiq.com</h4>
    <br>
    <div class="footer-bottom">
      © 2025 Hadhiq - All Rights Reserved
    </div>
  </footer>
</body>
</html>
