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
        $name = $_POST['name'];
        $email = $_POST['email'];

        $sql_update = "UPDATE customer SET name = ?, email = ? WHERE customer_id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("ssi", $name, $email, $customer_id);

        if ($stmt_update->execute()) {
            echo "Profile updated successfully!";
        } else {
            echo "Error updating profile: " . $conn->error;
        }
    }

  
    $sql = "SELECT CustomerID, name, email FROM customer WHERE CustomerID = ?";
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
        $name = $row['name'];
        $email = $row['email'];
    } else {
        echo "No data found for this customer.";
        exit();
    }

    if (isset($_GET['logout'])) {
        session_unset();
        session_destroy();
        header("Location: index.php");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Customer Profile</title>
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
        .edit-profile:hover {
            background: #7d1f35;
        }
        .profile-info {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.1);
            margin-top: 10px;
        }
        .profile-info i {
            color: #952544;
            margin-right: 5px;
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
    </style>
</head>
<body>
<header>
    <nav> 
        <a href="Customer_Homepage.html"><img id="logo"src="img/HadhiqBG.png"></a>
        <div class="nav-links">
            <ul>
                <li><a href="Customer_Homepage.php" >Home</a></li>
                <li><a href="Service_Booking.php">Book a Service</a></li>
                <li><a href="MyBooking.php">My Bookings</a></li>
                <li><a href="profileC.php" class = "active">Profile</a></li>
                <li><a href="logout.php">Log out</a></li>
    
    
            </ul>
        </div>
    </nav>
</header>

    <div class="profile-container">
        <div class="profile-header">
<!--            <img src="https://th.bing.com/th/id/OIP.cRT6RCVvwHTayfPtBx1GOAHaE8?rs=1&pid=ImgDetMain" class="profile-pic">-->
            <img src="img/userpic.jpg" alt="Customer" class="profile-pic">
<!--            <button class="edit-profile" onclick="editProfile()">✎</button>-->
<!--            <button type="button" class="edit-profile" onclick="editProfile()">✎</button>-->
            <h2 id="customer-name"><?= htmlspecialchars($name) ?></h2>
        </div>

        <div class="profile-info" id="profile-info">
            <p><strong><i class="fas fa-envelope"></i> Email:</strong> 
                <span id="email"><?= htmlspecialchars($email) ?></span>
            </p>
        </div>

        <button type="submit" class="save-btn" id="save-btn" style="display: none;">Save Changes</button>
    </div>
</form>

<script>
    function editProfile() {
        // Make name editable
        const nameEl = document.getElementById('customer-name');
        const nameVal = nameEl.innerText;
        nameEl.innerHTML = <input type='text' name='name' value='${name}' required>;

        // Make email editable
        const emailEl = document.getElementById('email');
        const emailVal = emailEl.innerText;
        emailEl.innerHTML = <input type='email' name='email' value='${email}' required>;

        document.getElementById('save-btn').style.display = 'block';
    }
     function saveProfile() {
                    let fields = ['name', 'email'];
                    fields.forEach(id => {
                        let element = document.getElementById(id);
                        let input = element.querySelector('input');
                        element.innerText = input.value;
                    });
                    document.getElementById('save-btn').style.display = 'none';
                }
</script>

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