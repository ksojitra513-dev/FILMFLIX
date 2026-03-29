<?php

$con = mysqli_connect("localhost", "root", "");

try {
    mysqli_select_db($con, "product");
} catch (Exception $e) {
    echo "Error in connecting with DB: " . $e->getMessage();
}

$create_table = "CREATE TABLE Product (
   P_Id INT AUTO_INCREMENT PRIMARY KEY,
   P_Name VARCHAR(30),
   P_Description VARCHAR(150),
   P_Price INT ,
   P_Category_Id INT,
   Discount INT,
   P_Final_Price INT,
   
   
)";

if (mysqli_query($con, $create_table)) {
   echo "Table created successfully";
} else {
   echo "Error creating table: " . mysqli_error($con);
}

//$create_table = "CREATE TABLE login (
//    email VARCHAR(100) UNIQUE,
//    password VARCHAR(255)
//)";
//
//if (mysqli_query($con, $create_table)) {
//    echo "Table created successfully";
//} else {
//    echo "Error creating table: " . mysqli_error($con);
//}

//$create_table = "CREATE TABLE contact  (
//    name varchar(50),
//    email varchar(50),
//    subject varchar(100),
//    message text
//)";
//if (mysqli_query($con, $create_table)) {
//    echo "Table created successfully";
//} else {
//    echo "Error creating table:" . mysqli_error($con);
//}

?>