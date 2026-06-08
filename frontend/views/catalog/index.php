<?php

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var array $categories         [id => name] */
/** @var int   $categoryId         active filter */
/** @var string $packagingType     active filter */

use common\models\Product;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

$this->title = 'Our Beers';
$products    = $dataProvider->getModels();
?>

<!-- ── Hero ──────────────────────────────────────────────────────── -->
<div class="py-4 mb-4 border-bottom">
    <h1 class="h3 mb-1">Our Beers</h1>
    <p class="text-muted mb-0">Handcrafted at Hofer BrauHaus. Browse our full lineup.</p>
</div>

<!-- ── Filter Bar ────────────────────────────────────────────────── -->
<div class="d-flex flex-wrap gap-2 mb-4 align-items-center">
    <span class="text-muted small me-1">Filter:</span>

    <?= Html::a('All', ['index'], ['class' => 'btn btn-sm ' . ($categoryId === 0 && empty($packagingType) ? 'btn-dark' : 'btn-outline-secondary')]) ?>

    <?php foreach ($categories as $id => $name): ?>
        <?= Html::a(
            Html::encode($name),
            ['index', 'category_id' => $id],
            ['class' => 'btn btn-sm ' . ($categoryId === $id ? 'btn-dark' : 'btn-outline-secondary')]
        ) ?>
    <?php endforeach; ?>

    <span class="vr mx-1"></span>

    <?php foreach (Product::packagingLabels() as $key => $label): ?>
        <?= Html::a(
            '<i class="bi ' . Product::packagingIcons()[$key] . ' me-1"></i>' . Html::encode($label),
            ['index', 'packaging_type' => $key],
            ['class' => 'btn btn-sm ' . ($packagingType === $key ? 'btn-dark' : 'btn-outline-secondary')]
        ) ?>
    <?php endforeach; ?>
</div>

<!-- ── Product Grid ──────────────────────────────────────────────── -->
<?php if (empty($products)): ?>
    <div class="alert alert-info">No products found for the selected filter.</div>
<?php else: ?>
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4 mb-4">
        <?php foreach ($products as $product): ?>
            <div class="col">
                <div class="card h-100 shadow-sm">
                    <!-- Placeholder image — replace with real image uploads in v2 -->
                    <div class="card-img-top bg-dark d-flex align-items-center justify-content-center" style="height:140px;">
                        <i class="bi <?= $product->getPackagingIcon() ?> text-warning" style="font-size:3rem;"></i>
                    </div>
                    <div class="card-body">
                        <span class="badge bg-secondary mb-1"><?= Html::encode($product->category->name ?? '') ?></span>
                        <h5 class="card-title mb-1"><?= Html::encode($product->name) ?></h5>
                        <p class="text-muted small mb-2">
                            <i class="bi <?= $product->getPackagingIcon() ?> me-1"></i><?= $product->getPackagingLabel() ?>
                        </p>
                        <p class="card-text small text-truncate"><?= Html::encode($product->description ?? '') ?></p>
                    </div>
                    <div class="card-footer d-flex justify-content-between align-items-center">
                        <strong class="text-dark"><?= $product->getFormattedPrice() ?></strong>
                        <?= Html::a('View Details', ['view', 'id' => $product->id], ['class' => 'btn btn-sm btn-outline-dark']) ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?= LinkPager::widget(['pagination' => $dataProvider->pagination]) ?>
<?php endif; ?>
