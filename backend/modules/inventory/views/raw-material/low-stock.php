<?php

/** @var yii\web\View $this */
/** @var backend\modules\inventory\models\RawMaterialSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Low Stock Alerts';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0 text-danger">
        <i class="bi bi-exclamation-triangle-fill me-2"></i><?= Html::encode($this->title) ?>
    </h1>
    <?= Html::a('<i class="bi bi-arrow-left me-1"></i>All Materials', ['index'], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
</div>

<div class="alert alert-warning">
    <i class="bi bi-info-circle me-1"></i>
    These materials are at or below their reorder level. Replenish stock to avoid production delays.
</div>

<div class="card shadow-sm border-danger">
    <div class="card-body p-0">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel'  => $searchModel,
            'tableOptions' => ['class' => 'table table-hover mb-0'],
            'layout'       => '{items}{pager}',
            'emptyText'    => '<div class="p-4 text-center text-success"><i class="bi bi-check-circle-fill me-2 fs-4"></i>All materials are adequately stocked.</div>',
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                [
                    'attribute' => 'name',
                    'format'    => 'raw',
                    'value'     => fn($m) => Html::a(Html::encode($m->name), ['view', 'id' => $m->id]),
                ],

                'unit',

                [
                    'label'  => 'Current Stock',
                    'format' => 'raw',
                    'filter' => false,
                    'value'  => fn($m) => "<strong class=\"text-danger\">{$m->stock_qty} {$m->unit}</strong>",
                ],

                [
                    'attribute' => 'reorder_level',
                    'filter'    => false,
                    'value'     => fn($m) => "{$m->reorder_level} {$m->unit}",
                ],

                [
                    'label'  => 'Deficit',
                    'format' => 'raw',
                    'filter' => false,
                    'value'  => function ($m) {
                        $deficit = $m->reorder_level - $m->stock_qty;
                        return $deficit > 0
                            ? "<span class=\"text-danger\">−{$deficit} {$m->unit}</span>"
                            : '<span class="text-warning">At threshold</span>';
                    },
                ],

                [
                    'class'    => ActionColumn::class,
                    'template' => '{view}',
                    'urlCreator' => fn($action, $model) => Url::to([$action, 'id' => $model->id]),
                    'buttons' => [
                        'view' => fn($url, $m) => Html::a(
                            '<i class="bi bi-sliders me-1"></i>Adjust',
                            ['view', 'id' => $m->id],
                            ['class' => 'btn btn-sm btn-warning']
                        ),
                    ],
                ],
            ],
        ]) ?>
    </div>
</div>
