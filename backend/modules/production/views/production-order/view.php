<?php

/** @var yii\web\View $this */
/** @var common\models\ProductionOrder $model */

use common\models\Batch;
use common\models\ProductionOrder;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

$this->title = $model->reference;
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h4 mb-0"><?= Html::encode($model->reference) ?></h1>
        <small class="text-muted">
            Product: <strong><?= Html::encode($model->product->name ?? '—') ?></strong>
            &nbsp;&middot;&nbsp; Target: <strong><?= $model->planned_qty ?> units</strong>
        </small>
    </div>
    <div class="d-flex gap-2 align-items-center">
        <?= $model->getStatusBadge() ?>
        <?= Html::a('<i class="bi bi-pencil me-1"></i>Edit', ['update', 'id' => $model->id], ['class' => 'btn btn-outline-primary btn-sm']) ?>
        <?= Html::a('<i class="bi bi-arrow-left me-1"></i>Back', ['index'], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
    </div>
</div>

<div class="row g-4">

    <!-- ── Batches ───────────────────────────────────────────────────── -->
    <div class="col-md-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-semibold"><i class="bi bi-layers me-1"></i>Batches</span>
                <?php if (in_array($model->status, [ProductionOrder::STATUS_PLANNED, ProductionOrder::STATUS_IN_PROGRESS])): ?>
                    <?= Html::a('<i class="bi bi-plus-lg me-1"></i>New Batch',
                        ['/production/batch/create', 'production_order_id' => $model->id],
                        ['class' => 'btn btn-sm btn-outline-dark']
                    ) ?>
                <?php endif; ?>
            </div>
            <div class="card-body p-0">
                <?php if (empty($model->batches)): ?>
                    <p class="p-3 text-muted mb-0">No batches yet. Create the first batch to begin brewing.</p>
                <?php else: ?>
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Batch #</th><th>Status</th><th>Size (L)</th>
                                <th>Brew Date</th><th>Yield</th><th>Brew Master</th><th></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($model->batches as $batch): ?>
                            <tr>
                                <td><?= Html::a(Html::encode($batch->batch_number), ['/production/batch/view', 'id' => $batch->id], ['class' => 'fw-semibold']) ?></td>
                                <td><?= $batch->getStatusBadge() ?></td>
                                <td><?= $batch->batch_size ?></td>
                                <td><small class="text-muted"><?= Html::encode($batch->brew_date ?? '—') ?></small></td>
                                <td><?= $batch->actual_yield !== null ? $batch->actual_yield . ' units' : '—' ?></td>
                                <td><small><?= Html::encode($batch->brewMaster->username ?? '—') ?></small></td>
                                <td>
                                    <?= Html::a('<i class="bi bi-eye"></i>', ['/production/batch/view', 'id' => $batch->id], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ── Right panel ───────────────────────────────────────────────── -->
    <div class="col-md-4">

        <!-- Workflow transitions -->
        <div class="card shadow-sm mb-4">
            <div class="card-header fw-semibold"><i class="bi bi-arrow-right-circle me-1"></i>Workflow</div>
            <div class="card-body">
                <p class="text-muted small mb-3">Status: <?= $model->getStatusBadge() ?></p>

                <?php foreach (ProductionOrder::TRANSITIONS[$model->status] ?? [] as $nextStatus): ?>
                    <?php $isCancel = $nextStatus === ProductionOrder::STATUS_CANCELLED; ?>
                    <?php $form = ActiveForm::begin(['action' => ['transition', 'id' => $model->id], 'method' => 'post']); ?>
                        <input type="hidden" name="status" value="<?= $nextStatus ?>">
                        <?= Html::submitButton(
                            ProductionOrder::statusLabels()[$nextStatus],
                            ['class' => 'btn w-100 mb-2 ' . ($isCancel ? 'btn-outline-danger' : 'btn-primary')]
                        ) ?>
                    <?php ActiveForm::end(); ?>
                <?php endforeach; ?>

                <?php if (empty(ProductionOrder::TRANSITIONS[$model->status])): ?>
                    <p class="text-muted small">No further transitions.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Details -->
        <div class="card shadow-sm">
            <div class="card-header fw-semibold"><i class="bi bi-info-circle me-1"></i>Details</div>
            <div class="card-body">
                <dl class="row mb-0 small">
                    <dt class="col-5 text-muted">Reference</dt>
                    <dd class="col-7"><?= Html::encode($model->reference) ?></dd>

                    <dt class="col-5 text-muted">Product</dt>
                    <dd class="col-7"><?= Html::encode($model->product->name ?? '—') ?></dd>

                    <dt class="col-5 text-muted">Target Qty</dt>
                    <dd class="col-7"><?= $model->planned_qty ?> units</dd>

                    <dt class="col-5 text-muted">Planned Date</dt>
                    <dd class="col-7"><?= Html::encode($model->planned_date ?? '—') ?></dd>

                    <dt class="col-5 text-muted">Sales Order</dt>
                    <dd class="col-7"><?= $model->salesOrder ? Html::encode($model->salesOrder->order_number) : '—' ?></dd>

                    <dt class="col-5 text-muted">Completed</dt>
                    <dd class="col-7"><?= Html::encode($model->completed_at ?? '—') ?></dd>

                    <?php if ($model->notes): ?>
                        <dt class="col-5 text-muted">Notes</dt>
                        <dd class="col-7"><?= Html::encode($model->notes) ?></dd>
                    <?php endif; ?>
                </dl>
            </div>
        </div>
    </div>

</div>
