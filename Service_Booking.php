<?php
session_start(); // Required to access $_SESSION
include 'db_connection.php';
$customerID = $_SESSION['user_id']; // ✅ Get logged-in customer ID from session

$serviceID = $_GET['service_id'] ?? null;
$service = null;
$reviews = [];

if ($serviceID) {
    // Get service and provider info
    $stmt = $conn->prepare("
        SELECT s.Type, s.Description, s.availability, p.Name AS provider_name, p.Email AS provider_email
        FROM service s
        JOIN provider p ON s.ProviderID = p.ProviderID
        WHERE s.ServiceID = ?
    ");
    $stmt->bind_param("i", $serviceID);
    $stmt->execute();
    $result = $stmt->get_result();
    $service = $result->fetch_assoc();
    $stmt->close();

    // Get reviews
    $stmt = $conn->prepare("SELECT CustomerID, Description FROM review WHERE ServiceID = ?");
    $stmt->bind_param("i", $serviceID);
    $stmt->execute();
    $reviews = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Service Booking</title>
  <link rel="stylesheet" href="style.css"/>
  <style>
  /* your original CSS remains unchanged */
  * {
    margin: 0;
    padding: 0;
    font-family: "Rowdies", "cursive";
    color: #004369;
  }

  html, body {
    margin: 0px;
    height: 100%;
    bottom: 100%;
  }

  body {
    background-color: #fff9f0 !important;
    box-sizing: border-box;
  }

  .container {
    max-width: 750px;
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    margin: 50px 0 50px 360px;
  }

  h1, h2 {
    text-align: center;
    color: #dc9750;
  }

  .section {
    margin-bottom: 30px;
    padding: 20px;
    background: #f4f4f4;
    border-radius: 8px;
  }

  #re {
    list-style-type: none;
    padding: 0;
  }

  #re {
    background: #fff;
    margin-bottom: 10px;
    padding: 10px;
    border-radius: 6px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  }

  table {
    width: 100%;
    border-collapse: collapse;
  }

  th, td {
    border: 1px solid #ddd;
    padding: 15px;
    text-align: center;
  }

  th {
    background-color: #dc9750;
    color: white;
  }

  button {
    padding: 10px 20px;
    background-color: #dc9750;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background 0.3s ease;
  }

  button:hover {
    background-color: #004369;
  }

  .booking-form, .confirmation-message {
    padding: 20px;
    background: white;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    margin-top: 20px;
  }

  .booking-form {
    display: none;
  }

  .booking-form input, .booking-form button {
    display: block;
    width: 100%;
    margin-bottom: 10px;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
  }

  .confirmation-message {
    display: none;
    text-align: center;
    font-size: 18px;
    color: #004369;
  }

  


  nav {
    display: flex;
    width: 90%;
    max-width: 1200px;
    margin: auto;
    justify-content: space-between;
    align-items: center;
    flex-wrap: nowrap;
  }

  .nav-links {
    flex: 1;
    text-align: right;
  }

  .nav-links ul {
    display: flex;
    justify-content: flex-end;
    white-space: nowrap;
    width: 100%;
  }

  .nav-links ul li {
    display: inline-block;
    padding: 15px 20px;
    font-family: "Rowdies", "cursive";
  }

  .nav-links ul li a {
    text-decoration: none;
    font-size: 16px;
  }

  .nav-links ul li a.active {
    border-bottom: 3px solid #922c40;
    font-weight: bold;
  }

  .nav-links ul li::after {
    content: '';
    width: 0%;
    height: 2px;
    background: #922c40;
    border: #922c40;
    display: block;
    margin: auto;
    transition: 0.5s;
  }

  .nav-links ul li:hover::after {
    width: 100%;
  }

  @media (max-width: 1400px) {
    nav {
      width: 80%;
    }

    .nav-links ul {
      justify-self: space-around;
    }

    .nav-links ul li {
      padding: 10px 15px;
    }
  }
  
  </style>
