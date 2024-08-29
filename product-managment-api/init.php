<?php
require_once "config.php";
  $json_data=file_get_contents($product_json_file_path);
  $products= json_decode($json_data,JSON_OBJECT_AS_ARRAY);
  //    print_r($products) ;
  $query="INSERT INTO products( name,category,price,stock,description )
  VALUES(?,?,?,?,?)";
  $stmt=$pdo->prepare($query);
 

  $stmt->bindParam(1,$name,PDO::PARAM_STR);
  $stmt->bindParam(2,$category,PDO::PARAM_STR);
  $stmt->bindParam(3,$price,PDO::PARAM_STR);
  $stmt->bindParam(4,$stock,PDO::PARAM_STR);
  $stmt->bindParam(5,$description,PDO::PARAM_STR);
  $incerted_row=0;
  foreach ($products['products'] as $k => $product) {
 
   $name= $product["name"];
   $category= $product["category"];
   $price=$product["price"];
   $stock= $product["stock"];
   $description= $product["description"];

   $stmt->execute();
   $incerted_row++;

  }
  if(count($products)== $incerted_row)
  {
   echo "succses";
  }
  else
  {
   "error";
  }
  ?>