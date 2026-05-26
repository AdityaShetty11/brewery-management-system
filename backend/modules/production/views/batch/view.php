<?php

/** @var yii\web\View $this */
/** @var common\models\Batch $batch  (alias: $model) */
/** @var array $materialMap  [id => 'name (unit)'] */
/** @var array $brewMasters  [id => username] */

use common\models\Batch;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

$model       = $batch;
$this->title = $model->batch_number;

$statusOrder  = array_keys(Batch::statusLabels());
$currentIndex = array_search($model->status, $statusOrder, true);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h4 mb-0"><?= Html::encode($model->batch_number) ?></h1>
        <small class="text-muted">
            Production Order:
            <?= Html::a(
                Html::encode($model->productionOrder->reference ?? '—'),
                ['/production/production-order/view', 'id' => $model->production_order_id],
                ['class' => 'link-secondary']
            ) ?>
            &nbsp;&middot;&nbsp; Product: <strong><?= Html::encode($model->productionOrder->product->name ?? '—') ?></strong>
        </small>
    </div>
    <div class="d-flex gap-2">
        <?= $model->getStatusBadge() ?>
        <?= Html::a('<i class="bi bi-arrow-left me-1"></i>Back',
            ['/production/production-order/view', 'id' => $model->production_order_id],
            ['class' => 'btn btn-outline-secondary btn-sm']
        ) ?>
    </div>
</div>

