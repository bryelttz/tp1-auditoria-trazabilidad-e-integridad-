<?php
/**
 * Calcula el hash de integridad para un registro
 * Usa MD5 para mantenerlo simple
 */
function calcularHash($fruta, $cantidad) {
    return md5($fruta . '-' . $cantidad);
}

/**
 * Registra una acción en la tabla de trazabilidad (OCULTA para el usuario)
 * 
 * @param PDO $pdo Conexión a la base de datos
 * @param string $tipo_accion INSERCIÓN, UPDATE o SOFT-DELETE
 * @param string $detalles Descripción de la acción
 */
function registrarLog($pdo, $tipo_accion, $detalles) {
    $usuario = 'admin';  // Usuario fijo para simplificar
    $sql = "INSERT INTO trazabilidad (usuario, fecha_hora, tipo_accion, detalles) 
            VALUES (?, NOW(), ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$usuario, $tipo_accion, $detalles]);
}

/**
 * Obtiene el stock actual de todas las frutas activas
 * 
 * @param PDO $pdo Conexión a la base de datos
 * @return array Lista de frutas activas
 */
function obtenerStock($pdo) {
    return $pdo->query("SELECT * FROM inventario WHERE eliminado = 0")->fetchAll();
}

/**
 * Verifica la integridad de los datos comparando el hash
 * Solo muestra alertas si hay problemas
 * 
 * @param PDO $pdo Conexión a la base de datos
 * @return array Lista de alertas (vacío si todo está bien)
 */
function verificarIntegridad($pdo) {
    $sql = "SELECT * FROM inventario WHERE eliminado = 0";
    $stmt = $pdo->query($sql);
    $alertas = [];
    
    while($fila = $stmt->fetch()) {
        $hash_calculado = calcularHash($fila['fruta'], $fila['cantidad']);
        if ($hash_calculado !== $fila['hash_control']) {
            $alertas[] = "⚠️ ALERTA DE INTEGRIDAD: El stock de " . ucfirst($fila['fruta']) . " fue modificado manualmente";
        }
    }
    
    return $alertas;
}
?>