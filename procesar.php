<?php
require_once 'conexion.php';
require_once 'funciones.php';

$mensaje = "";

// ============================================
// ACCIÓN: RESETEAR SISTEMA (INSERCIÓN)
// ============================================
if (isset($_POST['resetear'])) {
    // Limpiar tablas
    $pdo->query("TRUNCATE TABLE inventario");
    $pdo->query("TRUNCATE TABLE trazabilidad");
    
    // Cargar datos iniciales
    $frutas = [['banana', 12], ['manzana', 8], ['pera', 5]];
    foreach($frutas as $f) {
        $hash = calcularHash($f[0], $f[1]);
        $stmt = $pdo->prepare("INSERT INTO inventario (fruta, cantidad, eliminado, hash_control) VALUES (?, ?, 0, ?)");
        $stmt->execute([$f[0], $f[1], $hash]);
    }
    
    // 🔒 Trazabilidad OCULTA - Solo se guarda en BD
    registrarLog($pdo, 'INSERCIÓN', 'Datos iniciales cargados: banana(12), manzana(8), pera(5)');
    $mensaje = "✅ Sistema reiniciado correctamente";
}

// ACCIÓN: AGREGAR O RETIRAR STOCK (UPDATE)
if (isset($_POST['accion'])) {
    $fruta = $_POST['fruta'];
    $cantidad = (int)$_POST['cantidad'];
    $accion = $_POST['accion'];
    
    if ($cantidad <= 0) {
        $mensaje = "⚠️ La cantidad debe ser mayor a 0";
    } else {
        // Obtener stock actual
        $stmt = $pdo->prepare("SELECT cantidad FROM inventario WHERE fruta = ? AND eliminado = 0");
        $stmt->execute([$fruta]);
        $res = $stmt->fetch();
        
        if ($res) {
            $stock_actual = $res['cantidad'];
            $nuevo_stock = $stock_actual;
            
            if ($accion == 'agregar') {
                $nuevo_stock = $stock_actual + $cantidad;
                $mensaje = "✅ Agregadas $cantidad cajas de $fruta";
                // 🔒 Trazabilidad OCULTA
                registrarLog($pdo, 'UPDATE', "$fruta: $stock_actual → $nuevo_stock (+$cantidad)");
                
            } elseif ($accion == 'retirar') {
                if ($stock_actual >= $cantidad) {
                    $nuevo_stock = $stock_actual - $cantidad;
                    $mensaje = "✅ Retiradas $cantidad cajas de $fruta";
                    // 🔒 Trazabilidad OCULTA
                    registrarLog($pdo, 'UPDATE', "$fruta: $stock_actual → $nuevo_stock (-$cantidad)");
                } else {
                    $mensaje = "❌ Stock insuficiente. Disponible: $stock_actual";
                }
            }
            
            // Actualizar si hubo cambio
            if ($nuevo_stock != $stock_actual) {
                $hash = calcularHash($fruta, $nuevo_stock);
                $stmt = $pdo->prepare("UPDATE inventario SET cantidad = ?, hash_control = ? WHERE fruta = ?");
                $stmt->execute([$nuevo_stock, $hash, $fruta]);
            }
        }
    }
}
 if (isset($_GET['eliminar'])) {
    $fruta = $_GET['eliminar'];
    
    $stmt = $pdo->prepare("SELECT cantidad FROM inventario WHERE fruta = ? AND eliminado = 0");
    $stmt->execute([$fruta]);
    $res = $stmt->fetch();
    
    if ($res) {
        $cantidad = $res['cantidad'];
        
        // Marcar como eliminado
        $stmt = $pdo->prepare("UPDATE inventario SET eliminado = 1 WHERE fruta = ?");
        $stmt->execute([$fruta]);
        registrarLog($pdo, 'SOFT-DELETE', "$fruta (Stock: $cantidad unidades)");
        
        $mensaje = "🗑️ '$fruta' eliminada (baja lógica)";
    } else {
        $mensaje = "❌ Fruta no encontrada o ya eliminada";
    }
}

// Redirigir con mensaje
header("Location: index.php?mensaje=" . urlencode($mensaje));
exit();
?>