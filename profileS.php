<?php
ob_start();
session_start();
include("db_connection.php");

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['user_id'];

if (empty($user_id)) {
    echo "Invalid user ID.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];

    $sql_update = "UPDATE provider SET Email = ? WHERE ProviderID = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("si", $email, $user_id);

    if ($stmt_update->execute()) {
        echo "Profile updated successfully!";
    } else {
        echo "Error updating profile: " . $conn->error;
    }
}

// قراءة البيانات
$sql = "SELECT * FROM provider WHERE ProviderID = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    echo "Error preparing statement: " . $conn->error;
    exit();
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $name = $row['Name'];
    $email = $row['Email'];
} else {
    echo "No data found for this user.";
    exit();
}

if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}


// استعلام للحصول على الريفيوهات من قاعدة البيانات
$reviews_per_page = 5; // عدد المراجعات في كل صفحة
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $reviews_per_page;

$sql_reviews = "SELECT r.Description, c.Name as CustomerName
               FROM review r
               JOIN customer c ON r.CustomerID = c.CustomerID
               WHERE r.ServiceID = (SELECT ServiceID FROM provider WHERE ProviderID = ?)
               LIMIT ?, ?";

$stmt_reviews = $conn->prepare($sql_reviews);
$stmt_reviews->bind_param("iii", $user_id, $offset, $reviews_per_page);
$stmt_reviews->execute();
$reviews_result = $stmt_reviews->get_result();

$reviews = [];
if ($reviews_result->num_rows > 0) {
    while ($row = $reviews_result->fetch_assoc()) {
        $reviews[] = $row; // تخزين كل تعليق في مصفوفة
    }
} else {
    $reviews[] = ['CustomerName' => 'No reviews yet', 'Description' => 'Be the first to review.', 'ImageURL' => 'default.jpg'];
}

// حساب العدد الإجمالي للمراجعات
$sql_count = "SELECT COUNT(*) as total FROM review WHERE ServiceID = (SELECT ServiceID FROM provider WHERE ProviderID = ?)";
$stmt_count = $conn->prepare($sql_count);
$stmt_count->bind_param("i", $user_id);
$stmt_count->execute();
$count_result = $stmt_count->get_result();
$total_reviews = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_reviews / $reviews_per_page);
?>









