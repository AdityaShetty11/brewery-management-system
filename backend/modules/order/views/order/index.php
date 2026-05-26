<?php

/** @var yii\web\View $this */
/** @var backend\modules\order\models\OrderSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

use common\models\Order;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Orders';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0"><i class="bi bi-bag-check me-2 text-secondary"></i><?= Html::encode($this->title) ?></h1>
</div>

<!-- ── Status summary pills ──────────────────────────────────────── -->
<div class="d-flex flex-wrap gap-2 mb-3">
    <?php foreach (Order::statusLabels() as $key => $label):
        $badgeClass = Order::statusBadgeClass()[$key];
    ?>
        <?= Html::a(
            "<span class=\"badge {$badgeClass}\">{$label}</span>",
            ['index', 'OrderSearch[status]' => $key],
            ['class' => 'text-decoration-none']
        ) ?>
    <?php endforeach; ?>
    <?= Html::a('<span class="badge bg-dark">All</span>', ['index'], ['class' => 'text-decoration-none']) ?>
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
                    'attribute' => 'order_number',
                    'format'    => 'raw',
                    'value'     => fn($m) => Html::a(Html::encode($m->order_number), ['view', 'id' => $m->id]),
                ],

                [
                    'attribute' => 'customerUsername',
                    'label'     => 'Customer',
                    'value'     => fn($m) => $m->customer->username ?? '—',
                ],

                [
                    'attribute' => 'status',
                    'filter'    => Order::statusLabels(),
                    'format'    => 'raw',
                    'value'     => fn($m) => $m->getStatusBadge(),
                ],

                [
                    'label'  => 'Items',
                    'format' => 'raw',
                    'value'  => fn($m) => '<span class="badge bg-secondary">' . count($m->items) . '</span>',
                    'filter' => false,
                ],

                [
                    'attribute' => 'total_amount',
                    'format'    => 'raw',
                    'filter'    => false,
                    'value'     => fn($m) => '<strong>' . $m->getFormattedTotal() . '</strong>',
                ],

                [
                    'attribute' => 'created_at',
                    'format'    => 'datetime',
                    'filter'    => false,
                ],

                [
                    'class'    => ActionColumn::class,
                    'template' => '{view} {delete}',
                    'urlCreator' => fn($action, $model) => Url::to([$action, 'id' => $model->id]),
                    'buttons' => [
                        'view'   => fn($url, $model) => Html::a('<i class="bi bi-eye"></i>', $url, ['class' => 'btn btn-sm btn-outline-secondary me-1']),
                        'delete' => fn($url, $model) => in_array($model->status, [Order::STATUS_DRAFT, Order::STATUS_CANCELLED])
                            ? Html::a('<i class="bi bi-trash"></i>', $url, [
                                'class' => 'btn btn-sm btn-outline-danger',
                                'data'  => ['confirm' => "Delete order {$model->order_number}?", 'method' => 'post'],
                            ])
                            : '',
                    ],
                ],
            ],
        ]) ?>
    </div>
</div>
