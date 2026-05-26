<?php

/** @var yii\web\View $this */
/** @var array  $rows */
/** @var bool   $showLowOnly */
/** @var int    $totalMaterials */
/** @var int    $lowStockCount */
/** @var float  $totalStockIn */
/** @var float  $totalStockOut */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Inventory Report';
?>

<!-- Report tabs -->
<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link" href="<?= Url::to(['/report/report/sales']) ?>">
            <i class="bi bi-bag-fill me-1"></i>Sales
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link active" href="<?= Url::to(['/report/report/inventory']) ?>">
            <i class="bi bi-boxes me-1"></i>Inventory
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="<?= Url::to(['/report/report/production']) ?>">
            <i class="bi bi-gear-fill me-1"></i>Production
        </a>
    </li>
</ul>

<!-- Filter -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="get" class="row g-3 align-items-end">
            <div class="col-md-4">
                <div class="form-check mt-2">
                    <input class="form-check-input" type="checkbox" name="low_only" value="1"
                        id="lowOnly" <?= $showLowOnly ? 'checked' : '' ?>>
                    <label class="form-check-label" for="lowOnly">
                        Show low-stock materials only
                    </label>
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-dark btn-sm">
                    <i class="bi bi-search me-1"></i>Filter
                </button>
            </div>
            <div class="col text-muted small">
                Stock in/out totals cover the last 30 days.
            </div>
        </form>
    </div>
</div>

<!-- Summary stats -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card text-center shadow-sm">
            <div class="card-body py-3">
                <div class="fs-4 fw-bold"><?= $totalMaterials ?></div>
                <div class="text-muted small">Total Materials</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center shadow-sm <?= $lowStockCount > 0 ? 'border-danger' : '' ?>">
            <div class="card-body py-3">
                <div class="fs-4 fw-bold <?= $lowStockCount > 0 ? 'text-danger' : 'text-success' ?>">
                    <?= $lowStockCount ?>
                </div>
                <div class="text-muted small">Low Stock Alerts</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center shadow-sm">
            <div class="card-body py-3">
                <div class="fs-4 fw-bold text-success">+<?= number_format($totalStockIn, 2) ?></div>
                <div class="text-muted small">Stock In (30d)</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center shadow-sm">
            <div class="card-body py-3">
                <div class="fs-4 fw-bold text-danger">−<?= number_format($totalStockOut, 2) ?></div>
                <div class="text-muted small">Stock Out (30d)</div>
            </div>
        </div>
    </div>
</div>

<!-- Materials table -->
<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 small">
                <thead class="table-light">
                    <tr>
                        <th>Material</th>
                        <th>Unit</th>
                        <th class="text-end">Current Stock</th>
                        <th class="text-end">Reorder Level</th>
                        <th class="text-end">In (30d)</th>
                        <th class="text-end">Out (30d)</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rows)): ?>
                    <tr><td colspan="8" class="text-muted text-center py-4">No materials found.</td></tr>
                    <?php else: ?>
                    <?php foreach ($rows as $row): ?>
                    <?php $isLow = $row['stock_qty'] <= $row['reorder_level']; ?>
                    <tr class="<?= $isLow ? 'table-danger' : '' ?>">
                        <td class="fw-semibold"><?= Html::encode($row['name']) ?></td>
                        <td class="text-muted"><?= Html::encode($row['unit']) ?></td>
                        <td class="text-end fw-semibold <?= $isLow ? 'text-danger' : '' ?>">
                            <?= number_format((float) $row['stock_qty'], 3) ?>
                        </td>
                        <td class="text-end text-muted">
                            <?= number_format((float) $row['reorder_level'], 3) ?>
                        </td>
                        <td class="text-end text-success">
                            +<?= number_format((float) $row['total_in'], 3) ?>
                        </td>
                        <td class="text-end text-danger">
                            −<?= number_format((float) $row['total_out'], 3) ?>
                        </td>
                        <td>
                            <?php if ($isLow): ?>
                            <span class="badge bg-danger">Low</span>
                            <?php else: ?>
                            <span class="badge bg-success">OK</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?= Html::a('<i class="bi bi-eye"></i>',
                                ['/inventory/raw-material/view', 'id' => $row['id']],
                                ['class' => 'btn btn-sm btn-outline-secondary']) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
