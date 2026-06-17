<?php

/** @var yii\web\View $this */
/** @var common\models\Product $model */
/** @var array $categories  [id => name] */

use common\models\Product;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
?>

<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

<div class="row g-3">
    <div class="col-md-8">
        <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'autofocus' => true]) ?>
    </div>
    <div class="col-md-4">
        <?= $form->field($model, 'sku')->textInput(['maxlength' => true, 'placeholder' => 'e.g. HOPS-KEG-20L']) ?>
    </div>

    <div class="col-md-4">
        <?= $form->field($model, 'category_id')->dropDownList($categories, ['prompt' => '— Select Category —']) ?>
    </div>
    <div class="col-md-4">
        <?= $form->field($model, 'packaging_type')->dropDownList(Product::packagingLabels()) ?>
    </div>
    <div class="col-md-4">
        <?= $form->field($model, 'unit_price')->textInput(['type' => 'number', 'min' => '0', 'step' => '0.01']) ?>
    </div>

    <?php if (!$model->isNewRecord): ?>
    <div class="col-md-4">
        <?= $form->field($model, 'stock_qty')->textInput(['type' => 'number', 'min' => '0'])
            ->hint('Use the stock adjustment panel to change stock. This field is for correction only.') ?>
    </div>
    <?php endif; ?>

    <div class="col-md-4">
        <?= $form->field($model, 'is_active')->checkbox() ?>
    </div>

    <div class="col-12">
        <?= $form->field($model, 'description')->textarea(['rows' => 4]) ?>
    </div>

    <div class="col-12">
        <?= $form->field($model, 'imageFile')->fileInput(['accept' => 'image/*']) ?>
        <?php if (!$model->isNewRecord && $model->image): ?>
            <div class="mt-2 d-flex align-items-center gap-3">
                <img src="<?= Yii::$app->request->baseUrl . '/uploads/products/' . Html::encode($model->image) ?>"
                     alt="Current product image"
                     class="img-thumbnail" style="max-height: 100px; object-fit: contain;">
                <span class="text-muted small">Upload a new image to replace the current one.</span>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="mt-3 d-flex gap-2">
    <?= Html::submitButton($model->isNewRecord ? 'Create Product' : 'Save Changes', ['class' => 'btn btn-dark']) ?>
    <?= Html::a('Cancel', $model->isNewRecord ? ['index'] : ['view', 'id' => $model->id], ['class' => 'btn btn-outline-secondary']) ?>
</div>

<?php ActiveForm::end(); ?>
