<?php
include "config.php";
include "utils.php";
header('Access-Control-Allow-Origin: *');
//aquí agregamos solo los metodos que necesitemos
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');


$dbConn =  connect($db);

/*
  listar todos los productos o solo uno
 */
if ($_SERVER['REQUEST_METHOD'] == 'GET')
{
    if (isset($_GET['email']))
    {
      //Mostrar un post
      $sql = $dbConn->prepare("SELECT * FROM logins  where email=:email");
      $sql->bindValue(':id', $_GET['id']);
      $sql->execute();
      header("HTTP/1.1 200 OK");
      echo json_encode(  $sql->fetch(PDO::FETCH_ASSOC)  );
      exit();
	  }
    else {
      //Mostrar lista de post
      $sql = $dbConn->prepare("SELECT * FROM logins");
      $sql->execute();
      $sql->setFetchMode(PDO::FETCH_ASSOC);
      header("HTTP/1.1 200 OK");
      echo json_encode( $sql->fetchAll()  );
      exit();
	}
}

// Crear un nuevo post
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $input = $_POST;
    $sql = "INSERT INTO logins
          (email,password,estato)
          VALUES
          (:email, :password,:estato)";
    $statement = $dbConn->prepare($sql);
    bindAllValues($statement, $input);
    $statement->execute();
    $postId = $dbConn->lastInsertId();
    if($postId)
    {
      $input['email'] = $postId;
      header("HTTP/1.1 200 OK");
      echo json_encode($input);
      exit();
	 }
}

//Borrar
if ($_SERVER['REQUEST_METHOD'] == 'DELETE')
{
	$id = $_GET['id'];
  $statement = $dbConn->prepare("DELETE FROM logins where email=:email");
  $statement->bindValue(':email', $email);
  $statement->execute();
	header("HTTP/1.1 200 OK");
	exit();
}

//Actualizar
if ($_SERVER['REQUEST_METHOD'] == 'PUT')
{
    $input = $_GET;
    $postId = $input['email'];
    $fields = getParams($input);

    $sql = "
          UPDATE logins
          SET $fields
          WHERE email='$postId'
           ";

    $statement = $dbConn->prepare($sql);
    bindAllValues($statement, $input);

    $statement->execute();
    header("HTTP/1.1 200 OK");
    exit();
}


//En caso de que ninguna de las opciones anteriores se haya ejecutado
header("HTTP/1.1 400 Bad Request");
?>
