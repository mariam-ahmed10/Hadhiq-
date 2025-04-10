<?php

    session_start();
    include 'db_connection.php';
    
    if (isset($_SESSION['customer_id'])) {    
        header("Location: index.php");
    
        exit(); 
    }
    
    $customerName = $_SESSION['customer_name'] ?? 'Customer';
    $services = [];
    
    
    if(isset($_GET['search'])){
        $search = trim($_GET['search']);
        
        $stm = $conn->prepare("SELECT S.ServiceID, S.Type, S.Description, S.availability, 
            P.ProviderID, P.Name AS provider_name FROM service S
            JOIN provider P ON S.ProviderID = P.ProviderID
            WHERE S.Type LIKE CONCAT('%', ?) 
            OR S.Description LIKE CONCAT('%', ?)");

        $stm->bind_param("ss", $search, $search);
        $stm->execute();
        $services = $stm->get_result()->fetch_all(MYSQLI_ASSOC);
        $stm->close();
    }
    ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Customer Homepage</title>
        <link rel="stylesheet" href="style.css">
        <link rel="icon" type="image/png" href="img/Hadhiq2.png" sizes="32x32" />
        <link rel="icon" type="image/png" href="img/Hadhiq2.png" sizes="16x16" />
    
<!--    <script>
        function DirectToSrvices(){
            let searchInput = document.querySelector(".search-bar").value.trim();
            if ( searchInput ===""){
                alert("Please enter the services in the search bar, then click search.")
            }else{
            window.location.href="Customer_Services.html"
            }
        }   
    
        
    </script>-->
    </head>

    <body>
    		<nav> 
            <a href="Customer_Homepage.html"><img id="logo"src="img/HadhiqBG.png"></a>
            <div class="nav-links">
                <ul>
                    <li><a href="Customer_Homepage.php" class = "active">Home</a></li>
                    <li><a href="Service_Booking.php">Book a Service</a></li>
                    <li><a href="MyBooking.php">My Bookings</a></li>
                    <li><a href="profileC.php">Profile</a></li>
                    <li><a href="logout.php">Log out</a></li>
        
        
                </ul>
            </div>
        </nav>
     
    <header class="search-header">
        <div class="search-container">
            <div class="search-text">
                <h1>
                    
                    Discover Top-Rated Home Services
                </h1>
             </div>
            <form method="GET" action="Customer_Homepage.php" class="search-bar-container"> 
<!--            <div class="search-bar-container"> -->
                <input type="text" name="search" class="search-bar" placeholder="Search for a service..." 
        value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                <button type="submit" class="search-button">Search</button>
            </form>
        </div>
    </header>  
        
   
    <div class="services-container">
    <?php if (!empty($services)): ?>
        <?php foreach($services as $srv): ?>
            <?php 
                $type = strtolower($srv['Type']);
                $img = "img/{$type}.jpg";
                if (!file_exists($img)) $img = "img/default.jpg"; 
                $link = "Service_Booking.php?provider_id=" . $srv['ProviderID'] . "&service_id=" . $srv['ServiceID'];
            ?>
            <a href="<?= $link ?>" class="service-link">
                <div class="service-box">
                    <img src="<?= $img ?>" alt="<?= htmlspecialchars($srv['Type']) ?> Service">
                    <h3><?= htmlspecialchars($srv['Type']) ?></h3>
                    <p><strong>Description: </strong><?= htmlspecialchars($srv['Description']) ?></p>
                    <p><strong>Provider: </strong><?= htmlspecialchars($srv['provider_name']) ?></p>
                    <p><strong>Availability: </strong><?= htmlspecialchars($srv['availability']) ?></p>
                </div>
            </a>
        <?php endforeach; ?>
    <?php else: ?>
        <?php if(isset($_GET['search'])): ?>
            <div class="service-box">
                <p>No Services Found... Try searching for something else.</p>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

    <br><br><br><br> 
<!--        <div class = "service-box">
            <img src="img/mechanic.png" alt="mechanic service">
            <h3>Mechanic</h3>
            <p>Expert Mechanic solutions for your home</p>
        </div> 
        
        <div class = "service-box">
            <img class = "service-img" src="img/electrician.png" alt="electrical service">
            <h3>Electrical</h3>
            <p>Reliable electrical installations and repairs</p>
        </div> 

        <div class = "service-box">
            <img src="img/trimming.png" alt="Gardening service">
            <h3>Gardening</h3>
            <p>Transform your garden with professional care</p>
        </div>     -->
    
    
   
   
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