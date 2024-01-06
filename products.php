<?php
    include 'connection.php';
    session_start();
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
    }

    if (isset($_POST['logout'])) {
        session_destroy();
        header('location:register.php');
    }

    if (isset($_POST['add_to_cart'])) {
        if (!isset($user_id)) {
            header("Location:register.php?location=" . urlencode($_SERVER['REQUEST_URI']));
        }else{
        $product_id = $_POST['product_id'];
        $product_to_cart =  mysqli_query($conn,"SELECT `products`.id, `products`.name, weight_value, price FROM `products` 
                                                JOIN (SELECT product_id, weight_id, price FROM `product_attributes` GROUP BY product_id, weight_id) as pa
                                                ON `products`.id = pa.product_id JOIN `weight` on pa.weight_id = `weight`.id
                                                WHERE `products`.id = $product_id order by 'weight_value' LIMIT 1 ") or die ('query failed');
        $fetch_product_to_cart = mysqli_fetch_assoc($product_to_cart);
        $product_name = $_POST['product_name'];
        $product_image = $_POST['product_image'];
        $product_quantity = $_POST['product_quantity'];
        if (!isset($fetch_product_to_cart['weight_value'])) {
            $message[] = 'Sorry ! '.$product_name.' is currently out of stock .';
        }else{
            $product_weight = $fetch_product_to_cart['weight_value'];
            $product_price = $fetch_product_to_cart['price'];
            $sub_total = $product_quantity * $product_price;
        
            $cart_num = mysqli_query($conn,"SELECT * FROM `cart` WHERE name='$product_name' AND user_id = '$user_id' AND weight = '$product_weight'") or die ('query failed');
            if (mysqli_num_rows($cart_num)>0) {
                $message[] = ''.$product_name.' already exists in your cart, you can chose a differenct candle size from "view details".';
            }else{
                mysqli_query($conn,"INSERT INTO `cart`(`user_id`,`pid`,`name`,`weight`,`price`,`quantity`,`sub_total`,`image`) 
                                    VALUES ('$user_id','$product_id','$product_name','$product_weight','$product_price','$product_quantity','$sub_total', '$product_image')") or die ('query failed');
                $message[] = 'Product successfully added to your cart';
                }
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Incandescent-Shop</title>
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
    <?php include 'header.php';?>
    <!-- Navbar End -->

    <!-- Page Header Start -->
    <div class="container-fluid page-header mb-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container">
            <h1 class="display-3 mb-3 animated slideInDown">Products</h1>
            <nav aria-label="breadcrumb animated slideInDown">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a class="text-body" href="#">Home</a></li>
                    <li class="breadcrumb-item text-dark active" aria-current="page">products</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Page Header End -->


    <!-- Product Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-0 gx-5 align-items-end">
                <div class="col-lg-6">
                    <div class="section-header text-start mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 500px;">
                        <h1 id = "p-title" class=" display-5 mb-3" value="">All Collections</h1>
                        <p>Explore our candles collections, made for your enjoyment. Discover our intriguing fragrances, rare spices worked into unique scents</p>
                    </div>
                </div>
                <div class="col-lg-6 text-start text-lg-end wow slideInRight" data-wow-delay="0.1s">
                    <form method="post">
                        <select id="collection" name="selectcollection">
                            <option value = "">All Products</option>
                            <?php
                            $select_collection = mysqli_query($conn, "SELECT DISTINCT collection FROM `products`") or die('query failed');
                            if (mysqli_num_rows($select_collection)>0)  {
                                while($fetch_collection = mysqli_fetch_assoc($select_collection)) {
                            ?>
                            <option value = "<?php echo $fetch_collection['collection'];?>"><?php echo $fetch_collection['collection'];?></option>
                            <?php 
                                }
                            }
                            ?>
                        </select>
                    </form>
                </div>
            </div>
            <div class="tab-content">
                <div id="all" class="tab-pane fade show p-0 active">
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
                    <div class="row g-4 mt-3">
                        <?php
                            $select_products = mysqli_query($conn, "SELECT * FROM `products`") or die('query failed');
                            if ( mysqli_num_rows($select_products)>0) {
                                while($fetch_products = mysqli_fetch_assoc($select_products)) {
                        ?>
                            <div class="col-xl-3 col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                                <form method = "post" class="product-item">
                                    <div class="position-relative bg-light overflow-hidden">
                                        <img class="img-fluid w-100" src="./iimages/imagephp/<?php echo $fetch_products['image1'];?>">
                                        <div class="bg-secondary rounded text-white position-absolute start-0 top-0 m-4 py-1 px-3">New</div>
                                    </div>
                                    <div class="text-center p-4 p-input">
                                        <a class="d-block h5 mb-1" href="product_details.php?id=<?php echo $fetch_products['id']; ?>"><?php echo $fetch_products['name'];?></a>
                                        <p class="h6 mb-2"><?php echo $fetch_products['collection'];?></p>
                                        <span class="text-primary me-1">From $<?php echo $fetch_products['basic_price'];?></span>
                                        <input type="hidden" name="product_id" value = "<?php echo $fetch_products['id']; ?>">
                                        <input type="hidden" name="product_name" value = "<?php echo $fetch_products['name']; ?>">
                                        <input type="hidden" name="product_price" value = "<?php echo $fetch_products['basic_price']; ?>">
                                        <input type="hidden" name="product_quantity" value = "1" min = "1">
                                        <input type="hidden" name="product_image" value = "<?php echo $fetch_products['image1']; ?>">
                                        <!-- <span class="text-body text-decoration-line-through">$29.00</span> -->
                                    </div>
                                    <div class="d-flex border-top">
                                        <small class="w-50 text-center border-end py-2">
                                            <a class="text-body" href="product_details.php?id=<?php echo $fetch_products['id']; ?>"><i class="fa fa-eye text-primary me-2"> View Details</i></a>
                                        </small>
                                        <small class="w-50 text-center py-2">
                                            <button type ="submit" name= "add_to_cart" class="text-body" ><i class="fa fa-shopping-bag text-primary me-2"> Add To Cart</i></button>
                                        </small>
                                    </div>
                                </form>
                            </div>
                        <?php
                                }
                            }else{
                                echo'
                                    <div class="empty">
                                        <p>No Products Available</p>
                                    </div>
                                ';
                            }    
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Testimonial Start -->
    <div class="container-fluid bg-light bg-icon py-6 mb-5">
        <div class="container">
            <div class="section-header text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 500px;">
                <h1 class="display-5 mb-3">Customer Review</h1>
                <p>Here is a sample of our customer reviews in Incandescent.</p>
            </div>
            <div class="owl-carousel testimonial-carousel wow fadeInUp" data-wow-delay="0.1s">
            <?php
                $select_messages = mysqli_query($conn, "SELECT * FROM `message` WHERE privacy = 'public'") or die('query failed');
                if (mysqli_num_rows($select_messages)>0) {
                    while ($fetch_messages = mysqli_fetch_assoc($select_messages)) {

                ?>
                <div class="testimonial-item position-relative bg-white p-4 mt-4">
                    <i class="fa fa-quote-left fa-3x text-primary position-absolute top-0 start-0 mt-n4 ms-5"></i>
                    <p class="mb-5">"<?php echo $fetch_messages['message'];?>"</p>
                    <div class="d-flex align-items-center">
                        <!-- <img class="flex-shrink-0 rounded-circle" src="img/testimonial-1.jpg" alt=""> -->
                        <div>
                            <h5 class="mb-1"><?php echo $fetch_messages['name'];?></h5>
                            <span></span>
                        </div>
                    </div>
                </div>
                <?php    
                    }
                }else{
                    echo'
                        <div class="empty">
                            <p>No Messages</p>
                        </div>
                    ';
                }                   
                ?>
            </div>
        </div>
    </div>
    <!-- Testimonial End -->


    <!-- Blog Start 
     <div class="container-xxl py-5">
        <div class="container">
            <div class="section-header text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 500px;">
                <h1 class="display-5 mb-3">Latest Blog</h1>
                <p>Tempor ut dolore lorem kasd vero ipsum sit eirmod sit. Ipsum diam justo sed rebum vero dolor duo.</p>
            </div>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                    <img class="img-fluid" src="img/blog-1.jpg" alt="">
                    <div class="bg-light p-4">
                        <a class="d-block h5 lh-base mb-4" href="">How to cultivate organic fruits and vegetables in own firm</a>
                        <div class="text-muted border-top pt-4">
                            <small class="me-3"><i class="fa fa-user text-primary me-2"></i>Admin</small>
                            <small class="me-3"><i class="fa fa-calendar text-primary me-2"></i>01 Jan, 2045</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                    <img class="img-fluid" src="img/blog-2.jpg" alt="">
                    <div class="bg-light p-4">
                        <a class="d-block h5 lh-base mb-4" href="">How to cultivate organic fruits and vegetables in own firm</a>
                        <div class="text-muted border-top pt-4">
                            <small class="me-3"><i class="fa fa-user text-primary me-2"></i>Admin</small>
                            <small class="me-3"><i class="fa fa-calendar text-primary me-2"></i>01 Jan, 2045</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
                    <img class="img-fluid" src="img/blog-3.jpg" alt="">
                    <div class="bg-light p-4">
                        <a class="d-block h5 lh-base mb-4" href="">How to cultivate organic fruits and vegetables in own firm</a>
                        <div class="text-muted border-top pt-4">
                            <small class="me-3"><i class="fa fa-user text-primary me-2"></i>Admin</small>
                            <small class="me-3"><i class="fa fa-calendar text-primary me-2"></i>01 Jan, 2045</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> -->
    <!-- Product End -->


    <!-- Firm Visit Start 
    <div class="container-fluid bg-primary bg-icon mt-5 py-6">
        <div class="container">
            <div class="row g-5 align-items-center">
                <div class="col-md-7 wow fadeIn" data-wow-delay="0.1s">
                    <h1 class="display-5 text-white mb-3">Visit Our Firm</h1>
                    <p class="text-white mb-0">Tempor erat elitr rebum at clita. Diam dolor diam ipsum sit. Aliqu diam amet diam et eos. Clita erat ipsum et lorem et sit, sed stet lorem sit clita duo justo magna dolore erat amet. Diam dolor diam ipsum sit. Aliqu diam amet diam et eos.</p>
                </div>
                <div class="col-md-5 text-md-end wow fadeIn" data-wow-delay="0.5s">
                    <a class="btn btn-lg btn-secondary rounded-pill py-3 px-5" href="">Visit Now</a>
                </div>
            </div>
        </div>
    </div>
     Firm Visit End -->


    <!-- Testimonial Start 
    <div class="container-fluid bg-light bg-icon py-6">
        <div class="container">
            <div class="section-header text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 500px;">
                <h1 class="display-5 mb-3">Customer Review</h1>
                <p>Tempor ut dolore lorem kasd vero ipsum sit eirmod sit. Ipsum diam justo sed rebum vero dolor duo.</p>
            </div>
            <div class="owl-carousel testimonial-carousel wow fadeInUp" data-wow-delay="0.1s">
                <div class="testimonial-item position-relative bg-white p-5 mt-4">
                    <i class="fa fa-quote-left fa-3x text-primary position-absolute top-0 start-0 mt-n4 ms-5"></i>
                    <p class="mb-4">Tempor erat elitr rebum at clita. Diam dolor diam ipsum sit diam amet diam et eos. Clita erat ipsum et lorem et sit.</p>
                    <div class="d-flex align-items-center">
                        <img class="flex-shrink-0 rounded-circle" src="img/testimonial-1.jpg" alt="">
                        <div class="ms-3">
                            <h5 class="mb-1">Client Name</h5>
                            <span>Profession</span>
                        </div>
                    </div>
                </div>
                <div class="testimonial-item position-relative bg-white p-5 mt-4">
                    <i class="fa fa-quote-left fa-3x text-primary position-absolute top-0 start-0 mt-n4 ms-5"></i>
                    <p class="mb-4">Tempor erat elitr rebum at clita. Diam dolor diam ipsum sit diam amet diam et eos. Clita erat ipsum et lorem et sit.</p>
                    <div class="d-flex align-items-center">
                        <img class="flex-shrink-0 rounded-circle" src="img/testimonial-2.jpg" alt="">
                        <div class="ms-3">
                            <h5 class="mb-1">Client Name</h5>
                            <span>Profession</span>
                        </div>
                    </div>
                </div>
                <div class="testimonial-item position-relative bg-white p-5 mt-4">
                    <i class="fa fa-quote-left fa-3x text-primary position-absolute top-0 start-0 mt-n4 ms-5"></i>
                    <p class="mb-4">Tempor erat elitr rebum at clita. Diam dolor diam ipsum sit diam amet diam et eos. Clita erat ipsum et lorem et sit.</p>
                    <div class="d-flex align-items-center">
                        <img class="flex-shrink-0 rounded-circle" src="img/testimonial-3.jpg" alt="">
                        <div class="ms-3">
                            <h5 class="mb-1">Client Name</h5>
                            <span>Profession</span>
                        </div>
                    </div>
                </div>
                <div class="testimonial-item position-relative bg-white p-5 mt-4">
                    <i class="fa fa-quote-left fa-3x text-primary position-absolute top-0 start-0 mt-n4 ms-5"></i>
                    <p class="mb-4">Tempor erat elitr rebum at clita. Diam dolor diam ipsum sit diam amet diam et eos. Clita erat ipsum et lorem et sit.</p>
                    <div class="d-flex align-items-center">
                        <img class="flex-shrink-0 rounded-circle" src="img/testimonial-4.jpg" alt="">
                        <div class="ms-3">
                            <h5 class="mb-1">Client Name</h5>
                            <span>Profession</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
     Testimonial End -->


    <!-- Footer Start -->
    <div class="container-fluid bg-dark footer mt-5 pt-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container py-5">
            <div class="row g-5">
                <div class="col-lg-3 col-md-6">
                    <h2 class="fw-bold text-primary mb-4">Incandescent</h2>
                    <p>Experince unique Fragrances</p>
                    <div class="d-flex pt-2">
                        <a class="btn btn-square btn-outline-light rounded-circle me-1" href=""><i class="fab fa-twitter"></i></a>
                        <a class="btn btn-square btn-outline-light rounded-circle me-1" href=""><i class="fab fa-facebook-f"></i></a>
                        <a class="btn btn-square btn-outline-light rounded-circle me-1" href=""><i class="fab fa-youtube"></i></a>
                        <a class="btn btn-square btn-outline-light rounded-circle me-0" href=""><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-light mb-4">Address</h4>
                    <p><i class="fa fa-map-marker-alt me-3"></i>Cairo, Egypt</p>
                    <p><i class="fa fa-phone-alt me-3"></i>+012 345 67890</p>
                    <p><i class="fa fa-envelope me-3"></i>ahmadhelal13gm@gmail.com</p>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-light mb-4">Quick Links</h4>
                    <a class="btn btn-link" href="">About Us</a>
                    <a class="btn btn-link" href="">Contact Us</a>
                    <a class="btn btn-link" href="">Our Services</a>
                    <a class="btn btn-link" href="">Terms & Condition</a>
                    <a class="btn btn-link" href="">Support</a>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-light mb-4">Subscribe to our newsletter!</h4>
                    <p>Sign up to the newsletter to know about new products, collection releases and discounts.</p>
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
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
    
    <script>
        let selection = document.querySelector("#collection");
        let heading = document.querySelector("#p-title");
        let container = document.querySelector("#all");

        selection.addEventListener('change',() => {
            let collection = selection.value;
            heading.innerText = selection.options[selection.selectedIndex].text;

            $.ajax({ 
                url:"fetch-collection.php",
                data:"collection=" + collection,
                type: "POST",
                success: function(data){
                    $("#all").html(data);
                }
            });
        });
    </script>
</body>

</html>