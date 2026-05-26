<?php

/** @var yii\web\View $this */
/** @var frontend\models\ResetPasswordForm $model */

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

$this->title = 'Set New Password';
?>

<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h1 class="card-title h4 mb-4 text-center">Set New Password</h1>

                <?php $form = ActiveForm::begin(['id' => 'reset-password-form']); ?>

                    <?= $form->field($model, 'password')
                        ->passwordInput(['autofocus' => true, 'placeholder' => 'New password (min 8 characters)'])
                        ->label('New Password') ?>

                    <div class="d-grid mt-3">
                        <?= Html::submitButton('Save New Password', ['class' => 'btn btn-dark']) ?>
                    </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
