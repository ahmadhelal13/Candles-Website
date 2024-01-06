<?php
    include 'connection.php';
    session_start();
    $admin_id = $_SESSION['admin_id'];

    if (!isset($admin_id)) {
        header('location:register.php');
    }
    if (isset($_POST['logout'])) {
        session_destroy();
        header('location:register.php');
    }

    //adding products to database
    if (isset($_POST['add-product'])) {
        $product_name = mysqli_real_escape_string($conn, $_POST['name']);
        $product_collection = mysqli_real_escape_string($conn, $_POST['collection']);
        $product_motto = mysqli_real_escape_string($conn, $_POST['motto']);
        $product_detail = mysqli_real_escape_string($conn, $_POST['detail']);
        $product_scents = mysqli_real_escape_string($conn, $_POST['scents']);
        $product_basic_price = mysqli_real_escape_string($conn, $_POST['basic_price']);
        
        foreach( $_POST['size'] as $size) {
            $product_sizes[] = mysqli_real_escape_string($conn, $size);
        }
        

        foreach( $_POST['price'] as $price) {
            $product_prices[] = mysqli_real_escape_string($conn, $price);
        }
        
        $images = $_FILES['image']['name'];

        $images_count = count($images);
        $size_count = count($product_sizes);
        $price_count = count($product_prices);

        $select_product_name = mysqli_query($conn,"SELECT name FROM `products` WHERE name = '$product_name'") or die('query failed');
        if(mysqli_num_rows($select_product_name)>0) {
            $message[] = 'product name already exists';
        }else{
            $insert_product = mysqli_query($conn,"INSERT INTO `products`(`name`,`collection`,`motto`,`product_detail`,`scents`,`basic_price`,`image1`,`image2`,`image3`,`image4`)
                VALUES ('$product_name','$product_collection','$product_motto','$product_detail','$product_scents','$product_basic_price','$images[0]','$images[1]','$images[2]','$images[3]')") or die('query failed');
        
            for ($s=0;$s<=$size_count;$s++) {
                if ($product_prices[$s]=="") {
                    continue;
                }else{
                    $w_id=$s+1;
                    $insert_product_attr = mysqli_query($conn,"INSERT INTO `product_attributes`(`product_id`,`weight_id`,`price`)
                        VALUES ((select id FROM `products` WHERE name = '$product_name'),'$w_id','$product_prices[$s]')") or die('query failed'); 
                }
            }
            
            $result=false;
            for($i=0;$i<$images_count;$i++){
                $image_name= $_FILES['image']['name'][$i];
                $image_tmp_name = $_FILES['image']['tmp_name'][$i];
                $image_size = $_FILES['image']['size'][$i];
                $image_folder = './iimages/imagephp/'.$image_name;
                if ($image_size > 2000000){
                    $result=TRUE;
                    $message[] = 'image'.($i+1).' is too large';
                    continue;   
                }else{
                    move_uploaded_file($image_tmp_name, $image_folder);
                    }
            if ($result= false){
                $message[] = 'Product added successfully';
            }
            }
        }
    }

    //Deleting products from database
    if (isset($_GET['delete'])) {
        $delete_id = $_GET['delete'];
        $select_delete_image = mysqli_query($conn, "SELECT image1, image2, image3, image4 FROM `products` WHERE id = '$delete_id'") or die('query failed');
        $fetch_delete_image = mysqli_fetch_assoc($select_delete_image);
        
        
        foreach ($fetch_delete_image as $d_image=>$val){
            unlink('./iimages/imagephp/'.$fetch_delete_image[$d_image]);
        }

        mysqli_query($conn,"DELETE FROM `products` WHERE id = '$delete_id'") or die('query failed');
        mysqli_query($conn,"DELETE FROM `cart` WHERE pid = '$delete_id'") or die('query failed');

        header('location:admin_product.php');
    }

    
    //Editing products from database
    if (isset($_POST['update_product'])){
        $update_id = $_POST['update_id'];
        $update_product_name = mysqli_real_escape_string($conn, $_POST['update_name']);
        $update_product_collection = mysqli_real_escape_string($conn, $_POST['update_collection']);
        $update_product_motto = mysqli_real_escape_string($conn, $_POST['update_motto']);
        $update_product_detail = mysqli_real_escape_string($conn, $_POST['update_detail']);
        $update_product_scents = mysqli_real_escape_string($conn, $_POST['update_scents']);
        $update_product_basic_price = mysqli_real_escape_string($conn, $_POST['update_basic_price']);
        $update_product_basic_price= number_format($update_product_basic_price,2);
        $update_images = $_FILES['update_image']['name'];

       /*  foreach( $_POST['update_size'] as $update_size) {
            if (empty($update_size)) {
                $update_product_sizes[] = "";
            }else {
                $update_product_sizes[] = mysqli_real_escape_string($conn, $update_size);  
            }
        } */
            
        
        foreach( $_POST['update_price'] as $update_price) {
            if (empty($update_price)) {
                $update_product_prices[] = 0;
            }else {
                $update_product_prices[] = number_format(mysqli_real_escape_string($conn, $update_price),2);
                }
        }


        $update_images_count = count($update_images);
        $update_price_count = count($update_product_prices);

        $update_query = mysqli_query($conn, "UPDATE `products` SET `id`='$update_id',`name`= '$update_product_name',`collection`= '$update_product_collection',`motto`= '$update_product_motto',`product_detail`= '$update_product_detail',`scents`= '$update_product_scents',
            `basic_price`= '$update_product_basic_price',`image1`= '$update_images[0]',`image2`= '$update_images[1]',`image3`= '$update_images[2]',`image4`= '$update_images[3]' WHERE id = '$update_id'") or die('query failed');

        if($update_query) {
            $result=false;
            for($i=0;$i<$update_images_count;$i++){
                $update_image_name= $_FILES['update_image']['name'][$i];
                $update_image_tmp_name = $_FILES['update_image']['tmp_name'][$i];
                $update_image_size = $_FILES['update_image']['size'][$i];
                $image_folder = './iimages/imagephp/'.$update_image_name;
                if ($update_image_size > 2000000){
                    $result=TRUE;
                    $message[] = 'image'.($i+1).' is too large';
                }else{
                    move_uploaded_file($update_image_tmp_name, $image_folder);
                    }
            if ($result= false){
                $message[] = 'Product updated successfully';
            }
            }
        }
        $edit_product_attr = mysqli_query($conn, "SELECT * FROM `product_attributes` where product_id = '$update_id' ORDER BY `product_attributes`.`weight_id` ASC") or die('query failed');
        while ($fetch_edit_product_attr = mysqli_fetch_assoc($edit_product_attr)) {
            $previous_weights[]= $fetch_edit_product_attr['weight_id'];
            $previous_prices[]=$fetch_edit_product_attr['price'];
        }
        for ($u_s=0;$u_s<$update_price_count;$u_s++){
            $w_id=$u_s+1;
            if (in_array($w_id,$previous_weights)) {
                $p_index = array_search($w_id,$previous_weights);
                $prices_array[] = $previous_prices[$p_index];
            }else {
                $prices_array[] = "";
            }
        }
        for ($u_s=0;$u_s<$update_price_count;$u_s++){
            $w_id=$u_s+1;
            if (empty($update_product_prices[$u_s])) {
                    mysqli_query($conn,"DELETE FROM `product_attributes` WHERE id = '$update_id' AND weight_id ='$w_id'") or die('query failed');
            }elseif(($update_product_prices[$u_s]!=0) && ($prices_array[$u_s]=="")) {
                    mysqli_query($conn,"INSERT INTO `product_attributes`(`product_id`,`weight_id`,`price`)
                        VALUES ('$update_id','$w_id','$update_product_prices[$u_s]')") or die('query failed'); 
            }elseif (($update_product_prices[$u_s]!=0) && ($prices_array[$u_s]!="")) {
                    mysqli_query($conn,"UPDATE `product_attributes` SET `price`='$update_product_prices[$u_s]'
                    WHERE product_id = '$update_id' AND weight_id ='$w_id'") or die('query failed'); 
            }
        }
        header('location:admin_product.php');
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>admin panel</title>
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
    <?php include 'admin_header.php';?>
    <div class="line2"></div>
    
    <section class= "add-products">
        <div class="row g-3">
            <form id="addproducts" method="POST" action="" enctype="multipart/form-data">
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
                <div class="input-product">
                    <label>Product Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="input-product">
                    <label>Collection</label>
                    <input type="text" name="collection" class="form-control" required>
                </div>
                <div class="input-product">
                    <label>Motto</label>
                    <input type="text" name="motto" class="form-control" required>
                </div>
                <div class="input-product">
                    <label> Detail</label>
                    <textarea type="text" name="detail" class="form-control" required></textarea>
                </div>
                <div class="input-product">
                    <label>Scents</label>
                    <input type="text" name="scents" class="form-control" required>
                </div>
                <div class="input-product">
                    <label>Basic Price</label>
                    <input type="text" name="basic_price" class="form-control" required>
                </div>
                <div class="input-product">
                    <div class="candle_size">
                        <label>Candle Size</label>
                        <label class= "lcontainer">
                            <input type= "checkbox" name ="size[]" class="check-box" value= "g120">120 g<span class="checkmark"></span>
                        </label>
                        <div class="input-product g120">
                            <label>Price
                                <input type="text" name="price[]" class="form-control">
                            </label>
                        </div>
                    </div>
                    <div class="candle_size">
                        <label class= "lcontainer">
                            <input type= "checkbox" name ="size[]" class="check-box" value= "g150">150 g<span class="checkmark"></span>
                        </label>
                        <div class="input-product g150">
                            <label>Price
                                <input type="text" name="price[]" class="form-control">
                            </label>
                        </div>
                    </div>
                    <div class="candle_size">
                        <label class= "lcontainer">
                            <input type= "checkbox" name ="size[]" class="check-box" value= "g350">350 g<span class="checkmark"></span>
                        </label>
                        <div class="input-product g350">
                            <label>Price
                                <input type="text" name="price[]" class="form-control">
                            </label>
                        </div>
                    </div>
                </div>
                <div class="input-product">
                    <label> Image 1 </label>
                    <input type="file" name="image[]" class="form-control" accept = "image/jpg, image/jpeg, image/png, image/webp" required>
                </div>
                <div class="input-product">
                    <label>Image 2</label>
                    <input type="file" name="image[]" class="form-control" accept = "image/jpg, image/jpeg, image/png, image/webp" required>
                </div>
                <div class="input-product">
                    <label>Image 3</label>
                    <input type="file" name="image[]" class="form-control" accept = "image/jpg, image/jpeg, image/png, image/webp" required>
                </div>
                <div class="input-product">
                    <label>Image 4</label>
                    <input type="file" name="image[]" class="form-control" accept = "image/jpg, image/jpeg, image/png, image/webp" required>
                </div>
                <input type="submit" name="add-product" value= "add-product" class ="btn btn-primary rounded-pill px-5 ">
            </form>
        </div>
    </section>
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-0 gx-5 align-items-end">
                <div class="col-lg-6">
                    <div class="section-header text-start mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 500px;">
                        <h1 class="display-5 mb-3">Our Products</h1>
                    </div>
                </div>
                <!-- <div class="col-lg-6 text-start text-lg-end wow slideInRight" data-wow-delay="0.1s">
                    <ul class="nav nav-pills d-inline-flex justify-content-end mb-5">
                        <li class="nav-item me-2">
                            <a class="btn btn-outline-primary border-2 active" data-bs-toggle="pill" href="#all">All</a>
                        </li>
                        <li class="nav-item me-2">
                            <a class="btn btn-outline-primary border-2" data-bs-toggle="pill" href="#tab-1">Signature Collection</a>
                        </li>
                        <li class="nav-item me-2">
                            <a class="btn btn-outline-primary border-2" data-bs-toggle="pill" href="#tab-2">Spring Collection</a>
                        </li>
                        <li class="nav-item me-0">
                            <a class="btn btn-outline-primary border-2" data-bs-toggle="pill" href="#tab-3">New</a>
                        </li>
                    </ul>
                </div> -->
            </div>
            <div class="tab-content">
                <div id="all" class="tab-pane fade show p-0 active">
                    <div class="row g-4">
                        <?php
                            $select_products = mysqli_query($conn, "SELECT * FROM `products`") or die('query failed');
                           
                            if (mysqli_num_rows($select_products)>0) {
                                while($fetch_products = mysqli_fetch_assoc($select_products)) {
                        ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                            <div class="product-item">
                                <div class="position-relative bg-light overflow-hidden">
                                    <img class="img-fluid w-100" src="./iimages/imagephp/<?php echo $fetch_products['image1'];?>">
                                    <div class="bg-secondary rounded text-white position-absolute start-0 top-0 m-4 py-1 px-3">New</div>
                                </div>
                                <div class="text-center p-4">
                                    <a class="d-block h5 mb-2" href=""><?php echo $fetch_products['name'];?></a>
                                    <span class="text-primary me-1">From $<?php echo $fetch_products['basic_price'];?></span>
                                    <!-- <span class="text-body text-decoration-line-through">$29.00</span> -->
                                </div>
                                <div class="d-flex border-top">
                                    <small class="w-50 text-center border-end py-2">
                                        <a class="text-body view_detail" href="product_details.php?id=<?php echo $fetch_products['id']; ?>"><i class="fa fa-eye text-primary me-2">View details</i></a>
                                    </small>
                                    <small class="w-50 text-center py-2">
                                        <a class="text-body" href=""><i class="fa fa-shopping-bag text-primary me-2">Add to cart</i></a>
                                    </small>
                                </div>
                                <div class="d-flex border-top">
                                    <small class="w-50 text-center border-end py-2">
                                        <a href="admin_product.php?edit=<?php echo $fetch_products['id']; ?>" class="edit">Edit</a>
                                    </small>
                                    <small class="w-50 text-center py-2">
                                    <a href="admin_product.php?delete=<?php echo $fetch_products['id']; ?>" class="delete" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                                    </small>
                                </div>
                            </div>
                        </div>
                        <?php
                                }
                            }else{
                                echo'
                                    <div class="empty">
                                        <p>No Products added yet!</p>
                                    </div>
                                ';
                            }    
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="line"></div>
    <?php 
        if (isset($_GET['edit'])) {
            $edit_id = $_GET['edit'];
            $edit_product = mysqli_query($conn, "SELECT * FROM `products` WHERE id = '$edit_id'") or die('query failed');
            if (mysqli_num_rows($edit_product)>0) {
                $fetch_edit_product = mysqli_fetch_assoc($edit_product);
    ?>
    <section class="update-container">
        <div class="product-item">
            <div class="overflow-hidden">
                <img class="img-fluid w-100" src="./iimages/imagephp/<?php echo $fetch_edit_product['image1'];?>">
            </div>
            <form method = "POST" action="" enctype = "multipart/form-data">
                <div class="input-product">
                    <input type="hidden" name="update_id" class="form-control" value= "<?php echo $fetch_edit_product['id'];?>">
                </div>
                <div class="input-product">
                    <label>Product Name</label>
                    <input type="text" name="update_name" class="form-control" value= "<?php echo $fetch_edit_product['name'];?>">
                </div>
                <div class="input-product">
                    <label>Collection</label>
                    <input type="text" name="update_collection" class="form-control" value= "<?php echo $fetch_edit_product['collection'];?>">
                </div>
                <div class="input-product">
                    <label>Motto</label>
                    <input type="text" name="update_motto" class="form-control" value= "<?php echo $fetch_edit_product['motto'];?>">
                </div>
                <div class="input-product">
                    <label> Detail</label>
                    <textarea type="text" name="update_detail" class="form-control"><?php echo $fetch_edit_product['product_detail'];?></textarea>
                </div>
                <div class="input-product">
                    <label>Scents</label>
                    <input type="text" name="update_scents" class="form-control" value= "<?php echo $fetch_edit_product['scents'];?>">
                </div>
                <div class="input-product">
                    <label>Basic Price</label>
                    <input type="text" name="update_basic_price" class="form-control" value= "<?php echo $fetch_edit_product['basic_price'];?>">
                </div>

                <?php 
                    $edit_product_attr = mysqli_query($conn, "SELECT * FROM `product_attributes` where product_id = '$edit_id' ORDER BY `product_attributes`.`id` ASC") or die('query failed');
                    while($fetch_edit_product_attr = mysqli_fetch_assoc($edit_product_attr)) {
                        if ($fetch_edit_product_attr['weight_id']==1){
                            $g120 = $fetch_edit_product_attr['price'];
                        }elseif($fetch_edit_product_attr['weight_id']==2){
                            $g150 = $fetch_edit_product_attr['price'];
                        }elseif($fetch_edit_product_attr['weight_id']==3){
                            $g350 = $fetch_edit_product_attr['price'];
                        }
                    }
                ?>    
                <div class="input-product">
                    <label>Candle Size</label>
                    <label class= "lcontainer">
                        <input type= "checkbox" name ="update_size[]" class="check-box" value= "g120">120 g<span class="checkmark"></span>
                    </label>
                    <div class="input-product g120">
                        <label>Price
                            <input type="text" name="update_price[]" class="form-control" value= "<?php if (isset($g120)) {echo $g120;}else{echo "";}?>">
                        </label>
                    </div>
                    <label class= "lcontainer">
                        <input type= "checkbox" name ="update_size[]" class="check-box" value= "g150">150 g<span class="checkmark"></span>
                    </label>
                    <div class="input-product g150">
                        <label>Price
                            <input type="text" name="update_price[]" class="form-control" value= "<?php if (isset($g150)) {echo $g150;}else{echo "";}?>">
                        </label>
                    </div>
                    <label class= "lcontainer">
                        <input type= "checkbox" name ="update_size[]" class="check-box" value= "g350">350 g<span class="checkmark"></span>
                    </label>
                    <div class="input-product g350">
                        <label>Price
                            <input type="text" name="update_price[]" class="form-control" value= "<?php if (isset($g350)) {echo $g350;}else{echo "";}?>">
                        </label>
                    </div>
                </div>
                <div class="input-product">
                    <label> Image 1 </label>
                    <input type="file" name="update_image[]" class="form-control" accept = "image/jpg, image/jpeg, image/png, image/webp" value= "<?php echo $fetch_edit_product['image1'];?>">
                </div>
                <div class="input-product">
                    <label>Image 2</label>
                    <input type="file" name="update_image[]" class="form-control" accept = "image/jpg, image/jpeg, image/png, image/webp" value= "<?php echo $fetch_edit_product['image2'];?>">
                </div>
                <div class="input-product">
                    <label>Image 3</label>
                    <input type="file" name="update_image[]" class="form-control" accept = "image/jpg, image/jpeg, image/png, image/webp" value= "<?php echo $fetch_edit_product['image3'];?>">
                </div>
                <div class="input-product">
                    <label>Image 4</label>
                    <input type="file" name="update_image[]" class="form-control" accept = "image/jpg, image/jpeg, image/png, image/webp" value= "<?php echo $fetch_edit_product['image4'];?>">
                </div>
                <input type="submit" name="update_product" value= "update" class ="btn btn-primary rounded-pill px-5 ">
                <input type="submit" id="close-form" value= "cancel" class ="btn btn-primary rounded-pill px-5" onclick=this.parentElement.remove()>
            </form>

            <?php
                }
                /* echo "<script> document.querySelector('.update-container').style.display='block'</script>"; */
            } 
            ?>
        </div>
    </section>
    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script>
        
        let selection = document.getElementsByClassName('check-box')
        let result1 = document.getElementById('priceinput')
        let result2 = document.getElementById('weightinput')

        selection.addEventListener('click',() => {
            result1.innerText = selection.options[selection.selectedIndex].value;
            result2.value = selection.options[selection.selectedIndex].text;

        })
    </script>
    <script>
        let closeBtn= document.querySelector('#close-form');
        closeBtn.addEventListener("click",()=>{
            document.querySelector('.update-container').style.display="none";
        }); 
    </script>

    <script src="js/main.js"></script>
</body>
</html>