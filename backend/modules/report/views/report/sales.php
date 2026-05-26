<?php

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var string $dateFrom */
/** @var string $dateTo */
/** @var string $status */
/** @var int    $totalOrders */
/** @var float  $totalRevenue */
/** @var float  $avgOrderValue */

use common\models\Order;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Sales Report';
?>

<!-- Report tabs -->
<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link active" href="<?= Url::to(['/report/report/sales']) ?>">
            <i class="bi bi-bag-fill me-1"></i>Sales
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="<?= Url::to(['/report/report/inventory']) ?>">
            <i class="bi bi-boxes me-1"></i>Inventory
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="<?= Url::to(['/report/report/production']) ?>">
            <i class="bi bi-gear-fill me-1"></i>Production
        </a>
    </li>
</ul>

<!-- Filter form -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="get" action="" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold">From</label>
                <input type="date" name="date_from" class="form-control form-control-sm"
                    value="<?= Html::encode($dateFrom) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">To</label>
                <input type="date" name="date_to" class="form-control form-control-sm"
                    value="<?= Html::encode($dateTo) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    <?php foreach (Order::statusLabels() as $val => $label): ?>
                    <option value="<?= $val ?>" <?= $status === $val ? 'selected' : '' ?>>
                        <?= Html::encode($label) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-dark btn-sm flex-grow-1">
                    <i class="bi bi-search me-1"></i>Filter
                </button>
                <?= Html::a('<i class="bi bi-download me-1"></i>CSV',
                    ['/report/report/export-sales', 'date_from' => $dateFrom, 'date_to' => $dateTo, 'status' => $status],
                    ['class' => 'btn btn-outline-secondary btn-sm']) ?>
            </div>
        </form>
    </div>
</div>

<!-- Summary stats -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card text-center shadow-sm">
            <div class="card-body py-3">
                <div class="fs-4 fw-bold"><?= $totalOrders ?></div>
                <div class="text-muted small">Total Orders</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center shadow-sm">
            <div class="card-body py-3">
                <div class="fs-4 fw-bold text-success">$<?= number_format($totalRevenue, 2) ?></div>
                <div class="text-muted small">Total Revenue</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center shadow-sm">
            <div class="card-body py-3">
                <div class="fs-4 fw-bold text-primary">$<?= number_format($avgOrderValue, 2) ?></div>
                <div class="text-muted small">Avg. Order Value</div>
            </div>
        </div>
    </div>
</div>

<!-- Orders table -->
<div class="card shadow-sm">
    <div class="card-body p-0">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'tableOptions' => ['class' => 'table table-hover mb-0'],
            'layout'       => '{summary}<div class="table-responsive">{items}</div>{pager}',
            'summaryOptions' => ['class' => 'text-muted small px-3 pt-3'],
            'filterModel'  => null,
            'columns' => [
                [
                    'attribute' => 'order_number',
                    'format'    => 'raw',
                    'value'     => fn($o) => Html::a(Html::encode($o->order_number),
                        ['/order/order/view', 'id' => $o->id],
                        ['class' => 'fw-semibold text-decoration-none']),
                ],
                [
                    'label'  => 'Customer',
                    'value'  => fn($o) => $o->customer->username ?? '—',
                ],
                [
                    'label'  => 'Company',
                    'value'  => fn($o) => $o->company->name ?? '—',
                ],
                [
                    'attribute' => 'status',
                    'format'    => 'raw',
                    'value'     => fn($o) => $o->getStatusBadge(),
                ],
                [
                    'attribute' => 'total_amount',
                    'label'     => 'Total',
                    'format'    => 'raw',
                    'headerOptions' => ['class' => 'text-end'],
                    'contentOptions' => ['class' => 'text-end fw-semibold'],
                    'value'     => fn($o) => $o->getFormattedTotal(),
                ],
                [
                    'attribute' => 'created_at',
                    'label'     => 'Date',
                    'value'     => fn($o) => substr($o->created_at, 0, 10),
                ],
            ],
        ]) ?>
    </div>
</div>
