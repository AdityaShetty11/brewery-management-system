<?php

/** @var yii\web\View $this */
/** @var common\models\ProductionOrder $model */
/** @var array $productList  [id => name] */
/** @var array $orderList    [id => order_number] */

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
?>

<?php $form = ActiveForm::begin(); ?>

<div class="row g-3">
    <div class="col-md-6">
        <?= $form->field($model, 'product_id')->dropDownList($productList, ['prompt' => '— Select Product —']) ?>
    </div>
    <div class="col-md-3">
        <?= $form->field($model, 'planned_qty')->textInput(['type' => 'number', 'min' => 1]) ?>
    </div>
    <div class="col-md-3">
        <?= $form->field($model, 'planned_date')->input('date') ?>
    </div>

    <div class="col-md-6">
        <?= $form->field($model, 'order_id')
            ->dropDownList($orderList, ['prompt' => '— No linked sales order —'])
            ->hint('Link this production run to a confirmed sales order (optional)') ?>
    </div>

    <div class="col-12">
        <?= $form->field($model, 'notes')->textarea(['rows' => 3]) ?>
    </div>
</div>

<div class="mt-3 d-flex gap-2">
    <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Save Changes', ['class' => 'btn btn-dark']) ?>
    <?= Html::a('Cancel', $model->isNewRecord ? ['index'] : ['view', 'id' => $model->id], ['class' => 'btn btn-outline-secondary']) ?>
</div>

<?php ActiveForm::end(); ?>
