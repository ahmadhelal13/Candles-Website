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

   

    //Deleting products from database
    if (isset($_GET['delete'])) {
        $delete_id = $_GET['delete'];

        mysqli_query($conn, "DELETE FROM `users` where id = '$delete_id'") or die('query failed');
        $message[] = 'User removed successfully'; 
        header('location:admin_users.php');
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
    
    <!-- Testimonial Start -->
    <div class="container-fluid bg-light bg-icon py-6 mb-5">
        <div class="container">
            <div class="section-header text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 500px;">
                <h1 class="display-6 mb-3">User Accounts</h1>
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
            <div class="owl-carousel testimonial-carousel wow fadeInUp" data-wow-delay="0.1s">
                <?php
                    $select_users = mysqli_query($conn, "SELECT * FROM `users`") or die('query failed');
                    if (mysqli_num_rows($select_users)>0) {
                        while ($fetch_users = mysqli_fetch_assoc($select_users)) {

                ?>
                <div class="testimonial-item position-relative bg-white p-4 mt-4">
                    <i class="fa fa-quote-left fa-3x text-primary position-absolute top-0 start-0 mt-n4 ms-5"></i>
                    <p class="mb-6">User ID : <?php echo $fetch_users['id'];?></p>
                    <p class="mb-6">Name : <?php echo $fetch_users['name'];?></h5>
                    <p class="mb-6">Email : <?php echo $fetch_users['email'];?></p>
                    <p class="mb-6">User Type : <span style="color:<?php if($fetch_users['user_type'=='admin']){echo 'red';}?>"><?php echo $fetch_users['user_type'];?></span></p>
                    <a class= "deletemessage btn btn-primary rounded-pill " href="admin_users.php?delete=<?php echo $fetch_users['id'];?>" onclick="return confirm('Are you sure you want to delete this message?');">Delete User</a>
                </div>
                <?php    
                    }
                }else{
                    echo'
                        <div class="empty">
                            <p>No messages yet!</p>
                        </div>
                    ';
                }                   
                ?>
            </div>
        </div>  
    </div>
    
    
    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square rounded-circle back-to-top"><i class="bi bi-arrow-up"></i></a>


    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    
   
    <script>
        $(".testimonial-carousel").owlCarousel({
            autoplay: true,
            smartSpeed: 1000,
            margin: 25,
            loop: True,
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