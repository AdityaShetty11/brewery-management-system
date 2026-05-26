<?php

/** @var yii\web\View $this */
/** @var common\models\Batch $model */
/** @var array $brewMasters  [id => username] */

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
?>

<?php $form = ActiveForm::begin(); ?>

<div class="row g-3">
    <div class="col-md-4">
        <?= $form->field($model, 'batch_size')->textInput(['type' => 'number', 'min' => '0.01', 'step' => '0.01', 'autofocus' => true])
            ->label('Batch Size (litres)') ?>
    </div>
    <div class="col-md-4">
        <?= $form->field($model, 'brew_master_id')->dropDownList($brewMasters, ['prompt' => '— Assign Brew Master —']) ?>
    </div>
    <div class="col-md-4">
        <?= $form->field($model, 'brew_date')->input('date') ?>
    </div>

    <?php if (!$model->isNewRecord): ?>
    <div class="col-md-4">
        <?= $form->field($model, 'actual_yield')->textInput(['type' => 'number', 'min' => '0'])
            ->hint('Fill in when batch completes') ?>
    </div>
    <div class="col-md-4">
        <?= $form->field($model, 'completion_date')->input('date') ?>
    </div>
    <?php endif; ?>

    <div class="col-12">
        <?= $form->field($model, 'notes')->textarea(['rows' => 3]) ?>
    </div>
</div>

<div class="mt-3 d-flex gap-2">
    <?= Html::submitButton($model->isNewRecord ? 'Create Batch' : 'Save', ['class' => 'btn btn-dark']) ?>
    <?php if ($model->isNewRecord): ?>
        <?= Html::a('Cancel', ['/production/production-order/view', 'id' => $model->production_order_id], ['class' => 'btn btn-outline-secondary']) ?>
    <?php else: ?>
        <?= Html::a('Cancel', ['/production/batch/view', 'id' => $model->id], ['class' => 'btn btn-outline-secondary']) ?>
    <?php endif; ?>
</div>

<?php ActiveForm::end(); ?>
