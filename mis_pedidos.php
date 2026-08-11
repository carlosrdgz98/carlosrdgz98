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
// OBTENER PEDIDOS DEL USUARIO
// ==========================================

/* $sql = "
    SELECT
        id,
        subtotal,
        descuento,
        total,
        metodo_pago,  
        direccion,
        estado,
        fecha_creacion
    FROM pedidos
    WHERE usuario_id = ?
    ORDER BY id DESC
"; */


// remplazada 10-08-2026 carlos 
//cambie fecha_creacion por fecha_pedido para que se muestre la fecha correcta del pedido
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
    WHERE usuario_id = ?
    ORDER BY id DESC
";

$stmt = $conexion->prepare($sql);
$stmt->execute([$usuario_id]);

$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Mis pedidos - Tienda Manillas</title>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            padding: 30px;
            color: #222;
        }

        .contenedor {
            max-width: 1000px;
            margin: auto;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
        }

        .pedido {
            background: white;
            margin-bottom: 20px;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.10);
        }

        .pedido-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #ddd;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .numero {
            font-size: 20px;
            font-weight: bold;
        }

        .estado {
            padding: 8px 14px;
            border-radius: 20px;
            background: #fff3cd;
            color: #856404;
            font-weight: bold;
        }

        .datos {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        .dato {
            padding: 12px;
            background: #f8f8f8;
            border-radius: 8px;
        }

        .dato strong {
            display: block;
            margin-bottom: 5px;
        }

        .total {
            font-size: 22px;
            font-weight: bold;
        }

        .vacio {
            background: white;
            padding: 40px;
            text-align: center;
            border-radius: 12px;
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

        .boton:hover {
            background: #333;
        }

        @media (max-width: 700px) {

            .datos {
                grid-template-columns: 1fr;
            }

            .pedido-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

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

.boton:hover {
    background: #333;
}

    </style>

</head>

<body>


<div class="contenedor">

    <h1>📦 Mis pedidos</h1>


    <?php if (empty($pedidos)): ?>

        <div class="vacio">

            <h2>No tienes pedidos todavía.</h2>

            <p>
                Cuando realices una compra,
                aparecerá aquí.
            </p>

           <a href="./index.php" class="boton">
            Ver catálogo
            </a>

        </div>


    <?php else: ?>


        <?php foreach ($pedidos as $pedido): ?>

            <div class="pedido">


                <div class="pedido-header">

                    <div class="numero">

                        Pedido #<?= htmlspecialchars($pedido['id']) ?>

                    </div>


                    <div class="estado">

                        <?= htmlspecialchars($pedido['estado']) ?>

                    </div>

                </div>


                <div class="datos">


                    <div class="dato">

                        <strong>Total</strong>

                        <span class="total">

                            $<?= number_format(
                                $pedido['total'],
                                0,
                                ',',
                                '.'
                            ) ?>

                        </span>

                    </div>


                    <div class="dato">

                        <strong>Método de pago</strong>

                        <?= htmlspecialchars(
                            $pedido['metodo_pago']
                        ) ?>

                    </div>


                    <div class="dato">

                        <strong>Dirección de entrega</strong>

                        <?= htmlspecialchars(
                            $pedido['direccion']
                        ) ?>

                    </div>


                    <div class="dato">

                        <strong>Fecha</strong>

                        <?= htmlspecialchars(
                            $pedido['fecha_pedido']  // 
                        ) ?>

                    </div>

                    <a
                    href="./detalle_pedido.php?id=<?= (int)$pedido['id'] ?>"
                    class="boton"
                    >
    Ver detalles
</a>

                </div>


            </div>
            
            

        <?php endforeach; ?>


    <?php endif; ?>


    <div style="text-align:center;">

        <a
    href="index.php"
    class="boton"
>
    ← Seguir comprando
</a>

    </div>


</div>


</body>

</html>