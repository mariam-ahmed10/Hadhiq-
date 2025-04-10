<?php
ob_start();
session_start();
include("db_connection.php");

// التحقق مما إذا كان المستخدم قد سجل الدخول مسبقًا
if (isset($_SESSION['user_id'])) {
    $role = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : '';
    if ($role == 'customer')
        exit(header("location:Customer_Homepage.php"));
    else if ($role == 'provider')
        exit(header("location:Provider_Homepage.php")); 
}

// معالجة طلب التسجيل
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'register_customer') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($name) && !empty($email) && !empty($password)) {
        $hashedPassword = encryptPassword($password);

        // تأكد أن الإيميل غير موجود مسبقًا
        $check_sql = "SELECT * FROM customer WHERE Email = ?";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $error_msg = "Email already exists!";
        } else {
            $insert_sql = "INSERT INTO customer (Name, Email, Password) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($insert_sql);
            $stmt->bind_param("sss", $name, $email, $hashedPassword);
            
            if ($stmt->execute()) {
                $customer_id = $stmt->insert_id;
                $_SESSION['user_id'] = $customer_id;
                $_SESSION['user_role'] = 'customer';
                header("Location: Customer_Homepage.php");
                exit();
            } else {
                $error_msg = "Registration failed.";
            }

        }
    } else {
        $error_msg = "All fields are required!";
    }
}
// معالجة تسجيل مزود الخدمة
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'register_provider') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $service_type = $_POST['service_type'] ?? ''; // تأكد إن عندك input اسمه service_type في النموذج

    if (!empty($name) && !empty($email) && !empty($password) && !empty($service_type)) {
        $hashedPassword = encryptPassword($password);

        // التحقق من عدم تكرار الإيميل
        $check_sql = "SELECT * FROM provider WHERE Email = ?";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $error_msg = "Email already exists!";
        } else {
            $insert_sql = "INSERT INTO provider (Name, Email, Password, Service_Type) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_sql);
            $stmt->bind_param("ssss", $name, $email, $hashedPassword, $service_type);
            if ($stmt->execute()) {
                // Get the last inserted ID
                $provider_id = $stmt->insert_id;
                $_SESSION['user_id'] = $provider_id;
                $_SESSION['user_role'] = 'provider';
                header("Location: Provider_Homepage.php");
                exit();
            } else {
                $error_msg = "Provider registration failed.";
            }
        }
    } else {
        $error_msg = "All fields are required!";
    }
}

// دالة لتشفير كلمة المرور
function encryptPassword($password) {
    $options = [
        'cost' => 12,
    ];
    return password_hash($password, PASSWORD_BCRYPT, $options);
}

// دالة لتسجيل دخول العميل
function login_customer($email, $password) {
    global $conn;
    global $error_msg;

    if (empty($email) || empty($password)) {
        $error_msg = "All fields are required!";
        return false;
    }

    $emailAddress = mysqli_real_escape_string($conn, $email);
    $password = mysqli_real_escape_string($conn, $password);

    $sql = "SELECT * FROM `customer` WHERE `Email` = '$emailAddress';"; 
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['Password'])) {
            return $user['CustomerID'];
        } else {
            $error_msg = "Email or password is incorrect.";
            return false;
        }
    } else {
        $error_msg = "No account found with that email.";
        return false;
    }
}

