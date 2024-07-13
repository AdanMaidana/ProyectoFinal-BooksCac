<?php 

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "books_cac";


try
{
  $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e)
{
  echo "Conexión fallida: ". $e->getMessage();
}

?>