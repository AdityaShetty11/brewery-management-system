<?php

/** @var yii\web\View $this */
/** @var common\models\User $model */
/** @var string[] $roles  list of all role names */

use yii\helpers\Html;

$this->title = 'Add User';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0"><i class="bi bi-person-plus me-2 text-secondary"></i><?= Html::encode($this->title) ?></h1>
    <?= Html::a('<i class="bi bi-arrow-left me-1"></i>Back', ['index'], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <?= $this->render('_form', ['model' => $model, 'roles' => $roles, 'currentRole' => '']) ?>
    </div>
</div>
