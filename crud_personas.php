<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['admin'])) {
    header('Location: index.php');
    exit();
}

require 'db.php';  // Incluir la conexión a la base de datos

// Obtener la lista de personas con propiedades
$stmt = $conn->prepare("
    SELECT p.ci, p.nombre, p.apellido, p.genero, pr.lugar, pr.dimension, pr.tipo_impuesto 
    FROM Persona p
    JOIN Persona_propiedad pp ON p.ci = pp.ci
    JOIN Propiedad pr ON pp.id_propiedad = pr.id_propiedad");
$stmt->execute();
$personas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Mensaje de éxito si es que existe
$mensaje = '';
if (isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
    unset($_SESSION['mensaje']);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Personas y Propiedades</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h1>Gestión de Personas y Propiedades</h1>

    <!-- Mostrar mensaje de éxito -->
    <?php if ($mensaje): ?>
        <div class="alert alert-success">
            <?php echo $mensaje; ?>
        </div>
    <?php endif; ?>

    <!-- Sección para Crear Persona y Propiedad -->
    <h2>Agregar Nueva Persona y Propiedad</h2>
    <form action="create.php" method="POST">
        <div class="mb-3">
            <label for="ci" class="form-label">Cédula de Identidad (CI)</label>
            <input type="number" class="form-control" id="ci" name="ci" required>
        </div>
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
        </div>
        <div class="mb-3">
            <label for="apellido" class="form-label">Apellido</label>
            <input type="text" class="form-control" id="apellido" name="apellido" required>
        </div>
        <div class="mb-3">
            <label for="genero" class="form-label">Género</label>
            <select class="form-select" id="genero" name="genero" required>
                <option value="M">Masculino</option>
                <option value="F">Femenino</option>
                <option value="Otro">Otro</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="lugar" class="form-label">Lugar de la Propiedad</label>
            <input type="text" class="form-control" id="lugar" name="lugar" required>
        </div>
        <div class="mb-3">
            <label for="dimension" class="form-label">Dimensión de la Propiedad (m2)</label>
            <input type="number" step="0.01" class="form-control" id="dimension" name="dimension" required>
        </div>
        <div class="mb-3">
            <label for="tipo_impuesto" class="form-label">Tipo de Impuesto</label>
            <select class="form-select" id="tipo_impuesto" name="tipo_impuesto" required>
                <option value="Alto">Alto</option>
                <option value="Medio">Medio</option>
                <option value="Bajo">Bajo</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Agregar Persona y Propiedad</button>
    </form>

    <!-- Listado de Personas con Propiedades -->
    <h2 class="mt-5">Listado de Personas y Propiedades</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>CI</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Género</th>
                <th>Lugar de Propiedad</th>
                <th>Dimensión</th>
                <th>Tipo de Impuesto</th> <!-- Nueva columna -->
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($personas as $persona): ?>
                <tr>
                    <td><?php echo $persona['ci']; ?></td>
                    <td><?php echo $persona['nombre']; ?></td>
                    <td><?php echo $persona['apellido']; ?></td>
                    <td><?php echo $persona['genero']; ?></td>
                    <td><?php echo $persona['lugar']; ?></td>
                    <td><?php echo $persona['dimension']; ?> m2</td>
                    <td><?php echo $persona['tipo_impuesto']; ?></td> <!-- Muestra el tipo de impuesto -->
                    <td>
                        <a href="update.php?ci=<?php echo $persona['ci']; ?>" class="btn btn-warning btn-sm">Editar</a>
                        <a href="delete.php?ci=<?php echo $persona['ci']; ?>" class="btn btn-danger btn-sm">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <a href="logout.php" class="btn btn-danger mt-4">Cerrar Sesión</a>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
