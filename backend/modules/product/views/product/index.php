<?php

/** @var yii\web\View $this */
/** @var backend\modules\product\models\ProductSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

use common\models\Product;
use common\models\ProductCategory;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title    = 'Products';
$categoryList   = ProductCategory::find()->select(['name', 'id'])->indexBy('id')->column();
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0"><i class="bi bi-cup-straw me-2 text-secondary"></i><?= Html::encode($this->title) ?></h1>
    <div>
        <?= Html::a('<i class="bi bi-plus-lg me-1"></i>Add Product', ['create'], ['class' => 'btn btn-dark btn-sm']) ?>
        <?= Html::a('<i class="bi bi-tags me-1"></i>Categories', ['/product/category/index'], ['class' => 'btn btn-outline-secondary btn-sm ms-1']) ?>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel'  => $searchModel,
            'tableOptions' => ['class' => 'table table-hover table-striped mb-0'],
            'layout'       => '{items}{pager}',
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                'sku',

                [
                    'attribute' => 'name',
                    'format'    => 'raw',
                    'value'     => fn($m) => Html::a(Html::encode($m->name), ['view', 'id' => $m->id]),
                ],

                [
                    'attribute' => 'category_id',
                    'filter'    => $categoryList,
                    'value'     => fn($m) => $m->category->name ?? '—',
                ],

                [
                    'attribute' => 'packaging_type',
                    'filter'    => Product::packagingLabels(),
                    'format'    => 'raw',
                    'value'     => fn($m) => '<i class="bi ' . $m->getPackagingIcon() . ' me-1"></i>' . $m->getPackagingLabel(),
                ],

                [
                    'attribute' => 'unit_price',
                    'format'    => 'raw',
                    'value'     => fn($m) => $m->getFormattedPrice(),
                    'filter'    => false,
                ],

                [
                    'attribute' => 'stock_qty',
                    'format'    => 'raw',
                    'filter'    => false,
                    'value'     => function ($m) {
                        $badge = $m->isLowStock() ? 'bg-danger' : 'bg-success';
                        return "<span class=\"badge {$badge}\">{$m->stock_qty}</span>";
                    },
                ],

                [
                    'attribute' => 'is_active',
                    'filter'    => ['1' => 'Active', '0' => 'Inactive'],
                    'format'    => 'raw',
                    'value'     => fn($m) => $m->is_active
                        ? '<span class="badge bg-success">Active</span>'
                        : '<span class="badge bg-secondary">Inactive</span>',
                ],

                [
                    'class'    => ActionColumn::class,
                    'template' => '{view} {update} {delete}',
                    'urlCreator' => fn($action, $model) => Url::to([$action, 'id' => $model->id]),
                    'buttons' => [
                        'view'   => fn($url, $model) => Html::a('<i class="bi bi-eye"></i>', $url, ['class' => 'btn btn-sm btn-outline-secondary me-1']),
                        'update' => fn($url, $model) => Html::a('<i class="bi bi-pencil"></i>', $url, ['class' => 'btn btn-sm btn-outline-primary me-1']),
                        'delete' => fn($url, $model) => Html::a('<i class="bi bi-trash"></i>', $url, [
                            'class' => 'btn btn-sm btn-outline-danger',
                            'data'  => ['confirm' => "Delete \"{$model->name}\"?", 'method' => 'post'],
                        ]),
                    ],
                ],
            ],
        ]) ?>
    </div>
</div>
