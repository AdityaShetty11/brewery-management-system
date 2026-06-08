<?php

/** @var yii\web\View $this */
/** @var string $content */

use yii\helpers\Html;
use yii\helpers\Url;

$this->beginPage();
?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?> — Hofer Bräuhaus Admin</title>
    <?php $this->head() ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700&family=EB+Garamond:ital,wght@0,400;0,500;1,400&display=swap" rel="stylesheet">
    <style>
        :root {
            --brew-dark:      #1a0e00;
            --brew-darker:    #0d0700;
            --brew-gold:      #c8860a;
            --brew-gold-lt:   #e8a020;
            --brew-copper:    #8B4513;
            --brew-cream:     #f5e6c8;
            --brew-sidebar-w: 240px;
        }

        body {
            background: #f5ede0;
            font-family: 'EB Garamond', Georgia, serif;
            font-size: 1rem;
        }

        h1,h2,h3,h4,h5,h6,.cinzel { font-family: 'Cinzel', serif; }

        /* ── Sidebar ── */
        #sidebar {
            width: var(--brew-sidebar-w);
            height: 100vh;
            background: var(--brew-darker);
            border-right: 2px solid var(--brew-gold);
            position: fixed; top: 0; left: 0; z-index: 100;
            display: flex; flex-direction: column;
            overflow: hidden;
        }
        #sidebar .sidebar-brand {
            padding: 1rem 1.1rem 0.8rem;
            border-bottom: 1px solid rgba(200,134,10,0.35);
            display: flex; align-items: center; gap: 10px;
        }
        #sidebar .sidebar-brand img {
            width: 40px; height: 40px; object-fit: contain;
        }
        #sidebar .sidebar-brand-text {
            color: var(--brew-gold);
            font-family: 'Cinzel', serif;
            font-size: 0.9rem;
            font-weight: 700;
            line-height: 1.2;
        }
        #sidebar .sidebar-brand-sub {
            color: rgba(200,134,10,0.55);
            font-size: 0.68rem;
            font-family: 'EB Garamond', serif;
            letter-spacing: 0.5px;
        }
        #sidebar .nav-link {
            color: #c8a87a;
            padding: 0.5rem 1.1rem;
            font-size: 0.85rem;
            font-family: 'EB Garamond', serif;
            border-radius: 4px;
            margin: 1px 6px;
            transition: all 0.18s;
        }
        #sidebar .nav-link:hover {
            color: var(--brew-gold-lt);
            background: rgba(200,134,10,0.12);
        }
        #sidebar .nav-link.active {
            color: var(--brew-darker);
            background: var(--brew-gold);
            font-weight: 600;
        }
        #sidebar .nav-link.active i { color: var(--brew-darker); }
        #sidebar .nav-section {
            color: rgba(200,134,10,0.5);
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            padding: 0.9rem 1.1rem 0.2rem;
            font-family: 'Cinzel', serif;
        }

        /* ── Main content ── */
        #main-content {
            margin-left: var(--brew-sidebar-w);
            height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* ── Topbar ── */
        #topbar {
            height: 54px;
            flex-shrink: 0;
            background: var(--brew-darker);
            border-bottom: 2px solid var(--brew-gold);
            z-index: 99;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 1.5rem;
        }
        #topbar .page-title {
            color: var(--brew-gold);
            font-family: 'Cinzel', serif;
            font-size: 0.9rem;
            letter-spacing: 0.8px;
        }
        #topbar .btn-logout {
            background: transparent;
            border: 1px solid rgba(200,134,10,0.5);
            color: var(--brew-cream);
            font-family: 'Cinzel', serif;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            padding: 4px 12px;
            border-radius: 4px;
            transition: all 0.2s;
        }
        #topbar .btn-logout:hover {
            background: var(--brew-gold);
            border-color: var(--brew-gold);
            color: var(--brew-darker);
        }
        #topbar .user-badge {
            color: #c8a87a;
            font-size: 0.85rem;
            font-family: 'EB Garamond', serif;
        }

        /* ── Cards & Tables ── */
        .card {
            border: 1px solid rgba(200,134,10,0.2);
            border-radius: 6px;
        }
        .card-header {
            background: var(--brew-dark);
            color: var(--brew-gold);
            font-family: 'Cinzel', serif;
            font-size: 0.82rem;
            letter-spacing: 0.6px;
            border-bottom: 1px solid rgba(200,134,10,0.35);
        }
        .table thead th {
            background: var(--brew-dark);
            color: var(--brew-gold);
            font-family: 'Cinzel', serif;
            font-size: 0.78rem;
            letter-spacing: 0.5px;
            border-color: rgba(200,134,10,0.25);
        }
        .table thead th a,
        .table thead th a:hover,
        .table thead th a:visited {
            color: var(--brew-gold);
            text-decoration: none;
        }
        .table thead th a:hover {
            color: var(--brew-gold-lt);
        }
        .btn-primary {
            background: var(--brew-gold);
            border-color: var(--brew-gold);
            color: var(--brew-darker);
            font-family: 'Cinzel', serif;
            font-size: 0.8rem;
        }
        .btn-primary:hover {
            background: var(--brew-gold-lt);
            border-color: var(--brew-gold-lt);
            color: var(--brew-darker);
        }

        /* ── Scrollable content + footer ── */
        #content-scroll {
            flex: 1;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: rgba(200,134,10,0.35) transparent;
        }
        #main-footer {
            flex-shrink: 0;
            height: 38px;
            background: var(--brew-darker);
            border-top: 1px solid rgba(200,134,10,0.35);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            color: rgba(200,134,10,0.55);
            font-family: 'Cinzel', serif;
            font-size: 0.68rem;
            letter-spacing: 0.4px;
        }
        .footer-version {
            color: rgba(200,134,10,0.35);
        }
    </style>
