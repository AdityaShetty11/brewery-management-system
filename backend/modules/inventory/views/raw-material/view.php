<?php

/** @var yii\web\View $this */
/** @var common\models\RawMaterial $model */
/** @var backend\modules\inventory\models\StockTransactionSearch $txSearch */
/** @var yii\data\ActiveDataProvider $txProvider */

use common\models\StockTransaction;
use yii\bootstrap5\ActiveForm;
use yii\grid\GridView;
use yii\helpers\Html;

$this->title = $model->name;
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h4 mb-0"><?= Html::encode($model->name) ?></h1>
        <small class="text-muted">Unit: <strong><?= Html::encode($model->unit) ?></strong></small>
    </div>
    <div class="d-flex gap-2">
        <?= Html::a('<i class="bi bi-pencil me-1"></i>Edit', ['update', 'id' => $model->id], ['class' => 'btn btn-outline-primary btn-sm']) ?>
        <?= Html::a('<i class="bi bi-arrow-left me-1"></i>Back', ['index'], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
    </div>
</div>

<div class="row g-4">

    <!-- ── Left: stock card + adjustment ────────────────────────────── -->
    <div class="col-md-4">

        <!-- Stock level card -->
        <div class="card shadow-sm mb-4 <?= $model->isLowStock() ? 'border-danger' : '' ?>">
            <div class="card-header fw-semibold <?= $model->isLowStock() ? 'text-danger' : '' ?>">
                <i class="bi bi-boxes me-1"></i>Stock Level
                <?php if ($model->isLowStock()): ?>
                    <span class="badge bg-danger ms-1">LOW</span>
                <?php endif; ?>
            </div>
            <div class="card-body text-center py-4">
                <div class="display-5 fw-bold <?= $model->isLowStock() ? 'text-danger' : 'text-success' ?>">
                    <?= number_format((float) $model->stock_qty, 3) ?>
                </div>
                <div class="text-muted"><?= Html::encode($model->unit) ?></div>
                <hr>
                <div class="small text-muted">
                    Reorder level: <strong><?= number_format((float) $model->reorder_level, 3) ?> <?= Html::encode($model->unit) ?></strong>
                </div>
            </div>
        </div>

        <!-- Manual adjustment card -->
        <div class="card shadow-sm mb-4">
            <div class="card-header fw-semibold"><i class="bi bi-sliders me-1"></i>Manual Adjustment</div>
            <div class="card-body">
                <?php $form = ActiveForm::begin([
                    'action' => ['adjust-stock', 'id' => $model->id],
                    'method' => 'post',
                ]); ?>

                <div class="mb-2">
                    <label class="form-label small">Transaction Type</label>
                    <select name="type" class="form-select form-select-sm">
                        <option value="<?= StockTransaction::TYPE_IN ?>">
                            Stock In (receiving delivery)
                        </option>
                        <option value="<?= StockTransaction::TYPE_OUT ?>">
                            Stock Out (write-off / spoilage)
                        </option>
                        <option value="<?= StockTransaction::TYPE_ADJUSTMENT ?>">
                            Adjustment (stock count correction)
                        </option>
                    </select>
                </div>

                <div class="mb-2">
                    <label class="form-label small">
                        Quantity (<?= Html::encode($model->unit) ?>)
                    </label>
                    <input type="number" name="qty"
                        class="form-control form-control-sm"
                        min="0.001" step="0.001" value="1" required>
                </div>

                <div class="mb-3">
                    <label class="form-label small">Notes</label>
                    <input type="text" name="notes"
                        class="form-control form-control-sm"
                        placeholder="e.g. Received from supplier, Spoilage">
                </div>

                <?= Html::submitButton(
                    '<i class="bi bi-check2-circle me-1"></i>Apply',
                    ['class' => 'btn btn-dark w-100']
                ) ?>

                <?php ActiveForm::end(); ?>
            </div>
        </div>

        <!-- Material details card -->
        <div class="card shadow-sm">
            <div class="card-header fw-semibold"><i class="bi bi-info-circle me-1"></i>Details</div>
            <div class="card-body">
                <dl class="row mb-0 small">
                    <dt class="col-5 text-muted">Description</dt>
                    <dd class="col-7"><?= Html::encode($model->description ?? '—') ?></dd>

                    <dt class="col-5 text-muted">Created</dt>
                    <dd class="col-7"><?= Html::encode($model->created_at) ?></dd>

                    <dt class="col-5 text-muted">Updated</dt>
                    <dd class="col-7"><?= Html::encode($model->updated_at) ?></dd>
                </dl>
            </div>
        </div>
    </div>

    <!-- ── Right: transaction history ───────────────────────────────── -->
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header fw-semibold">
                <i class="bi bi-clock-history me-1"></i>Stock Transaction History
            </div>
            <div class="card-body p-0">
                <?= GridView::widget([
                    'dataProvider' => $txProvider,
                    'filterModel'  => $txSearch,
                    'tableOptions' => ['class' => 'table table-hover mb-0'],
                    'layout'       => '{items}{pager}',
                    'emptyText'    => '<p class="text-muted p-3 mb-0">No transactions recorded yet.</p>',
                    'columns' => [
                        [
                            'attribute' => 'created_at',
                            'label'     => 'Date',
                            'filter'    => false,
                            'format'    => 'raw',
                            'value'     => fn($t) => '<small>' . Html::encode($t->created_at) . '</small>',
                        ],
                        [
                            'attribute' => 'transaction_type',
                            'filter'    => StockTransaction::typeLabels(),
                            'format'    => 'raw',
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
                            'value'     => function ($t) use ($model) {
                                $sign  = $t->transaction_type === 'in' ? '+' : '−';
                                $color = $t->transaction_type === 'in' ? 'text-success' : 'text-danger';
                                return "<span class=\"fw-semibold {$color}\">{$sign}{$t->quantity} {$model->unit}</span>";
                            },
                        ],
                        [
                            'attribute' => 'reference_type',
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
    </div>

</div>
