<?php
    include 'connection.php';
    session_start();
    $user_id = $_SESSION['user_id'];

    if (!isset($user_id)) {
        header('location:register.php');
    }
    if (isset($_POST['logout'])) {
        session_destroy();
        header('location:register.php');
    }

    if (isset($_GET['delete'])) {
        $delete_id = $_GET['delete'];
        mysqli_query($conn,"DELETE FROM `cart` WHERE id = '$delete_id'") or die('query failed');
        header('location:cart.php');
    }

    if (isset($_POST['update'])) {
        $cart_id = $_POST['cart_id'];
        $product_name = $_POST['name'];
        $weight_update = $_POST['weight_update'];
        $quantity_update= $_POST['quantity_update'];
        $pid = $_POST['product_id'];
        mysqli_begin_transaction($conn);
        $update_cart = mysqli_query($conn, "UPDATE `cart` SET `weight`='$weight_update',
                                    `price`= (select price FROM `product_attributes` join `weight` on
                                    `product_attributes`.weight_id = `weight`.id  WHERE product_id = '$pid' 
                                     and `weight`.weight_value = '$weight_update'),
                                    `quantity`= '$quantity_update' WHERE id = '$cart_id'") or die('query failed');
        mysqli_query($conn, "UPDATE `cart` SET `sub_total`= (SELECT  price * quantity FROM `cart` WHERE id = '$cart_id' ) where id ='$cart_id'") or die('query failed');

        $cart_num = mysqli_query($conn,"SELECT * FROM `cart` WHERE pid = '$pid' AND user_id = '$user_id' AND weight = '$weight_update'") or die ('query failed');
        if (mysqli_num_rows($cart_num)>1) {
            mysqli_rollback($conn);
            $message[] = ''.$product_name.' ('.$weight_update.') already exists in your cart';
        }else{
            mysqli_commit($conn);
        }
    }

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Incandescent-cart</title>
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


    <!-- Page header start -->
    <?php include 'header.php';?>
    <div class="container-fluid page-header wow fadeIn" data-wow-delay="0.1s">
        <div class="container">
            <h1 class="display-3 mb-3 animated slideInDown">My Cart</h1>
            <nav aria-label="breadcrumb animated slideInDown">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a class="text-body" href="#">Home</a></li>
                    <li class="breadcrumb-item text-dark active" aria-current="page">Cart</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Page Header End -->

    <!-- Cart Table -->
    <section id="cart" class="m2">
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
        <table class="m2" width="100%" overflow-x ="scroll">
            <thead>
                <tr>
                    <td>Image</td>
                    <td>Product</td>
                    <td>Price</td>
                    <td>Weight</td>
                    <td>Quantity</td>
                    <td>Subtotal</td>
                    <td>Update / Remove</td>
                </tr>
            </thead>
            <tbody>
                <?php
                    $cart_total = 0;
                    $select_cart = mysqli_query($conn,"SELECT * FROM `cart` where user_id = '$user_id'") or die ('query failed');
                    if (mysqli_num_rows($select_cart)>0) {
                        while ($fetch_cart = mysqli_fetch_assoc($select_cart)) {
                            $cart_total += $fetch_cart['sub_total'];
                            $product_id = $fetch_cart['pid'];
                            $cart_id = $fetch_cart['id'];
                ?>
                <form method="post">
                    <tr>
                        <input type="hidden" name="cart_id" value = "<?php echo $fetch_cart['id']; ?>">
                        <input type="hidden" name="product_id" value = "<?php echo $fetch_cart['pid']; ?>">
                        <input type="hidden" name="name" value = "<?php echo $fetch_cart['name']; ?>">
                        <td><img src="./iimages/imagephp/<?php echo $fetch_cart['image'];?>" alt=""></td>
                        <td><?php echo $fetch_cart['name'];?></td>
                        <td>$ <?php echo $fetch_cart['price'];?></td>
                        <td>
                            <select id="candle_weight" name= "weight_update">
                                <option  hidden value = "<?php echo $fetch_cart['weight'];?>"><?php echo $fetch_cart['weight'];?></option>
                                <?php
                                $select_product_attr = mysqli_query($conn, "SELECT `cart`.id, `product_attributes`.weight_id,weight_value,`product_attributes`.price 
                                                                    FROM `product_attributes` join `weight` on `product_attributes`.weight_id = `weight`.id 
                                                                    JOIN `cart` ON `product_attributes`.product_id = `cart`.pid WHERE `cart`.id = $cart_id
                                                                    ORDER BY `product_attributes`.`weight_id` ASC") or die('query failed');
                                if (mysqli_num_rows($select_product_attr)>0)  {
                                    while($fetch_product_attr = mysqli_fetch_assoc($select_product_attr)) {
                                ?>
                                <option value = "<?php echo $fetch_product_attr['weight_value'];?>" ><?php echo $fetch_product_attr['weight_value'];?></option>
                                <?php 
                                    }
                                }
                                ?>
                            </select>
                        </td>
                        <td class="text-center">
                            <input type="number" name="quantity_update" min="1" value="<?php echo $fetch_cart['quantity'];?>">
                        </td>
                        <td class="text-center">$ <?php echo $fetch_cart['sub_total'];?></td>
                        <td class="text-center">
                            <button name= "update" class="btn btn-primary py-1 px-3 m-2"><i class="fa fa-pencil"></i>Update</button>
                            <a class="btn btn-primary py-1 px-2 " href="cart.php?delete=<?php echo $fetch_cart['id']; ?>" onclick="return confirm('Are you sure you want to delete this product?');"><i class=" fa fa-trash"></i> Remove</a>
                        </td>
                    </tr>
                </form>
                <?php
                    }
                }
                ?>
            </tbody>
        </table> 
    </section>
    <!-- Cart Table -->

    <!-- Cart add -->
    <section id="cart-add" class="m2">
        <div id="coupon">
            <h3>Apply Coupon</h3>
            <div>
                <input type="text" placeholder="Enter Your Coupon">
                <button class="btn btn-primary py-2 px-3 ">Apply</button>
            </div>
        </div>
        <div id="subtotal">
            <h3>Cart Total</h3>
            <table>
                <tr>
                    <td>Cart Subtotal</td>
                    <?php
                        if ($cart_total != 0) 
                        {$shipping=5.00; 
                         $Total = $cart_total + $shipping;}
                        else{$cart_total=0.00;
                            $shipping=0.00; 
                            $Total=0.00;}
                        
                    ?>
                    <td>$ <?php printf("%.2f",$cart_total);?></td>
                </tr>
                <tr>
                    <td>Shipping</td>
                    <td>$ <?php  printf("%.2f",$shipping);?></td>
                </tr>
                <tr>
                    <td>Total</td>
                    <td>$ <?php printf("%.2f",$Total);?></td>
                </tr>
            </table>
            <a href="checkout.php" target="_blank"><button  class="btn btn-primary py-2 px-3">Proceed to checkout</button></a>
        </div>
    </section>
    <!-- Cart add -->


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
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
</body>

</html>