<?php

/** @var yii\web\View $this */
/** @var backend\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

$this->title = 'Admin Login';
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title>Hops & Barrel — Admin</title>
    <?php $this->head() ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body { background: #1a1d20; min-height: 100vh; display: flex; align-items: center; }
        .login-card { border: none; border-radius: 12px; }
        .login-logo { font-size: 2rem; }
    </style>
</head>
<body>
<?php $this->beginBody() ?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4 col-sm-8">
            <div class="card login-card shadow-lg p-4">
                <div class="text-center mb-4">
                    <div class="login-logo">🍺</div>
                    <h4 class="fw-bold mt-2">Hops & Barrel</h4>
                    <p class="text-muted small">Admin Panel</p>
                </div>

                <?php foreach (['success', 'error', 'danger'] as $type): ?>
                    <?php if (Yii::$app->session->hasFlash($type)): ?>
                        <div class="alert alert-<?= $type === 'error' ? 'danger' : $type ?>">
                            <?= Html::encode(Yii::$app->session->getFlash($type)) ?>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>

                <?php $form = ActiveForm::begin(['id' => 'backend-login-form']); ?>

                    <?= $form->field($model, 'username')
                        ->textInput(['autofocus' => true, 'placeholder' => 'Username', 'class' => 'form-control'])
                        ->label('Username') ?>

                    <?= $form->field($model, 'password')
                        ->passwordInput(['placeholder' => 'Password', 'class' => 'form-control'])
                        ->label('Password') ?>

                    <?= $form->field($model, 'rememberMe')
                        ->checkbox(['template' => "<div class=\"form-check\">{input} {label}</div>\n{error}"]) ?>

                    <div class="d-grid mt-3">
                        <?= Html::submitButton('Sign In', ['class' => 'btn btn-dark btn-lg']) ?>
                    </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
