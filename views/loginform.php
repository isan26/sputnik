<h1>Acceso a su cuenta</h1>
<p>Introduzca su email y contraseña</p>
<?= Html::beginForm('?action=login'); ?>
<?= Html::activeLabel($model, 'email') ?>
<br>
<?= Html::activeTextInput($model, 'email') ?>
<br>
<?= Html::activeLabel($model, 'pass') ?>
<br>
<?= Html::activePasswordInput($model, 'pass') ?>
<br>
<?= Html::submitButton('Enviar') ?>
<?= Html::a('Recuperar contraseña', get_site_url() . "/" . get_option('acorec_mail_pag') . "?action=passresset") ?>
<?php if ($model->errors): ?>
    <ul>
        <?php foreach ($model->errors as $error): ?>
            <li><?= $error ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?= Html::endForm(); ?>
