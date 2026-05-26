<?php

/** @var yii\web\View $this */
/** @var common\models\Batch $model */
/** @var array $brewMasters */

use yii\helpers\Html;

$this->title = 'Edit Batch';
?>
<div class="mb-3">
    <?= Html::a('<i class="bi bi-arrow-left me-1"></i>Back to Batch', ['/production/batch/view', 'id' => $model->id], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
</div>
<div class="card shadow-sm">
    <div class="card-body">
        <h2 class="h5 mb-4">Edit Batch</h2>
        <?= $this->render('_form', ['model' => $model, 'brewMasters' => $brewMasters]) ?>
    </div>
</div>
