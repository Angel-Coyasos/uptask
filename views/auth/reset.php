<div class="contenedor reset">

    <?php include_once __DIR__ . '/../templates/nombre-sitio.php' ?>

    <div class="contenedor-sm">
        <p class="descripcion-pagina">Coloca tu Nuevo Password</p>

        <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

        <?php if ($mostrar) : ?>

            <form class="formulario" method="POST">
                <div class="campo">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Tu Password">
                </div>
                <div class="campo">
                    <label for="password2">Comfirmar Password</label>
                    <input type="password" id="password2" name="password2" placeholder="Confirmar Tu Password">
                </div>

                <input type="submit" class="boton" value="Guardar Password">
            </form>

        <?php endif; ?>

        <div class="acciones">
            <a href="/">Ya tienes cuenta? Iniciar Sesion</a>
            <a href="/forgot">¿Olvidastes tu Password?</a>
        </div>
    </div><!-- .contenedor-sm -->

</div>
