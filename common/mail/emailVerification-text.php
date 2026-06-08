<?php

/** @var yii\web\View $this */
/** @var common\models\User $user */

$verifyLink = Yii::$app->urlManager->createAbsoluteUrl(['site/verify-email', 'token' => $user->verification_token]);
?>
Welcome to Hofer Bräuhaus, <?= $user->username ?>!

Please verify your email by visiting the link below:

<?= $verifyLink ?>

If you did not create an account, ignore this email.

© <?= date('Y') ?> Hofer Bräuhaus
