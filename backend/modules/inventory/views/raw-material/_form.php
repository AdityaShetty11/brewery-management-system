<?php

/** @var yii\web\View $this */
/** @var common\models\RawMaterial $model */

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

?>
<?php $form = ActiveForm::begin(['id' => 'raw-material-form']); ?>

<div class="row g-3">
    <div class="col-md-6">
        <?= $form->field($model, 'name')->textInput([
            'maxlength' => true,
            'placeholder' => 'e.g. Pale Malt, Cascade Hops',
        ]) ?>
    </div>

    <div class="col-md-3">
        <?= $form->field($model, 'unit')->textInput([
            'maxlength' => true,
            'placeholder' => 'e.g. kg, L, g',
        ])->hint('The unit of measure for all stock quantities.') ?>
    </div>

    <div class="col-md-3">
        <?= $form->field($model, 'reorder_level')->input('number', [
            'min' => '0',
            'step' => '0.001',
            'placeholder' => '0.000',
        ])->hint('Alert is shown when stock falls below this level.') ?>
    </div>

    <div class="col-12">
        <?= $form->field($model, 'description')->textarea([
            'rows' => 3,
            'placeholder' => 'Optional description or notes about this material',
        ]) ?>
    </div>
</div>

<hr>

<div class="d-flex gap-2">
    <?= Html::submitButton(
        '<i class="bi bi-check2-circle me-1"></i>' . ($model->isNewRecord ? 'Create Material' : 'Save Changes'),
        ['class' => 'btn btn-dark']
    ) ?>
    <?= Html::a('<i class="bi bi-x-circle me-1"></i>Cancel', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
</div>

<?php ActiveForm::end(); ?>
