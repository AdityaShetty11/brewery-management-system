<?php

/** @var yii\web\View $this */
/** @var int   $ordersThisMonth */
/** @var float $revenueThisMonth */
/** @var int   $activeBatches */
/** @var int   $lowStockCount */
/** @var array $revenueLabels */
/** @var array $revenueValues */
/** @var array $topProductLabels */
/** @var array $topProductValues */
/** @var array $batchStatusLabels */
/** @var array $batchStatusCounts */
/** @var array $orderStatusLabels */
/** @var array $orderStatusCounts */
/** @var common\models\Order[]  $recentOrders */
/** @var common\models\Batch[]  $recentBatches */

use common\models\Batch;
use common\models\Order;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Dashboard';

$this->registerJsFile('https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js',
    ['position' => \yii\web\View::POS_HEAD]);

$revenueLabelsJson     = json_encode($revenueLabels);
$revenueValuesJson     = json_encode($revenueValues);
$topProductLabelsJson  = json_encode($topProductLabels);
$topProductValuesJson  = json_encode($topProductValues);
$batchStatusLabelsJson = json_encode($batchStatusLabels);
$batchStatusCountsJson = json_encode($batchStatusCounts);
$orderStatusLabelsJson = json_encode($orderStatusLabels);
$orderStatusCountsJson = json_encode($orderStatusCounts);

