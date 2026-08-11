<?php

session_start();


// ==========================================
// VERIFICAR USUARIO
// ==========================================

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}


// ==========================================
// VERIFICAR CARRITO
// ==========================================

if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
    header("Location: carrito.php");
    exit;
}


// ==========================================
// OBTENER CARRITO
// ==========================================

$carrito = $_SESSION['carrito'];

$total = 0;

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Finalizar compra - Tienda Manillas</title>


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
            max-width: 1100px;
            margin: auto;
        }


        h1 {
            text-align: center;
            margin-bottom: 30px;
        }


        .contenido {
            display: grid;
            grid-template-columns: 1.3fr 1fr;
            gap: 30px;
        }


        .caja {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.10);
        }


        h2 {
            margin-bottom: 20px;
        }


        /* PRODUCTOS */

        .producto {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px 0;
            border-bottom: 1px solid #ddd;
        }


        .producto img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
        }


        .producto-info {
            flex: 1;
        }


        .producto-info h3 {
            margin-bottom: 8px;
        }


        .producto-info p {
            margin: 4px 0;
            color: #555;
        }


        .subtotal-producto {
            font-weight: bold;
            font-size: 17px;
        }


        /* TOTAL */

        .total {
            display: flex;
            justify-content: space-between;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 2px solid #222;
            font-size: 22px;
            font-weight: bold;
        }


        /* FORMULARIO */

        label {
            display: block;
            margin-top: 15px;
            margin-bottom: 8px;
            font-weight: bold;
        }


        textarea,
        select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
        }


        textarea {
            height: 110px;
            resize: vertical;
        }


        .boton {
            width: 100%;
            margin-top: 25px;
            padding: 15px;
            border: none;
            border-radius: 8px;
            background: #111;
            color: white;
            font-size: 17px;
            cursor: pointer;
        }


        .boton:hover {
            background: #333;
        }


        .volver {
            display: block;
            margin-top: 20px;
            text-align: center;
            color: #111;
            text-decoration: none;
        }


        @media (max-width: 768px) {

            .contenido {
                grid-template-columns: 1fr;
            }

        }

    </style>

</head>


<body>


<div class="contenedor">


    <h1>Finalizar compra</h1>


    <div class="contenido">


        <!-- =====================================
             RESUMEN DEL PEDIDO
        ====================================== -->

        <div class="caja">

            <h2>Resumen de tu pedido</h2>


            <?php foreach ($carrito as $item): ?>

                <?php

                $precio = (float)$item['precio'];

                $cantidad = (int)$item['cantidad'];

                $subtotal_producto = $precio * $cantidad;

                $total += $subtotal_producto;

                ?>


                <div class="producto">


                    <?php if (!empty($item['imagen'])): ?>

                        <img
                            src="img/<?= htmlspecialchars($item['imagen']) ?>"  //no me muestra la imagen del producto
                            alt="<?= htmlspecialchars($item['nombre']) ?>"
                        >

                    <?php endif; ?>


                    <div class="producto-info">

                        <h3>
                            <?= htmlspecialchars($item['nombre']) ?>
                        </h3>


                        <p>
                            Precio:
                            $<?= number_format($precio, 0, ',', '.') ?>
                        </p>


                        <p>
                            Cantidad:
                            <?= $cantidad ?>
                        </p>

                    </div>


                    <div class="subtotal-producto">

                        $<?= number_format(
                            $subtotal_producto,
                            0,
                            ',',
                            '.'
                        ) ?>

                    </div>


                </div>


            <?php endforeach; ?>


            <!-- TOTAL -->

            <div class="total">

                <span>Total</span>

                <span>
                    $<?= number_format($total, 0, ',', '.') ?>
                </span>

            </div>


        </div>


        <!-- =====================================
             DATOS DE ENTREGA
        ====================================== -->

        <div class="caja">

            <h2>Datos de entrega</h2>


            <form
                action="procesar_pedido.php"
                method="POST"
            >


                <label for="direccion">
                    Dirección de entrega
                </label>


                <textarea
                    name="direccion"
                    id="direccion"
                    placeholder="Ejemplo: Calle 45 # 20-30, apartamento 201"
                    required
                ></textarea>


                <label for="metodo_pago">
                    Método de pago
                </label>


                <select
                    name="metodo_pago"
                    id="metodo_pago"
                    required
                >

                    <option value="">
                        Selecciona un método de pago
                    </option>


                    <option value="Contra entrega">
                        Contra entrega
                    </option>


                    <option value="Transferencia">
                        Transferencia bancaria
                    </option>


                    <option value="Nequi">
                        Nequi
                    </option>


                    <option value="Daviplata">
                        Daviplata
                    </option>

                </select>


                <button
                    type="submit"
                    class="boton"
                >
                    Confirmar pedido
                </button>


            </form>


            <a
                href="./carrito.php"
                class="volver"
            >
                ← Volver al carrito
            </a>


        </div>


    </div>


</div>


</body>

</html>

