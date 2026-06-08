<?php

/** @var yii\web\View $this */
/** @var frontend\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

$this->title = 'Login';
?>

<div class="row justify-content-center">
    <div class="col-md-5">

        <div class="card shadow-sm">
            <div class="card-body p-4">
                <div class="text-center mb-3">
                    <img src="/images/logo.png" alt="Hofer BrauHaus Logo" style="width:90px;height:90px;object-fit:contain;">
                </div>
                <h1 class="card-title h4 mb-4 text-center">Sign In</h1>

                <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

                    <?= $form->field($model, 'username')
                        ->textInput(['autofocus' => true, 'placeholder' => 'Username'])
                        ->label('Username') ?>

                    <?= $form->field($model, 'password')
                        ->passwordInput(['placeholder' => 'Password'])
                        ->label('Password') ?>

                    <?= $form->field($model, 'rememberMe')
                        ->checkbox(['template' => "<div class=\"form-check\">{input} {label}</div>\n{error}"]) ?>

                    <div class="d-grid mt-3">
                        <?= Html::submitButton('Sign In', ['class' => 'btn btn-dark btn-lg']) ?>
                    </div>

                <?php ActiveForm::end(); ?>

                <hr>
                <div class="text-center small">
                    <?= Html::a('Forgot password?', ['/site/request-password-reset']) ?>
                    &nbsp;&middot;&nbsp;
                    <?= Html::a('Create account', ['/site/signup']) ?>
                </div>
            </div>
        </div>

    </div>
</div>
