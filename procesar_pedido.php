<?php

session_start();

require_once 'php/conexion.php';

// ==========================================
// 1. VERIFICAR SESIÓN
// ==========================================

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}


// ==========================================
// 2. VERIFICAR CARRITO
// ==========================================

if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
    header("Location: carrito.php");
    exit;
}


// ==========================================
// 3. RECIBIR DATOS DEL CHECKOUT
// ==========================================

$direccion = trim($_POST['direccion'] ?? '');
$metodo_pago = trim($_POST['metodo_pago'] ?? '');


// ==========================================
// 4. VALIDAR DATOS
// ==========================================

if (empty($direccion)) {
    die("La dirección de entrega es obligatoria.");
}

if (empty($metodo_pago)) {
    die("Debes seleccionar un método de pago.");
}


// ==========================================
// 5. DATOS DEL USUARIO Y CARRITO
// ==========================================

$usuario_id = $_SESSION['usuario_id'];

$carrito = $_SESSION['carrito'];

$subtotal = 0;

$productos = [];


// ==========================================
// 6. INICIAR TRANSACCIÓN
// ==========================================

try {

    $conexion->beginTransaction();


    // ======================================
    // 7. OBTENER PRODUCTOS Y VERIFICAR STOCK
    // ======================================

    foreach ($carrito as $id_producto => $item) {

        // Obtener cantidad
        if (is_array($item)) {

            $cantidad = isset($item['cantidad'])
                ? (int)$item['cantidad']
                : 0;

        } else {

            $cantidad = (int)$item;
        }


        if ($cantidad <= 0) {
            throw new Exception("Cantidad inválida para el producto.");
        }


        // Buscar producto
        $sql = "SELECT * FROM productos WHERE id = ? FOR UPDATE";

        $stmt = $conexion->prepare($sql);

        $stmt->execute([$id_producto]);

        $producto = $stmt->fetch(PDO::FETCH_ASSOC);


        // Verificar que exista
        if (!$producto) {

            throw new Exception(
                "Uno de los productos ya no existe."
            );
        }


        // Verificar stock
        if ((int)$producto['stock'] < $cantidad) {

            throw new Exception(
                "No hay suficiente stock de: "
                . $producto['nombre']
            );
        }


        // Calcular subtotal del producto
        $precio = (float)$producto['precio'];

        $subtotal_producto = $precio * $cantidad;

        $subtotal += $subtotal_producto;


        // Guardar información
        $productos[] = [

            'id' => $producto['id'],

            'nombre' => $producto['nombre'],

            'cantidad' => $cantidad,

            'precio' => $precio,

            'subtotal' => $subtotal_producto
        ];
    }


    // ======================================
    // 8. DESCUENTO
    // ======================================

    $descuento = 0;

    $total = $subtotal - $descuento;


    // ======================================
    // 9. CREAR PEDIDO
    // ======================================

    $sql = "
        INSERT INTO pedidos
        (
            usuario_id,
            subtotal,
            descuento,
            total,
            metodo_pago,
            direccion,
            estado
        )
        VALUES
        (
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?
        )
    ";

    $stmt = $conexion->prepare($sql);

    $stmt->execute([

        $usuario_id,

        $subtotal,

        $descuento,

        $total,

        $metodo_pago,

        $direccion,

        'pendiente'
    ]);


    // Obtener ID del pedido
    $pedido_id = $conexion->lastInsertId();


    // ======================================
    // 10. GUARDAR DETALLES DEL PEDIDO
    // ======================================

    $sql_detalle = "
        INSERT INTO detalle_pedido
        (
            pedido_id,
            producto_id,
            cantidad,
            precio,
            subtotal
        )
        VALUES
        (
            ?,
            ?,
            ?,
            ?,
            ?
        )
    ";

    $stmt_detalle = $conexion->prepare($sql_detalle);


    foreach ($productos as $producto) {

        $stmt_detalle->execute([

            $pedido_id,

            $producto['id'],

            $producto['cantidad'],

            $producto['precio'],

            $producto['subtotal']
        ]);
    }


    // ======================================
    // 11. DESCONTAR STOCK
    // ======================================

    $sql_stock = "
        UPDATE productos
        SET stock = stock - ?
        WHERE id = ?
    ";

    $stmt_stock = $conexion->prepare($sql_stock);


    foreach ($productos as $producto) {

        $stmt_stock->execute([

            $producto['cantidad'],

            $producto['id']
        ]);
    }


    // ======================================
    // 12. CONFIRMAR TRANSACCIÓN
    // ======================================

    $conexion->commit();


    // ======================================
    // 13. VACIAR CARRITO
    // ======================================

    $_SESSION['carrito'] = [];


    // ======================================
    // 14. GUARDAR ID DEL PEDIDO
    // ======================================

    $_SESSION['pedido_id'] = $pedido_id;


    // ======================================
    // 15. MOSTRAR CONFIRMACIÓN
    // ======================================

    ?>

    <!DOCTYPE html>
    <html lang="es">

    <head>

        <meta charset="UTF-8">

        <meta name="viewport"
              content="width=device-width, initial-scale=1.0">

        <title>Pedido realizado</title>

        <style>

            body {
                font-family: Arial, sans-serif;
                background: #f5f5f5;
                margin: 0;
                padding: 40px;
            }

            .contenedor {
                max-width: 600px;
                margin: auto;
                background: white;
                padding: 40px;
                border-radius: 15px;
                text-align: center;
                box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            }

            h1 {
                color: #222;
            }

            .pedido {
                font-size: 20px;
                font-weight: bold;
                margin: 20px 0;
            }

            .total {
                font-size: 24px;
                font-weight: bold;
                margin: 20px 0;
            }

            .boton {
                display: inline-block;
                margin-top: 20px;
                padding: 14px 25px;
                background: #111;
                color: white;
                text-decoration: none;
                border-radius: 8px;
            }

            .boton:hover {
                background: #333;
            }

        </style>

    </head>

    <body>

        <div class="contenedor">

            <h1>🎉 ¡Pedido realizado!</h1>

            <p>
                Tu pedido ha sido registrado correctamente.
            </p>

            <div class="pedido">

                Número de pedido:
                #<?= htmlspecialchars($pedido_id) ?>

            </div>

            <div class="total">

                Total:
                $<?= number_format($total, 0, ',', '.') ?>

            </div>

            <p>
                Método de pago:
                <?= htmlspecialchars($metodo_pago) ?>
            </p>

            <p>
                Tu pedido se encuentra en estado:
                <strong>Pendiente</strong>
            </p>

            <a href="./index.php" class="boton">
                Seguir comprando
            </a>

        </div>

    </body>

    </html>

    <?php


} catch (Exception $e) {


    // ======================================
    // SI OCURRE UN ERROR
    // ======================================

    if ($conexion->inTransaction()) {

        $conexion->rollBack();
    }

    ?>

    <!DOCTYPE html>
    <html lang="es">

    <head>

        <meta charset="UTF-8">

        <title>Error</title>

        <style>

            body {
                font-family: Arial, sans-serif;
                background: #f5f5f5;
                padding: 40px;
            }

            .error {
                max-width: 600px;
                margin: auto;
                background: white;
                padding: 30px;
                border-radius: 12px;
                text-align: center;
            }

            .boton {
                display: inline-block;
                margin-top: 20px;
                padding: 12px 20px;
                background: #111;
                color: white;
                text-decoration: none;
                border-radius: 8px;
            }

        </style>

    </head>

    <body>

        <div class="error">

            <h1>❌ No se pudo realizar el pedido</h1>

            <p>
                <?= htmlspecialchars($e->getMessage()) ?>
            </p>

            <a href="checkout.php" class="boton">
                Volver al checkout
            </a>

        </div>

    </body>

    </html>

    <?php

}
?>