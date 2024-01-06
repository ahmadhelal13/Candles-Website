<?php 
    if (isset($_POST['logout'])) {
        session_destroy();
        header('location:register.php');
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>User header</title>
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
<!--     <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
 -->
    <!-- Libraries Stylesheet -->
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
<!--     <link href="css/bootstrap.min.css" rel="stylesheet">
 -->
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
    <div class="container-fluid fixed-top px-2 wow fadeIn" data-wow-delay="0.1s">
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
        <?php
            if (isset($user_id)) { 
            $select_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die ('query failed');
            $cart_num_rows = mysqli_num_rows($select_cart);
            }
            else {
                $cart_num_rows = 0;
            }
        ?>
        <nav class="navbar navbar-expand-lg navbar-light px-lg-5 wow fadeIn" data-wow-delay="0.1s">
            <button type="button" class="navbar-toggler me-4 ms-2" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                <span class="navbar-toggler-icon">  <?php if ($cart_num_rows != 0) { echo'
                            <sup>'.$cart_num_rows.'</sup>
                            ';}?></span>
            </button>
           
            <a href="index.php" class="navbar-brand ms-4 ms-lg-0">
                <img src="iimages/3.png" alt="Incandescent Logo">
            </a>
            
            <div class="collapse navbar-collapse pt-0" id="navbarCollapse">
                <div class="navbar-nav ms-auto p-1 p-lg-0">
                    <a href="index.php" class="nav-item nav-link">Home</a>
                    <a href="about.php" class="nav-item nav-link">About us</a>
                    <a href="orders.php" class="nav-item nav-link">Orders</a>
                    <a href="products.php" class="nav-item nav-link">Products</a>
                    <a href="contact.php" class="nav-item nav-link">Contact us</a>
                </div>
                <div class="navbar-2 d-lg-inline-flex p-3 ">
                    <!-- <a class="btn-sm-square bg-white rounded-circle ms-3" href="">
                        <small class="fa fa-search text-body"></small>
                    </a> -->
                        <a class="btn-sm-square bg-white rounded-circle ms-2 mt-2" id="user-btn">
                            <small class="fa fa-user text-body"></small>
                        </a>
                        <a class="btn-sm-square bg-white rounded-circle ms-2 mt-2 text-center" href="cart.php">
                            <small class="fa fa-shopping-bag text-body"></small>
                            <?php if ($cart_num_rows != 0) { echo'
                            <sup>'.$cart_num_rows.'</sup>
                            ';}?>
                        </a>
                    </div>
                <div class = "user-box">
                    <?php
                        if (isset($user_id)) { echo '
                        <p>Username : <span>'.$_SESSION['user_name'].'</span></p>
                        <p>Email : <span>'.$_SESSION['user_email'].'</span></p>
                        <form method="post">
                            <button type="submit" class="btn btn-primary logout-btn" name = "logout">Log out</button>
                        </form>';
                        }
                        else { echo '
                            <form method="post">
                            <button type="submit" class="btn btn-primary logout-btn" name = "logout"> Register</button>
                            <button type="submit" class="btn btn-primary logout-btn" name = "logout"> Login</button>
                        </form>';
                        }
                    ?>
                </div>
            </div>
        </nav>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Template Javascript -->
    <script type="text/javascript" src="js/main.js"></script>
    <script>
        let userbtn = document.querySelector('#user-btn');
        userbtn.addEventListener('click',function(){
            let userbox = document.querySelector('.user-box');
            userbox.classList.toggle('active');
        });
    </script>
</body>