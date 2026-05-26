<?php

/** @var yii\web\View $this */
/** @var string $content */

use yii\helpers\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

$this->beginPage();
?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?> — Hops & Barrel Brewery</title>
    <?php $this->head() ?>

    <!-- Bootstrap 5 CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        body { background-color: #fafafa; }
        .navbar-brand { font-weight: 700; letter-spacing: 0.5px; }
        footer { background: #212529; color: #adb5bd; }
    </style>
</head>
<body>
<?php $this->beginBody() ?>

<!-- ── Navigation ──────────────────────────────────── -->
<nav class="navbar navbar-expand-md navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="<?= Yii::$app->homeUrl ?>">
            <i class="bi bi-cup-hot-fill me-1"></i> Hops & Barrel
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?= \yii\helpers\Url::to(['/site/index']) ?>">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= \yii\helpers\Url::to(['/catalog/index']) ?>">Our Beers</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= \yii\helpers\Url::to(['/site/about']) ?>">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= \yii\helpers\Url::to(['/site/contact']) ?>">Contact</a>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto">
                <?php if (Yii::$app->user->isGuest): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= \yii\helpers\Url::to(['/site/login']) ?>">
                            <i class="bi bi-box-arrow-in-right"></i> Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-outline-light btn-sm ms-2 px-3"
                           href="<?= \yii\helpers\Url::to(['/site/signup']) ?>">
                            Register
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= \yii\helpers\Url::to(['/customer/dashboard']) ?>">
                            <i class="bi bi-speedometer2"></i> My Account
                        </a>
                    </li>
                    <li class="nav-item">
                        <?= Html::beginForm(['/site/logout'], 'post', ['class' => 'd-inline']) ?>
                        <button type="submit" class="nav-link border-0 bg-transparent">
                            <i class="bi bi-box-arrow-right"></i> Logout
                            (<?= Html::encode(Yii::$app->user->identity->username) ?>)
                        </button>
                        <?= Html::endForm() ?>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- ── Flash Messages ─────────────────────────────── -->
<div class="container mt-3">
    <?php foreach (['success', 'info', 'warning', 'error', 'danger'] as $type): ?>
        <?php if (Yii::$app->session->hasFlash($type)): ?>
            <div class="alert alert-<?= $type === 'error' ? 'danger' : $type ?> alert-dismissible fade show">
                <?= Html::encode(Yii::$app->session->getFlash($type)) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>

<!-- ── Page Content ───────────────────────────────── -->
<main class="container py-4">
    <?= $content ?>
</main>

<!-- ── Footer ─────────────────────────────────────── -->
<footer class="py-4 mt-5">
    <div class="container text-center">
        <small>&copy; <?= date('Y') ?> Hops & Barrel Brewery. All rights reserved.</small>
    </div>
</footer>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
