<?php

/** @var yii\web\View $this */
/** @var common\models\User $model */
/** @var string[] $roles */
/** @var string $currentRole */

use yii\helpers\Html;

$this->title = 'Edit: ' . $model->username;
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0"><i class="bi bi-pencil-square me-2 text-secondary"></i><?= Html::encode($this->title) ?></h1>
    <div class="d-flex gap-2">
        <?= Html::a('<i class="bi bi-eye me-1"></i>View', ['view', 'id' => $model->id], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
        <?= Html::a('<i class="bi bi-arrow-left me-1"></i>Back', ['index'], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <?= $this->render('_form', ['model' => $model, 'roles' => $roles, 'currentRole' => $currentRole]) ?>
    </div>
</div>
