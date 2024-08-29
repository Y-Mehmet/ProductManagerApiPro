<?php

require("vendor/autoload.php");
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
$sec_key='85ldofi';
$payload= array(
    'isd' => 'localhost',
    'aud' =>'localhost',
    'username' => "mehmet",
    'password' => "12345",
    "user_type"=> "consumer"

);
// $encode=JWT::encode($payload,$sec_key,'HS256');
// // $decode= JWT::decode($encode,new Key($sec_key,'HS256'));
// // echo $encode;
// // print_r($decode);
// $header=apache_request_headers();
// if($header['Authorization'])
// {
//     $header=$header['Authorization'];
//     $decode= JWT::decode($header,new Key($sec_key,'HS256'));
// }
// echo $decode->username;
// var_dump($header);
function encode($payload,$sec_key,$alg='HS256'){
    try {
        $encode=JWT::encode($payload,$sec_key,$alg);
        

        return $encode;
    } catch (Exception $error) {
      echo json_encode(array(
        "status"=>"0",
        "mesage"=>$error->getMessage()
      ));
    }

}
// echo encode($payload,$sec_key);
function decode($sec_key='85ldofi',$alg='HS256')
{
    $header=apache_request_headers();
    $header=$header['Authorization'];
   if(preg_match('/Bearer\s(\S+)/',$header,$match))
   {
    $header=$match[1];
    try {
        $decode= JWT::decode($header,new Key($sec_key,$alg));
        return $decode;
    } catch (Exception $error) {
        echo json_encode(array(
            "status"=>"0",
            "mesage"=>$error->getMessage()
          ));
    }
   }
}
function getUserType()
{
  $data=  decode();
  if( $data->user_type=='seller')
  {
    return true;
  }
  return false;
 

}
// echo encode($payload,$sec_key);
// echo "\n docede edilmiş hali \n";

// $data= decode();
// if($data->username=='mehmet')
// {
//     echo "\n user name ".$data->username." pass ".$data->password;
//     http_response_code(200);
// }