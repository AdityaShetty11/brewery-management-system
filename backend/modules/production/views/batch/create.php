<?php

/** @var yii\web\View $this */
/** @var common\models\Batch $model */
/** @var common\models\ProductionOrder $productionOrder */
/** @var array $brewMasters */

use yii\helpers\Html;

$this->title = 'New Batch';
?>
<div class="mb-3">
    <?= Html::a('<i class="bi bi-arrow-left me-1"></i>Back to ' . Html::encode($productionOrder->reference),
        ['/production/production-order/view', 'id' => $productionOrder->id],
        ['class' => 'btn btn-outline-secondary btn-sm']
    ) ?>
</div>
<div class="card shadow-sm">
    <div class="card-body">
        <h2 class="h5 mb-1">New Brewing Batch</h2>
        <p class="text-muted small mb-4">
            Production Order: <strong><?= Html::encode($productionOrder->reference) ?></strong>
            &nbsp;&middot;&nbsp; Product: <strong><?= Html::encode($productionOrder->product->name ?? '—') ?></strong>
        </p>
        <?= $this->render('_form', ['model' => $model, 'brewMasters' => $brewMasters]) ?>
    </div>
</div>
