<?php

/** @var yii\web\View $this */
/** @var common\models\User $user */

use yii\helpers\Html;

$verifyLink = Yii::$app->urlManager->createAbsoluteUrl(['site/verify-email', 'token' => $user->verification_token]);
?>
<div style="font-family: Georgia, serif; max-width: 560px; margin: 0 auto; background: #fdf6ec; border: 1px solid #c8860a; border-radius: 8px; overflow: hidden;">
    <div style="background: #0d0700; padding: 24px; text-align: center;">
        <h1 style="color: #c8860a; margin: 0; font-size: 1.4rem; letter-spacing: 1px;">Hofer Bräuhaus</h1>
    </div>
    <div style="padding: 32px 40px;">
        <h2 style="color: #1a0e00;">Welcome, <?= Html::encode($user->username) ?>!</h2>
        <p style="color: #4a3020;">Thank you for registering. Please click the button below to verify your email address and activate your account.</p>
        <div style="text-align: center; margin: 32px 0;">
            <?= Html::a('Verify Email Address', $verifyLink, [
                'style' => 'background:#c8860a;color:#0d0700;padding:12px 28px;text-decoration:none;border-radius:4px;font-weight:bold;letter-spacing:1px;',
            ]) ?>
        </div>
        <p style="color: #7a6050; font-size: 0.85rem;">If you did not create an account, you can safely ignore this email.</p>
    </div>
    <div style="background: #1a0e00; padding: 14px; text-align: center;">
        <small style="color: #7a6050;">&copy; <?= date('Y') ?> Hofer Bräuhaus. All rights reserved.</small>
    </div>
</div>
