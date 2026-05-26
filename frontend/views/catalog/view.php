<?php

/** @var yii\web\View $this */
/** @var common\models\Product $model */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = $model->name;
?>

<div class="mb-3">
    <?= Html::a('<i class="bi bi-arrow-left me-1"></i>Back to Catalog', ['index'], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
</div>

<div class="row g-4">
    <!-- ── Image / Icon ─────────────────────────────────────────────── -->
    <div class="col-md-4">
        <div class="card shadow-sm text-center p-5 bg-dark">
            <i class="bi <?= $model->getPackagingIcon() ?> text-warning" style="font-size:6rem;"></i>
        </div>
    </div>

    <!-- ── Details ──────────────────────────────────────────────────── -->
    <div class="col-md-8">
        <span class="badge bg-secondary mb-2"><?= Html::encode($model->category->name ?? '') ?></span>
        <h1 class="h3 mb-1"><?= Html::encode($model->name) ?></h1>
        <p class="text-muted mb-3">
            <i class="bi <?= $model->getPackagingIcon() ?> me-1"></i><?= $model->getPackagingLabel() ?>
            &nbsp;&middot;&nbsp; SKU: <?= Html::encode($model->sku) ?>
        </p>

        <p class="lead"><?= Html::encode($model->description ?? 'No description available.') ?></p>

        <hr>

        <div class="d-flex align-items-center gap-4 mb-4">
            <div>
                <div class="text-muted small">Unit Price</div>
                <div class="fs-4 fw-bold"><?= $model->getFormattedPrice() ?></div>
            </div>
            <div>
                <div class="text-muted small">Availability</div>
                <?php if ($model->stock_qty > 0): ?>
                    <span class="badge bg-success fs-6">In Stock</span>
                <?php else: ?>
                    <span class="badge bg-danger fs-6">Out of Stock</span>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!Yii::$app->user->isGuest && $model->stock_qty > 0): ?>
            <?= Html::a(
                '<i class="bi bi-bag-plus me-1"></i>Place Order',
                ['/order/create', 'product_id' => $model->id],
                ['class' => 'btn btn-dark btn-lg']
            ) ?>
        <?php elseif (Yii::$app->user->isGuest): ?>
            <p class="text-muted">
                <?= Html::a('Sign in', ['/site/login']) ?> or
                <?= Html::a('register', ['/site/signup']) ?>
                to place an order.
            </p>
        <?php endif; ?>
    </div>
</div>
