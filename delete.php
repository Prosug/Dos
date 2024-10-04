<?php
session_start();
require 'db.php';

// Obtener el CI de la persona que se quiere eliminar
$ci = $_GET['ci'];

try {
    // Eliminar la relación entre persona y propiedad
    $stmt = $conn->prepare("DELETE FROM Persona_propiedad WHERE ci = :ci");
    $stmt->bindParam(':ci', $ci);
    $stmt->execute();

    // Eliminar la persona
    $stmt = $conn->prepare("DELETE FROM Persona WHERE ci = :ci");
    $stmt->bindParam(':ci', $ci);
    $stmt->execute();

    // Guardar mensaje de éxito
    $_SESSION['mensaje'] = "Persona y propiedad eliminadas con éxito.";

    // Redirigir a admin.php
    header('Location: admin.php');
    exit();
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
