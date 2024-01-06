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
    <?php include 'admin_header.php'; ?>
    <div class="line4"></div>
    <section class="dashboard">
        <div class="box-container">
        <div class="dbbox">
        <?php 
                    $total_sales = 0;
                    $select_sales = mysqli_query($conn, "SELECT * FROM `order`")
                        or die('query failed');
                    while ($fetch_sales = mysqli_fetch_assoc($select_sales)) {
                        $total_sales += $fetch_sales['total_price'];
                    }
                ?>
                <h3>$ <?php printf("%.2f",$total_sales); ?></h3>
                <p>Total Sales</p>
            </div>
            <div class="dbbox">
                <?php 
                    $total_pendings = 0;
                    $select_pendings = mysqli_query($conn, "SELECT * FROM `order` WHERE payment_status = 'pending'")
                        or die('query failed');
                    while ($fetch_pending = mysqli_fetch_assoc($select_pendings)) {
                        $total_pendings += $fetch_pending['total_price'];
                    }
                ?>
                <h3>$ <?php printf("%.2f",$total_pendings); ?></h3>
                <p>Total Pendings</p>
            </div>
            <div class="dbbox">
                <?php 
                    $total_completes = 0;
                    $select_completes = mysqli_query($conn, "SELECT * FROM `order` WHERE payment_status = 'complete'")
                        or die('query failed');
                    while ($fetch_completes = mysqli_fetch_assoc($select_completes)) {
                        $total_completes += $fetch_completes['total_price'];
                    }
                ?> 
                <h3>$ <?php printf("%.2f",$total_completes); ?></h3>
                <p>Total Completed</p>
            </div>
            <div class="dbbox">
                <?php 
                    $select_orders = mysqli_query($conn, "SELECT * FROM `order`") or die('query failed');
                    $num_of_orders = mysqli_num_rows($select_orders);
                ?> 
                <h3><?php echo $num_of_orders; ?></h3>
                <p>Orders placed</p>
            </div>
            <div class="dbbox">
                <?php 
                    $select_admins = mysqli_query($conn, "SELECT * FROM `order` where payment_status = 'pending'") or die('query failed');
                    $num_of_admins = mysqli_num_rows($select_admins);
                ?>
                <h3><?php echo $num_of_admins; ?></h3>
                <p>Orders Pending</p>
            </div>
            <div class="dbbox">
                <?php 
                    $select_products = mysqli_query($conn, "SELECT * FROM `products`") or die('query failed');
                    $num_of_products = mysqli_num_rows($select_products);
                ?> 
                <h3><?php echo $num_of_products; ?></h3>
                <p>Products available</p>
            </div>
            <div class="dbbox">
                <?php 
                    $select_users = mysqli_query($conn, "SELECT * FROM `users` where user_type ='user'") or die('query failed');
                    $num_of_users = mysqli_num_rows($select_users);
                ?> 
                <h3><?php echo $num_of_users; ?></h3>
                <p>Registered customers</p>
            </div>
            <div class="dbbox">
                <?php 
                    $select_messages = mysqli_query($conn, "SELECT * FROM `message`") or die('query failed');
                    $num_of_messages = mysqli_num_rows($select_messages);
                ?> 
                <h3><?php echo $num_of_messages; ?></h3>
                <p>Messages</p>
            </div>
        </div>
    </section>
</body>
</html>