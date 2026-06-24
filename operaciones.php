<?php
require_once 'conexion.php';

$mensaje = "";
$tipo_mensaje = "";
$usuario_operador = "admin_frutas"; 

// Función básica para calcular el hash de integridad
function calcularHash($fruta, $cantidad, $eliminado) {
    return hash('sha256', $fruta . '-' . $cantidad . '-' . $eliminado);
}

// Procesar modificaciones de stock
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['resetear'])) {
        $pdo->query("TRUNCATE TABLE inventario");
        $pdo->query("TRUNCATE TABLE trazabilidad");
        
        $frutas_inicio = [['banana', 12], ['manzana', 8], ['pera', 5]];
        foreach($frutas_inicio as $f) {
            $h = calcularHash($f[0], $f[1], 0);
            $stmt = $pdo->prepare("INSERT INTO inventario (fruta, cantidad, eliminado, hash_control) VALUES (?, ?, 0, ?)");
            $stmt->execute([$f[0], $f[1], $h]);
        }
        
        $stmtLog = $pdo->prepare("INSERT INTO trazabilidad (usuario, fecha_hora, tipo_accion, detalles) VALUES (?, NOW(), 'INSERCION', 'Reinicio completo del inventario de fábrica')");
        $stmtLog->execute([$usuario_operador]);

        $mensaje = "🔄 Base de datos reiniciada y limpia.";
        $tipo_mensaje = "success";
    } 
    else {
        $fruta = $_POST['fruta'];
        $cantidad = (int)$_POST['cantidad'];
        $accion = $_POST['accion'];

        if ($cantidad <= 0) {
            $mensaje = "⚠️ Cantidad no válida.";
            $tipo_mensaje = "danger";
        } else {
            $stmt = $pdo->prepare("SELECT cantidad FROM inventario WHERE fruta = ? AND eliminado = 0");
            $stmt->execute([$fruta]);
            $resultado = $stmt->fetch();

            if ($resultado) {
                $stock_actual = $resultado['cantidad'];
                $nuevo_stock = $stock_actual;

                if ($accion === 'agregar') {
                    $nuevo_stock = $stock_actual + $cantidad;
                    $mensaje = "✅ Se agregaron $cantidad cajas de " . ucfirst($fruta) . "s.";
                    $tipo_mensaje = "success";
                } 
                elseif ($accion === 'retirar') {
                    if ($stock_actual >= $cantidad) {
                        $nuevo_stock = $stock_actual - $cantidad;
                        $mensaje = "📉 Se retiraron $cantidad cajas de " . ucfirst($fruta) . "s.";
                        $tipo_mensaje = "success";
                    } else {
                        $mensaje = "❌ Stock insuficiente.";
                        $tipo_mensaje = "danger";
                        $accion = "error";
                    }
                }

                if ($accion !== "error") {
                    $nuevo_hash = calcularHash($fruta, $nuevo_stock, 0);
                    $updateStmt = $pdo->prepare("UPDATE inventario SET cantidad = ?, hash_control = ? WHERE fruta = ?");
                    $updateStmt->execute([$nuevo_stock, $nuevo_hash, $fruta]);

                    $detalles_cambio = "Cambio de stock de $stock_actual a $nuevo_stock unidades.";
                    $logStmt = $pdo->prepare("INSERT INTO trazabilidad (usuario, fecha_hora, tipo_accion, detalles) VALUES (?, NOW(), 'UPDATE', ?)");
                    $logStmt->execute([$usuario_operador, $detalles_cambio]);
                }
            }
        }
    }
}

// Procesar Soft-Delete por URL
if (isset($_GET['delete'])) {
    $fruta_del = $_GET['delete'];
    $stmt = $pdo->prepare("SELECT cantidad FROM inventario WHERE fruta = ? AND eliminado = 0");
    $stmt->execute([$fruta_del]);
    $res = $stmt->fetch();
    
    if($res) {
        $nuevo_hash = calcularHash($fruta_del, $res['cantidad'], 1);
        $delStmt = $pdo->prepare("UPDATE inventario SET eliminado = 1, hash_control = ? WHERE fruta = ?");
        $delStmt->execute([$nuevo_hash, $fruta_del]);
        
        $logStmt = $pdo->prepare("INSERT INTO trazabilidad (usuario, fecha_hora, tipo_accion, detalles) VALUES (?, NOW(), 'SOFT-DELETE', ?)");
        $logStmt->execute([$usuario_operador, "Baja lógica de la fruta: $fruta_del"]);
        
        $mensaje = "🗑️ Fruta '" . ucfirst($fruta_del) . "' dada de baja mediante Soft-Delete.";
        $tipo_mensaje = "success";
    }
}
?>