<?php

use function PHPSTORM_META\elementType;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

ini_set('display_errors',true);

require_once("config.php");

require_once("login-user.php");
$error="";

$methodType= $_SERVER["REQUEST_METHOD"];
$url= $_SERVER["REQUEST_URI"];
$baseUrl="/product-managment-api/index.php";
$parameter_r= explode($baseUrl,$url);
@$parameter= $parameter_r[1];
$parameter= substr($parameter,1);


/// değişkenler
$defaultLimit=10;
$pattern='/^products.*/';
if(preg_match($pattern,$parameter))
{
  if($methodType== "GET" )
  { 
    
       
    $query="SELECT * FROM products WHERE 1=1";
    $params= [];
  
    if (isset($_GET["id"])) {
      $id = $_GET["id"];  
      $query.= " AND id= :id";
      $params[":id"]=$id;
    }
    if(isset($_GET["category"]))
    {
      $category= $_GET["category"];
      $query.= " AND category= :category";
      $params[":category"]= $category;
    }
    if( isset($_GET["minPrice"])&& isset($_GET["maxPrice"]))
    {
      $minPrice= $_GET["minPrice"];
      if($minPrice<0)
      {
        http_response_code(400);
        echo  json_encode(array(
          "status"=>"0",
          "mesage"=>"minPrice negatif değer olamaz",

        ));
       
        $minPrice=0;
      }
      $maxPrice= $_GET["maxPrice"];
  
      $query.=" AND price BETWEEN :minPrice  AND  :maxPrice";
      $params[":minPrice"]= $minPrice;
      $params[":maxPrice"]= $maxPrice;
  
    }
      if(isset($_GET["keyword"]))
    {
      $keyword= "%".$_GET["keyword"]."%";
      $query.=" AND (name LIKE :keyword OR description LIKE :keyword)";
      $params[":keyword"]= $keyword;
      
    }
    
    if(isset($_GET["order"]))
    {
      $order= $_GET["order"];
      if($order== "DESC" || $order== "desc")
      {
        $query.="  ORDER BY stock DESC";
      }
      else
      {
        $query.="  ORDER BY stock ASC";
      }
     
     
  
    }if(isset($_GET["page"])&& isset($_GET["limit"]))
    {
      $page=$_GET["page"];
      if( $page<=0)
      {
        http_response_code(400);
       
        echo  json_encode(array(
          "status"=>"0",
          "mesage"=>"page değeri 0 ya da negatif olamaz",
          
        ));
        $page=1;
      }
      $limit=$_GET["limit"];
      $qry= " SELECT COUNT(id) AS productCount  FROM products";
      $statment=$pdo->prepare($qry);
      $statment->execute();
      $count= $statment->fetch(PDO::FETCH_ASSOC);
      $productCount=$count["productCount"];
      $startProductIndex= ($page*$limit)- $limit;
      if($startProductIndex<= $productCount)
      {
        $query.=" LIMIT $startProductIndex, $limit";
      }
    } else
    {
      $query.=" LIMIT 0, $defaultLimit";
    }
    
    
    header("Content-Type: application/json");
    $stmt=$pdo->prepare($query);
     $stmt->execute($params);
    $products= $stmt->fetchAll(\PDO::FETCH_ASSOC);
    $products= json_encode($products);
    print_r($products);
    
    
      
  }
  elseif($methodType=="POST")
  {$all_headers= getallheaders();
    $jwt= $all_headers["Authorization"];
        if(isset($jwt))
        {
          
          try {
           
            $decode_data= decode();
            $user_type= $decode_data->user_type;
            if($user_type== "seller")
            {
              $product = json_decode(file_get_contents('php://input'), true);
              $name=$product["name"];
              $category=$product["category"];
              $price= $product["price"];
              $stock=$product["stock"];
              $description= $product["description"];
              //print_r($product);
              $query="INSERT INTO products( name,category,price,stock,description ) VALUES (?,?,?,?,?)";
              
              if(isset($name) && isset($category) &&isset($price) &&isset($stock) &&isset($description)  )
              {
                $stmt=$pdo->prepare($query);
                $stmt->bindParam(1,$name,PDO::PARAM_STR);
                $stmt->bindParam(2,$category,PDO::PARAM_STR);
                $stmt->bindParam(3,$price,PDO::PARAM_STR);
                $stmt->bindParam(4,$stock,PDO::PARAM_STR);
                $stmt->bindParam(5,$description,PDO::PARAM_STR);
                $stmt->execute();
                http_response_code(200);
              
                echo  json_encode(array(
                  "status"=>"1",
                  "mesage"=>"yeni ürün eklendi",
                  
                ));
              }
              else
              {
                http_response_code(400);
                echo  json_encode(array(
                  "status"=>"0",
                  "mesage"=>"eksik deger var",
                  
                ));
              }
              
            }
            else
            {
              http_response_code(401);
              echo json_encode(array(
                "status"=> "0",
                "mesage"=> "yetkisiz kullanici user_type ".$user_type,

              ));
            }
           
            
          
           
           
          } catch (Exception $e) {
            http_response_code(500);
          echo $e->getMessage();
          }
   
        }
  
  }
  elseif($methodType=="PUT")
  {$all_headers= getallheaders();
    $jwt= $all_headers["Authorization"];
        if(isset($jwt))
        {
          
          try {
           
            $decode_data= decode();
            $user_type= $decode_data->user_type;
            if($user_type== "seller")
            {
              if(isset($_GET["id"]))
    {
      
      $product=json_decode(file_get_contents('php://input'),true);
  
      $name=$product["name"];
      $category=$product["category"];
      $price= $product["price"];
      $stock=$product["stock"];
      $description= $product["description"];
      $id= $_GET["id"];
      $qry= " SELECT COUNT(id) AS productCount  FROM products WHERE id= :id";
      $param=[];
      $param[":id"]=$id;
      $statment=$pdo->prepare($qry);
      $statment->execute($param);
      $count= $statment->fetch(PDO::FETCH_ASSOC);
      $productCount=$count["productCount"];
  
     if($productCount==1)
     {
      $query= "UPDATE products SET  name=?,category=?,price=?,stock=?,description=? WHERE id=? " ;
      $stmt=$pdo->prepare($query);
      $stmt->bindParam(1,$name,PDO::PARAM_STR);
      $stmt->bindParam(2,$category,PDO::PARAM_STR);
      $stmt->bindParam(3,$price,PDO::PARAM_STR);
      $stmt->bindParam(4,$stock,PDO::PARAM_STR);
      $stmt->bindParam(5,$description,PDO::PARAM_STR);
      $stmt->bindParam(6,$id,PDO::PARAM_INT);
     
      $stmt->execute();
    $products= $stmt->fetchAll(\PDO::FETCH_ASSOC);
    $products= json_encode($products);
    if(isset($products))
    {
      http_response_code(200);
     
      echo  json_encode(array(
        "status"=>"1",
        "mesage"=>"basarila guncellendi",
        
      ));
    }

     }
     else
    {
      http_response_code(404);
      echo  json_encode(array(
        "status"=>"0",
        "mesage"=>"id bulunamadi",
        
      ));
    }
    }
    else
    {
      http_response_code(400);
      echo  json_encode(array(
        "status"=>"0",
        "mesage"=>"lutfen bir id girin",
        
      ));
    }
            }
            else
            {
              http_response_code(401);
              echo json_encode(array(
                "status"=> "0",
                "mesage"=> "yetkisiz kullanici user_type ".$user_type,

              ));
            }
           
            
            
           
           
          } catch (Exception $e) {
            http_response_code(500);
          
          echo  json_encode(array(
            "status"=>"0",
            "mesage"=> $e->getMessage()
            
          ));
          }
    
  }
    
  }
  elseif($methodType=="DELETE")
  {
    $all_headers= getallheaders();
    $jwt= $all_headers["Authorization"];
        if(isset($jwt))
        {
          
          try {
           
            $decode_data= decode();
            $user_type= $decode_data->user_type;
            if($user_type== "seller")
            {
              if(isset($_GET["id"]))
    {
      $id= $_GET["id"];
      $qry= " SELECT COUNT(id) AS productCount  FROM products WHERE id= :id";
      $param=[];
      $param[":id"]=$id;
      $statment=$pdo->prepare($qry);
      $statment->execute($param);
      $count= $statment->fetch(PDO::FETCH_ASSOC);
      $productCount=$count["productCount"];

     if($productCount==1)
     {
      $query=" DELETE FROM products WHERE id=?";
      $stmt=$pdo->prepare($query);
      $stmt->bindParam(1,$id,PDO::PARAM_INT);
      $stmt->execute();
      http_response_code(200);
     
      echo  json_encode(array(
        "status"=>"0",
        "mesage"=>$id." idili ürün silindi"
        
      ));
     }
     else
     {
      http_response_code(400);
      echo  json_encode(array(
        "status"=>"0",
        "mesage"=>" lütfen fakli bir id giriniz id ile eslesen product count: ".$productCount
        
      ));
   
     }
     
    }
            }
            else
            {
              http_response_code(401);
              echo json_encode(array(
                "status"=> "0",
                "mesage"=> "yetkisiz kullanici user_type ".$user_type,

              ));
            }
           
            
           
           
           
          } catch (Exception $e) {
            http_response_code(500);
          
          echo json_encode(array(
            "status"=> "0",
            "mesage"=> $e->getMessage()

          ));
          }
    
  }
}
}
elseif($methodType=="POST" && $parameter=="login")
{
  
 
  
  $user = json_decode(file_get_contents('php://input'), true);
  $user_id=$user["user_id"];
  $user_email=$user["user_email"];
  $user_password=$user["user_password"];
  $user_name=$user["user_name"];
  $user_type= $user["user_type"];
  $qry=" SELECT COUNT(user_id) AS userCount FROM users WHERE  user_email= :user_email AND user_password= :user_password";
  $param= [];
  if(isset($user_name) && isset($user_id) &&isset($user_email) &&isset($user_password) )
    {
      $param[":user_email"]=$user_email;
      $param[":user_password"]=$user_password;
     
    $stmt=$pdo->prepare($qry);
    $stmt->execute($param);
    $data= $stmt->fetch(PDO::FETCH_ASSOC);
    $count= $data["userCount"];
    
      if($count==1)
      {
        if(!isset($user_type))
        {
          $user_type="consumer";
        }
        $sec_key='85ldofi';
      $payload= array(
         'isd' => 'localhost',
         'aud' =>'localhost',
         'username' => $user_email,
         'password' => $user_password,
         "user_type"=> $user_type,
         

        );
      $jwt= encode($payload,$sec_key);
     
     
      
        echo json_encode(array(
          "token"=>"$jwt",
          "status"=> 1,
          "message"=>" giris basarili"
        ));
       
        
      }
      elseif($count==0)
      {
        http_response_code(404);
        echo json_encode(array(
          
          "status"=> 0,
          "message"=>" kullanıcı bulunamadi"
        ));
      }
      else 
      {
        http_response_code(400);
        echo json_encode(array(
          
          "status"=> 0,
          "message"=>"duplicate olabilir count: ".$count
        ));
      
      }
    
    
    }
      else
    {
      http_response_code(400);
      echo json_encode(array(
        "status"=> 0,
        "message"=>" eksik değer var ",

      ));
      
    }

}
elseif($methodType=="POST" && $parameter=="read_data")
{
  $all_headers= getallheaders();
  $jwt= $all_headers["Authorization"];
      if(isset($jwt))
      {
        
        try {
         
          $decode_data= decode();
          $user_email= $decode_data->username;
          $user_password= $decode_data->password;

          if(isset($user_email))
          {
            header("Content-Type: application/json");
            echo json_encode(array(
              "user_email"=>"$user_email",
              "user_password"=> "$user_password",
              "jwt"=>"$jwt",


                        ));
          }
          else
          {
            http_response_code(400);
            echo json_encode(array(
              "status"=> 0,
              "message"=>"username değeri bos ",
      
            ));
          }
         
          
          http_response_code(200);
         
         
        } catch (Exception $e) {
          http_response_code(500);
        echo $e->getMessage();
        }
      }
      else
      {
        echo json_encode(array(
          "status"=> 0,
          "message"=>"jwt değeri bos ",
  
        ));
      }
}
elseif($methodType=="POST" && $parameter=="register")
{
  
  $user = json_decode(file_get_contents('php://input'), true);
  $user_id=$user["user_id"];
  $user_email=$user["user_email"];
  $user_password=$user["user_password"];
  $user_name=$user["user_name"];
  try {
    $user_type=$user["user_type"];
  } catch (Exception $e) {
    
    echo json_encode(array(
      "status"=> 0,
      "message"=>$e->getMessage()

    ));
  }
  //
  $qry=" SELECT COUNT(user_id) AS userCount FROM users WHERE  user_email= :user_email OR user_id=:user_id";
  $paramtr= [];
  if( isset($user_email) && isset($user_id)  )
    {
      
      $paramtr[":user_email"]=$user_email;
      $paramtr[":user_id"]=$user_id;
     
     // header("Content-Type: application/json");
    $stmt=$pdo->prepare($qry);
    $stmt->execute($paramtr);
    $data= $stmt->fetch(PDO::FETCH_ASSOC);
    $count= $data["userCount"];
    
      if($count<1)
      {
     $query="INSERT INTO users( user_id,user_email,user_password,user_name,user_type ) VALUES (?,?,?,?,?)";
    $stmt=$pdo->prepare($query);
    if(isset($user_name) && isset($user_id) &&isset($user_email) &&isset($user_password) )
    {
      if(!isset($user_type))
      {
        $user_type="consumer";
      }
      $stmt->bindParam(1,$user_id,PDO::PARAM_STR);
      $stmt->bindParam(2,$user_email,PDO::PARAM_STR);
      $stmt->bindParam(3,$user_password,PDO::PARAM_STR);
      $stmt->bindParam(4,$user_name,PDO::PARAM_STR);
      $stmt->bindParam(5,$user_type,PDO::PARAM_STR);

      $stmt->execute();
      http_response_code(200);
      echo json_encode(array(
        "status"=> 1,
        "message"=>"kayit basarili"
  
      ));
     
    }
    else
    {
      http_response_code(400);
      echo json_encode(array(
        "status"=> 0,
        "message"=>"eksik deger var"
  
      ));
    }

      }
      else
      { http_response_code(400);
        echo json_encode(array(
          "status"=> 0,
          "message"=>"id veya username zaten  mevcut "
    
        ));
      }

  }
}
else
  {
    http_response_code(404);
    echo json_encode(array(
      "status"=> 0,
      "message"=>"yanlis url ".$parameter

    ));
    
  }


?>