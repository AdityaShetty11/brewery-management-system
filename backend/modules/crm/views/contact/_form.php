<?php

/** @var yii\web\View $this */
/** @var common\models\CustomerContact $model */
/** @var array $companies  [id => name] */

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
?>

<?php $form = ActiveForm::begin(); ?>

<div class="row g-3">
    <div class="col-12">
        <?= $form->field($model, 'company_id')->dropDownList($companies, ['prompt' => '— Select Company —']) ?>
    </div>

    <div class="col-md-6">
        <?= $form->field($model, 'first_name')->textInput(['maxlength' => true, 'autofocus' => true]) ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($model, 'last_name')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="col-md-6">
        <?= $form->field($model, 'role')->textInput(['maxlength' => true, 'placeholder' => 'e.g. Purchasing Manager']) ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="col-12">
        <?= $form->field($model, 'email')->input('email', ['maxlength' => true]) ?>
    </div>

    <div class="col-12">
        <?= $form->field($model, 'notes')->textarea(['rows' => 3]) ?>
    </div>
</div>

<div class="mt-3 d-flex gap-2">
    <?= Html::submitButton($model->isNewRecord ? 'Add Contact' : 'Save Changes', ['class' => 'btn btn-dark']) ?>
    <?php if (!$model->isNewRecord): ?>
        <?= Html::a('Cancel', ['/crm/company/view', 'id' => $model->company_id], ['class' => 'btn btn-outline-secondary']) ?>
    <?php endif; ?>
</div>

<?php ActiveForm::end(); ?>
