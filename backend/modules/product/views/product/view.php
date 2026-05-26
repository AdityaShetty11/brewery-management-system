<?php

/** @var yii\web\View $this */
/** @var common\models\Product $model */
/** @var common\models\StockTransaction[] $transactions */

use common\models\StockTransaction;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

$this->title = $model->name;
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h4 mb-0"><?= Html::encode($model->name) ?></h1>
        <small class="text-muted">SKU: <?= Html::encode($model->sku) ?></small>
    </div>
    <div class="d-flex gap-2">
        <?= Html::a('<i class="bi bi-pencil me-1"></i>Edit', ['update', 'id' => $model->id], ['class' => 'btn btn-outline-primary btn-sm']) ?>
        <?= Html::a(
            $model->is_active ? '<i class="bi bi-eye-slash me-1"></i>Deactivate' : '<i class="bi bi-eye me-1"></i>Activate',
            ['toggle-active', 'id' => $model->id],
            ['class' => 'btn btn-outline-warning btn-sm', 'data' => ['method' => 'post']]
        ) ?>
        <?= Html::a('<i class="bi bi-arrow-left me-1"></i>Back', ['index'], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
    </div>
</div>

<div class="row g-4">

    <!-- ── Product Details ──────────────────────────────────────────── -->
    <div class="col-md-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header fw-semibold"><i class="bi bi-info-circle me-1"></i>Details</div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-5 text-muted">Category</dt>
                    <dd class="col-sm-7"><?= Html::encode($model->category->name ?? '—') ?></dd>

                    <dt class="col-sm-5 text-muted">Packaging</dt>
                    <dd class="col-sm-7">
                        <i class="bi <?= $model->getPackagingIcon() ?> me-1"></i>
                        <?= Html::encode($model->getPackagingLabel()) ?>
                    </dd>

                    <dt class="col-sm-5 text-muted">Unit Price</dt>
                    <dd class="col-sm-7"><strong><?= $model->getFormattedPrice() ?></strong></dd>

                    <dt class="col-sm-5 text-muted">Status</dt>
                    <dd class="col-sm-7">
                        <?= $model->is_active
                            ? '<span class="badge bg-success">Active</span>'
                            : '<span class="badge bg-secondary">Inactive</span>' ?>
                    </dd>

                    <dt class="col-sm-5 text-muted">Description</dt>
                    <dd class="col-sm-7"><?= Html::encode($model->description ?? '—') ?></dd>
                </dl>
            </div>
        </div>

        <!-- ── Stock card ──────────────────────────────────────────── -->
        <div class="card shadow-sm <?= $model->isLowStock() ? 'border-danger' : '' ?>">
            <div class="card-header fw-semibold <?= $model->isLowStock() ? 'text-danger' : '' ?>">
                <i class="bi bi-boxes me-1"></i>Current Stock
                <?php if ($model->isLowStock()): ?>
                    <span class="badge bg-danger ms-2">LOW</span>
                <?php endif; ?>
            </div>
            <div class="card-body text-center">
                <div class="display-5 fw-bold <?= $model->isLowStock() ? 'text-danger' : 'text-success' ?>">
                    <?= $model->stock_qty ?>
                </div>
                <div class="text-muted small">units in stock</div>
            </div>
        </div>

        <!-- ── Manual stock adjustment ─────────────────────────────── -->
        <div class="card shadow-sm mt-4">
            <div class="card-header fw-semibold"><i class="bi bi-sliders me-1"></i>Manual Adjustment</div>
            <div class="card-body">
                <?php $form = ActiveForm::begin([
                    'action' => ['adjust-stock', 'id' => $model->id],
                    'method' => 'post',
                ]); ?>
                <div class="mb-2">
                    <label class="form-label small">Type</label>
                    <select name="type" class="form-select form-select-sm">
                        <option value="<?= StockTransaction::TYPE_IN ?>">Stock In</option>
                        <option value="<?= StockTransaction::TYPE_OUT ?>">Stock Out</option>
                        <option value="<?= StockTransaction::TYPE_ADJUSTMENT ?>">Adjustment</option>
                    </select>
                </div>
                <div class="mb-2">
                    <label class="form-label small">Quantity</label>
                    <input type="number" name="qty" class="form-control form-control-sm" min="1" value="1" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small">Notes</label>
                    <input type="text" name="notes" class="form-control form-control-sm" placeholder="Reason…">
                </div>
                <?= Html::submitButton('Apply', ['class' => 'btn btn-sm btn-dark w-100']) ?>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>

    <!-- ── Stock History ────────────────────────────────────────────── -->
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header fw-semibold"><i class="bi bi-clock-history me-1"></i>Stock Transaction History</div>
            <div class="card-body p-0">
                <?php if (empty($transactions)): ?>
                    <p class="text-muted p-3 mb-0">No transactions recorded yet.</p>
                <?php else: ?>
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Qty</th>
                                <th>Reference</th>
                                <th>Notes</th>
                                <th>By</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($transactions as $tx): ?>
                            <tr>
                                <td><small><?= Html::encode($tx->created_at) ?></small></td>
                                <td>
                                    <?php
                                    $badgeClass = match ($tx->transaction_type) {
                                        'in'         => 'bg-success',
                                        'out'        => 'bg-danger',
                                        default      => 'bg-secondary',
                                    };
                                    ?>
                                    <span class="badge <?= $badgeClass ?>"><?= Html::encode($tx->getTypeLabel()) ?></span>
                                </td>
                                <td><?= Html::encode($tx->quantity) ?></td>
                                <td><small class="text-muted"><?= Html::encode($tx->reference_type ?? '—') ?></small></td>
                                <td><small><?= Html::encode($tx->notes ?? '—') ?></small></td>
                                <td><small><?= Html::encode($tx->createdBy->username ?? '—') ?></small></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>