<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <title>Service Provider Profile</title>
        <style>

            * {
                margin: 0;
                padding: 0;
                font-family: "Rowdies", "cursive";
                color: #004369;
            }
            html, body {
                margin:0px;
                height:100%;
                bottom:100%;
            }
            body {
                background-color: #fff9f0;
                box-sizing: border-box;

            }
            .headers{
                min-height: 40%;
                width: 100%;

            }
            #logo{
                max-width:170px;
            }
            nav{
                display: flex;
                width: 90%;
                max-width: 1200px;
                margin: auto;
                justify-content: space-between;
                align-items: center;
                flex-wrap: nowrap;
            }
            .nav-links{
                flex: 1;
                text-align: right;
            }

            .nav-links ul{
                display: flex;
                justify-content: flex-end;
                white-space: nowrap;
                width: 100%;

            }

            .nav-links ul li {
                display: inline-block;
                padding: 15px 20px;
                font-family:"Rowdies", "cursive";
            }
            .nav-links ul li a{

                text-decoration: none;
                font-size: 16px;
            }
            .nav-links ul li a.active {
                border-bottom: 3px solid #922c40;
                font-weight: bold;
            }
            .nav-links ul li::after{
                content: '';
                width: 0%;
                height: 2px;
                background:  #922c40;
                border: #922c40;
                display: block;
                margin: auto;
                transition: 0.5s;
            }
            .nav-links ul li:hover::after{
                width:100%
            }
            @media(max-width:1400px)  {
                nav{
                    width: 80%;
                }
                .nav-links ul{
                    justify-self: space-around;
                }

                .nav-links ul li{
                    padding: 10px 15px ;
                }
            }

            /--------------------------FOOTER---------------------------/
            footer{
                width: 100%;
                text-align:center;

            }
            footer h4, h5{
                margin-top: 20px;
                margin-bottom:10px;
                margin-top:10px;

                font-weight: 600;

            }
            .footer-bottom {
                text-align: center;
                padding: 15px 0;
                font-size: 14px;
                color: #777;
                background-color: #faefe0;
            }

            body {
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 0;
            }
            .profile-container {
                width: 50%;
                margin: 50px auto;
                background: white;
                padding: 20px;
                border-radius: 10px;
                box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
                position: relative;
            }
            .profile-header {
                text-align: center;
                padding-bottom: 20px;
                border-bottom: 2px solid #ddd;
                position: relative;
            }
            .profile-pic {
                width: 120px;
                height: 120px;
                border-radius: 50%;
                object-fit: cover;
                margin-bottom: 10px;
            }
            .edit-profile {
                position: absolute;
                top: 10px;
                right: 10px;
                background: #952544;
                color: white;
                width: 35px;
                height: 35px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                text-decoration: none;
                font-size: 16px;
                font-weight: bold;
                cursor: pointer;
                border: none;
            }

            .profile-info i {
                color: #952544;
            }

            /* Hide the scrollbar completely */
            .review-container {
                display: flex;
                overflow-x: auto;
                gap: 15px;
                padding-top: 10px;
                scroll-behavior: smooth;

                /* Hides scrollbar for all browsers */
                scrollbar-width: none; /* Firefox */
                -ms-overflow-style: none;  /* Internet Explorer 11 */
            }

            /* Hides scrollbar for Chrome, Edge, Safari */
            .review-container::-webkit-scrollbar {
                display: none;
            }


            .edit-profile:hover {
                background: #0056b3;
            }
            .profile-info {
                background: #f9f9f9;
                padding: 15px;
                border-radius: 10px;
                box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.1);
                margin-top: 10px;
            }
            .profile-info input {
                width: 100%;
                padding: 5px;
                margin: 5px 0;
                border: 1px solid #ddd;
                border-radius: 5px;
                font-size: 14px;
            }
            .save-btn {
                display: none;
                background: #28a745;
                color: white;
                border: none;
                padding: 10px 15px;
                border-radius: 5px;
                cursor: pointer;
                margin-top: 10px;
            }
            .save-btn:hover {
                background: #218838;
            }
            .reviews {
                margin-top: 20px;
                padding: 15px;
                background: #fff;
                border-radius: 10px;
                box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.1);
            }
            .reviews h3 {
                font-size: 18px;
                border-bottom: 2px solid #ddd;
                padding-bottom: 10px;
            }
            .review-container {
                display: flex;
                overflow-x: auto;
                gap: 15px;
                padding-top: 10px;
                scroll-behavior: smooth;
            }
            .review {
                background: #f9f9f9;
                padding: 15px;
                border-radius: 8px;
                min-width: 250px;
                box-shadow: 0px 0px 3px rgba(0, 0, 0, 0.1);
                font-size: 14px;
                position: relative;
            }
            .review img {
                width: 35px;
                height: 35px;
                border-radius: 50%;
                object-fit: cover;
                position: absolute;
                top: 10px;
                left: 10px;
                border: 2px solid white;
                box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.2);
            }
            .review-content {
                padding-left: 50px; /* Makes space for the image */
            }
            .stars {
                color: gold;
                font-size: 16px;
                margin-top: 5px;
            }
        </style>
    </head>

    <header>
        <nav> 
            <a href="Provider_Homepage.php"><img id="logo"src="img/HadhiqBG.png"></a>
            <div class="nav-links">
                <ul>
                    <li><a href="Provider_Homepage.php">Home</a></li>
                    <li><a href="UpCoomingBooking.php">Upcoming Bookings</a></li>
                    <li><a href="profileS.php"  class = "active">Profile</a></li>
                    <li><a href="?logout=true">Log out</a></li>
                </ul>
            </div>
        </nav>
        <br><br>
    </header>
    <body>
        <div class="profile-container">
            <div class="profile-header">
                <img src="img/provpic.jpg" alt="Service Provider" class="profile-pic">
                <h2 id="provider-name"><?php echo htmlspecialchars($name); ?></h2>
            </div>

            <div class="profile-info" id="profile-info">
                <p><strong><i class="fas fa-envelope"></i> Email:</strong> <span id="email"><?php echo htmlspecialchars($email); ?></span></p>

            </div>
            <button class="save-btn" id="save-btn" onclick="saveProfile()">Save Changes</button>

            <div class="reviews">
                <h3>⭐ Customer Reviews</h3>
                <div class="review-container">
                    <?php if (count($reviews) > 1): ?>
                        <?php foreach ($reviews as $review): ?>
                            <div class="review">
                                <img src="https://www.w3schools.com/w3images/avatar2.png" alt="Profile Picture" style="width: 50px; height: 50px; border-radius: 50%;"> 
                                <div class="review-content">
                                    <strong><?php echo htmlspecialchars($review['CustomerName']); ?></strong>
                                    <p><?php echo htmlspecialchars($review['Description']); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No reviews yet. Be the first to review!</p>
                    <?php endif; ?>
                </div>
            </div>

           
    </body>
    <footer>
        <hr>

        <h4>Contact Us At: </h4>
        <h4>support@Hadhiq.com</h4>
        <br>
        <div class="footer-bottom">
            © 2025 Hadhiq - All Rights Reserved
        </div>
    </footer>

</html>