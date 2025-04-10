<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upcoming Bookings</title>
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

/*--------------------------FOOTER---------------------------*/
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
        .container {
            width: 60%;
            margin: 50px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
        }
        .notification {
            background: #fffae6;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            border-left: 5px solid #ffcc00;
            display: none;
			PADDING: 25px
        }
        .booking {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.1);
        }
        .buttons {
            display: flex;
            gap: 10px;
        }
        .accept, .decline {
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            color: white;
            font-weight: bold;
        }
        .accept {
            background: #28a745;
        }
        .accept:hover {
            background: #218838;
        }
        .decline {
            background: #dc3545;
        }
        .decline:hover {
            background: #c82333;
        }
    </style>
</head>

<header>
    <nav> 
        <a href="Provider_Homepage.php"><img id="logo"src="img/HadhiqBG.png"></a>
        <div class="nav-links">
            <ul>
                <li><a href="Provider_Homepage.php">Home</a></li>
                <li><a href="UpCoomingBooking.php" class = "active">Upcoming Bookings</a></li>
                <li><a href="profileS.php">Profile</a></li>
              <li><a href="logout.php">Log out</a></li></ul>
      </div>
    </nav>
<br><br>
</header>
<?php
$conn = new mysqli("localhost", "root", "", "_____dhiq (3)");

if (isset($_POST['bookingId']) && isset($_POST['status'])) {
    $bookingId = $_POST['bookingId'];
    $status = $_POST['status'];

    // تأكد من أن الاستعلام يعمل بشكل صحيح
    $sql = "UPDATE Booking SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $bookingId);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to update status"]);
    }

    $stmt->close();
    $conn->close();
} 
?>



<body>
  <div class="container">
    <h2>Upcoming Bookings</h2>

    <div id="notification" class="notification">
      📢 <strong>New Booking Request Received!</strong> Check the details below and take action.
    </div>

    <div id="bookings-container">
      <!-- سيتم إدراج البوكينجات هنا تلقائيًا -->
    </div>
  </div>

  <script>
    window.onload = function () {
      fetch('get_bookings.php')
        .then(response => response.json())
        .then(data => {
          const container = document.getElementById('bookings-container');

          if (data.length > 0) {
            document.getElementById('notification').style.display = 'block';
          }

          data.forEach(booking => {
            const div = document.createElement('div');
            div.className = 'booking';
            div.innerHTML = `
  <div class="booking-details">
    <p><strong>Customer:</strong> ${booking.CustomerName}</p>
    <p class="customer-email" style="display: none;">${booking.CustomerEmail}</p> 
    <p><strong>Service:</strong> ${booking.ServiceType}</p>
    <p><strong>Description:</strong> ${booking.Description}</p>
    <p><strong>Date & Time:</strong> ${booking.availability}</p>
  </div>
  <div class="buttons">
    <button class="accept" onclick="handleBooking(${booking.id}, this, 'Accepted')">Accept</button>
    <button class="decline" onclick="handleBooking(${booking.id}, this, 'Declined')">Decline</button>
  </div>
`;

            container.appendChild(div);
          });
        });
    };
    
   function handleBooking(bookingId, button, status) {
  let booking = button.closest('.booking');
  let buttonsContainer = button.parentElement;
  buttonsContainer.innerHTML = ''; // حذف الأزرار

  if (status === 'Accepted') {
    booking.style.background = '#d4edda';
    booking.innerHTML += '<p style="color: green; font-weight: bold;">✔ Accepted </p>';
  } else {
    booking.style.background = '#f8d7da';
    booking.innerHTML += '<p style="color: red; font-weight: bold;">✖ Declined </p>';
  }

  // إرسال الحالة إلى السيرفر
  fetch('update_booking_status.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: `bookingId=${bookingId}&status=${status}`
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      console.log("Updated successfully");
    } else {
      console.log("Update failed", data);
    }
  })
  .catch(error => {
    console.error("Error updating booking status:", error);
  });

  alert(`Booking has been ${status.toLowerCase()}.`);
}

function updateBookingStatus(bookingId, status) {
  fetch('update_booking_status.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: `bookingId=${bookingId}&status=${status}`
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      console.log('Booking status updated successfully');
    } else {
      console.log('Error updating booking status');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('There was an issue updating the booking status.');
  });
}
</script>

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