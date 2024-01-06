<?php
    include 'connection.php';
    include 'alert.php';
    session_start();
    $user_id = $_SESSION['user_id'];

    if (!isset($user_id)) {
        header('location:register.php');
    }
    if (isset($_POST['logout'])) {
        session_destroy();
        header('location:register.php');
    }

    $cart_total = 0;
    $select_cart = mysqli_query($conn,"SELECT * FROM `cart` where user_id = '$user_id'") or die ('query failed');
    $total_products = mysqli_num_rows($select_cart);
    if ($total_products>0) {
        while ($fetch_cart = mysqli_fetch_assoc($select_cart)) {
            $cart_total += $fetch_cart['sub_total'];
        }
    }
    if ($cart_total != 0) 
        {$shipping=5.00; 
        $Total = $cart_total + $shipping;}
    else{$cart_total=0.00;
        $shipping=0.00; 
        $Total=0.00;}

    if (isset($_POST['place_order'])) {

        if ($Total== 0.00) {
            $warning_msg[]= 'Your cart is empty !';
            $message[] = 'Your Cart is empty !';
        }else{
        $filter_name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
        $name = mysqli_real_escape_string($conn,$filter_name);

        $filter_email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
        $email = mysqli_real_escape_string($conn,$filter_email);

        $filter_number = filter_var($_POST['number'], FILTER_SANITIZE_STRING);
        $number = mysqli_real_escape_string($conn,$filter_number);

        $method = mysqli_real_escape_string($conn,$_POST['method']);
        
        $filter_building = filter_var($_POST['building'], FILTER_SANITIZE_STRING);
        $filter_street = filter_var($_POST['street'], FILTER_SANITIZE_STRING);
        $filter_city = filter_var($_POST['city'], FILTER_SANITIZE_STRING);
        $filter_pcode = filter_var($_POST['pcode'], FILTER_SANITIZE_STRING);

        $address = mysqli_real_escape_string($conn,'building no.'.$filter_building.','.$filter_street.','.$filter_city.','.$filter_pcode);

        $placed_on = date('d-M-Y');

        mysqli_query($conn, "INSERT INTO `order` (`user_id`,`name`,`number`,`email`,`method`,`address`,`total_products`,`total_price`,`placed_on`,`payment_status`)
                        VALUES ('$user_id','$name','$number','$email','$method','$address','$total_products','$cart_total','$placed_on', 'pending')");
        
        mysqli_query($conn, "INSERT INTO `order_items` (`order_id`,`product_id`,`weight`,`quantity`,`subtotal`)
                    SELECT `order`.id, cart.pid, cart.weight, cart.quantity, cart.sub_total FROM cart LEFT JOIN `order` 
                    ON cart.user_id = `order`.user_id AND `order`.id = (SELECT MAX(id) FROM `order`)");

        mysqli_query($conn,"DELETE FROM  `cart` where user_id= '$user_id'");
        
        $success_msg[]= 'Order Placed Successfully';
        $message[] = 'Order Placed Successfully';
        };
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Incandescent-Contact us</title>
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

    <!-- sweet alert -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.0/dist/sweetalert2.min.css">
</head>

<body>
   <!-- Spinner Start -->
   <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
    <div class="spinner-border text-primary" role="status"></div>
</div>
    <!-- Spinner End -->


    <!-- Page header start -->
    <?php include 'header.php';?>
    <!-- Page Header End -->

    <!-- Checkout Form Start -->
    
    <div class="checkout-banner mb-3">    
        <h1 class="display-5 mb-3 ">Checkout Summary</h1>
    </div>
    <div class="container-xxl mt-3 pt-3">
        <div class= "checkout-form">
            <div id="subtotal">
                <h3>Total Price</h3>
                <table>
                    <tr>
                        <td>Cart Total</td>
                        <td>$ <?php printf("%.2f",$cart_total);?></td>
                    </tr>
                    <tr>
                        <td>Shipping</td>
                        <td>$ <?php  printf("%.2f",$shipping);?></td>
                    </tr>
                    <tr>
                        <td><strong>Total</strong></td>
                        <td><strong>$ <?php printf("%.2f",$Total);?></strong></td>
                    </tr>
                </table>
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
            </div>
            <Form method="post">
                <h2 class="title">Payment Details</h2>
                <div class= "flex">
                    <div class= "box">
                        <div class = "input-field">
                            <p>Name <span>*</span></p>
                            <input type="text" name ="name" placeholder="Enter your name" class="input" required>
                        </div>
                        <div class = "input-field">
                            <p>Phone Number <span>*</span></p>
                            <input type="number" name ="number" placeholder="Enter your number" class="input" required>
                        </div>
                        <div class = "input-field">
                            <p>Email <span>*</span></p>
                            <input type="email" name ="email" placeholder="Enter your email" class="input" required>
                        </div>
                        <div class = "input-field">
                            <p>Payment method <span>*</span></p>
                            <select name="method" class="input" required>
                                <option value="cash on delivery">cash on delivery</option>
                            </select>
                        </div>
                    </div>
                    <div class= "box">
                        <div class = "input-field">
                            <p>City <span>*</span></p>
                            <input type="text" name ="city" placeholder="Enter city name" class="input" required>
                        </div>
                        <div class = "input-field">
                            <p>Address 01 <span>*</span></p>
                            <input type="text" name ="building" placeholder="e.g. apartment & building no." class="input" required>
                        </div>
                        <div class = "input-field">
                            <p>Address 02 <span>*</span></p>
                            <input type="text" name ="street" placeholder="e.g. street name" class="input" required>
                        </div>
                        <div class = "input-field">
                            <p>Postal code <span></span></p>
                            <input type="text" name ="pcode" maxlength="8" placeholder="e.g. 11553" class="input">
                        </div>
                    </div>
                </div>
                <button type="submit" name="place_order" class="btn btn-primary rounded-pill">Place Order</button> 
            </Form>
        </div>
    </div>
    <!-- Checkout Form End -->

    <!-- Footer Start -->
    <div class="container-fluid bg-dark footer mt-5 pt-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container py-5">
            <div class="row g-5">
                <div class="col-lg-3">
                    <h2 class="fw-bold text-primary mb-4">Incandescent</h2>
                    <p>Experince unique Fragrances</p>
                    <div class="d-flex pt-2">
                        <a class="btn btn-square btn-outline-light rounded-circle me-1" href=""><i class="fab fa-twitter"></i></a>
                        <a class="btn btn-square btn-outline-light rounded-circle me-1" href=""><i class="fab fa-facebook-f"></i></a>
                        <a class="btn btn-square btn-outline-light rounded-circle me-1" href=""><i class="fab fa-youtube"></i></a>
                        <a class="btn btn-square btn-outline-light rounded-circle me-0" href=""><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 ">
                    <h5 class="text-light mb-4">Address</h5>
                    <p><i class="fa fa-map-marker-alt me-3"></i>Cairo, Egypt</p>
                    <p><i class="fa fa-phone-alt me-3"></i>+012 345 67890</p>
                    <p><i class="fa fa-envelope me-3"></i>ahmadhelal13gm@gmail.com</p>
                </div>
                <div class="col-lg-3 ">
                    <h4 class="text-light mb-4">Quick Links</h4>
                    <a class="btn btn-link" href="about.php">About Us</a>
                    <a class="btn btn-link" href="contact.php">Contact Us</a>
                    <a class="btn btn-link" href="products.php">Our products</a>
                    <a class="btn btn-link" href="">Terms & Condition</a>
                    <a class="btn btn-link" href="contact.php">Support</a>
                </div>
                <div class="col-lg-3">
                    <h4 class="text-light mb-4">Subscribe to our newsletter!</h4>
                    <p>Sign up for the latest products, collection releases and discounts.</p>
                    <div class="position-relative mx-auto" style="max-width: 400px;">
                        <input class="form-control bg-transparent w-100 py-3 ps-4 pe-5" type="text" placeholder="Your email">
                        <button type="button" class="btn btn-primary py-2 position-absolute top-0 end-0 mt-2 me-2">Sign Up</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid copyright">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                        &copy; <a href="#">Incandescent</a>, All Right Reserved.
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                        <!--/*** This template is free as long as you keep the footer author’s credit link/attribution link/backlink. If you'd like to use the template without the footer author’s credit link/attribution link/backlink, you can purchase the Credit Removal License from "https://htmlcodex.com/credit-removal". Thank you for your support. ***/-->
                        Designed By <a href="https://htmlcodex.com">HTML Codex</a>
                        <br>Distributed By: <a href="https://themewagon.com" target="_blank">ThemeWagon</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer End -->


    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square rounded-circle back-to-top"><i class="bi bi-arrow-up"></i></a>


    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Template Javascript -->
    <script>
        $(".testimonial-carousel").owlCarousel({
            autoplay: true,
            smartSpeed: 1000,
            margin: 25,
            loop: false,
            center: true,
            dots: false,
            nav: true,
            navText : [
                '<i class="bi bi-chevron-left"></i>',
                '<i class="bi bi-chevron-right"></i>'
            ],
            responsive: {
                0:{
                    items:1
                },
                768:{
                    items:2
                },
                992:{
                    items:3
                }
            }
        });
    </script>
    <script src="js/main.js"></script>

</body>

</html>