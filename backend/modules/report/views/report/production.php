<?php

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var string $dateFrom */
/** @var string $dateTo */
/** @var string $status */
/** @var int    $totalBatches */
/** @var int    $completedCount */
/** @var int    $totalYield */

use common\models\Batch;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Production Report';
?>

<!-- Report tabs -->
<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link" href="<?= Url::to(['/report/report/sales']) ?>">
            <i class="bi bi-bag-fill me-1"></i>Sales
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="<?= Url::to(['/report/report/inventory']) ?>">
            <i class="bi bi-boxes me-1"></i>Inventory
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link active" href="<?= Url::to(['/report/report/production']) ?>">
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
                    <?php foreach (Batch::statusLabels() as $val => $label): ?>
                    <option value="<?= $val ?>" <?= $status === $val ? 'selected' : '' ?>>
                        <?= Html::encode($label) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-dark btn-sm w-100">
                    <i class="bi bi-search me-1"></i>Filter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Summary stats -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card text-center shadow-sm">
            <div class="card-body py-3">
                <div class="fs-4 fw-bold"><?= $totalBatches ?></div>
                <div class="text-muted small">Total Batches</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center shadow-sm">
            <div class="card-body py-3">
                <div class="fs-4 fw-bold text-success"><?= $completedCount ?></div>
                <div class="text-muted small">Completed</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center shadow-sm">
            <div class="card-body py-3">
                <div class="fs-4 fw-bold text-primary"><?= number_format($totalYield) ?></div>
                <div class="text-muted small">Total Yield (units)</div>
            </div>
        </div>
    </div>
</div>

<!-- Batches table -->
<div class="card shadow-sm">
    <div class="card-body p-0">
        <?= GridView::widget([
            'dataProvider'   => $dataProvider,
            'tableOptions'   => ['class' => 'table table-hover mb-0'],
            'layout'         => '{summary}<div class="table-responsive">{items}</div>{pager}',
            'summaryOptions' => ['class' => 'text-muted small px-3 pt-3'],
            'filterModel'    => null,
            'columns' => [
                [
                    'attribute' => 'batch_number',
                    'format'    => 'raw',
                    'value'     => fn($b) => Html::a(Html::encode($b->batch_number),
                        ['/production/batch/view', 'id' => $b->id],
                        ['class' => 'fw-semibold text-decoration-none']),
                ],
                [
                    'label'  => 'Product',
                    'value'  => fn($b) => $b->productionOrder->product->name ?? '—',
                ],
                [
                    'label'  => 'Brew Master',
                    'value'  => fn($b) => $b->brewMaster->username ?? '—',
                ],
                [
                    'attribute' => 'status',
                    'format'    => 'raw',
                    'value'     => fn($b) => $b->getStatusBadge(),
                ],
                [
                    'attribute' => 'batch_size',
                    'label'     => 'Size (L)',
                    'headerOptions' => ['class' => 'text-end'],
                    'contentOptions' => ['class' => 'text-end'],
                    'value'     => fn($b) => number_format((float) $b->batch_size, 1),
                ],
                [
                    'label'   => 'Yield / Efficiency',
                    'format'  => 'raw',
                    'headerOptions' => ['class' => 'text-end'],
                    'contentOptions' => ['class' => 'text-end'],
                    'value'   => function ($b) {
                        if ($b->status !== Batch::STATUS_COMPLETED) {
                            return '<span class="text-muted">—</span>';
                        }
                        $planned = (int) ($b->productionOrder->planned_qty ?? 0);
                        $actual  = (int) ($b->actual_yield ?? 0);
                        $pct     = $planned > 0 ? round($actual / $planned * 100) : 0;
                        $color   = $pct >= 90 ? 'success' : ($pct >= 70 ? 'warning' : 'danger');
                        return "<span class=\"fw-semibold text-{$color}\">{$actual}</span>"
                            . " <small class=\"text-muted\">/ {$planned} ({$pct}%)</small>";
                    },
                ],
                [
                    'attribute' => 'brew_date',
                    'label'     => 'Brew Date',
                    'value'     => fn($b) => $b->brew_date ?? '—',
                ],
                [
                    'attribute' => 'completion_date',
                    'label'     => 'Completed',
                    'value'     => fn($b) => $b->completion_date ?? '—',
                ],
            ],
        ]) ?>
    </div>
</div>