<!-- ── Progress bar ──────────────────────────────────────────────── -->
<div class="card shadow-sm mb-4">
    <div class="card-body py-3">
        <div class="d-flex justify-content-between">
            <?php foreach (Batch::statusLabels() as $key => $label):
                $idx   = array_search($key, $statusOrder, true);
                $done  = $currentIndex !== false && $idx <= $currentIndex;
                $icon  = Batch::statusIcons()[$key] ?? 'bi-circle';
            ?>
                <div class="text-center flex-fill">
                    <div class="fs-4 <?= $done ? 'text-success' : 'text-secondary' ?>">
                        <i class="bi <?= $icon ?>"></i>
                    </div>
                    <div class="small <?= $key === $model->status ? 'fw-bold' : 'text-muted' ?>"><?= $label ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="row g-4">

    <!-- ── Ingredients ───────────────────────────────────────────────── -->
    <div class="col-md-7">
        <div class="card shadow-sm mb-4">
            <div class="card-header fw-semibold">
                <i class="bi bi-basket me-1"></i>Ingredients
                <?php if ($model->status === Batch::STATUS_PLANNED): ?>
                    <small class="text-muted fw-normal">(deducted from stock when brewing starts)</small>
                <?php elseif ($model->status === Batch::STATUS_BREWING): ?>
                    <small class="text-success fw-normal"><i class="bi bi-check-circle me-1"></i>Already deducted</small>
                <?php endif; ?>
            </div>
            <div class="card-body p-0">
                <?php if (empty($model->ingredients)): ?>
                    <p class="p-3 text-muted mb-0">No ingredients added yet.</p>
                <?php else: ?>
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr><th>Material</th><th>Quantity</th><th>Unit</th>
                            <?php if ($model->status === Batch::STATUS_PLANNED): ?><th></th><?php endif; ?></tr>
                        </thead>
                        <tbody>
                        <?php foreach ($model->ingredients as $ing): ?>
                            <tr>
                                <td><?= Html::encode($ing->rawMaterial->name ?? '—') ?></td>
                                <td><?= $ing->quantity ?></td>
                                <td><small class="text-muted"><?= Html::encode($ing->rawMaterial->unit ?? '') ?></small></td>
                                <?php if ($model->status === Batch::STATUS_PLANNED): ?>
                                    <td>
                                        <?= Html::a('<i class="bi bi-trash"></i>',
                                            ['/production/batch/remove-ingredient', 'id' => $ing->id],
                                            ['class' => 'btn btn-sm btn-outline-danger',
                                             'data'  => ['method' => 'post', 'confirm' => 'Remove?']]
                                        ) ?>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <!-- Add ingredient (planned only) -->
            <?php if ($model->status === Batch::STATUS_PLANNED): ?>
            <div class="card-footer bg-light">
                <?php $form = ActiveForm::begin(['action' => ['/production/batch/add-ingredient', 'batchId' => $model->id], 'method' => 'post']); ?>
                <div class="row g-2 align-items-end">
                    <div class="col-md-6">
                        <label class="form-label small">Raw Material</label>
                        <select name="raw_material_id" class="form-select form-select-sm" required>
                            <option value="">— Select —</option>
                            <?php foreach ($materialMap as $matId => $matLabel): ?>
                                <option value="<?= $matId ?>"><?= Html::encode($matLabel) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small">Quantity</label>
                        <input type="number" name="quantity" class="form-control form-control-sm" min="0.001" step="0.001" value="1" required>
                    </div>
                    <div class="col-md-3">
                        <?= Html::submitButton('<i class="bi bi-plus-lg"></i> Add', ['class' => 'btn btn-dark btn-sm w-100']) ?>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ── Right panel ───────────────────────────────────────────────── -->
    <div class="col-md-5">

        <!-- Workflow -->
        <div class="card shadow-sm mb-4">
            <div class="card-header fw-semibold"><i class="bi bi-arrow-right-circle me-1"></i>Advance Batch</div>
            <div class="card-body">
                <?php $nextStatus = $model->getNextStatus(); ?>

                <?php if ($nextStatus): ?>
                    <?php $isComplete = $nextStatus === Batch::STATUS_COMPLETED; ?>

                    <?php $form = ActiveForm::begin(['action' => ['/production/batch/transition', 'id' => $model->id], 'method' => 'post']); ?>
                        <input type="hidden" name="status" value="<?= $nextStatus ?>">

                        <?php if ($isComplete): ?>
                            <div class="mb-3">
                                <label class="form-label small">Actual Yield (units produced)</label>
                                <input type="number" name="actual_yield" class="form-control form-control-sm"
                                    min="0" value="<?= $model->productionOrder->planned_qty ?>"
                                    placeholder="Leave blank to use planned qty">
                                <div class="form-text">This quantity will be added to product stock.</div>
                            </div>
                        <?php endif; ?>

                        <?php
                        $icon  = Batch::statusIcons()[$nextStatus] ?? 'bi-arrow-right';
                        $label = Batch::statusLabels()[$nextStatus];
                        $confirmMsg = $nextStatus === Batch::STATUS_BREWING
                            ? 'Start brewing? This will deduct ingredients from raw material stock.'
                            : ($isComplete ? 'Mark as completed? This will add yield to product stock.' : null);
                        ?>

                        <?= Html::submitButton(
                            "<i class=\"bi {$icon} me-1\"></i>Move to: {$label}",
                            array_filter([
                                'class' => 'btn btn-primary w-100',
                                'data'  => $confirmMsg ? ['confirm' => $confirmMsg] : null,
                            ])
                        ) ?>
                    <?php ActiveForm::end(); ?>
                <?php else: ?>
                    <p class="text-success mb-0"><i class="bi bi-check-circle-fill me-1"></i>Batch complete.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Batch details -->
        <div class="card shadow-sm">
            <div class="card-header fw-semibold"><i class="bi bi-info-circle me-1"></i>Details</div>
            <div class="card-body">
                <dl class="row mb-0 small">
                    <dt class="col-6 text-muted">Batch Size</dt>
                    <dd class="col-6"><?= $model->batch_size ?> L</dd>

                    <dt class="col-6 text-muted">Brew Master</dt>
                    <dd class="col-6"><?= Html::encode($model->brewMaster->username ?? '—') ?></dd>

                    <dt class="col-6 text-muted">Brew Date</dt>
                    <dd class="col-6"><?= Html::encode($model->brew_date ?? '—') ?></dd>

                    <dt class="col-6 text-muted">Completed</dt>
                    <dd class="col-6"><?= Html::encode($model->completion_date ?? '—') ?></dd>

                    <dt class="col-6 text-muted">Actual Yield</dt>
                    <dd class="col-6"><?= $model->actual_yield !== null ? $model->actual_yield . ' units' : '—' ?></dd>

                    <?php if ($model->notes): ?>
                        <dt class="col-6 text-muted">Notes</dt>
                        <dd class="col-6"><?= Html::encode($model->notes) ?></dd>
                    <?php endif; ?>
                </dl>
            </div>
        </div>
    </div>

</div>
