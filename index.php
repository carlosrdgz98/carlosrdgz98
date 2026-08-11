<?php

require_once "php/conexion.php";


// Obtener productos activos

$sql = "SELECT
            productos.*,
            categorias.nombre AS categoria_nombre

        FROM productos

        LEFT JOIN categorias
        ON productos.categoria_id = categorias.id

        WHERE productos.estado = 'activo'

        ORDER BY productos.id DESC";


$stmt = $conexion->prepare($sql);

$stmt->execute();

$productos = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Brillantezza</title>

    <link
        rel="stylesheet"
        href="css/estilos.css"
    >

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


        .barra {

            max-width: 1200px;

            margin: auto;

            display: flex;

            justify-content: space-between;

            align-items: center;

            flex-wrap: wrap;

            gap: 15px;

        }


        .barra h1 {
            margin: 0;
        }


        .barra a {

            color: white;

            text-decoration: none;

            margin-left: 15px;

        }


        .principal {

            max-width: 1200px;

            margin: 40px auto;

            padding: 0 20px;

        }


        .bienvenida {

            text-align: center;

            margin-bottom: 40px;

        }


        .productos {

            display: grid;

            grid-template-columns:
                repeat(
                    auto-fit,
                    minmax(250px, 1fr)
                );

            gap: 25px;

        }


        .producto {

            background: white;

            border-radius: 12px;

            overflow: hidden;

            box-shadow:
                0 4px 15px rgba(0,0,0,0.1);

        }


        .producto img {

            width: 100%;

            height: 250px;

            object-fit: cover;

        }


        .producto-info {

            padding: 20px;

        }


        .producto h3 {

            margin-top: 0;

        }


        .categoria {

            color: #777;

            font-size: 14px;

        }


        .precio {

            font-size: 24px;

            font-weight: bold;

            margin: 15px 0;

        }


        .stock {

            margin-bottom: 15px;

        }


        .btn-carrito {

            display: block;

            width: 100%;

            padding: 12px;

            border: none;

            border-radius: 6px;

            background: #222;

            color: white;

            cursor: pointer;

            font-size: 16px;

        }


        .sin-productos {

            text-align: center;

            background: white;

            padding: 40px;

            border-radius: 10px;

        }

    </style>

</head>


<body>


<!-- =====================================
     HEADER
====================================== -->

<header>

    <div class="barra">

        <h1>
            ✨ Brillantezza
        </h1>


        <nav>

            <a href="index.php">
                Inicio
            </a>

            <a href="login.html">
                Iniciar sesión
            </a>

            <a href="registro.html">
                Registrarse
            </a>

            <a href="#">
                🛒 Carrito
            </a>

        </nav>

    </div>

</header>



<!-- =====================================
     CONTENIDO
====================================== -->

<main class="principal">


    <section class="bienvenida">

        <h2>
            ✨ Encuentra tu manilla ideal
        </h2>

        <p>
            Diseños únicos para complementar tu estilo.
        </p>

    </section>



    <!-- =====================================
         PRODUCTOS
    ====================================== -->

    <section>

        <h2>
            🛍️ Brillantezza - Catálogo Accesorios
        </h2>


        <div class="productos">


            <?php if (count($productos) > 0): ?>


                <?php foreach ($productos as $producto): ?>


                    <article class="producto">


                        <!-- IMAGEN -->

                        <?php if (!empty($producto['imagen'])): ?>

                            <img
                                src="uploads/productos/<?= htmlspecialchars($producto['imagen']) ?>"
                                alt="<?= htmlspecialchars($producto['nombre']) ?>"
                            >

                        <?php else: ?>

                            <div
                                style="
                                    height:250px;
                                    display:flex;
                                    align-items:center;
                                    justify-content:center;
                                    background:#ddd;
                                "
                            >

                                📷 Sin imagen

                            </div>

                        <?php endif; ?>


                        <div class="producto-info">


                            <!-- NOMBRE -->

                            <h3>

                                <?= htmlspecialchars(
                                    $producto['nombre']
                                ) ?>

                            </h3>


                            <!-- CATEGORÍA -->

                            <p class="categoria">

                                🏷️

                                <?= htmlspecialchars(
                                    $producto['categoria_nombre']
                                    ?? 'Sin categoría'
                                ) ?>

                            </p>


                            <!-- DESCRIPCIÓN -->

                            <p>

                                <?= htmlspecialchars(
                                    $producto['descripcion']
                                ) ?>

                            </p>


                            <!-- PRECIO -->

                            <p class="precio">

                                $<?= number_format(
                                    $producto['precio'],
                                    0,
                                    ',',
                                    '.'
                                ) ?>

                            </p>


                            <!-- STOCK -->

                            <p class="stock">

                                📦 Disponible:

                                <?= htmlspecialchars(
                                    $producto['stock']
                                ) ?>

                            </p>


                            <!-- BOTÓN -->

                            <?php if ($producto['stock'] > 0): ?>

                                <button
                                    class="btn-carrito"
                                    onclick="agregarAlCarrito(<?= $producto['id'] ?>)"
                                >

                                    🛒 Agregar al carrito

                                </button>

                            <?php else: ?>

                                <button
                                    class="btn-carrito"
                                    disabled
                                >

                                    ❌ Agotado

                                </button>

                            <?php endif; ?>


                        </div>


                    </article>


                <?php endforeach; ?>


            <?php else: ?>


                <div class="sin-productos">

                    <h3>
                        😔 No hay manillas disponibles.
                    </h3>

                    <p>
                        Pronto tendremos nuevos productos.
                    </p>

                </div>


            <?php endif; ?>


        </div>

    </section>


</main>


<script> // remplazado por el script de agregar al carrito

function agregarAlCarrito(id) {

    const datos = new FormData();

    datos.append("id", id);


    fetch("php/agregar_carrito.php", {

        method: "POST",

        body: datos

    })

    .then(response => response.text())

    .then(resultado => {

        if (resultado === "OK") {

            window.location.href = "carrito.php";

        } else {

            alert(resultado);

        }

    })

    .catch(error => {

        console.error(error);

        alert(
            "❌ Ocurrió un error al agregar el producto."
        );

    });

}

</script> //remplazado por el script de agregar al carrito


</body>

</html>