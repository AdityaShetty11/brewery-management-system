<?php

/** @var yii\web\View $this */
/** @var common\models\Order $model */
/** @var common\models\Product[] $products */

use common\models\Order;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

$this->title = 'Order ' . $model->order_number;
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h4 mb-0"><?= Html::encode($model->order_number) ?></h1>
        <small class="text-muted">
            Customer: <strong><?= Html::encode($model->customer->username ?? '—') ?></strong>
            <?php if ($model->company): ?>
                &nbsp;&middot;&nbsp; Company: <strong><?= Html::encode($model->company->name) ?></strong>
            <?php endif; ?>
        </small>
    </div>
    <div class="d-flex gap-2 align-items-center">
        <?= $model->getStatusBadge() ?>
        <?= Html::a('<i class="bi bi-arrow-left me-1"></i>Back', ['index'], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
    </div>
</div>

<div class="row g-4">

    <!-- ── Left: Items + Add item ─────────────────────────────────── -->
    <div class="col-md-8">

        <!-- Items table -->
        <div class="card shadow-sm mb-4">
            <div class="card-header fw-semibold"><i class="bi bi-list-ul me-1"></i>Order Items</div>
            <div class="card-body p-0">
                <?php if (empty($model->items)): ?>
                    <p class="p-3 text-muted mb-0">No items yet.</p>
                <?php else: ?>
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th><th>SKU</th><th>Qty</th>
                                <th>Unit Price</th><th>Subtotal</th>
                                <?php if ($model->isEditable()): ?><th></th><?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($model->items as $item): ?>
                            <tr>
                                <td><?= Html::encode($item->product->name ?? '—') ?></td>
                                <td><small class="text-muted"><?= Html::encode($item->product->sku ?? '') ?></small></td>
                                <td><?= $item->quantity ?></td>
                                <td>$<?= number_format($item->unit_price, 2) ?></td>
                                <td><strong><?= $item->getFormattedSubtotal() ?></strong></td>
                                <?php if ($model->isEditable()): ?>
                                    <td>
                                        <?= Html::a('<i class="bi bi-trash"></i>',
                                            ['remove-item', 'itemId' => $item->id],
                                            ['class' => 'btn btn-sm btn-outline-danger',
                                             'data'  => ['method' => 'post', 'confirm' => 'Remove this item?']]
                                        ) ?>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="<?= $model->isEditable() ? 4 : 4 ?>" class="text-end fw-semibold">Total</td>
                                <td colspan="<?= $model->isEditable() ? 2 : 1 ?>">
                                    <strong class="fs-5"><?= $model->getFormattedTotal() ?></strong>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                <?php endif; ?>
            </div>
        </div>

        <!-- Add item (draft only) -->
        <?php if ($model->isEditable()): ?>
        <div class="card shadow-sm mb-4">
            <div class="card-header fw-semibold"><i class="bi bi-plus-circle me-1"></i>Add Item</div>
            <div class="card-body">
                <?php $form = ActiveForm::begin(['action' => ['add-item', 'id' => $model->id], 'method' => 'post']); ?>
                <div class="row g-2 align-items-end">
                    <div class="col-md-7">
                        <label class="form-label small">Product</label>
                        <select name="product_id" class="form-select form-select-sm" required>
                            <option value="">— Select Product —</option>
                            <?php foreach ($products as $p): ?>
                                <option value="<?= $p->id ?>">
                                    <?= Html::encode($p->name) ?> (<?= $p->getPackagingLabel() ?>) — <?= $p->getFormattedPrice() ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small">Quantity</label>
                        <input type="number" name="quantity" class="form-control form-control-sm" min="1" value="1" required>
                    </div>
                    <div class="col-md-2">
                        <?= Html::submitButton('<i class="bi bi-plus-lg"></i> Add', ['class' => 'btn btn-dark btn-sm w-100']) ?>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Notes -->
        <div class="card shadow-sm">
            <div class="card-header fw-semibold"><i class="bi bi-chat-left-text me-1"></i>Notes</div>
            <div class="card-body">
                <?php $form = ActiveForm::begin(['action' => ['update-notes', 'id' => $model->id], 'method' => 'post']); ?>
                <textarea name="notes" class="form-control form-control-sm mb-2" rows="3"><?= Html::encode($model->notes ?? '') ?></textarea>
                <?= Html::submitButton('Save Notes', ['class' => 'btn btn-sm btn-outline-secondary']) ?>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>

    <!-- ── Right: Status + Timeline ───────────────────────────────── -->
    <div class="col-md-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header fw-semibold"><i class="bi bi-arrow-right-circle me-1"></i>Workflow</div>
            <div class="card-body">
                <p class="text-muted small mb-3">Current status: <?= $model->getStatusBadge() ?></p>

                <?php $transitions = Order::TRANSITIONS[$model->status] ?? []; ?>

                <?php if (empty($transitions)): ?>
                    <p class="text-muted small">No further transitions available.</p>
                <?php else: ?>
                    <?php foreach ($transitions as $nextStatus): ?>
                        <?php $isDeliver = $nextStatus === Order::STATUS_DELIVERED; ?>
                        <?php $isCancel  = $nextStatus === Order::STATUS_CANCELLED; ?>
                        <?php $btnClass  = $isCancel ? 'btn-outline-danger' : ($isDeliver ? 'btn-success' : 'btn-primary'); ?>

                        <?php $form = ActiveForm::begin(['action' => ['transition', 'id' => $model->id], 'method' => 'post']); ?>
                            <input type="hidden" name="status" value="<?= $nextStatus ?>">
                            <?= Html::submitButton(
                                Order::statusLabels()[$nextStatus],
                                [
                                    'class' => "btn {$btnClass} w-100 mb-2",
                                    'data'  => $isDeliver ? ['confirm' => 'Mark as delivered? This will deduct stock.'] : [],
                                ]
                            ) ?>
                        <?php ActiveForm::end(); ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Order metadata -->
        <div class="card shadow-sm">
            <div class="card-header fw-semibold"><i class="bi bi-info-circle me-1"></i>Details</div>
            <div class="card-body">
                <dl class="row mb-0 small">
                    <dt class="col-5 text-muted">Order #</dt>
                    <dd class="col-7"><?= Html::encode($model->order_number) ?></dd>

                    <dt class="col-5 text-muted">Placed</dt>
                    <dd class="col-7"><?= Html::encode($model->created_at) ?></dd>

                    <dt class="col-5 text-muted">Confirmed</dt>
                    <dd class="col-7"><?= Html::encode($model->confirmed_at ?? '—') ?></dd>

                    <dt class="col-5 text-muted">Delivered</dt>
                    <dd class="col-7"><?= Html::encode($model->delivered_at ?? '—') ?></dd>

                    <dt class="col-5 text-muted">Total</dt>
                    <dd class="col-7"><strong><?= $model->getFormattedTotal() ?></strong></dd>
                </dl>
            </div>
        </div>
    </div>

</div>
