<?php foreach($alertas as $key => $alerta) : ?>
    <?php foreach($alerta as $mensaje) : ?>
        <div class="alerta <?= $key; ?>">
            <?= $mensaje; ?>
        </div>
    <?php endforeach; ?>
<?php endforeach; ?>