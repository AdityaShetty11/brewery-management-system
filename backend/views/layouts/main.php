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
    <title><?= Html::encode($this->title) ?> — Hops & Barrel Admin</title>
    <?php $this->head() ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body { background: #f0f2f5; }

        /* Sidebar */
        #sidebar {
            width: 240px; min-height: 100vh;
            background: #1a1d20; position: fixed; top: 0; left: 0; z-index: 100;
            padding-top: 60px;
        }
        #sidebar .nav-link { color: #adb5bd; padding: 0.6rem 1.25rem; font-size: 0.9rem; }
        #sidebar .nav-link:hover, #sidebar .nav-link.active { color: #fff; background: rgba(255,255,255,0.08); border-radius: 6px; }
        #sidebar .nav-section { color: #6c757d; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; padding: 1rem 1.25rem 0.25rem; }

        /* Main content */
        #main-content { margin-left: 240px; }

        /* Topbar */
        #topbar {
            height: 56px; background: #fff;
            border-bottom: 1px solid #dee2e6;
            position: sticky; top: 0; z-index: 99;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 1.5rem;
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
    <div class="px-3 pb-3 border-bottom border-secondary">
        <span class="text-white fw-bold fs-5">Hops &amp; Barrel</span>
        <div class="text-muted small">Admin Panel</div>
    </div>

    <ul class="nav flex-column mt-2 px-2">
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
            <a class="nav-link<?= $a('audit') ?>" href="<?= Url::to(['/audit/index']) ?>">
                <i class="bi bi-shield-check me-2"></i>Audit Log
            </a>
        </li>
        <?php endif; ?>
    </ul>
</nav>

<!-- ── Main Content ────────────────────────────────── -->
<div id="main-content">
    <!-- Topbar -->
    <div id="topbar">
        <div class="fw-semibold text-secondary"><?= Html::encode($this->title) ?></div>
        <div class="d-flex align-items-center gap-3">
            <?php if (!Yii::$app->user->isGuest): ?>
            <span class="text-muted small">
                <i class="bi bi-person-circle me-1"></i>
                <?= Html::encode(Yii::$app->user->identity->username) ?>
            </span>
            <?= Html::beginForm(['/site/logout'], 'post') ?>
            <button type="submit" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-box-arrow-right"></i> Logout
            </button>
            <?= Html::endForm() ?>
            <?php endif; ?>
        </div>
    </div>

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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
