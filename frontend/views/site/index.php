<?php

/** @var yii\web\View $this */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Hofer BrauHaus';
?>

<div class="py-5 text-center">
    <img src="/images/logo.png" alt="Hofer BrauHaus Logo" class="mb-4" style="width:140px;height:140px;object-fit:contain;">
    <h1 class="display-5 fw-bold">Hofer BrauHaus</h1>
    <p class="lead text-muted col-md-6 mx-auto">
        Craft beers brewed with passion. Browse our catalog, place orders, and track deliveries — all in one place.
    </p>
    <div class="d-flex gap-3 justify-content-center mt-4">
        <?= Html::a('<i class="bi bi-grid me-1"></i>Browse Catalog', ['catalog/index'], ['class' => 'btn btn-dark btn-lg']) ?>
        <?php if (Yii::$app->user->isGuest): ?>
            <?= Html::a('<i class="bi bi-person me-1"></i>Sign In', ['site/login'], ['class' => 'btn btn-outline-secondary btn-lg']) ?>
        <?php else: ?>
            <?= Html::a('<i class="bi bi-bag me-1"></i>My Orders', ['order/index'], ['class' => 'btn btn-outline-secondary btn-lg']) ?>
        <?php endif; ?>
    </div>
</div>

<hr class="my-5">

<div class="row g-4 text-center">
    <div class="col-md-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="fs-1 mb-3">🌾</div>
                <h5 class="fw-bold">Craft Ingredients</h5>
                <p class="text-muted small">Every batch starts with the finest hops, malts, and yeasts sourced from trusted suppliers.</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="fs-1 mb-3">⚗️</div>
                <h5 class="fw-bold">Masterful Brewing</h5>
                <p class="text-muted small">Our brew masters oversee every stage — from mashing to fermentation — to ensure consistent quality.</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="fs-1 mb-3">🚚</div>
                <h5 class="fw-bold">Direct Delivery</h5>
                <p class="text-muted small">Order online and receive your kegs, cans, or bottles delivered directly to your venue.</p>
            </div>
        </div>
    </div>
</div>
