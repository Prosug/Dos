<?php
session_start();
require 'db.php';  // Incluir el archivo de conexión a la base de datos

// Obtener los datos del formulario
$username = $_POST['username'];
$password = $_POST['password'];

try {
    // Preparar la consulta SQL para verificar el nombre de usuario
    $stmt = $conn->prepare("SELECT * FROM administrativos WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    // Verificar si el usuario existe
    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificar la contraseña (sin cifrado)
        if ($password === $user['password']) {
            // Iniciar sesión
            $_SESSION['admin'] = $user['username'];
            header('Location: admin.php');
            exit();
        } else {
            // Contraseña incorrecta
            header('Location: sesion.php?error=1');
            exit();
        }
    } else {
        // Usuario no encontrado
        header('Location: sesion.php?error=1');
        exit();
    }
} catch (PDOException $e) {
    echo "Error en la conexión: " . $e->getMessage();
}
?>