</head>
<body>
<?php $this->beginBody() ?>

<?php
$route = Yii::$app->requestedRoute ?? '';
$a = fn(string $prefix) => str_starts_with($route, ltrim($prefix, '/')) ? ' active' : '';
?>
<!-- ── Sidebar ─────────────────────────────────────── -->
<nav id="sidebar" class="d-flex flex-column">
    <div class="sidebar-brand">
        <img src="/images/logo.png" alt="Logo" onerror="this.style.display='none'">
        <div>
            <div class="sidebar-brand-text">Hofer Bräuhaus</div>
            <div class="sidebar-brand-sub">Admin Panel</div>
        </div>
    </div>

    <ul class="nav flex-column mt-2 px-2" style="overflow-y: auto; overflow-x: hidden; flex: 1; flex-wrap: nowrap; scrollbar-width: thin; scrollbar-color: rgba(200,134,10,0.4) transparent;">
        <li class="nav-section">Overview</li>
        <li class="nav-item">
            <a class="nav-link<?= $a('dashboard') ?>" href="<?= Url::to(['/dashboard/index']) ?>">
                <i class="bi bi-speedometer2 me-2"></i>Dashboard
            </a>
        </li>

        <li class="nav-section">Operations</li>
        <li class="nav-item">
            <a class="nav-link<?= $a('order/order') ?>" href="<?= Url::to(['/order/order/index']) ?>">
                <i class="bi bi-bag-check me-2"></i>Orders
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link<?= $a('production') ?>" href="<?= Url::to(['/production/production-order/index']) ?>">
                <i class="bi bi-gear me-2"></i>Production
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link<?= $a('inventory/raw-material') ?>" href="<?= Url::to(['/inventory/raw-material/index']) ?>">
                <i class="bi bi-boxes me-2"></i>Raw Materials
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link<?= $a('inventory/stock-transaction') ?>" href="<?= Url::to(['/inventory/stock-transaction/index']) ?>">
                <i class="bi bi-journal-text me-2"></i>Inventory Ledger
            </a>
        </li>

        <li class="nav-section">Catalog</li>
        <li class="nav-item">
            <a class="nav-link<?= $a('product/product') ?>" href="<?= Url::to(['/product/product/index']) ?>">
                <i class="bi bi-cup-straw me-2"></i>Products
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link<?= $a('product/category') ?>" href="<?= Url::to(['/product/category/index']) ?>">
                <i class="bi bi-tags me-2"></i>Categories
            </a>
        </li>

        <li class="nav-section">CRM</li>
        <li class="nav-item">
            <a class="nav-link<?= $a('crm/company') ?>" href="<?= Url::to(['/crm/company/index']) ?>">
                <i class="bi bi-building me-2"></i>Companies
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link<?= $a('crm/contact') ?>" href="<?= Url::to(['/crm/contact/index']) ?>">
                <i class="bi bi-person-lines-fill me-2"></i>Contacts
            </a>
        </li>

        <li class="nav-section">Reports</li>
        <li class="nav-item">
            <a class="nav-link<?= $route === 'report/report/sales' ? ' active' : '' ?>" href="<?= Url::to(['/report/report/sales']) ?>">
                <i class="bi bi-bag-fill me-2"></i>Sales Report
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link<?= $route === 'report/report/inventory' ? ' active' : '' ?>" href="<?= Url::to(['/report/report/inventory']) ?>">
                <i class="bi bi-box-seam me-2"></i>Inventory Report
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link<?= $route === 'report/report/production' ? ' active' : '' ?>" href="<?= Url::to(['/report/report/production']) ?>">
                <i class="bi bi-gear-fill me-2"></i>Production Report
            </a>
        </li>

        <?php if (Yii::$app->user->can('manageUsers')): ?>
        <li class="nav-section">Admin</li>
        <li class="nav-item">
            <a class="nav-link<?= $a('user') ?>" href="<?= Url::to(['/user/index']) ?>">
                <i class="bi bi-people me-2"></i>Users
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link<?= $route === 'user/create' ? ' active' : '' ?>" href="<?= Url::to(['/user/create']) ?>">
                <i class="bi bi-person-plus me-2"></i>Add User
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link<?= $a('role') ?>" href="<?= Url::to(['/role/index']) ?>">
                <i class="bi bi-shield-lock me-2"></i>Roles
            </a>
        </li>
        <?php endif; ?>
    </ul>
