<?php

/** @var yii\web\View $this */
/** @var common\models\CustomerContact $model */
/** @var array $companies */

use yii\helpers\Html;

$this->title = 'Add Contact';
$backUrl = $model->company_id ? ['/crm/company/view', 'id' => $model->company_id] : ['/crm/company/index'];
?>

<div class="mb-3">
    <?= Html::a('<i class="bi bi-arrow-left me-1"></i>Back', $backUrl, ['class' => 'btn btn-outline-secondary btn-sm']) ?>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <h2 class="card-title h5 mb-4">New Contact</h2>
        <?= $this->render('_form', ['model' => $model, 'companies' => $companies]) ?>
    </div>
</div>
