<?php

session_start();


// ==========================================
// OBTENER CARRITO
// ==========================================

$carrito = $_SESSION['carrito'] ?? [];


// ==========================================
// CALCULAR TOTAL
// ==========================================

$total = 0;

$totalProductos = 0;


foreach ($carrito as $producto) {

    $subtotal =
        $producto['precio'] *
        $producto['cantidad'];

    $total += $subtotal;

    $totalProductos += $producto['cantidad'];

}

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Mi carrito</title>

    <style>

        body {

            margin: 0;

            font-family: Arial, sans-serif;

            background: #f5f5f5;

        }


        header {

            background: #222;

            color: white;

            padding: 20px;

        }


        header a {

            color: white;

            text-decoration: none;

        }


        .contenedor {

            max-width: 1000px;

            margin: 40px auto;

            padding: 20px;

        }


        .producto {

            background: white;

            display: flex;

            align-items: center;

            gap: 20px;

            padding: 20px;

            margin-bottom: 15px;

            border-radius: 10px;

            box-shadow:
                0 3px 10px rgba(0,0,0,0.1);

        }


        .producto img {

            width: 120px;

            height: 120px;

            object-fit: cover;

            border-radius: 8px;

        }


        .info {

            flex: 1;

        }


        .cantidad {

            width: 70px;

            padding: 8px;

        }


        .btn {

            padding: 10px 15px;

            border: none;

            border-radius: 6px;

            cursor: pointer;

            text-decoration: none;

            display: inline-block;

        }


        .actualizar {

            background: #2980b9;

            color: white;

        }


        .eliminar {

            background: #c0392b;

            color: white;

        }


        .vaciar {

            background: #555;

            color: white;

        }


        .comprar {

            background: #222;

            color: white;

            font-size: 18px;

        }


        .resumen {

            background: white;

            padding: 25px;

            border-radius: 10px;

            margin-top: 25px;

        }


        .total {

            font-size: 28px;

            font-weight: bold;

        }


        @media (max-width: 700px) {

            .producto {

                flex-direction: column;

                align-items: flex-start;

            }

        }

    </style>

</head>


<body>


<header>

    <h1>
        🛒 Mi carrito
    </h1>

    <a href="index.php">
        ⬅️ Seguir comprando
    </a>

</header>


<main class="contenedor">


<?php if (count($carrito) > 0): ?>


    <h2>

        Productos en tu carrito

    </h2>


    <?php foreach ($carrito as $producto): ?>


        <div class="producto">


            <!-- IMAGEN -->

            <?php if (!empty($producto['imagen'])): ?>

                <img
                    src="uploads/productos/<?= htmlspecialchars($producto['imagen']) ?>"
                    alt="<?= htmlspecialchars($producto['nombre']) ?>"
                >

            <?php endif; ?>


            <div class="info">


                <h3>

                    <?= htmlspecialchars(
                        $producto['nombre']
                    ) ?>

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

                    <?= $producto['cantidad'] ?>

                </p>


                <p>

                    Subtotal:

                    <strong>

                        $<?= number_format(
                            $producto['precio']
                            *
                            $producto['cantidad'],
                            0,
                            ',',
                            '.'
                        ) ?>

                    </strong>

                </p>


            </div>


            <!-- ACTUALIZAR -->

            <form
                action="php/actualizar_carrito.php"
                method="POST"
            >

                <input
                    type="hidden"
                    name="id"
                    value="<?= $producto['id'] ?>"
                >


                <input
                    class="cantidad"
                    type="number"
                    name="cantidad"
                    value="<?= $producto['cantidad'] ?>"
                    min="1"
                    max="<?= $producto['stock'] ?>"
                >


                <button
                    class="btn actualizar"
                    type="submit"
                >

                    Actualizar

                </button>

            </form>


            <!-- ELIMINAR -->

            <a
                class="btn eliminar"
                href="php/eliminar_carrito.php?id=<?= $producto['id'] ?>"
            >

                🗑️

            </a>


        </div>


    <?php endforeach; ?>


    <!-- RESUMEN -->

    <div class="resumen">


        <p>

            📦 Total de productos:

            <strong>
                <?= $totalProductos ?>
            </strong>

        </p>


        <p class="total">

            Total:

            $<?= number_format(
                $total,
                0,
                ',',
                '.'
            ) ?>

        </p>


        <a
            class="btn vaciar"
            href="php/vaciar_carrito.php"
            onclick="return confirm('¿Vaciar todo el carrito?');"
        >

            🗑️ Vaciar carrito

        </a>


        <a
            class="btn comprar"
            href="checkout.php"
        >

            💳 Continuar compra

        </a>


    </div>


<?php else: ?>


    <div class="resumen">

        <h2>
            🛒 Tu carrito está vacío
        </h2>

        <p>
            Todavía no has agregado ninguna manilla.
        </p>

        <a
            class="btn comprar"
            href="index.php"
        >

            🛍️ Ver manillas

        </a>

    </div>


<?php endif; ?>


</main>


</body>

</html>