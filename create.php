<?php
session_start();
require 'db.php';  // Incluir la conexión a la base de datos

// Obtener los datos del formulario
$ci = $_POST['ci'];
$nombre = $_POST['nombre'];
$apellido = $_POST['apellido'];
$genero = $_POST['genero'];
$lugar = $_POST['lugar'];
$dimension = $_POST['dimension'];

try {
    // Insertar la persona
    $stmt = $conn->prepare("INSERT INTO Persona (ci, nombre, apellido, genero) VALUES (:ci, :nombre, :apellido, :genero)");
    $stmt->bindParam(':ci', $ci);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':apellido', $apellido);
    $stmt->bindParam(':genero', $genero);
    $stmt->execute();

    // Insertar la propiedad
    $stmt = $conn->prepare("INSERT INTO Propiedad (lugar, dimension) VALUES (:lugar, :dimension)");
    $stmt->bindParam(':lugar', $lugar);
    $stmt->bindParam(':dimension', $dimension);
    $stmt->execute();

    // Obtener el último id_propiedad insertado
    $id_propiedad = $conn->lastInsertId();

    // Relacionar persona con la propiedad
    $stmt = $conn->prepare("INSERT INTO Persona_propiedad (ci, id_propiedad) VALUES (:ci, :id_propiedad)");
    $stmt->bindParam(':ci', $ci);
    $stmt->bindParam(':id_propiedad', $id_propiedad);
    $stmt->execute();

    // Guardar mensaje de éxito en la sesión
    $_SESSION['mensaje'] = "Persona y propiedad agregadas con éxito.";

    // Redirigir a admin.php
    header('Location: admin.php');
    exit();

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
