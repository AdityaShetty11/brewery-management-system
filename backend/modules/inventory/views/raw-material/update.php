<?php

/** @var yii\web\View $this */
/** @var common\models\RawMaterial $model */

use yii\helpers\Html;

$this->title = 'Edit: ' . $model->name;
?>
<div class="mb-3">
    <?= Html::a('<i class="bi bi-arrow-left me-1"></i>Back to ' . Html::encode($model->name), ['view', 'id' => $model->id], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
</div>

<div class="card shadow-sm">
    <div class="card-header fw-semibold">
        <i class="bi bi-pencil me-1"></i>Edit Raw Material
    </div>
    <div class="card-body">
        <?= $this->render('_form', ['model' => $model]) ?>
    </div>
</div>
