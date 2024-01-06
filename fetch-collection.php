<?php 
    include("connection.php");
    if (isset($_POST['collection'])) {
        $collection = $_POST['collection'];

        if ($collection == "") {
            $select_products = mysqli_query($conn, "SELECT * FROM `products`") or die('query failed');
        }else{
            $select_products = mysqli_query($conn, "SELECT * FROM `products` where collection = '$collection'") or die('query failed');
        }
    }
?>

<div class="row g-4">
    <?php
        if ( mysqli_num_rows($select_products)>0) {
            while($fetch_products = mysqli_fetch_assoc($select_products)) {
    ?>
    <div class="col-xl-3 col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
        <div class="product-item">
            <div class="position-relative bg-light overflow-hidden">
                <img class="img-fluid w-100" src="./iimages/imagephp/<?php echo $fetch_products['image1'];?>">
                <div class="bg-secondary rounded text-white position-absolute start-0 top-0 m-4 py-1 px-3">New</div>
            </div>
            <div class="text-center p-4">
            <a class="d-block h5 mb-1" href=""><?php echo $fetch_products['name'];?></a>
                <p class="h6 mb-2"><?php echo $fetch_products['collection'];?></p>
                <span class="text-primary me-1">From $<?php echo $fetch_products['basic_price'];?></span>
                <!-- <span class="text-body text-decoration-line-through">$29.00</span> -->
            </div>
            <div class="d-flex border-top">
                <small class="w-50 text-center border-end py-2">
                    <a class="text-body view_detail" href="product_details.php?id=<?php echo $fetch_products['id']; ?>"><i class="fa fa-eye text-primary me-2"> View details</i></a>
                </small>
                <small class="w-50 text-center py-2">
                    <a class="text-body" href=""><i class="fa fa-shopping-bag text-primary me-2"> Add to cart</i></a>
                </small>
            </div>
        </div>
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

