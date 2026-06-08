<?php

/** @var yii\web\View $this */
/** @var string $content */

use yii\helpers\Html;

$this->beginPage();
?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?> — Hofer Bräuhaus</title>
    <?php $this->head() ?>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700&family=EB+Garamond:ital,wght@0,400;0,500;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        :root {
            --brew-dark:      #1a0e00;
            --brew-darker:    #0d0700;
            --brew-gold:      #c8860a;
            --brew-gold-lt:   #e8a020;
            --brew-copper:    #8B4513;
            --brew-cream:     #f5e6c8;
            --brew-parchment: #fdf6ec;
            --brew-text:      #2c1810;
        }

        body {
            background-color: var(--brew-parchment);
            color: var(--brew-text);
            font-family: 'EB Garamond', Georgia, serif;
            font-size: 1.05rem;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        h1, h2, h3, h4, h5, h6, .navbar-brand, .cinzel {
            font-family: 'Cinzel', serif;
        }

        /* ── Navbar ── */
        .navbar-brew {
            background: var(--brew-darker);
            border-bottom: 2px solid var(--brew-gold);
        }
        .navbar-brew .navbar-brand {
            color: var(--brew-gold) !important;
            font-size: 1.3rem;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .navbar-brew .navbar-brand img {
            width: 44px;
            height: 44px;
            object-fit: contain;
        }
        .navbar-brew .nav-link {
            color: var(--brew-cream) !important;
            font-family: 'Cinzel', serif;
            font-size: 0.82rem;
            letter-spacing: 0.8px;
            padding: 0.5rem 0.9rem;
            transition: color 0.2s;
        }
        .navbar-brew .nav-link:hover,
        .navbar-brew .nav-link.active {
            color: var(--brew-gold-lt) !important;
        }
        .navbar-brew .navbar-toggler {
            border-color: var(--brew-gold);
        }
        .navbar-brew .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(200,134,10,1)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }
        .btn-brew {
            background: var(--brew-gold);
            color: var(--brew-darker);
            border: none;
            font-family: 'Cinzel', serif;
            font-size: 0.82rem;
            letter-spacing: 0.8px;
            font-weight: 600;
        }
        .btn-brew:hover {
            background: var(--brew-gold-lt);
            color: var(--brew-darker);
        }
        .btn-outline-brew {
            border: 1px solid var(--brew-gold);
            color: var(--brew-gold);
            background: transparent;
            font-family: 'Cinzel', serif;
            font-size: 0.82rem;
            letter-spacing: 0.8px;
        }
        .btn-outline-brew:hover {
            background: var(--brew-gold);
            color: var(--brew-darker);
        }

        /* ── Divider ornament ── */
        .brew-divider {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin: 2rem 0;
            color: var(--brew-gold);
        }
        .brew-divider::before,
        .brew-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--brew-gold);
            opacity: 0.4;
        }

        /* ── Cards ── */
        .card {
            border: 1px solid rgba(200,134,10,0.2);
            background: #fff;
        }
        .card-header-brew {
            background: var(--brew-dark);
            color: var(--brew-gold);
            font-family: 'Cinzel', serif;
            font-size: 0.85rem;
            letter-spacing: 0.8px;
            border-bottom: 1px solid var(--brew-gold);
        }

        /* ── Hero banner ── */
        .brew-hero {
            background: linear-gradient(160deg, var(--brew-darker) 0%, #3d1c00 100%);
            color: var(--brew-cream);
            padding: 4rem 0;
            text-align: center;
            border-bottom: 3px solid var(--brew-gold);
        }
        .brew-hero h1 {
            color: var(--brew-gold);
            font-size: 2.8rem;
            text-shadow: 0 2px 8px rgba(0,0,0,0.6);
        }
        .brew-hero .lead {
            color: var(--brew-cream);
            opacity: 0.85;
        }

        /* ── Main content wrapper ── */
        #page-content {
            flex: 1;
        }

        /* ── Footer ── */
        footer {
            background: var(--brew-darker);
            color: #a08060;
            border-top: 2px solid var(--brew-gold);
            font-family: 'Cinzel', serif;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
        }
        footer a { color: var(--brew-gold); text-decoration: none; }
        footer a:hover { color: var(--brew-gold-lt); }
    </style>
</head>
<body>
<?php $this->beginBody() ?>

<!-- ── Navigation ──────────────────────────────────── -->
<nav class="navbar navbar-expand-md navbar-brew">
    <div class="container">
        <a class="navbar-brand" href="<?= Yii::$app->homeUrl ?>">
            <img src="/images/logo.png" alt="Hofer Bräuhaus Logo" onerror="this.style.display='none'">
            Hofer Bräuhaus
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
            <ul class="navbar-nav ms-auto align-items-center gap-1">
                <?php if (Yii::$app->user->isGuest): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= \yii\helpers\Url::to(['/site/login']) ?>">
                            <i class="bi bi-box-arrow-in-right"></i> Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-brew btn-sm px-3"
                           href="<?= \yii\helpers\Url::to(['/site/signup']) ?>">
                            Register
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= \yii\helpers\Url::to(['/order/index']) ?>">
                            <i class="bi bi-bag me-1"></i>My Orders
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
<div id="page-content">
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
</div><!-- /#page-content -->

<!-- ── Footer ─────────────────────────────────────── -->
<footer class="py-4">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-4 text-center text-md-start mb-2 mb-md-0">
                <img src="/images/logo.png" alt="logo" height="40" class="me-2" onerror="this.style.display='none'">
                <span style="color:var(--brew-gold);font-size:1rem;">Hofer Bräuhaus</span>
            </div>
            <div class="col-md-4 text-center mb-2 mb-md-0">
                <small>&copy; <?= date('Y') ?> Hofer Bräuhaus. All rights reserved.</small>
            </div>
            <div class="col-md-4 text-center text-md-end">
                <a href="/site/about" class="me-3">About</a>
                <a href="/site/contact">Contact</a>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