</nav>

<!-- ── Main Content ────────────────────────────────── -->
<div id="main-content">
    <!-- Topbar -->
    <div id="topbar">
        <div class="page-title"><?= Html::encode($this->title) ?></div>
        <div class="d-flex align-items-center gap-3">
            <?php if (!Yii::$app->user->isGuest): ?>
            <span class="user-badge">
                <i class="bi bi-person-circle me-1"></i>
                <?= Html::encode(Yii::$app->user->identity->username) ?>
            </span>
            <?= Html::beginForm(['/site/logout'], 'post') ?>
            <button type="submit" class="btn-logout">
                <i class="bi bi-box-arrow-right me-1"></i>Logout
            </button>
            <?= Html::endForm() ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Scrollable content area -->
    <div id="content-scroll">
        <!-- Flash messages -->
        <div class="px-4 pt-3">
            <?php foreach (['success', 'info', 'warning', 'error', 'danger'] as $type): ?>
                <?php if (Yii::$app->session->hasFlash($type)): ?>
                    <div class="alert alert-<?= $type === 'error' ? 'danger' : $type ?> alert-dismissible fade show">
                        <?= Html::encode(Yii::$app->session->getFlash($type)) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <!-- Page Content -->
        <div class="p-4">
            <?= $content ?>
        </div>
    </div>

    <!-- Footer -->
    <footer id="main-footer">
        <span>&copy; <?= date('Y') ?> Hofer Bräuhaus &mdash; Admin Panel</span>
        <span class="footer-version">Brewery Management System</span>
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
