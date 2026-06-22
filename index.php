<?php
require_once 'conexion.php';
require_once 'funciones.php';

// Obtener mensaje si existe
$mensaje = isset($_GET['mensaje']) ? $_GET['mensaje'] : '';

// Obtener stock actual
$frutas = obtenerStock($pdo);

// Verificar integridad
$alertas = verificarIntegridad($pdo);

// Obtener últimos logs de trazabilidad
$logs = $pdo->query("SELECT * FROM trazabilidad ORDER BY id DESC LIMIT 10")->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Trazabilidad - Frutas</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>🍎 Sistema de Trazabilidad - Frutas</h1>
            <p>Usuario: <strong>admin</strong></p>
        </header>

        <!-- Mostrar mensaje -->
        <?php if ($mensaje): ?>
            <div class="mensaje <?php echo strpos($mensaje, '✅') !== false ? 'exito' : (strpos($mensaje, '⚠️') !== false ? 'alerta' : 'error'); ?>">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <!-- Alertas de integridad -->
        <?php foreach($alertas as $alerta): ?>
            <div class="mensaje alerta-integridad">
                <?php echo $alerta; ?>
            </div>
        <?php endforeach; ?>

        <!-- Tarjetas de stock -->
        <div class="grid">
            <?php foreach($frutas as $f): ?>
                <div class="card">
                    <div class="icono">
                        <?php 
                        $iconos = ['banana' => '🍌', 'manzana' => '🍎', 'pera' => '🍐'];
                        echo $iconos[$f['fruta']] ?? '🍇';
                        ?>
                    </div>
                    <h3><?php echo ucfirst($f['fruta']); ?></h3>
                    <div class="cantidad"><?php echo $f['cantidad']; ?></div>
                    <a href="procesar.php?eliminar=<?php echo $f['fruta']; ?>" class="btn-eliminar">
                        🗑️ Eliminar
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Formulario de movimientos -->
        <div class="formulario">
            <h2>📝 Registrar Movimiento</h2>
            <form action="procesar.php" method="POST">
                <div class="form-group">
                    <label for="fruta">Seleccionar Fruta:</label>
                    <select name="fruta" id="fruta" required>
                        <?php foreach($frutas as $f): ?>
                            <option value="<?php echo $f['fruta']; ?>">
                                <?php echo ucfirst($f['fruta']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="cantidad">Cantidad de Cajas:</label>
                    <input type="number" name="cantidad" id="cantidad" value="1" min="1" required>
                </div>

                <div class="botones">
                    <button type="submit" name="accion" value="agregar" class="btn btn-agregar">
                        ➕ Agregar
                    </button>
                    <button type="submit" name="accion" value="retirar" class="btn btn-retirar">
                        ➖ Retirar
                    </button>
                </div>

                <button type="submit" name="resetear" value="1" class="btn btn-reset">
                    🔄 Resetear Sistema
                </button>
            </form>
        </div>

        <!-- Tabla de trazabilidad -->
        <div class="trazabilidad">
            <h2>📋 Registro de Trazabilidad</h2>
            <div class="tabla-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Fecha y Hora</th>
                            <th>Tipo de Acción</th>
                            <th>Detalles</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($logs as $log): ?>
                            <tr>
                                <td><?php echo $log['id']; ?></td>
                                <td><?php echo $log['usuario']; ?></td>
                                <td><?php echo date('d/m/Y H:i:s', strtotime($log['fecha_hora'])); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo strtolower($log['tipo_accion']); ?>">
                                        <?php echo $log['tipo_accion']; ?>
                                    </span>
                                </td>
                                <td><?php echo $log['detalles']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <footer>
            <p>Sistema de Trazabilidad v1.0 - <?php echo date('Y'); ?></p>
            <p><small>Hash de integridad: MD5</small></p>
        </footer>
    </div>
</body>
</html>