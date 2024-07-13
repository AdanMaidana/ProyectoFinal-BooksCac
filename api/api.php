<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");


include "db.php";
include "Libro.php";


$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
  case 'GET':
    handleGet($conn);
    break;

  case 'POST':
    handlePost($conn);
    break;

  case 'PUT':
    handlePut($conn);
    break;

  case 'DELETE':
    handleDelete($conn);
    break;

  default:
    echo json_encode(['message' => 'Metodo no permitido']);
    break;
}

//Pedir los libros
function handleGet($conn)
{
  $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

  //devuelvo un libro segun el id proporcionado
  if ($id > 0) 
  {
    $stmt = $conn->prepare("SELECT * FROM libros WHERE ID = ?");
    $stmt->execute([$id]);
    $libro = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($libro) 
    {
      $libroObj = Libro::fromArray($libro);
      echo json_encode($libroObj->toArray());
    } 

    else 
    {
      http_response_code(404);
      echo json_encode(['message' => 'No se encontró ningún libro.']);
    }
  }

  //devuelvo todos los libros
  else 
  {
    $stmt = $conn->query("SELECT * FROM libros");
    $libros = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($libros) 
    {
      $libroObjs = array_map(fn ($libro) => Libro::fromArray($libro)->toArray(), $libros);
      echo json_encode(['libros' => $libroObjs]);
    } 
    
    else {
      echo json_encode(['message' => 'No se encontraron libros.']);
    }
  }
}



//Guardar libro
function handlePost($conn)
{

  if ($conn === null) 
  {
      echo json_encode(['message' => 'Error en la conexión a la base de datos']);
      return;
  }

  $data = json_decode(file_get_contents('php://input'), true);

  $requiredfieldss = ['titulo', 'autor', 'generos'];

  foreach ($requiredfieldss as $fields) {
    if (!isset($data[$fields])) {
      echo json_encode(['message' => 'Datos incompletos']);
      return;
    }
  }

  $libro = Libro::fromArray($data);

  try {

    $stmt = $conn->prepare("INSERT INTO libros (titulo, autor, generos, sinopsis) VALUES(?, ?, ?, ?)");
    $stmt->execute([
      $libro->titulo,
      $libro->autor,
      $libro->generos,
      $libro->sinopsis,
    ]);

    echo json_encode(['message' => 'Libro ingresado correctamente']);

  } 

  catch (PDOException $e) 
  {
    echo json_encode(['message' => 'Error al ingresar libro', 'error' => $e->getMessage()]);
  }

}




//Modificar libro
function handlePut($conn)
{
  $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

  if ($id > 0) {
    $data = json_decode(file_get_contents('php://input'), true);
    $libro = Libro::fromArray($data);
    $libro->id=$id;
    
    $fields=[];
    $params=[];

    if ($libro->titulo !== null) {
      $fields[]='titulo = ?';
      $params[]=$libro->titulo;
    }

    if ($libro->autor !== null) {
      $fields[]='autor = ?';
      $params[]=$libro->autor;
    }

    if ($libro->generos !== null) {
      $fields[]='generos = ?';
      $params[]=$libro->generos;
    }

    if ($libro->sinopsis !== null) {
      $fields[]='sinopsis = ?';
      $params[]=$libro->sinopsis;
    }

    if(!empty($fields)) {
      $params[]=$id;
      $stmt=$conn->prepare('UPDATE libros SET ' . implode(', ',$fields). "WHERE id = ?");
      $stmt->execute($params);
      echo json_encode(['message'=> 'Libro actualizado correctamente']);
    }

    else 
    {
      echo json_encode(['message'=> 'No hay nada para actualizar']);
    }


  } else {
    echo json_encode(['message' => 'Id no encontrado']);
  }
}




//Borrar libro
function handleDelete($conn)
{
  $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

  if ($id > 0) 
  {
    $stmt = $conn->prepare("DELETE FROM libros WHERE ID = ?");
    $stmt->execute([$id]);
    echo json_encode(['message' => 'Libro eliminado correctamente']);
  } 

  else 
  {
    echo json_encode(['message' => 'Id no encontrado']);
  }
}
