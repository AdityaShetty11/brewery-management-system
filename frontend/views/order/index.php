<?php

/** @var yii\web\View $this */
/** @var common\models\Order[] $orders */

use common\models\Order;
use yii\helpers\Html;

$this->title = 'My Orders';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h4 mb-0">My Orders</h1>
    <?= Html::a('<i class="bi bi-plus-lg me-1"></i>New Order', ['create'], ['class' => 'btn btn-dark']) ?>
</div>

<?php if (empty($orders)): ?>
    <div class="text-center py-5 text-muted">
        <i class="bi bi-bag-x" style="font-size:3rem;"></i>
        <p class="mt-3">You haven't placed any orders yet.</p>
        <?= Html::a('Browse our beers', ['/catalog/index'], ['class' => 'btn btn-outline-dark']) ?>
    </div>
<?php else: ?>
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Order #</th>
                        <th>Date</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td>
                            <?= Html::a(Html::encode($order->order_number), ['view', 'id' => $order->id], ['class' => 'fw-semibold']) ?>
                        </td>
                        <td><small class="text-muted"><?= Html::encode(date('d M Y', strtotime($order->created_at))) ?></small></td>
                        <td><span class="badge bg-secondary"><?= count($order->items) ?></span></td>
                        <td><strong><?= $order->getFormattedTotal() ?></strong></td>
                        <td><?= $order->getStatusBadge() ?></td>
                        <td>
                            <?= Html::a('View', ['view', 'id' => $order->id], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
                            <?php if ($order->canTransitionTo(Order::STATUS_CANCELLED)): ?>
                                <?= Html::a('Cancel', ['cancel', 'id' => $order->id], [
                                    'class' => 'btn btn-sm btn-outline-danger ms-1',
                                    'data'  => ['method' => 'post', 'confirm' => 'Cancel this order?'],
                                ]) ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>