$this->registerJs(<<<JS
const statusColors = {
    planned: '#6c757d', brewing: '#ffc107', fermenting: '#0dcaf0',
    packaging: '#0d6efd', completed: '#198754',
    draft: '#adb5bd', confirmed: '#0d6efd', in_production: '#ffc107',
    delivered: '#198754', cancelled: '#dc3545'
};

// Revenue trend
new Chart(document.getElementById('revenueChart'), {
    type: 'line',
    data: {
        labels: {$revenueLabelsJson},
        datasets: [{
            label: 'Revenue (USD)',
            data: {$revenueValuesJson},
            borderColor: '#198754',
            backgroundColor: 'rgba(25,135,84,0.1)',
            tension: 0.3,
            fill: true,
            pointBackgroundColor: '#198754',
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { callback: v => '\$' + v.toLocaleString() } }
        }
    }
});

// Top products
new Chart(document.getElementById('topProductsChart'), {
    type: 'bar',
    data: {
        labels: {$topProductLabelsJson},
        datasets: [{
            label: 'Revenue (USD)',
            data: {$topProductValuesJson},
            backgroundColor: 'rgba(13,110,253,0.8)',
            borderRadius: 4,
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { beginAtZero: true, ticks: { callback: v => '\$' + v.toLocaleString() } }
        }
    }
});

// Batch status doughnut
const batchLabels = {$batchStatusLabelsJson};
new Chart(document.getElementById('batchStatusChart'), {
    type: 'doughnut',
    data: {
        labels: batchLabels,
        datasets: [{
            data: {$batchStatusCountsJson},
            backgroundColor: batchLabels.map(l => statusColors[l] || '#adb5bd'),
            hoverOffset: 8,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'bottom' } },
        cutout: '60%',
    }
});

// Order status doughnut
const orderLabels = {$orderStatusLabelsJson};
new Chart(document.getElementById('orderStatusChart'), {
    type: 'doughnut',
    data: {
        labels: orderLabels,
        datasets: [{
            data: {$orderStatusCountsJson},
            backgroundColor: orderLabels.map(l => statusColors[l] || '#adb5bd'),
            hoverOffset: 8,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'bottom' } },
        cutout: '60%',
    }
});
JS);
?>

<!-- ── KPI cards ──────────────────────────────────────────────── -->
<div class="row g-3 mb-4">

    <div class="col-sm-6 col-xl-3">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="fs-1 text-primary"><i class="bi bi-bag-check"></i></div>
                <div>
                    <div class="text-muted small">Orders This Month</div>
                    <div class="fs-3 fw-bold"><?= $ordersThisMonth ?></div>
                    <div class="text-muted small"><?= date('F Y') ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="fs-1 text-success"><i class="bi bi-currency-dollar"></i></div>
                <div>
                    <div class="text-muted small">Revenue This Month</div>
                    <div class="fs-3 fw-bold">$<?= number_format($revenueThisMonth, 2) ?></div>
                    <div class="text-muted small">excl. cancelled orders</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="fs-1 text-warning"><i class="bi bi-fire"></i></div>
                <div>
                    <div class="text-muted small">Active Batches</div>
                    <div class="fs-3 fw-bold"><?= $activeBatches ?></div>
                    <div class="text-muted small">in progress</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <?php if ($lowStockCount > 0): ?>
        <div class="card h-100 shadow-sm border-danger border-2">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="fs-1 text-danger"><i class="bi bi-exclamation-triangle-fill"></i></div>
                <div>
                    <div class="text-muted small">Low Stock Alerts</div>
                    <div class="fs-3 fw-bold text-danger"><?= $lowStockCount ?></div>
                    <?= Html::a('View alerts →', ['/inventory/raw-material/low-stock'], ['class' => 'small text-danger']) ?>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="fs-1 text-success"><i class="bi bi-check-circle-fill"></i></div>
                <div>
                    <div class="text-muted small">Low Stock Alerts</div>
                    <div class="fs-3 fw-bold text-success">None</div>
                    <div class="text-muted small">all materials OK</div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

</div>

<!-- ── Charts row 1 ──────────────────────────────────────────── -->
<div class="row g-3 mb-4">

    <div class="col-lg-8">
        <div class="card shadow-sm h-100">
            <div class="card-header fw-semibold">
                <i class="bi bi-graph-up me-1"></i>Revenue Trend — Last 6 Months
            </div>
            <div class="card-body">
                <canvas id="revenueChart" height="100"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm h-100">
            <div class="card-header fw-semibold">
                <i class="bi bi-pie-chart me-1"></i>Orders by Status
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="orderStatusChart"></canvas>
            </div>
        </div>
    </div>

</div>

<!-- ── Charts row 2 ──────────────────────────────────────────── -->
<div class="row g-3 mb-4">

    <div class="col-lg-8">
        <div class="card shadow-sm h-100">
            <div class="card-header fw-semibold">
                <i class="bi bi-bar-chart-line me-1"></i>Top 5 Products by Revenue
            </div>
            <div class="card-body">
                <canvas id="topProductsChart" height="110"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm h-100">
            <div class="card-header fw-semibold">
                <i class="bi bi-pie-chart me-1"></i>Batches by Status
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="batchStatusChart"></canvas>
            </div>
        </div>
    </div>

</div>

<!-- ── Recent activity ────────────────────────────────────────── -->
<div class="row g-3">

    <!-- Recent Orders -->
    <div class="col-lg-7">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center fw-semibold">
                <span><i class="bi bi-bag me-1"></i>Recent Orders</span>
                <?= Html::a('View all', ['/order/order/index'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Status</th>
                            <th class="text-end">Total</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentOrders)): ?>
                        <tr><td colspan="5" class="text-muted text-center py-3">No orders yet.</td></tr>
                        <?php else: ?>
                        <?php foreach ($recentOrders as $order): ?>
                        <tr>
                            <td>
                                <?= Html::a(Html::encode($order->order_number),
                                    ['/order/order/view', 'id' => $order->id],
                                    ['class' => 'text-decoration-none fw-semibold']) ?>
                            </td>
                            <td class="text-muted"><?= Html::encode($order->customer->username ?? '—') ?></td>
                            <td><?= $order->getStatusBadge() ?></td>
                            <td class="text-end fw-semibold"><?= $order->getFormattedTotal() ?></td>
                            <td class="text-muted"><?= substr($order->created_at, 0, 10) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Batches -->
    <div class="col-lg-5">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center fw-semibold">
                <span><i class="bi bi-fire me-1"></i>Recent Batches</span>
                <?= Html::a('View all', ['/production/batch/index'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th>Batch #</th>
                            <th>Product</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentBatches)): ?>
                        <tr><td colspan="3" class="text-muted text-center py-3">No batches yet.</td></tr>
                        <?php else: ?>
                        <?php foreach ($recentBatches as $batch): ?>
                        <tr>
                            <td>
                                <?= Html::a(Html::encode($batch->batch_number),
                                    ['/production/batch/view', 'id' => $batch->id],
                                    ['class' => 'text-decoration-none fw-semibold']) ?>
                            </td>
                            <td class="text-muted"><?= Html::encode($batch->productionOrder->product->name ?? '—') ?></td>
                            <td><?= $batch->getStatusBadge() ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
