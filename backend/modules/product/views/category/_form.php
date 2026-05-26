<?php

/** @var yii\web\View $this */
/** @var common\models\ProductCategory $model */

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
?>

<?php $form = ActiveForm::begin(); ?>

<div class="row g-3">
    <div class="col-md-6">
        <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'autofocus' => true]) ?>
    </div>
    <div class="col-12">
        <?= $form->field($model, 'description')->textarea(['rows' => 3]) ?>
    </div>
</div>

<div class="mt-3 d-flex gap-2">
    <?= Html::submitButton($model->isNewRecord ? 'Create Category' : 'Save Changes', ['class' => 'btn btn-dark']) ?>
    <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
</div>

<?php ActiveForm::end(); ?>
