<?php

/** @var yii\web\View $this */
/** @var common\models\ProductionOrder $model */
/** @var array $productList */
/** @var array $orderList */

use yii\helpers\Html;

$this->title = 'Edit: ' . $model->reference;
?>
<div class="mb-3">
    <?= Html::a('<i class="bi bi-arrow-left me-1"></i>Back', ['view', 'id' => $model->id], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
</div>
<div class="card shadow-sm">
    <div class="card-body">
        <h2 class="h5 mb-4">Edit Production Order</h2>
        <?= $this->render('_form', ['model' => $model, 'productList' => $productList, 'orderList' => $orderList]) ?>
    </div>
</div>
