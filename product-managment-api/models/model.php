<?php
 $host="localhost";
 $username="root";
 $password="";
 $database="new_product_manager";
 $dsn="mysql:host=$host;dbname=$database";


 
try {
 $pdo= new PDO($dsn,$username,$password);
} catch (PDOException $e) {
 echo $e->getMessage();
}
echo "db bağlandı";
$query= "SELECT * FROM products ";
    $stmt= $pdo->prepare($query);
    $stmt->execute();
    $products=$stmt->fetchAll(PDO::FETCH_ASSOC);
    echo" <pre>";
    print_r($products);
    echo "<pre>";
 
   
class Model
{
  function getAllProducts()
  {
    
  }
}
?>