</head>

<body>
  <nav>
      <a href="Customer_Homepage.php"><img id="logo" src="img/HadhiqBG.png" alt="Logo" style="max-width:170px;"></a>
    <div class="nav-links">
      <ul style="display:flex; justify-content:flex-end; list-style:none;">
        <li><a href="Customer_Homepage.php">Home</a></li>
        <li><a href="Service_Booking.php" class="active">Book a Service</a></li>
        <li><a href="MyBooking.php">My Bookings</a></li>
        <li><a href="profileC.php">Profile</a></li>
       <li><a href="logout.php">Log out</a></li>
      </ul>
    </div>

  </nav>

  <div class="container">
    <h1>Service Booking Page</h1>

    <?php if ($service): ?>
      <section class="section">
        <h2>Service Information</h2>
        <p><strong>Service Type:</strong> <?= htmlspecialchars($service['Type']) ?></p>
        <p><strong>Description:</strong> <?= htmlspecialchars($service['Description']) ?></p>
      </section>

      <section class="section">
        <h2>Service Provider Information</h2>
        <p><strong>Name:</strong> <?= htmlspecialchars($service['provider_name']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($service['provider_email']) ?></p>
      </section>

      <section class="section">
        <h2>Customer Reviews</h2>
        <ul>
          <?php if (!empty($reviews)): ?>
            <?php foreach ($reviews as $review): ?>
              <li>"<?= htmlspecialchars($review['Description']) ?>" - Customer #<?= $review['CustomerID'] ?></li>
            <?php endforeach; ?>
          <?php else: ?>
            <li>No reviews yet.</li>
          <?php endif; ?>
        </ul>
      </section>

      <section class="section">
  <h2>Available Days and Times</h2>
  <p><strong>Availability: </strong><?= htmlspecialchars($service['availability']) ?></p>
</section>

      <!-- Book Now button to reveal the form -->
      <section class="section">
        <h2>Ready to Book?</h2>
        <button onclick="showBookingForm()">Book Now</button>
      </section>

     <!-- Booking form -->
<section class="section booking-form" id="bookingForm">
  <h2>Confirm Your Booking</h2>
  <form action="confirm_booking.php" method="POST">
    <label for="customerName">Full Name</label>
    <input type="text" id="customerName" name="customerName" placeholder="Enter your name" required>

    <label for="phoneNumber">Phone Number</label>
    <input type="tel" id="phoneNumber" name="phoneNumber" placeholder="Enter your phone number" required>

    <label for="location">Location</label>
    <input type="text" id="location" name="location" placeholder="Enter your location" required>

    <!-- Hidden Inputs -->
    <input type="hidden" name="timeslot" value="<?= htmlspecialchars($service['availability']) ?>">
    <input type="hidden" name="serviceID" value="<?= htmlspecialchars($serviceID) ?>">

    <!-- Don't pass customerID from form — it's better to handle in session inside PHP (in confirm_booking.php) -->

    <button type="submit">Confirm Booking</button>
  </form>
</section>


      <div class="confirmation-message" id="confirmationMessage">
        Thank you! Your booking has been confirmed.
      </div>
    <?php else: ?>
      <p style="text-align:center;">Service not found.</p>
    <?php endif; ?>
  </div>

  <script>
    function showBookingForm() {
      const form = document.getElementById("bookingForm");
      form.style.display = "block";
      form.scrollIntoView({ behavior: "smooth" });
    }

    function confirmBooking(event) {
      event.preventDefault();
      document.getElementById("bookingForm").style.display = "none";
      document.getElementById("confirmationMessage").style.display = "block";
    }
  </script>

  <footer>
    <hr>
    <h4>Contact Us At: support@Hadhiq.com</h4>
    <div class="footer-bottom">© 2025 Hadhiq - All Rights Reserved</div>
  </footer>
</body>
</html>
