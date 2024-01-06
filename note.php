


<!-- SQL QUERIES -->

    <!-- Min price for each candle -->  
    (SELECT `products`.id, `products`.name, weight_value, price FROM `products` 
    JOIN (SELECT product_id, weight_id, price FROM `product_attributes` GROUP BY product_id, weight_id) as pa ON `products`.id = pa.product_id 
    JOIN `weight` on pa.weight_id = `weight`.id WHERE `products`.id = 1 order by weight_value LIMIT 1 )
    <!-- Min price for each candle --> 