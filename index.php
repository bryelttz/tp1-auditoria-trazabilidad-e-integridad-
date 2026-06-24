<?php
require_once 'conexion.php';
require_once 'funciones.php';

// Obtener mensaje si existe
$mensaje = isset($_GET['mensaje']) ? $_GET['mensaje'] : '';

// Obtener stock actual
$frutas = obtenerStock($pdo);

// Verificar integridad (solo alertas visibles si hay problemas)
 $alertas = verificarIntegridad($pdo);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Stock - Frutas</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>📦 Control de Stock de Frutas</h1>
            <p>Usuario: <strong>admin</strong></p>
        </header>

        <!-- Mostrar mensaje -->
        <?php if ($mensaje): ?>
            <div class="mensaje <?php echo strpos($mensaje, '✅') !== false ? 'exito' : (strpos($mensaje, '⚠️') !== false ? 'alerta' : 'error'); ?>">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <!-- Alertas de integridad (solo si hay problemas) -->
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

        <!-- Footer simple -->
        <footer>
            <p>Sistema con trazabilidad completa - <?php echo date('Y'); ?></p>
        </footer>
    </div>
</body>
</html>