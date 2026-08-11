<?php

session_start();

require_once 'php/conexion.php';

// ==========================================
// VERIFICAR SESIÓN
// ==========================================

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];


// ==========================================
// OBTENER ID DEL PEDIDO
// ==========================================

$pedido_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($pedido_id <= 0) {
    die("Pedido no válido.");
}


// ==========================================
// OBTENER PEDIDO
// ==========================================

$sql = "
    SELECT
        id,
        subtotal,
        descuento,
        total,
        metodo_pago,
        direccion,
        estado,
        fecha_pedido
    FROM pedidos
    WHERE id = ?
    AND usuario_id = ?
";

$stmt = $conexion->prepare($sql);

$stmt->execute([
    $pedido_id,
    $usuario_id
]);

$pedido = $stmt->fetch(PDO::FETCH_ASSOC);


// ==========================================
// VERIFICAR QUE EL PEDIDO PERTENEZCA AL USUARIO
// ==========================================

if (!$pedido) {
    die("El pedido no existe o no tienes permiso para verlo.");
}


// ==========================================
// OBTENER PRODUCTOS DEL PEDIDO
// ==========================================

$sql = "
    SELECT
        dp.producto_id,
        dp.cantidad,
        dp.precio,
        dp.subtotal,
        p.nombre,
        p.imagen
    FROM detalle_pedido dp
    INNER JOIN productos p
        ON dp.producto_id = p.id
    WHERE dp.pedido_id = ?
";

$stmt = $conexion->prepare($sql);

$stmt->execute([
    $pedido_id
]);

$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        Detalle del pedido #<?= htmlspecialchars($pedido['id']) ?>
    </title>


    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }


        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            color: #222;
            padding: 30px;
        }


        .contenedor {
            max-width: 1000px;
            margin: auto;
        }


        h1 {
            text-align: center;
            margin-bottom: 30px;
        }


        .caja {
            background: white;
            padding: 25px;
            margin-bottom: 25px;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.10);
        }


        .informacion {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }


        .dato {
            background: #f8f8f8;
            padding: 15px;
            border-radius: 8px;
        }


        .dato strong {
            display: block;
            margin-bottom: 7px;
        }


        .producto {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 18px 0;
            border-bottom: 1px solid #ddd;
        }


        .producto:last-child {
            border-bottom: none;
        }


        .producto img {
            width: 90px;
            height: 90px;
            object-fit: cover;
            border-radius: 10px;
        }


        .producto-info {
            flex: 1;
        }


        .producto-info h3 {
            margin-bottom: 8px;
        }


        .producto-info p {
            margin: 5px 0;
            color: #555;
        }


        .subtotal-producto {
            font-weight: bold;
            font-size: 18px;
        }


        .resumen {
            max-width: 400px;
            margin-left: auto;
        }


        .fila {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
        }


        .total {
            border-top: 2px solid #222;
            margin-top: 10px;
            padding-top: 15px;
            font-size: 23px;
            font-weight: bold;
        }


        .botones {
            text-align: center;
            margin-top: 25px;
        }


        .boton {
            display: inline-block;
            padding: 12px 20px;
            margin: 5px;
            background: #111;
            color: white;
            text-decoration: none;
            border-radius: 8px;
        }


        .boton:hover {
            background: #333;
        }


        @media (max-width: 700px) {

            .informacion {
                grid-template-columns: 1fr;
            }


            .producto {
                flex-wrap: wrap;
            }


            .resumen {
                max-width: 100%;
            }

        }

    </style>

</head>


<body>


<div class="contenedor">


    <h1>
        📦 Detalle del pedido #<?= htmlspecialchars($pedido['id']) ?>
    </h1>


    <!-- =====================================
         INFORMACIÓN DEL PEDIDO
    ====================================== -->

    <div class="caja">

        <h2>Información del pedido</h2>

        <br>


        <div class="informacion">


            <div class="dato">

                <strong>Estado</strong>

                <?= htmlspecialchars($pedido['estado']) ?>

            </div>


            <div class="dato">

                <strong>Fecha del pedido</strong>

                <?= htmlspecialchars($pedido['fecha_pedido']) ?>

            </div>


            <div class="dato">

                <strong>Método de pago</strong>

                <?= htmlspecialchars($pedido['metodo_pago']) ?>

            </div>


            <div class="dato">

                <strong>Dirección de entrega</strong>

                <?= htmlspecialchars($pedido['direccion']) ?>

            </div>


        </div>

    </div>


    <!-- =====================================
         PRODUCTOS
    ====================================== -->

    <div class="caja">

        <h2>Productos comprados</h2>


        <?php if (empty($productos)): ?>

            <p>
                No se encontraron productos para este pedido.
            </p>

        <?php else: ?>


            <?php foreach ($productos as $producto): ?>

                <div class="producto">


                    <?php if (!empty($producto['imagen'])): ?>

                        <img
                            src="./img/<?= htmlspecialchars($producto['imagen']) ?>"
                            alt="<?= htmlspecialchars($producto['nombre']) ?>"
                        >

                    <?php endif; ?>


                    <div class="producto-info">

                        <h3>
                            <?= htmlspecialchars($producto['nombre']) ?>
                        </h3>


                        <p>

                            Precio:
                            $<?= number_format(
                                $producto['precio'],
                                0,
                                ',',
                                '.'
                            ) ?>

                        </p>


                        <p>

                            Cantidad:
                            <?= (int)$producto['cantidad'] ?>

                        </p>

                    </div>


                    <div class="subtotal-producto">

                        $<?= number_format(
                            $producto['subtotal'],
                            0,
                            ',',
                            '.'
                        ) ?>

                    </div>


                </div>

            <?php endforeach; ?>


        <?php endif; ?>

    </div>


    <!-- =====================================
         RESUMEN
    ====================================== -->

    <div class="caja">

        <div class="resumen">


            <div class="fila">

                <span>Subtotal</span>

                <span>

                    $<?= number_format(
                        $pedido['subtotal'],
                        0,
                        ',',
                        '.'
                    ) ?>

                </span>

            </div>


            <div class="fila">

                <span>Descuento</span>

                <span>

                    $<?= number_format(
                        $pedido['descuento'],
                        0,
                        ',',
                        '.'
                    ) ?>

                </span>

            </div>


            <div class="fila total">

                <span>Total</span>

                <span>

                    $<?= number_format(
                        $pedido['total'],
                        0,
                        ',',
                        '.'
                    ) ?>

                </span>

            </div>


        </div>

    </div>


    <!-- =====================================
         BOTONES
    ====================================== -->

    <div class="botones">

        <a
            href="./mis_pedidos.php"
            class="boton"
        >
            ← Volver a mis pedidos
        </a>


        <a
            href="./index.php"
            class="boton"
        >
            Seguir comprando
        </a>

    </div>


</div>


</body>

</html>