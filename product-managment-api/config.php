<?php
    $host="localhost";
    $username="root";
    $password="";
    $database="new_product_manager";
    $dsn="mysql:host=$host;dbname=$database";

    // products file path
    $product_json_file_path="products.json";
    
   try {
    $pdo= new PDO($dsn,$username,$password);
   } catch (PDOException $e) {
    echo json_encode(array(
        "status"=> "0",
        "mesage"=> $e->getMessage()
    ));
    
   }
 

?>