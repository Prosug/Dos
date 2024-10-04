<?php
session_start();
require 'db.php';  // Incluir la conexión a la base de datos

// Verificar si se ha proporcionado un CI válido
if (!isset($_GET['ci'])) {
    header('Location: admin.php');  // Redirigir si no se proporciona CI
    exit();
}

$ci = $_GET['ci'];

// Obtener los datos de la persona y su propiedad desde la base de datos
$stmt = $conn->prepare("
    SELECT p.ci, p.nombre, p.apellido, p.genero, pr.lugar, pr.dimension, pr.tipo_impuesto 
    FROM Persona p
    JOIN Persona_propiedad pp ON p.ci = pp.ci
    JOIN Propiedad pr ON pp.id_propiedad = pr.id_propiedad
    WHERE p.ci = :ci
");
$stmt->bindParam(':ci', $ci);
$stmt->execute();
$persona = $stmt->fetch(PDO::FETCH_ASSOC);

// Verificar si se encontró a la persona
if (!$persona) {
    $_SESSION['mensaje'] = "Persona no encontrada.";
    header('Location: admin.php');
    exit();
}

// Procesar la actualización cuando se envía el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ci = $_POST['ci'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $genero = $_POST['genero'];
    $lugar = $_POST['lugar'];
    $dimension = $_POST['dimension'];
    $tipo_impuesto = $_POST['tipo_impuesto']; // Captura el tipo de impuesto

    try {
        // Actualizar los datos de la persona
        $stmt = $conn->prepare("UPDATE Persona SET nombre = :nombre, apellido = :apellido, genero = :genero WHERE ci = :ci");
        $stmt->bindParam(':ci', $ci);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellido', $apellido);
        $stmt->bindParam(':genero', $genero);
        $stmt->execute();

        // Actualizar los datos de la propiedad
        $stmt = $conn->prepare("UPDATE Propiedad 
                                JOIN Persona_propiedad pp ON Propiedad.id_propiedad = pp.id_propiedad
                                SET lugar = :lugar, dimension = :dimension, tipo_impuesto = :tipo_impuesto 
                                WHERE pp.ci = :ci");
        $stmt->bindParam(':ci', $ci);
        $stmt->bindParam(':lugar', $lugar);
        $stmt->bindParam(':dimension', $dimension);
        $stmt->bindParam(':tipo_impuesto', $tipo_impuesto); // Actualiza el tipo de impuesto
        $stmt->execute();

        // Guardar mensaje de éxito en la sesión
        $_SESSION['mensaje'] = "Persona y propiedad actualizadas con éxito.";

        // Redirigir a admin.php
        header('Location: admin.php');
        exit();

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Persona y Propiedad</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h1>Editar Persona y Propiedad</h1>

    <!-- Formulario de actualización -->
    <form action="update.php?ci=<?php echo $persona['ci']; ?>" method="POST">
        <div class="mb-3">
            <label for="ci" class="form-label">Cédula de Identidad (CI)</label>
            <input type="number" class="form-control" id="ci" name="ci" value="<?php echo $persona['ci']; ?>" readonly>
        </div>
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $persona['nombre']; ?>" required>
        </div>
        <div class="mb-3">
            <label for="apellido" class="form-label">Apellido</label>
            <input type="text" class="form-control" id="apellido" name="apellido" value="<?php echo $persona['apellido']; ?>" required>
        </div>
        <div class="mb-3">
            <label for="genero" class="form-label">Género</label>
            <select class="form-select" id="genero" name="genero" required>
                <option value="M" <?php echo ($persona['genero'] === 'M') ? 'selected' : ''; ?>>Masculino</option>
                <option value="F" <?php echo ($persona['genero'] === 'F') ? 'selected' : ''; ?>>Femenino</option>
                <option value="Otro" <?php echo ($persona['genero'] === 'Otro') ? 'selected' : ''; ?>>Otro</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="lugar" class="form-label">Lugar de la Propiedad</label>
            <input type="text" class="form-control" id="lugar" name="lugar" value="<?php echo $persona['lugar']; ?>" required>
        </div>
        <div class="mb-3">
            <label for="dimension" class="form-label">Dimensión de la Propiedad (m2)</label>
            <input type="number" step="0.01" class="form-control" id="dimension" name="dimension" value="<?php echo $persona['dimension']; ?>" required>
        </div>
        <div class="mb-3">
            <label for="tipo_impuesto" class="form-label">Tipo de Impuesto</label>
            <select class="form-select" id="tipo_impuesto" name="tipo_impuesto" required>
                <option value="Alto" <?php echo ($persona['tipo_impuesto'] === 'Alto') ? 'selected' : ''; ?>>Alto</option>
                <option value="Medio" <?php echo ($persona['tipo_impuesto'] === 'Medio') ? 'selected' : ''; ?>>Medio</option>
                <option value="Bajo" <?php echo ($persona['tipo_impuesto'] === 'Bajo') ? 'selected' : ''; ?>>Bajo</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="admin.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
