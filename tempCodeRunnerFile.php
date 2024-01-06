<?php
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
        
        foreach( $_POST['update_price'] as $update_price) {
            if ($update_price!="") {
                $update_product_prices[] = number_format(mysqli_real_escape_string($conn, $update_price),2);
            }else {
                $update_product_prices[] = "";
                }
        }

        echo ($update_product_prices);
    }
?>