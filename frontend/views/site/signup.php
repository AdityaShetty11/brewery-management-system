<?php

/** @var yii\web\View $this */
/** @var frontend\models\SignupForm $model */

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

$this->title = 'Create Account';
?>

<div class="row justify-content-center">
    <div class="col-md-6">

        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h1 class="card-title h4 mb-4 text-center">
                    <i class="bi bi-person-plus-fill text-warning me-2"></i>Create Account
                </h1>

                <?php $form = ActiveForm::begin(['id' => 'signup-form']); ?>

                    <?= $form->field($model, 'username')
                        ->textInput(['autofocus' => true, 'placeholder' => 'Choose a username']) ?>

                    <?= $form->field($model, 'email')
                        ->input('email', ['placeholder' => 'you@example.com']) ?>

                    <?= $form->field($model, 'password')
                        ->passwordInput(['placeholder' => 'At least 8 characters']) ?>

                    <div class="d-grid mt-3">
                        <?= Html::submitButton('Register', ['class' => 'btn btn-dark btn-lg']) ?>
                    </div>

                <?php ActiveForm::end(); ?>

                <hr>
                <div class="text-center small">
                    Already have an account? <?= Html::a('Sign in', ['/site/login']) ?>
                </div>
            </div>
        </div>

    </div>
</div>
