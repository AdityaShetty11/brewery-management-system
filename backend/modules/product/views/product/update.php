<?php

/** @var yii\web\View $this */
/** @var common\models\Product $model */
/** @var array $categories */

use yii\helpers\Html;

$this->title = 'Edit: ' . $model->name;
?>

<div class="mb-3">
    <?= Html::a('<i class="bi bi-arrow-left me-1"></i>Back to Product', ['view', 'id' => $model->id], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <h2 class="card-title h5 mb-4">Edit Product</h2>
        <?= $this->render('_form', ['model' => $model, 'categories' => $categories]) ?>
    </div>
</div>
