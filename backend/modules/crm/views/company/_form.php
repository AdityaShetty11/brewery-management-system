<?php

/** @var yii\web\View $this */
/** @var common\models\CustomerCompany $model */
/** @var yii\bootstrap5\ActiveForm $form */

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
?>

<?php $form = ActiveForm::begin(); ?>

<div class="row g-3">
    <div class="col-md-8">
        <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'autofocus' => true]) ?>
    </div>
    <div class="col-md-4">
        <?= $form->field($model, 'industry')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="col-md-6">
        <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($model, 'email')->input('email', ['maxlength' => true]) ?>
    </div>

    <div class="col-12">
        <?= $form->field($model, 'address')->textarea(['rows' => 2]) ?>
    </div>

    <div class="col-md-6">
        <?= $form->field($model, 'city')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($model, 'country')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="col-12">
        <?= $form->field($model, 'notes')->textarea(['rows' => 3]) ?>
    </div>
</div>

<div class="mt-3 d-flex gap-2">
    <?= Html::submitButton($model->isNewRecord ? 'Create Company' : 'Save Changes', ['class' => 'btn btn-dark']) ?>
    <?= Html::a('Cancel', $model->isNewRecord ? ['index'] : ['view', 'id' => $model->id], ['class' => 'btn btn-outline-secondary']) ?>
</div>

<?php ActiveForm::end(); ?>
