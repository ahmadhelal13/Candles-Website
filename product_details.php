<?php
    include 'connection.php';
    session_start();
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
    }

    if (isset($_POST['logout'])) {
        session_destroy();
    }

    if (isset($_GET['id'])) {
        $detail_id = (($_GET['id']));
    }

    if (isset($_POST['add_to_cart1'])) {
        if (!isset($user_id)) {
            header("Location:register.php?location=" . urlencode($_SERVER['REQUEST_URI']));
        }else{
            $product_price = trim($_POST['candle_weight'],'$');
            if ($product_price != null) {
                $product_id = $_POST['product_id'];
                $product_name = $_POST['product_name'];
                $product_weight = $_POST['weightinput'];
                $product_image = $_POST['product_image'];
                $product_quantity = $_POST['product_quantity'];
                $sub_total = $product_quantity * $product_price;
                $cart_num = mysqli_query($conn,"SELECT * FROM `cart` WHERE name='$product_name' AND user_id = '$user_id' AND weight = '$product_weight'") or die ('query failed');
                if (mysqli_num_rows($cart_num)>0) {
                    $message[] = 'Product already exists in your cart';
                }else{
                    mysqli_query($conn,"INSERT INTO `cart`(`user_id`,`pid`,`name`,`weight`,`price`,`quantity`,`sub_total`,`image`) 
                        VALUES ('$user_id','$product_id','$product_name','$product_weight','$product_price','$product_quantity','$sub_total', '$product_image')");
                    $message[] = 'Product successfully added to your cart';
                }
            }
            else{
                $message[] = 'Please select candle size !';
            }
        }
    }

    if (isset($_POST['add_to_cart2'])) {
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
                $message2[] = 'Sorry ! '.$product_name.' is currently out of stock .';
            }else{
                $product_weight = $fetch_product_to_cart['weight_value'];
                $product_price = $fetch_product_to_cart['price'];
                $sub_total = $product_quantity * $product_price;
            
                $cart_num = mysqli_query($conn,"SELECT * FROM `cart` WHERE name='$product_name' AND user_id = '$user_id' AND weight = '$product_weight'") or die ('query failed');
                if (mysqli_num_rows($cart_num)>0) {
                    $message2[] = ''.$product_name.' already exists in your cart, you can chose a differenct candle size from "view details".';
                }else{
                    mysqli_query($conn,"INSERT INTO `cart`(`user_id`,`pid`,`name`,`weight`,`price`,`quantity`,`sub_total`,`image`) 
                                        VALUES ('$user_id','$product_id','$product_name','$product_weight','$product_price','$product_quantity','$sub_total', '$product_image')") or die ('query failed');
                    $message2[] = 'Product successfully added to your cart';
                }
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>sproduct</title>
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


    <!-- Page Header Start -->
    <?php include 'header.php';?>
    <!-- Page Header End -->
    <!-- Product Details Start -->
    <div id="productdetails" class="py-6">
        <?php
            $select_product_detail = mysqli_query($conn, "SELECT * FROM `products` WHERE id = '$detail_id'") or die('query failed'); 
            if (mysqli_num_rows($select_product_detail)>0) {
                $fetch_product_detail = mysqli_fetch_assoc($select_product_detail)
        ?>
        <div class="single-pro-image">
            <img src="./iimages/imagephp/<?php echo $fetch_product_detail['image1'];?>" width="100%" id="MainImg" alt="">
            <div class="small-img-group">
                <div class="small-img-col">
                    <img src="./iimages/imagephp/<?php echo $fetch_product_detail['image1'];?>" width="100%" class="small-img" alt="">
                </div>
                <div class="small-img-col">
                    <img src="./iimages/imagephp/<?php echo $fetch_product_detail['image2'];?>" width="100%" class="small-img" alt="">
                </div>
                <div class="small-img-col">
                    <img src="./iimages/imagephp/<?php echo $fetch_product_detail['image3'];?>" width="100%" class="small-img" alt="">
                </div>
                <div class="small-img-col">
                    <img src="./iimages/imagephp/<?php echo $fetch_product_detail['image4'];?>" width="100%" class="small-img" alt="">
                </div>
            </div>
        </div>
        <form method = "post" class="single-pro-details">
            <h4><?php echo $fetch_product_detail["name"]; ?></h4>
            <h6><?php echo $fetch_product_detail["motto"]; ?></h6>
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
            <h5 name= "priceinput" value= "" id = "priceinput"></h5>
            <select id="candle_weight" name= "candle_weight">
                <option hidden value = "">Select Candle Size</option>
                <?php
                $select_product_attr = mysqli_query($conn, "SELECT * FROM `product_attributes` left join `weight` on `product_attributes`.weight_id = `weight`.id 
                 WHERE `product_attributes`.product_id = '$detail_id' ORDER BY `product_attributes`.`weight_id` ASC") or die('query failed');
                if (mysqli_num_rows($select_product_attr)>0)  {
                    while($fetch_product_attr = mysqli_fetch_assoc($select_product_attr)) {
                ?>
                <option value = "$ <?php echo $fetch_product_attr['price'];?>" ><?php echo $fetch_product_attr['weight_value'];?></option>
                <?php 
                    }
                }
                ?>
            </select>
            
            <input type="number" name="product_quantity" value="1" min="1">
            <input type="hidden" name="weightinput" id="weightinput" value="1">
            <input type="hidden" name="product_id" value = "<?php echo $fetch_product_detail['id']; ?>">
            <input type="hidden" name="product_name" value = "<?php echo $fetch_product_detail['name']; ?>">
            <input type="hidden" name="product_image" value = "<?php echo $fetch_product_detail['image1']; ?>">
            <button name= "add_to_cart1" class="btn btn-primary py-2 px-3 ">Add to Cart</button>
            <h3>Product Details</h3>
            <span><?php echo $fetch_product_detail["product_detail"]; ?></span> 
            
            <div class="collapsible">
                <div class="item">
                    <div class="header">
                        Scents
                    </div>
                    <div class="content">
                        <div class="body">
                            <?php echo $fetch_product_detail["scents"]; ?>
                        </div>
                    </div>
                </div>
                <div class="item">
                    <div class="header">
                        Key Ingredients
                    </div>
                    <div class="content">
                        <div class="body">
                            Palm Coconut wax blend, Fragrance Oils, 100% Cotton Wick, Touch of Paraffin Wax (just enough to enhance the fragrance delivery). 
                            All Incandescent products are vegan, gluten and cruelty-free, and contain no phthalates or parabens.
                        </div>
                    </div>
                </div>    
            </div>
        </form>
        <?php
                        
            }
            else{
                    echo'
                        <div class="empty">
                            <p>No product found</p>
                        </div>
                    ';
            }    
        ?>
    </div>
    <!-- Product Details End -->
    
    <!-- Signature Collection -->
    <div class="container-fluid my-5 py-6">
        <div class="container">
            <div class="section-header text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 500px;">
                <h1 class="display-5 mb-3">Similar Products</h1>
            </div>
            <div class="tab-content">
                <div id="all" class="tab-pane fade show p-0 active">
                    <?php 
                        if (isset($message2)) {
                            foreach ($message2 as $message2) {
                                echo '
                                    <div class= "message">
                                        <Span>'.$message2.'</span>
                                        <i class="bi bi-x-circle" onclick="this.parentElement.remove()"></i>
                                    </div>
                                ';
                            }
                        }
                    ?>
                    <div class="row g-4 mt-3">
                        <?php
                            $select_products = mysqli_query($conn, "SELECT * FROM `products`ORDER BY RAND() LIMIT 4") or die('query failed');
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
                                            <a class="text-body view_detail" href="product_details.php?id=<?php echo $fetch_products['id']; ?>"><i class="fa fa-eye text-primary me-2"> View details</i></a>
                                        </small>
                                        <small class="w-50 text-center py-2">
                                            <button type ="submit" name= "add_to_cart2" class="text-body view_detail" href=""><i class="fa fa-shopping-bag text-primary me-2"> Add to cart</i></button>
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
                            <div class="col-12 text-center wow fadeInUp" data-wow-delay="0.1s">
                                    <a class="btn btn-primary rounded-pill py-3 px-5" href="products.php">More Products</a>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Start -->
    <div class="container-fluid bg-dark footer mt-5 pt-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container py-5">
            <div class="row g-5">
                <div class="col-lg-3 col-md-6">
                    <h1 class="fw-bold text-primary mb-4">Incandescent</h1>
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

    <script>
        var MainImg = document.getElementById("MainImg");
        var smallimg = document.getElementsByClassName("small-img");

        smallimg[0].onclick = function(){
            MainImg.src = smallimg[0].src;
        }
        smallimg[1].onclick = function(){
            MainImg.src = smallimg[1].src;
        }
        smallimg[2].onclick = function(){
            MainImg.src = smallimg[2].src;
        }
        smallimg[3].onclick = function(){
            MainImg.src = smallimg[3].src;
        }

        const headersaccordion = document.querySelectorAll(".collapsible .header");

        headersaccordion.forEach(header=> {
            header.addEventListener("click", event => {
                header.classList.toggle("active");
                const itemcontent = header.nextElementSibling;
                if(header.classList.contains("active")) {
                    itemcontent.style.maxHeight = itemcontent.scrollHeight+ "px";
                }
                else{
                    itemcontent.style.maxHeight = 0;
                }
            });  
        });
    </script>

    <script>
        let selection = document.getElementById('candle_weight')
        let result1 = document.getElementById('priceinput')
        let result2 = document.getElementById('weightinput')

        selection.addEventListener('change',() => {
            result1.innerText = selection.options[selection.selectedIndex].value;
            result2.value = selection.options[selection.selectedIndex].text;

        })
    </script>

    <!-- <script>
         $(function () {
            $('#products').change(function () {
                $('#priceinput').val($('#products option:selected').attr('data-price'));
            });
        });
    </script> -->


    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square rounded-circle back-to-top"><i class="bi bi-arrow-up"></i></a>


    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>


    <!-- Template Javascript -->
    <script src="js/main.js"></script>
</body>

</html>