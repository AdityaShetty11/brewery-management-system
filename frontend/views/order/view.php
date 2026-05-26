<?php

/** @var yii\web\View $this */
/** @var common\models\Order $model */

use common\models\Order;
use yii\helpers\Html;

$this->title = 'Order ' . $model->order_number;

$statusSteps = [
    Order::STATUS_DRAFT         => ['icon' => 'bi-pencil',        'label' => 'Draft'],
    Order::STATUS_CONFIRMED     => ['icon' => 'bi-check-circle',  'label' => 'Confirmed'],
    Order::STATUS_IN_PRODUCTION => ['icon' => 'bi-gear',          'label' => 'In Production'],
    Order::STATUS_DELIVERED     => ['icon' => 'bi-truck',         'label' => 'Delivered'],
];

$statusOrder   = array_keys($statusSteps);
$currentIndex  = array_search($model->status, $statusOrder, true);
$isCancelled   = $model->status === Order::STATUS_CANCELLED;
?>

<div class="mb-3">
    <?= Html::a('<i class="bi bi-arrow-left me-1"></i>My Orders', ['index'], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
</div>

<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h1 class="h4 mb-0"><?= Html::encode($model->order_number) ?></h1>
        <small class="text-muted">Placed <?= Html::encode(date('d M Y, H:i', strtotime($model->created_at))) ?></small>
    </div>
    <?= $model->getStatusBadge() ?>
</div>

<!-- ── Progress tracker ──────────────────────────────────────────── -->
<?php if (!$isCancelled): ?>
<div class="card shadow-sm mb-4">
    <div class="card-body py-3">
        <div class="d-flex justify-content-between">
            <?php foreach ($statusSteps as $key => $step):
                $stepIdx  = array_search($key, $statusOrder, true);
                $done     = $currentIndex !== false && $stepIdx <= $currentIndex;
                $current  = $key === $model->status;
                $dotClass = $done ? 'text-success' : 'text-secondary';
            ?>
                <div class="text-center flex-fill">
                    <div class="fs-4 <?= $dotClass ?>">
                        <i class="bi <?= $step['icon'] ?>"></i>
                    </div>
                    <div class="small <?= $current ? 'fw-bold' : 'text-muted' ?>"><?= $step['label'] ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php else: ?>
    <div class="alert alert-danger">This order has been cancelled.</div>
<?php endif; ?>

<div class="row g-4">

    <!-- ── Items ────────────────────────────────────────────────────── -->
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header fw-semibold">Order Items</div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr><th>Product</th><th>Packaging</th><th>Qty</th><th>Unit</th><th>Subtotal</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($model->items as $item): ?>
                        <tr>
                            <td><?= Html::encode($item->product->name ?? '—') ?></td>
                            <td><small class="text-muted"><?= Html::encode($item->product->getPackagingLabel() ?? '') ?></small></td>
                            <td><?= $item->quantity ?></td>
                            <td>$<?= number_format($item->unit_price, 2) ?></td>
                            <td><strong><?= $item->getFormattedSubtotal() ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="4" class="text-end fw-semibold">Total</td>
                            <td><strong class="fs-5"><?= $model->getFormattedTotal() ?></strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- ── Summary + Actions ────────────────────────────────────────── -->
    <div class="col-md-4">
        <div class="card shadow-sm mb-3">
            <div class="card-header fw-semibold">Summary</div>
            <div class="card-body">
                <dl class="row mb-0 small">
                    <dt class="col-6 text-muted">Order total</dt>
                    <dd class="col-6"><strong><?= $model->getFormattedTotal() ?></strong></dd>

                    <?php if ($model->company): ?>
                        <dt class="col-6 text-muted">Company</dt>
                        <dd class="col-6"><?= Html::encode($model->company->name) ?></dd>
                    <?php endif; ?>

                    <?php if ($model->notes): ?>
                        <dt class="col-6 text-muted">Notes</dt>
                        <dd class="col-6"><?= Html::encode($model->notes) ?></dd>
                    <?php endif; ?>
                </dl>
            </div>
        </div>

        <!-- Draft actions -->
        <?php if ($model->isEditable()): ?>
            <div class="card shadow-sm border-warning">
                <div class="card-body">
                    <p class="text-muted small mb-3">Your order is still a draft. Submit it when you're ready.</p>
                    <?= Html::beginForm(['submit', 'id' => $model->id], 'post') ?>
                    <?= Html::submitButton('<i class="bi bi-send me-1"></i>Submit Order', ['class' => 'btn btn-dark w-100 mb-2']) ?>
                    <?= Html::endForm() ?>

                    <?= Html::beginForm(['cancel', 'id' => $model->id], 'post') ?>
                    <?= Html::submitButton('Cancel Order', [
                        'class' => 'btn btn-outline-danger w-100 btn-sm',
                        'data'  => ['confirm' => 'Cancel this order?'],
                    ]) ?>
                    <?= Html::endForm() ?>
                </div>
            </div>
        <?php elseif ($model->canTransitionTo(Order::STATUS_CANCELLED)): ?>
            <div class="card shadow-sm">
                <div class="card-body">
                    <?= Html::beginForm(['cancel', 'id' => $model->id], 'post') ?>
                    <?= Html::submitButton('Cancel Order', [
                        'class' => 'btn btn-outline-danger w-100 btn-sm',
                        'data'  => ['confirm' => 'Cancel this order?'],
                    ]) ?>
                    <?= Html::endForm() ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

</div>
