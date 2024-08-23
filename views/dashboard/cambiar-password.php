<?php include_once __DIR__ . '/header-dashboard.php' ?>

<div class="contenedor-sm">
    <?php include_once  __DIR__ . '/../templates/alertas.php'?>

    <a href="/perfil" class="enlace">Volver al Perfil</a>

    <form method="POST" class="formulario" action="/cambiar-password">

        <div class="campo">
            <label for="password">Password Actual</label>
            <input type="password" id="password" name="password_actual" placeholder="Tu Password Actual">
        </div>

        <div class="campo">
            <label for="nPassword">Nuevo Password</label>
            <input type="password" id="nPassword" name="password_nuevo" placeholder="Tu Nuevo Password">
        </div>
        <div class="campo">
            <label for="cnPassword">Comfirmar Nuevo Password</label>
            <input type="password" id="cnPassword" name="confirmar_password_nuevo" placeholder="Confirmar Tu Nuevo Password">
        </div>

        <input type="submit" value="Guardar Cambios">
    </form>
</div>

<?php include_once __DIR__ . '/footer-dashboard.php' ?>