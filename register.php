<?php
    include 'connection.php';
    session_start();
    //Register
    if (isset($_POST['register-btn'])) {
        $filter_name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
        $name = mysqli_real_escape_string($conn, $filter_name);

        $filter_email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
        $email = mysqli_real_escape_string($conn, $filter_email);
    
        $filter_password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
        $password = mysqli_real_escape_string($conn, $filter_password);

        $filter_cpassword = filter_var($_POST['cpassword'], FILTER_SANITIZE_STRING);
        $cpassword = mysqli_real_escape_string($conn, $filter_cpassword);

        $select_user = mysqli_query($conn, "SELECT * FROM `users` WHERE email = '$email'") or die('query failed');

        if (mysqli_num_rows($select_user)>0) {
            $message[] = 'User is already registered';
        }else{
            if ($password != $cpassword) {
                $message[] = 'Wrong password';
            }else{
                mysqli_query($conn, "INSERT INTO `users`(`name`,`email`,`password`) VALUES ('$name','$email','$password')") or die('query failed');
                $message[] = 'Registerd successfully';
                header('location:register.php');
            }
        }
    }else{
        //login
        if (isset($_POST['login-btn'])) {
            $filter_email = filter_var($_POST['Lemail'], FILTER_SANITIZE_STRING);
            $email = mysqli_real_escape_string($conn, $filter_email);
        
            $filter_password = filter_var($_POST['Lpassword'], FILTER_SANITIZE_STRING);
            $password = mysqli_real_escape_string($conn, $filter_password);

            $redirect = NULL;
            if($_POST['location'] != '') {
                $redirect = $_POST['location'];
            }

            $select_user = mysqli_query($conn, "SELECT * FROM `users` WHERE email = '$email' and password = '$password'") or die('query failed');

            if (mysqli_num_rows($select_user)==0) {
                $message[]= 'Incorrect username or password';
            }else if (mysqli_num_rows($select_user)>0) {
                $row = mysqli_fetch_assoc($select_user);
                if ($row['user_type']== 'admin') {
                    $_SESSION['admin_name'] = $row['name'];
                    $_SESSION['admin_email'] = $row['email'];
                    $_SESSION['admin_id'] = $row['id'];
                    header('location:admin_panel.php');
                }else if ($row['user_type']=='user') {
                    $_SESSION['user_name'] = $row['name'];
                    $_SESSION['user_email'] = $row['email'];
                    $_SESSION['user_id'] = $row['id'];
                    if($redirect) {
                        header("Location:". $redirect);
                    } else {
                        header("Location:index.php");
                    }
                }
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Incandescent-Scented Candles</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500&family=Lora:wght@600;700&display=swap" rel="stylesheet"> 

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
</head>

<body>
    
    <!-- Spinner Start -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-border text-primary" role="status"></div>
    </div>
    <!-- Spinner End -->

    <!-- Navbar Start -->
    <div class="container-fluid fixed-top px-0 wow fadeIn" data-wow-delay="0.1s">
        <!-- <div class="top-bar row gx-0 align-items-center d-none d-lg-flex">
            <div class="col-lg-6 px-5 text-start">
                <small><i class="fa fa-map-marker-alt me-2"></i>Cairo, Egypt</small>
                <small class="ms-4"><i class="fa fa-envelope me-2"></i>info@example.com</small>
            </div>
            <div class="col-lg-6 px-5 text-end">
                <small>Follow us:</small>
                <a class="text-body ms-3" href=""><i class="fab fa-facebook-f"></i></a>
                <a class="text-body ms-3" href=""><i class="fab fa-twitter"></i></a>
                <a class="text-body ms-3" href=""><i class="fab fa-linkedin-in"></i></a>
                <a class="text-body ms-3" href=""><i class="fab fa-instagram"></i></a>
            </div>
        </div> -->
        <nav class="navbar navbar-expand-lg navbar-light py-lg-0 px-lg-5 wow fadeIn" data-wow-delay="0.1s">
            <button type="button" class="navbar-toggler me-4 ms-2" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a href="index.html" class="navbar-brand ms-4 ms-lg-0">
                <img src="iimages/3.png" alt="Incandescent Logo">
            </a>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <div class="navbar-nav ms-auto p-4 p-lg-0">
                    <a href="index.php" class="nav-item nav-link">Home</a>
                    <a href="about.php" class="nav-item nav-link">About us</a>
                    <a href="orders.php" class="nav-item nav-link">Orders</a>
                    <a href="products.php" class="nav-item nav-link">Products</a>
                    <a href="contact.php" class="nav-item nav-link">Contact us</a>
                    <!-- <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Pages</a>
                        <div class="dropdown-menu m-0">
                            <a href="blog.html" class="dropdown-item">Blog Grid</a>
                            <a href="feature.html" class="dropdown-item">Our Features</a>
                            <a href="testimonial.html" class="dropdown-item">Testimonial</a>
                            <a href="404.html" class="dropdown-item">404 Page</a>
                        </div>
                    </div> -->
                </div>
                <div class="d-none d-lg-flex ms-2">
                    <a class="btn-sm-square bg-white rounded-circle ms-3" href="">
                        <small class="fa fa-search text-body"></small>
                    </a>
                    <a class="btn-sm-square bg-white rounded-circle ms-3" href="register.php">
                        <small class="fa fa-user text-body"></small>
                    </a>
                    <a class="btn-sm-square bg-white rounded-circle ms-3"  href="">
                        <small class="fa fa-shopping-bag text-body"></small>
                    </a>
                </div>
            </div>
        </nav>
    </div>
    <!-- Navbar End -->
    
    <!-- login-form  -->
    <div id='login-form'class='login-page'>
        <div class="form-box">
            <!-- Pop up message -->
            <?php 
                if (isset($message)) {
                    foreach ($message as $message) {
                        echo '
                            <div class= "message">
                                <Span>'.$message.'</span>
                                <i class="bi bi-x-circle" onclick="this.parentElement.remove()"></i>
                            </div>
                        ';
                    }
                }
            ?>
            <div class='button-box'>
                <div id='btn'></div>
                <button type='button'onclick='login()'class='toggle-btn'>Log In</button>
                <button type='button'onclick='register()'class='toggle-btn'>Register</button>
            </div>
            <form id='login' class='input-group-login' method ='post'>
                <input type='text'class='input-field' name ="Lemail" placeholder='Email' required >
                <input type='password'class='input-field' name= "Lpassword" placeholder='Enter Password' required>
                <input type='checkbox'class='check-box'><span>Remember Password</span>
                <?php 
                    echo '<input type="hidden" name="location" value="';
                    if(isset($_GET['location'])) {
                        echo htmlspecialchars($_GET['location']);
                    }
                    echo '" />';
                ?>
                <button type='submit'class='submit-btn' name='login-btn'>Log in</button>
            </form>
            <form id='register' class='input-group-register' method ='post'>
                <input type='text'class='input-field' name = "name" placeholder='Full Name' required>
                <input type='email'class='input-field' name = "email" placeholder='Email' required>
                <input type='password'class='input-field' name = "password" placeholder='Password' required>
                <input type='password'class='input-field' name = "cpassword" placeholder='Confirm Password'  required>
                <!-- <input type='checkbox'class='check-box'><span>I agree to the terms and conditions</span> -->
                <button type='submit'class='submit-btn' name='register-btn'>Register</button>
            </form>
        </div>
    </div>
    <!-- login-form -->
    



    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square rounded-circle back-to-top"><i class="bi bi-arrow-up"></i></a>


    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Register and Login -->
    <script>
        var x=document.getElementById('login');
        var y=document.getElementById('register');
        var z=document.getElementById('btn');
        function register()
            {
                x.style.left='-400px';
                y.style.left='50px';
                z.style.left='110px';
            }
        function login()
            {
                x.style.left='50px';
                y.style.left='450px';
                z.style.left='0px';
            }
    </script>
    
    <script>
    var modal = document.getElementById('login-form');
    window.onclick = function(event) 
    {
        if (event.target == modal) 
        {
            modal.style.display = "none";
        }
    }

    </script>
    
    <!-- Template Javascript -->
    <script src="js/main.js"></script>
    <script src=" https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
</body>

</html>

