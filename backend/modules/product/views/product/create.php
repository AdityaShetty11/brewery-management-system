<?php

/** @var yii\web\View $this */
/** @var common\models\Product $model */
/** @var array $categories */

$this->title = 'Add Product';
?>

<div class="mb-3">
    <?= \yii\helpers\Html::a('<i class="bi bi-arrow-left me-1"></i>Back', ['index'], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <h2 class="card-title h5 mb-4">New Product</h2>
        <?= $this->render('_form', ['model' => $model, 'categories' => $categories]) ?>
    </div>
</div>
