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
    <title>Hofer BrauHaus — Admin</title>
    <?php $this->head() ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700&family=EB+Garamond:ital,wght@0,400;0,500;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        :root {
            --brew-dark:   #1a0e00;
            --brew-darker: #0d0700;
            --brew-gold:   #c8860a;
            --brew-gold-lt:#e8a020;
            --brew-cream:  #f5e6c8;
        }
        body {
            background: radial-gradient(ellipse at center, #2a1500 0%, var(--brew-darker) 70%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'EB Garamond', Georgia, serif;
        }
        .login-card {
            background: #fdf6ec;
            border: 1px solid rgba(200,134,10,0.4);
            border-radius: 10px;
            box-shadow: 0 12px 40px rgba(0,0,0,0.6);
        }
        .login-card .logo-wrap img {
            width: 80px; height: 80px; object-fit: contain;
        }
        .login-card .logo-wrap .fallback { font-size: 3rem; }
        .login-card h4 {
            font-family: 'Cinzel', serif;
            color: var(--brew-dark);
            font-size: 1.3rem;
            letter-spacing: 1px;
        }
        .login-card .subtitle {
            color: rgba(139,69,19,0.7);
            font-size: 0.85rem;
            font-family: 'Cinzel', serif;
            letter-spacing: 0.5px;
        }
        .login-card .form-label {
            font-family: 'Cinzel', serif;
            font-size: 0.78rem;
            letter-spacing: 0.5px;
            color: var(--brew-dark);
        }
        .login-card .form-control {
            border-color: rgba(200,134,10,0.35);
            background: #fff;
        }
        .login-card .form-control:focus {
            border-color: var(--brew-gold);
            box-shadow: 0 0 0 3px rgba(200,134,10,0.15);
        }
        .btn-brew-login {
            background: var(--brew-dark);
            color: var(--brew-gold);
            border: 1px solid var(--brew-gold);
            font-family: 'Cinzel', serif;
            font-size: 0.9rem;
            letter-spacing: 1px;
            padding: 0.6rem;
            transition: all 0.2s;
        }
        .btn-brew-login:hover {
            background: var(--brew-gold);
            color: var(--brew-darker);
            border-color: var(--brew-gold);
        }
        .divider-gold {
            border-color: rgba(200,134,10,0.3);
            margin: 1.2rem 0;
        }
    </style>
    <title>Hofer Bräuhaus — Admin</title>
</head>
<body>
<?php $this->beginBody() ?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4 col-sm-8">
            <div class="login-card p-4 p-md-5">
                <div class="text-center mb-4">
                    <div class="logo-wrap mb-2">
                        <img src="/images/logo.png" alt="Hofer Bräuhaus Logo"
                             onerror="this.style.display='none';this.nextElementSibling.style.display='block'">
                        <div class="fallback" style="display:none">🍺</div>
                    </div>
                    <h4>Hofer Bräuhaus</h4>
                    <div class="subtitle">Admin Panel</div>
                </div>

                <hr class="divider-gold">

                <?php foreach (['success', 'error', 'danger'] as $type): ?>
                    <?php if (Yii::$app->session->hasFlash($type)): ?>
                        <div class="alert alert-<?= $type === 'error' ? 'danger' : $type ?>">
                            <?= Html::encode(Yii::$app->session->getFlash($type)) ?>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>

                <?php $form = ActiveForm::begin(['id' => 'backend-login-form']); ?>

                    <?= $form->field($model, 'username')
                        ->textInput(['autofocus' => true, 'placeholder' => 'Enter username'])
                        ->label('Username') ?>

                    <?= $form->field($model, 'password')
                        ->passwordInput(['placeholder' => 'Enter password'])
                        ->label('Password') ?>

                    <?= $form->field($model, 'rememberMe')
                        ->checkbox(['template' => "<div class=\"form-check\">{input} {label}</div>\n{error}"]) ?>

                    <div class="d-grid mt-4">
                        <?= Html::submitButton('<i class="bi bi-box-arrow-in-right me-2"></i>Sign In', [
                            'class' => 'btn btn-brew-login btn-lg',
                            'encode' => false,
                        ]) ?>
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
