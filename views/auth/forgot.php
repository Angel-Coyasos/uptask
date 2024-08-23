<div class="contenedor forgot">

    <?php include_once __DIR__ . '/../templates/nombre-sitio.php' ?>

    <div class="contenedor-sm">
        <p class="descripcion-pagina">Recuper Tu Acceso UpTask</p>

        <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

        <form class="formulario" method="POST" action="/forgot">
            <div class="campo">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Tu Email">
            </div>

            <input type="submit" class="boton" value="Enviar Instrucciones">
        </form>

        <div class="acciones">
            <a href="/">Ya tienes cuenta? Iniciar Sesion</a>
            <a href="/crear">¿Aun no tienes una cuenta? obtener una</a>
        </div>
    </div><!-- .contenedor-sm -->

</div>
