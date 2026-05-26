<?php

/** @var yii\web\View $this */
/** @var common\models\CustomerContact $model */
/** @var array $companies */

use yii\helpers\Html;

$this->title = 'Edit Contact';
?>

<div class="mb-3">
    <?= Html::a('<i class="bi bi-arrow-left me-1"></i>Back to Company', ['/crm/company/view', 'id' => $model->company_id], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <h2 class="card-title h5 mb-4">Edit Contact</h2>
        <?= $this->render('_form', ['model' => $model, 'companies' => $companies]) ?>
    </div>
</div>
