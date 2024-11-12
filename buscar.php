<?php
require_once("./clases/paqueadero.php");
$parqueadero = new Parqueadero();
$mensaje = '';
$vehiculo = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['buscar'])) {
        $vehiculo = $parqueadero->buscarVehiculo($_POST['placa']);
        if(!$vehiculo) {
            $mensaje = '<div class="alert alert-warning">Vehículo no encontrado</div>';
        }
    } elseif(isset($_POST['retirar']) && isset($_POST['id'])) {
        $resultado = $parqueadero->registrarSalida($_POST['id']);
        if(isset($resultado['error'])) {
            $mensaje = '<div class="alert alert-danger">' . $resultado['error'] . '</div>';
        } else {
            $mensaje = '<div class="alert alert-success">Vehículo retirado. Valor a pagar: $' . 
                       $resultado['valorPagar'] . ' USD ('. $resultado['horasEstacionado'] . ' horas)</div>';
        }
    }
}

$vehiculosActivos = $parqueadero->listarVehiculosActivos();


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parqueadero "El Aguacatal" - Búsqueda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Parqueadero "El Aguacatal"</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Registro</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="buscar.php">Buscar/Retirar</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    
    <div class="container mt-5">
        <h2 class="text-center mb-4">Buscar/Retirar Vehículo</h2>
        <?php echo $mensaje; ?>

        <!-- Formulario de búsqueda -->
        <form action="" method="POST" class="mb-4">
            <div class="input-group">
                <input type="text" class="form-control" name="placa" placeholder="Ingrese la placa" required>
                <button type="submit" name="buscar" class="btn btn-primary">Buscar</button>
            </div>
        </form>

        <!-- Resultado de búsqueda -->
        <?php if($vehiculo && $vehiculo['horaSalida'] === null): ?>
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Vehículo Encontrado</h5>
                <p>Placa: <?php echo $vehiculo['placa']; ?></p>
                <p>Marca: <?php echo $vehiculo['marca']; ?></p>
                <p>Color: <?php echo $vehiculo['color']; ?></p>
                <p>Cliente: <?php echo $vehiculo['nombreCliente']; ?></p>
                <p>Ubicación: Piso <?php echo $vehiculo['piso']; ?>, Posición <?php echo $vehiculo['posicion']; ?></p>
                <p>Ingreso: <?php echo $vehiculo['horaIngreso']; ?></p>
                <form action="" method="POST">
                    <input type="hidden" name="id" value="<?php echo $vehiculo['id']; ?>">
                    <button type="submit" name="retirar" class="btn btn-danger">Retirar Vehículo</button>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <!-- Lista de vehículos activos -->
        <h3 class="mb-3">Vehículos Actualmente en el Parqueadero</h3>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Piso</th>
                        <th>Posición</th>
                        <th>Placa</th>
                        <th>Marca</th>
                        <th>Color</th>
                        <th>Cliente</th>
                        <th>Ingreso</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($vehiculosActivos) > 0): ?>
                        <?php foreach ($vehiculosActivos as $vehiculoActivo): ?>
                            <tr>
                                <td><?php echo $vehiculoActivo['piso']; ?></td>
                                <td><?php echo $vehiculoActivo['posicion']; ?></td>
                                <td><?php echo $vehiculoActivo['placa']; ?></td>
                                <td><?php echo $vehiculoActivo['marca']; ?></td>
                                <td><?php echo $vehiculoActivo['color']; ?></td>
                                <td><?php echo $vehiculoActivo['nombreCliente']; ?></td>
                                <td><?php echo $vehiculoActivo['horaIngreso']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">No hay vehículos en el parqueadero</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
