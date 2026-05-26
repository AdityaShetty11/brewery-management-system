<?php

/** @var yii\web\View $this */
/** @var common\models\CrmInteraction $model */
/** @var common\models\CustomerCompany $company */
/** @var array $contactList  [id => fullName] */

use common\models\CrmInteraction;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
?>

<?php $form = ActiveForm::begin(); ?>

<div class="row g-3">
    <div class="col-md-6">
        <?= $form->field($model, 'type')->dropDownList(CrmInteraction::typeLabels()) ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($model, 'contact_id')->dropDownList(
            $contactList,
            ['prompt' => '— No specific contact —']
        ) ?>
    </div>

    <div class="col-12">
        <?= $form->field($model, 'interaction_at')->input('datetime-local') ?>
    </div>

    <div class="col-12">
        <?= $form->field($model, 'summary')->textarea(['rows' => 5, 'placeholder' => 'Summarise the interaction…']) ?>
    </div>
</div>

<div class="mt-3 d-flex gap-2">
    <?= Html::submitButton($model->isNewRecord ? 'Log Interaction' : 'Save Changes', ['class' => 'btn btn-dark']) ?>
    <?= Html::a('Cancel', ['/crm/company/view', 'id' => $model->company_id], ['class' => 'btn btn-outline-secondary']) ?>
</div>

<?php ActiveForm::end(); ?>
