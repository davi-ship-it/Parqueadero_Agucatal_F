<?php
require_once("./clases/paqueadero.php");
$parqueadero = new Parqueadero();
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $resultado = $parqueadero->registrarIngreso(
        $_POST["placa"],
        $_POST["marca"],
        $_POST["color"],
        $_POST["nombreCliente"],
        $_POST["documentoCliente"],
        $_POST["horaIngreso"]
    );

    if(isset($resultado['error'])) {
        $mensaje = '<div class="alert alert-danger">' . $resultado['error'] . '</div>';
    } else {
        $mensaje = '<div class="alert alert-success">Vehículo registrado exitosamente en Piso ' . 
                   $resultado['piso'] . ', Posición ' . $resultado['posicion'] . '</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parqueadero "El Aguacatal" - Registro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Parqueadero "El Aguacatal"</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" href="index2.php">Registro</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="buscar.php">Buscar/Retirar</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2 class="text-center mb-4">Registro de Entrada</h2>
        <?php echo $mensaje; ?>
        <form action="" method="POST" class="p-4 border rounded shadow-sm bg-light">
            <div class="mb-3">
                <label for="placa" class="form-label">Placa:</label>
                <input type="text" class="form-control" name="placa" id="placa" required>
            </div>
            <div class="mb-3">
                <label for="marca" class="form-label">Marca:</label>
                <input type="text" class="form-control" name="marca" id="marca" required>
            </div>
            <div class="mb-3">
                <label for="color" class="form-label">Color:</label>
                <input type="text" class="form-control" name="color" id="color" required>
            </div>
            <div class="mb-3">
                <label for="nombreCliente" class="form-label">Nombre del Cliente:</label>
                <input type="text" class="form-control" name="nombreCliente" id="nombreCliente" required>
            </div>
            <div class="mb-3">
                <label for="documentoCliente" class="form-label">Documento del Cliente:</label>
                <input type="text" class="form-control" name="documentoCliente" id="documentoCliente" required>
            </div>
            <div class="mb-3">
                <label for="horaIngreso" class="form-label">Hora de Ingreso:</label>
                <input type="datetime-local" class="form-control" name="horaIngreso" id="horaIngreso" required>
            </div>
            <button type="submit" class="btn btn-primary">Registrar Entrada</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>