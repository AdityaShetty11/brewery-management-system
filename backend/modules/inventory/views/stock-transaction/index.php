<?php

/** @var yii\web\View $this */
/** @var backend\modules\inventory\models\StockTransactionSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

use common\models\StockTransaction;
use yii\grid\GridView;
use yii\helpers\Html;

$this->title = 'Inventory Ledger';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h4 mb-0"><i class="bi bi-journal-text me-2"></i>Inventory Ledger</h1>
        <small class="text-muted">Full history of all stock movements across raw materials and finished goods.</small>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel'  => $searchModel,
            'tableOptions' => ['class' => 'table table-hover mb-0'],
            'layout'       => '{summary}<div class="table-responsive">{items}</div>{pager}',
            'summaryOptions' => ['class' => 'text-muted small px-3 pt-3'],
            'emptyText'    => '<p class="text-muted p-4 mb-0 text-center">No transactions found.</p>',
            'columns' => [
                [
                    'attribute' => 'created_at',
                    'label'     => 'Date / Time',
                    'filter'    => false,
                    'format'    => 'raw',
                    'headerOptions' => ['style' => 'width:160px'],
                    'value'     => fn($t) => '<small class="text-muted">' . Html::encode($t->created_at) . '</small>',
                ],
                [
                    'attribute' => 'item_type',
                    'label'     => 'Item Type',
                    'filter'    => StockTransaction::itemTypeLabels(),
                    'format'    => 'raw',
                    'headerOptions' => ['style' => 'width:140px'],
                    'value'     => function ($t) {
                        $class = $t->item_type === StockTransaction::ITEM_RAW
                            ? 'bg-warning text-dark'
                            : 'bg-primary';
                        $label = StockTransaction::itemTypeLabels()[$t->item_type] ?? $t->item_type;
                        return "<span class=\"badge {$class}\">{$label}</span>";
                    },
                ],
                [
                    'label'  => 'Item',
                    'filter' => false,
                    'format' => 'raw',
                    'value'  => function ($t) {
                        if ($t->item_type === StockTransaction::ITEM_RAW) {
                            $mat = $t->rawMaterial;
                            if ($mat) {
                                return Html::a(Html::encode($mat->name),
                                    ['/inventory/raw-material/view', 'id' => $mat->id],
                                    ['class' => 'text-decoration-none']);
                            }
                        } elseif ($t->item_type === StockTransaction::ITEM_FINISHED) {
                            $prod = $t->product;
                            if ($prod) {
                                return Html::a(Html::encode($prod->name),
                                    ['/product/product/view', 'id' => $prod->id],
                                    ['class' => 'text-decoration-none']);
                            }
                        }
                        return '<span class="text-muted">—</span>';
                    },
                ],
                [
                    'attribute' => 'transaction_type',
                    'label'     => 'Type',
                    'filter'    => StockTransaction::typeLabels(),
                    'format'    => 'raw',
                    'headerOptions' => ['style' => 'width:120px'],
                    'value'     => function ($t) {
                        $class = match ($t->transaction_type) {
                            'in'   => 'bg-success',
                            'out'  => 'bg-danger',
                            default => 'bg-secondary',
                        };
                        return "<span class=\"badge {$class}\">{$t->getTypeLabel()}</span>";
                    },
                ],
                [
                    'attribute' => 'quantity',
                    'filter'    => false,
                    'format'    => 'raw',
                    'headerOptions' => ['style' => 'width:120px'],
                    'contentOptions' => ['class' => 'text-end'],
                    'headerOptions' => ['class' => 'text-end', 'style' => 'width:120px'],
                    'value'     => function ($t) {
                        $sign  = $t->transaction_type === 'in' ? '+' : '−';
                        $color = $t->transaction_type === 'in' ? 'text-success' : 'text-danger';
                        return "<span class=\"fw-semibold {$color}\">{$sign}{$t->quantity}</span>";
                    },
                ],
                [
                    'attribute' => 'reference_type',
                    'label'     => 'Reference',
                    'filter'    => false,
                    'format'    => 'raw',
                    'value'     => fn($t) => $t->reference_type
                        ? '<span class="badge bg-light text-dark border">'
                            . Html::encode($t->reference_type)
                            . ($t->reference_id ? ' #' . $t->reference_id : '')
                            . '</span>'
                        : '—',
                ],
                [
                    'label'  => 'Notes',
                    'filter' => false,
                    'value'  => fn($t) => $t->notes ?? '—',
                ],
                [
                    'label'  => 'By',
                    'filter' => false,
                    'format' => 'raw',
                    'value'  => fn($t) => '<small class="text-muted">'
                        . Html::encode($t->createdBy->username ?? 'system')
                        . '</small>',
                ],
            ],
        ]) ?>
    </div>
</div>
