<?php

/** @var yii\web\View $this */
/** @var backend\modules\inventory\models\RawMaterialSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

use common\models\RawMaterial;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Raw Materials';

// Count low stock for the alert badge
$lowStockCount = RawMaterial::find()
    ->andWhere('stock_qty <= reorder_level')
    ->count();
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="d-flex align-items-center gap-3">
        <h1 class="h4 mb-0"><i class="bi bi-boxes me-2 text-secondary"></i><?= Html::encode($this->title) ?></h1>
        <?php if ($lowStockCount > 0): ?>
            <?= Html::a(
                "<i class=\"bi bi-exclamation-triangle-fill me-1\"></i>{$lowStockCount} Low Stock",
                ['low-stock'],
                ['class' => 'badge bg-danger text-decoration-none fs-6']
            ) ?>
        <?php endif; ?>
    </div>
    <div class="d-flex gap-2">
        <?= Html::a('<i class="bi bi-clock-history me-1"></i>Transaction Log',
            ['/inventory/stock-transaction/index'],
            ['class' => 'btn btn-outline-secondary btn-sm']
        ) ?>
        <?= Html::a('<i class="bi bi-plus-lg me-1"></i>Add Material',
            ['create'],
            ['class' => 'btn btn-dark btn-sm']
        ) ?>
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

                [
                    'attribute' => 'name',
                    'format'    => 'raw',
                    'value'     => fn($m) => Html::a(Html::encode($m->name), ['view', 'id' => $m->id]),
                ],

                'unit',

                [
                    'attribute' => 'stock_qty',
                    'filter'    => false,
                    'format'    => 'raw',
                    'value'     => function ($m) {
                        $badge = $m->isLowStock() ? 'bg-danger' : 'bg-success';
                        $icon  = $m->isLowStock() ? '<i class="bi bi-exclamation-triangle-fill me-1"></i>' : '';
                        return "<span class=\"badge {$badge}\">{$icon}{$m->stock_qty} {$m->unit}</span>";
                    },
                ],

                [
                    'attribute' => 'reorder_level',
                    'filter'    => false,
                    'value'     => fn($m) => "{$m->reorder_level} {$m->unit}",
                ],

                [
                    'label'  => 'Status',
                    'format' => 'raw',
                    'filter' => false,
                    'value'  => fn($m) => $m->isLowStock()
                        ? '<span class="badge bg-danger">Low Stock</span>'
                        : '<span class="badge bg-success">OK</span>',
                ],

                [
                    'class'    => ActionColumn::class,
                    'template' => '{view} {update} {delete}',
                    'urlCreator' => fn($action, $model) => Url::to([$action, 'id' => $model->id]),
                    'buttons' => [
                        'view'   => fn($url, $m) => Html::a('<i class="bi bi-eye"></i>',   $url, ['class' => 'btn btn-sm btn-outline-secondary me-1']),
                        'update' => fn($url, $m) => Html::a('<i class="bi bi-pencil"></i>', $url, ['class' => 'btn btn-sm btn-outline-primary me-1']),
                        'delete' => fn($url, $m) => Html::a('<i class="bi bi-trash"></i>',  $url, [
                            'class' => 'btn btn-sm btn-outline-danger',
                            'data'  => ['confirm' => "Delete \"{$m->name}\"?", 'method' => 'post'],
                        ]),
                    ],
                ],
            ],
        ]) ?>
    </div>
</div>
