<?php
require_once("Conexion.php");

class Parqueadero extends Conexion {
     private $conexion;

    public function __construct() {
        parent::__construct();
        $this->conexion = $this->conect();
    }


private function encontrarEspacioLibre() {
    // Buscar el primer espacio libre en cada piso
    for($piso = 1; $piso <= 4; $piso++) {
        // Contar cuántos vehículos hay en el piso actual
        $sql = "SELECT COUNT(*) as total FROM parqueadero WHERE piso = :piso AND horaSalida IS NULL";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':piso', $piso);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Si hay espacio disponible en el piso
        if($result['total'] < 10) {
            // Obtener las posiciones ocupadas en el piso actual
            $sql = "SELECT posicion FROM parqueadero WHERE piso = :piso AND horaSalida IS NULL ORDER BY posicion";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':piso', $piso);
            $stmt->execute();
            $posiciones_ocupadas = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            // Encontrar la primera posición libre (incremental)
            for($i = 1; $i <= 10; $i++) {
                // Si la posición no está ocupada, la retornamos
                if(!in_array($i, $posiciones_ocupadas)) {
                    return ['piso' => $piso, 'posicion' => $i];
                }
            }
        }
    }
    // Si no hay espacio disponible en ningún piso
    return false;
}

    public function registrarIngreso($placa, $marca, $color, $nombreCliente, $documentoCliente, $horaIngreso) {
        try {
            // Verificar si el vehículo ya está en el parqueadero
            $vehiculoExistente = $this->buscarVehiculo($placa);
            if($vehiculoExistente && $vehiculoExistente['horaSalida'] === null) {
                return ['error' => 'El vehículo ya se encuentra en el parqueadero'];
            }

            $espacioLibre = $this->encontrarEspacioLibre();
            if(!$espacioLibre) {
                return ['error' => 'No hay espacios disponibles en el parqueadero'];
            }

            $sql = "INSERT INTO parqueadero(placa, marca, color, nombreCliente, documentoCliente, horaIngreso, piso, posicion) 
                    VALUES (:placa, :marca, :color, :nombreCliente, :documentoCliente, :horaIngreso, :piso, :posicion)";
            $insert = $this->conexion->prepare($sql);

            $arrData = [
                ':placa' => $placa,
                ':marca' => $marca,
                ':color' => $color,
                ':nombreCliente' => $nombreCliente,
                ':documentoCliente' => $documentoCliente,
                ':horaIngreso' => $horaIngreso,
                ':piso' => $espacioLibre['piso'],
                ':posicion' => $espacioLibre['posicion']
            ];

            $insert->execute($arrData);
            return [
                'success' => true,
                'mensaje' => 'Vehículo registrado exitosamente',
                'piso' => $espacioLibre['piso'],
                'posicion' => $espacioLibre['posicion']
            ];

        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }


public function registrarSalida($id) {
    try {
        $sql = "SELECT * FROM parqueadero WHERE id = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([':id' => $id]);
        $vehiculo = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$vehiculo) {
            return ['error' => 'Vehículo no encontrado'];
        }

        if ($vehiculo['horaSalida'] !== null) {
            return ['error' => 'Este vehículo ya ha sido retirado'];
        }

        date_default_timezone_set('America/Bogota'); 

        $horaSalida = date('Y-m-d H:i:s');
        $horasEstacionado = (strtotime($horaSalida) - strtotime($vehiculo['horaIngreso'])) / 3600;
        $valorPagar = ceil($horasEstacionado) * 2;

        $sql = "UPDATE parqueadero SET 
                horaSalida = :horaSalida,
                valorPagar = :valorPagar 
                WHERE id = :id";
        
        $update = $this->conexion->prepare($sql);
        $update->execute([
            ':horaSalida' => $horaSalida,
            ':valorPagar' => $valorPagar,
            ':id' => $id
        ]);

        // Eliminar el registro después de registrar la salida
        $sqlDelete = "DELETE FROM parqueadero WHERE id = :id";
        $delete = $this->conexion->prepare($sqlDelete);
        $delete->execute([':id' => $id]);

        return [
            'success' => true,
            'mensaje' => 'Salida registrada y vehículo eliminado de la base de datos',
            'valorPagar' => $valorPagar,
            'horasEstacionado' => ceil($horasEstacionado)
        ];

        

    } catch (Exception $e) {
        return ['error' => $e->getMessage()];
    }
}




    public function buscarVehiculo($placa) {
        try {
            $sql = "SELECT * FROM parqueadero WHERE placa = :placa ORDER BY id DESC LIMIT 1";
            $select = $this->conexion->prepare($sql);
            $select->execute([':placa' => $placa]);
            return $select->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function listarVehiculosActivos() {
        try {
            $sql = "SELECT * FROM parqueadero WHERE horaSalida IS NULL ORDER BY piso, posicion";
            $select = $this->conexion->prepare($sql);
            $select->execute();
            return $select->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}