<?php
include "config.php";
include "utils.php";
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header('Content-type: text/html; charset=utf-8');
header('credentials: same-origin');
//aquí agregamos solo los metodos que necesitemos
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');   




$dbConn =  connect($db);

/*
  listar todos los productos o solo uno
 */
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  if (isset($_GET['id'])) {
    //Mostrar un post
    $sql = $dbConn->prepare("SELECT * FROM articulos where id=:id");
    $sql->bindValue(':id', $_GET['id']);
    $sql->execute();
    header("HTTP/1.1 200 OK");
    echo json_encode($sql->fetch(PDO::FETCH_ASSOC));
    exit();
  } else {
    //Mostrar lista de post
    $sql = $dbConn->prepare("SELECT * FROM articulos");
    $sql->execute();
    $sql->setFetchMode(PDO::FETCH_ASSOC);
    header("HTTP/1.1 200 OK");
    echo json_encode($sql->fetchAll());
    exit();
  }
}

// Crear un nuevo post
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $input = $_POST;
  $sql = "INSERT INTO articulos
          (id,categoria,nombre,precio,url,url2,url3)
          VALUES
          (:id, :categoria, :nombre, :precio,:url,:url2,:url3)";


  $statement = $dbConn->prepare($sql);
  bindAllValues($statement, $input);
  $statement->execute();
  $postId = $dbConn->lastInsertId();
  if ($postId) {
    $input['id'] = $postId;
    header("HTTP/1.1 200 OK");
    echo json_encode($input);
    exit();
  }
}

//Borrar
if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
  $id = $_GET['id'];
  $statement = $dbConn->prepare("DELETE FROM articulos where id=:id");
  $statement->bindValue(':id', $id);
  $statement->execute();
  header("HTTP/1.1 200 OK");
  exit();
}

//Actualizar
if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
  $input = $_GET;
  $postId = $input['id'];
  $fields = getParams($input);

  $sql = "
          UPDATE articulos
          SET $fields
          WHERE id='$postId'
           ";

  $statement = $dbConn->prepare($sql);
  bindAllValues($statement, $input);

  $statement->execute();
  header("HTTP/1.1 200 OK");
  exit();
}


//En caso de que ninguna de las opciones anteriores se haya ejecutado
header("HTTP/1.1 400 Bad Request");