// معالجة تسجيل الدخول
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email']) && isset($_POST['password']) && !isset($_POST['action'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // حاول تسجيل العميل أولاً
    $customer_id = login_customer($email, $password);
    if ($customer_id) {
        $_SESSION['user_id'] = $customer_id;
        $_SESSION['user_role'] = 'customer';
        $_SESSION['CustomerID'] = $customer_id;

        header("Location: Customer_Homepage.php");
        exit();
    }

    // إذا ما كان عميل، جرب كمزود
    $provider_id = login_provider($email, $password);
   if ($provider_id) {
    $_SESSION['user_id'] = $provider_id;
    $_SESSION['user_role'] = 'provider';
    $_SESSION['ProviderID'] = $provider_id;

    header("Location: Provider_Homepage.php");
    exit();
}


    // إذا ما نجحت المحاولتين
    $error_msg = "Email or password is incorrect.";
}
// دالة لتسجيل دخول المزود
function login_provider($email, $password) {
    global $conn;
    global $error_msg;

    if (empty($email) || empty($password)) {
        $error_msg = "All fields are required!";
        return false;
    }

    $emailAddress = mysqli_real_escape_string($conn, $email);
    $password = mysqli_real_escape_string($conn, $password);

    $sql = "SELECT * FROM `provider` WHERE `Email` = '$emailAddress';"; 
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['Password'])) {
            return $user['ProviderID'];
        } else {
            $error_msg = "Email or password is incorrect.";
            return false;
        }
    } else {
        $error_msg = "No account found with that email.";
        return false;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hathiq</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@48,400,0,0">
    <link rel="stylesheet" href="style2.css">
    <script src="script.js" defer></script>
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
        .navbar{
            background-color: #004369;
            color: #004369 !important; 
        }
        body {
            background-color: #fff9f0;
            box-sizing: border-box;
            
        }
        .signup, .provider-signup { display: none; }
        .form-popup .form-box {
            display: flex;
            align-items: stretch;
        }
        #logo{
	max-width:170px;
}
        .form-box .form-details {
            width: 40%;
            min-height: 450px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: #070707;  
            background: url("images/login-img.jpg") center/cover no-repeat;
        }

        .form-box .form-content {
            width: 60%;
            padding: 35px;
            max-height: 600px;
            overflow-y: auto; 
        }

        .form-popup.show-signup .login .form-details,
        .form-popup.show-signup .signup .form-details,
        .form-popup.show-signup .provider-signup .form-details {
            display: flex;
        }
        .headerimg{
	background: url('img/header.jpg') no-repeat center center/cover;
	height: 70vh;
	display: flex;
	align-items: center;
	justify-content: center;
	text-align: center;
	position: relative;
	margin-top: 30px;
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
    </style>
</head>
<body>
   
    
    <?php if (!empty($error_msg)): ?>
    <div class="alert error-alert">
      <?= $error_msg ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($success_msg)): ?>
        <div class="alert success-alert">
           <?= $success_msg ?>
        </div>
     <?php endif; ?>
    <header >
        
        <nav class="navbar">
            <span class="hamburger-btn material-symbols-rounded">menu</span>
            <a href="#" class="logo">
                <img src="img/HadhiqBG.png" alt="logo" id="logo">
                <h2>Hadhiq</h2>
            </a>
            <ul class="links">
                <span class="close-btn material-symbols-rounded">close</span>
                <li><a href="#">Home</a></li>
                <li><a href="#">Products</a></li>
                <li><a href="#">About Us</a></li>
                <li><a href="#">Contact Us</a></li>
            </ul>
            <button class="login-btn" id="login-btn">LOG IN</button>//////////هنا يحدد يسجل دخول من
        </nav>
    </header>
<div class="headerimg"></div>
    <div class="blur-bg-overlay"></div>
    <?php if (isset($_GET['logged_out'])): ?>
           <div class="logout-message" style="background-color: #d4edda; color: #155724;
                padding: 10px; margin: 20px; border: 1px solid #c3e6cb; border-radius: 5px;">
               You have been logged out successfully.
           </div>
        <?php endif; ?>
    <div class="form-popup">
        <span class="close-btn material-symbols-rounded">close</span>
        
        <!-- Login Form -->
        <div class="form-box login" id="login-form">
            <div class="form-details">
                <h2>Welcome Back</h2>
                <p>Please log in using your personal information to stay connected with us.</p>
            </div>
            <div class="form-content">
                <h2>LOGIN</h2>
              <form action="" method="POST">
                    <div class="input-field">
                        <input type="email" id="email" name="email" class="texeInput" required>
                        <label>Email</label>
                    </div>
                    <div class="input-field">
                        <input type="password" id="p" name="password" class="texeInput" required minlength="8" maxlength="8">
                        <label>Password</label>
                    </div>
                    
                    <button type="submit">Log In</button>
                </form>
                <div class="bottom-link">
                    Don't have an account?
                    <a href="#" id="signup-link">Sign Up</a>
                </div>
                <div class="bottom-link">
                    Are you a provider?
                    <a href="#" id="provider-signup-link">Sign Up as Provider</a>
                </div>
            </div>
        </div>

        <!-- Regular Sign Up Form -->
        <div class="form-box signup" id="signup-form">
            <div class="form-details">
                <h2>Create Account</h2>
                <p>Please sign up using your personal information.</p>
            </div>
            <div class="form-content">
                <h2>SIGN UP</h2>
                <form action="" method="POST">
                    <input type="hidden" name="action" value="register_customer">
                    <div class="input-field">
                        <input type="text" name="name" required>
                        <label>Enter your name</label>
                    </div>
                    <div class="input-field">
                        <input type="email" name="email" required>
                        <label>Enter your email</label>
                    </div>
                    <div class="input-field">
                        <input type="password" name="password" required minlength="8" maxlength="8">
                        <label>Create password</label>
                    </div>
                    <div class="policy-text">
                        <input type="checkbox" name="policy" required>
                        <label for="policy">I agree to the <a href="#">Terms & Conditions</a></label>
                    </div>
                    <button type="submit">Sign Up</button>
                </form>
                <div class="bottom-link">
                    Already have an account? 
                    <a href="#" id="login-link">Login</a>
                </div>
            </div>
        </div>
        
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const loginForm = document.getElementById("login-form");
            const signupForm = document.getElementById("signup-form");
            const providerSignupForm = document.getElementById("provider-signup-form");

            const loginBtn = document.getElementById("login-btn");
            const signupLink = document.getElementById("signup-link");
            const providerSignupLink = document.getElementById("provider-signup-link");
            const loginLinks = document.querySelectorAll("#login-link, #login-link-2");

            // Show Sign Up Form
            signupLink.addEventListener("click", function (e) {
                e.preventDefault();
                signupForm.style.display = "block";
                providerSignupForm.style.display = "none";
                loginForm.style.display = "none";
            });

            // Show Provider Sign Up Form
            providerSignupLink.addEventListener("click", function (e) {
                e.preventDefault();
                providerSignupForm.style.display = "block";
                signupForm.style.display = "none";
                loginForm.style.display = "none";
            });
        });
    </script>

       <!-- Provider Sign Up Form -->
