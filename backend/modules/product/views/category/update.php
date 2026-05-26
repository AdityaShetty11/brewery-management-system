<?php

/** @var yii\web\View $this */
/** @var common\models\ProductCategory $model */

use yii\helpers\Html;

$this->title = 'Edit: ' . $model->name;
?>

<div class="mb-3">
    <?= Html::a('<i class="bi bi-arrow-left me-1"></i>Back', ['index'], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <h2 class="card-title h5 mb-4">Edit Category</h2>
        <?= $this->render('_form', ['model' => $model]) ?>
    </div>
</div>
