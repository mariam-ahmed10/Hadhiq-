
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyBooking</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
      <style>
        * {
            margin: 0;
            padding: 0;
            font-family: "Rowdies", "cursive";
            color: #004369 !important;
        }
        html, body {
            margin:0px;
            height:100%;
            bottom:100%;
        }
        

        .card {
            margin-bottom: 20px;
        }

        .card-title {
            font-size: 1.3rem;
            font-weight: bold;
        }

        .btn {
            margin-right: 5px;
        }

        .back-arrow {
            font-size: 24px;
            cursor: pointer;
            position: absolute;
            top: 20px;
            left: 20px;
            text-decoration: none;
            color: black;
        }

        .back-arrow:hover {
            color: gray;
        }

        
/*--------------------------HEADER-|-------------------------- */

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
    color: #004369;

	
}
.headers{
	min-height: 40%;
	width: 100%;    
     color: #004369;
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
.notification {
  background: #fffae6;
  padding: 15px;
  border-radius: 5px;
  margin-bottom: 15px;
  border-left: 5px solid #ffcc00;
  display: none;
  font-size: 16px;
}

      </style>

    <nav>
  <a href="Customer_Homepage.html"><img id="logo" src="img/HadhiqBG.png" style="max-width:170px;"></a>
  <div class="nav-links">
    <ul style="list-style: none; display: flex; justify-content: flex-end; gap: 20px;">
      <li><a href="Customer_Homepage.pho">Home</a></li>
      <li><a href="Service_Booking.php">Book a Service</a></li>
      <li><a href="MyBooking.php" class="active">My Bookings</a></li>
      <li><a href="profileC.php">Profile</a></li>
      <li><a href="index.php">Log out</a></li>
    </ul>
  </div>
</nav>
</head>
<body>

<div class="container mt-5">
    <h2 class="mb-4">My Bookings</h2>
    <div id="notification" class="notification" style="display: none;">
  📢 <strong>Update:</strong> You have bookings that were recently updated!
</div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th scope="col">Service</th>
                <th scope="col">Time</th>
                <th scope="col">Location</th>
                <th scope="col">Status</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody id="bookings-list"></tbody>
    </table>
</div>


<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content" style="background-color: #fff9f0;">
      <div class="modal-header">
        <h5 class="modal-title">Edit Booking</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="editBookingId">
        <div class="mb-3">
          <label for="editLocation" class="form-label">Location</label>
          <input type="text" class="form-control" id="editLocation" placeholder="Enter new location">
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-primary" id="saveChanges">Save Changes</button>
      </div>
    </div>
  </div>
</div>


<!-- Review Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content" style="background-color: #fff9f0;">
      <div class="modal-header">
        <h5 class="modal-title">Submit Your Review</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="reviewServiceId">
        <textarea class="form-control" id="reviewText" rows="3" placeholder="Write your feedback here..."></textarea>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button class="btn btn-success" id="submitReview">Submit Review</button>
      </div>
    </div>
  </div>
</div>
<!-- JavaScript Section -->
<script>
document.addEventListener("DOMContentLoaded", function () {
  fetch("my_bookings.php")
    .then((res) => res.json())
    
    .then((bookings) => {
      const tbody = document.getElementById("bookings-list");
      let hasUpdates = false;

      bookings.forEach((booking) => {
          if (booking.status === "Accepted" || booking.status === "Declined") {
  hasUpdates = true;
}

        const row = document.createElement("tr");
        row.innerHTML = `
          <td>${booking.service_name}</td>
          <td>${booking.availability}</td>
          <td>${booking.location}</td>
          <td>${booking.status}</td>
          <td>
            <button class="btn btn-primary edit-btn" data-id="${booking.id}" data-location="${booking.location}" data-bs-toggle="modal" data-bs-target="#editModal">Edit</button>
            <button class="btn btn-danger cancel-btn" data-id="${booking.id}">Cancel</button>
            <button class="btn btn-success review-btn" data-service-id="${booking.service_id}" data-bs-toggle="modal" data-bs-target="#reviewModal">Review</button>
          </td>
        `;
        tbody.appendChild(row);
      });
if (hasUpdates) {
  document.getElementById("notification").style.display = "block";
}

      // Edit Handler
      document.querySelectorAll(".edit-btn").forEach((btn) =>
        btn.addEventListener("click", function () {
          document.getElementById("editBookingId").value = this.dataset.id;
          document.getElementById("editLocation").value = this.dataset.location;
        })
      );

      // Save Changes
      document.getElementById("saveChanges").addEventListener("click", function () {
        const bookingId = document.getElementById("editBookingId").value;
        const newLocation = document.getElementById("editLocation").value.trim();

        if (!newLocation) return alert("Please enter a location.");

        const formData = new FormData();
        formData.append("booking_id", bookingId);
        formData.append("new_location", newLocation);

        fetch("update_booking.php", { method: "POST", body: formData })
          .then((res) => res.text())
          .then((msg) => {
            alert(msg);
            location.reload();
          });
      });

      // Cancel Handler
      document.querySelectorAll(".cancel-btn").forEach((btn) =>
        btn.addEventListener("click", function () {
          const bookingId = this.dataset.id;
          if (confirm("Are you sure you want to cancel?")) {
            const formData = new FormData();
            formData.append("booking_id", bookingId);

            fetch("cancel_booking.php", { method: "POST", body: formData })
              .then((res) => res.text())
              .then((msg) => {
                alert(msg);
                location.reload();
              });
          }
        })
      );

      // Review Handler
      document.querySelectorAll(".review-btn").forEach((btn) =>
        btn.addEventListener("click", function () {
          document.getElementById("reviewServiceId").value = this.dataset.serviceId;
        })
      );

      // Submit Review
      document.getElementById("submitReview").addEventListener("click", function () {
        const serviceId = document.getElementById("reviewServiceId").value;
        const review = document.getElementById("reviewText").value.trim();

        if (!review) return alert("Please enter a review.");

        const formData = new FormData();
        formData.append("service_id", serviceId);
        formData.append("customer_id", <?= json_encode($_SESSION['customer_id'] ?? 0) ?>);
        formData.append("review", review);

        fetch("submit_review.php", { method: "POST", body: formData })
          .then((res) => res.text())
          .then((msg) => {
            alert(msg);
            const modal = bootstrap.Modal.getInstance(document.getElementById("reviewModal"));
            modal.hide();
          });
      });
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

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