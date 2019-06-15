<h1>Recuperar contraseña</h1>
<p>Introduzca su email</p>
<?= Html::beginForm('?action=passresset'); ?>
<?= Html::activeLabel($model, 'email') ?>
<br>
<?= Html::activeTextInput($model, 'email') ?>

<br>
<?= Html::submitButton('Enviar') ?>
<?php if ($model->errors): ?>
    <ul>
        <?php foreach ($model->errors as $error): ?>
            <li><?= $error ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?= Html::endForm(); ?>