<div class="form-box provider-signup" id="provider-signup-form">
    <div class="form-details">
        <h2>Create Account as Service Provider</h2>
        <p>Please sign up to offer your services.</p>
    </div>
    <div class="form-content">
        <h2>Sign Up as a Service Provider</h2>
        <form action="" method="POST">
            <input type="hidden" name="action" value="register_provider">
            
            <div class="input-field">
                <input type="text" name="name" class="textInput2" required>
                <label>Full Name</label>
            </div>
            <div class="input-field">
                <input type="email" name="email" class="textInput2" required>
                <label>Email</label>
            </div>
            <div class="input-field">
                <input type="password" name="password" class="textInput2" required minlength="8" maxlength="8">
                <label>Password</label>
            </div>
            <div class="input-field">
                <input type="text" name="service_type" class="textInput2" required>
                <label>Service Type</label>
            </div>
            <div class="policy-text">
                <input type="checkbox" name="policy" required>
                <label>
                    I agree to the <a href="#">Terms & Conditions</a>
                </label>
            </div>
            <button type="submit">Sign Up as a Service Provider</button>
        </form>

        <div class="bottom-link">
            Already have an account? 
            <a href="#" id="login-link-2">Login</a>
        </div>
    </div>
</div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const loginForm = document.getElementById("login-form");
            const signupForm = document.getElementById("signup-form");
            const providerSignupForm = document.getElementById("provider-signup-form");

            const loginBtn = document.getElementById("login-btn");
            const signupLink = document.getElementById("signup-link");
            const providerSignupLink = document.getElementById("provider-signup-link");
            const loginLinks = document.querySelectorAll("#login-link, #login-link-2");

            // Show Sign Up Form
            signupLink.addEventListener("click", function (e) {
                e.preventDefault();
                signupForm.style.display = "block";
                providerSignupForm.style.display = "none";
                loginForm.style.display = "none";
            });

            // Show Provider Sign Up Form
            providerSignupLink.addEventListener("click", function (e) {
                e.preventDefault();
                providerSignupForm.style.display = "block";
                signupForm.style.display = "none";
                loginForm.style.display = "none";
            });

            // Show Login Form
            loginBtn.addEventListener("click", function () {
                loginForm.style.display = "block";
                signupForm.style.display = "none";
                providerSignupForm.style.display = "none";
            });

            // Show Login Form from Sign Up Forms
            loginLinks.forEach(link => {
                link.addEventListener("click", function (e) {
                    e.preventDefault();
                    loginForm.style.display = "block";
                    signupForm.style.display = "none";
                    providerSignupForm.style.display = "none";
                });
            });
        });
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



