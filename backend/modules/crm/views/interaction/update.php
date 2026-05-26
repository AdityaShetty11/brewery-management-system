<?php

/** @var yii\web\View $this */
/** @var common\models\CrmInteraction $model */
/** @var common\models\CustomerCompany $company */
/** @var array $contactList */

use yii\helpers\Html;

$this->title = 'Edit Interaction';
?>

<div class="mb-3">
    <?= Html::a('<i class="bi bi-arrow-left me-1"></i>Back to ' . Html::encode($company->name),
        ['/crm/company/view', 'id' => $company->id],
        ['class' => 'btn btn-outline-secondary btn-sm']
    ) ?>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <h2 class="card-title h5 mb-4">Edit Interaction</h2>
        <?= $this->render('_form', ['model' => $model, 'company' => $company, 'contactList' => $contactList]) ?>
    </div>
</div>
