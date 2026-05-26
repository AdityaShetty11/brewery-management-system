<?php

/** @var yii\web\View $this */
/** @var frontend\models\PasswordResetRequestForm $model */

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

$this->title = 'Forgot Password';
?>

<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h1 class="card-title h4 mb-1 text-center">Reset Password</h1>
                <p class="text-muted text-center small mb-4">
                    Enter the email address on your account and we'll send you a reset link.
                </p>

                <?php $form = ActiveForm::begin(['id' => 'request-password-reset-form']); ?>

                    <?= $form->field($model, 'email')
                        ->input('email', ['autofocus' => true, 'placeholder' => 'you@example.com'])
                        ->label('Email Address') ?>

                    <div class="d-grid mt-3">
                        <?= Html::submitButton('Send Reset Link', ['class' => 'btn btn-dark']) ?>
                    </div>

                <?php ActiveForm::end(); ?>

                <hr>
                <div class="text-center small">
                    <?= Html::a('Back to login', ['/site/login']) ?>
                </div>
            </div>
        </div>
    </div>
</div>
