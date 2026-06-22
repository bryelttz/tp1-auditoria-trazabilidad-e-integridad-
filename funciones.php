<?php
// ============================================
// FUNCIONES DEL SISTEMA
// ============================================

/**
 * Calcula el hash de integridad para un registro
 */
function calcularHash($fruta, $cantidad) {
    return md5($fruta . '-' . $cantidad);
}

/**
 * Registra una acción en la tabla de trazabilidad
 */
function registrarLog($pdo, $tipo_accion, $detalles) {
    $usuario = 'admin'; // Usuario fijo para simplificar
    $sql = "INSERT INTO trazabilidad (usuario, fecha_hora, tipo_accion, detalles) 
            VALUES (?, NOW(), ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$usuario, $tipo_accion, $detalles]);
}

/**
 * Obtiene el stock actual de todas las frutas activas
 */
function obtenerStock($pdo) {
    return $pdo->query("SELECT * FROM inventario WHERE eliminado = 0")->fetchAll();
}

/**
 * Verifica la integridad de los datos
 */
function verificarIntegridad($pdo) {
    $sql = "SELECT * FROM inventario WHERE eliminado = 0";
    $stmt = $pdo->query($sql);
    $alertas = [];
    
    while($fila = $stmt->fetch()) {
        $hash_calculado = calcularHash($fila['fruta'], $fila['cantidad']);
        if ($hash_calculado !== $fila['hash_control']) {
            $alertas[] = "⚠️ El stock de " . ucfirst($fila['fruta']) . " fue modificado manualmente";
        }
    }
    
    return $alertas;
}
?>