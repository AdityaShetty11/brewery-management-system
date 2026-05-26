<?php

/** @var yii\web\View $this */
/** @var common\models\Product[] $products */
/** @var array $companies  [id => name] */

use common\models\Product;
use yii\helpers\Html;

$this->title = 'Place an Order';

// Group products by category for the UI
$grouped = [];
foreach ($products as $p) {
    $grouped[$p->category->name ?? 'Other'][] = $p;
}
?>

<div class="mb-3">
    <?= Html::a('<i class="bi bi-arrow-left me-1"></i>Back to My Orders', ['index'], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
</div>

<h1 class="h4 mb-4">Place a New Order</h1>

<?php $form = \yii\bootstrap5\ActiveForm::begin(['id' => 'order-form']); ?>

<div class="row g-4">

    <!-- ── Product selector ──────────────────────────────────────── -->
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header fw-semibold"><i class="bi bi-cup-straw me-1"></i>Select Products</div>
            <div class="card-body" id="items-container">
                <!-- First item row -->
                <div class="item-row row g-2 align-items-end mb-3">
                    <div class="col-md-8">
                        <label class="form-label small">Product</label>
                        <select name="product_id[]" class="form-select form-select-sm" required>
                            <option value="">— Choose a product —</option>
                            <?php foreach ($grouped as $catName => $catProducts): ?>
                                <optgroup label="<?= Html::encode($catName) ?>">
                                    <?php foreach ($catProducts as $p): ?>
                                        <option value="<?= $p->id ?>"
                                            data-price="<?= $p->unit_price ?>"
                                            <?= $p->stock_qty === 0 ? 'disabled' : '' ?>>
                                            <?= Html::encode($p->name) ?>
                                            (<?= $p->getPackagingLabel() ?>) — <?= $p->getFormattedPrice() ?>
                                            <?= $p->stock_qty === 0 ? ' [Out of stock]' : '' ?>
                                        </option>
                                    <?php endforeach; ?>
                                </optgroup>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small">Qty</label>
                        <input type="number" name="quantity[]" class="form-control form-control-sm" min="1" value="1" required>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-outline-danger btn-sm w-100 remove-row" disabled>
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="button" id="add-row" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-plus-lg me-1"></i>Add Another Product
                </button>
            </div>
        </div>
    </div>

    <!-- ── Order details ─────────────────────────────────────────── -->
    <div class="col-md-4">
        <div class="card shadow-sm mb-3">
            <div class="card-header fw-semibold"><i class="bi bi-info-circle me-1"></i>Details</div>
            <div class="card-body">
                <?php if (!empty($companies)): ?>
                    <div class="mb-3">
                        <label class="form-label small">Company (optional)</label>
                        <select name="company_id" class="form-select form-select-sm">
                            <option value="">— Personal / No company —</option>
                            <?php foreach ($companies as $id => $name): ?>
                                <option value="<?= $id ?>"><?= Html::encode($name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>

                <div class="mb-3">
                    <label class="form-label small">Notes</label>
                    <textarea name="notes" class="form-control form-control-sm" rows="3"
                        placeholder="Delivery instructions, special requests…"></textarea>
                </div>
            </div>
        </div>

        <div class="d-grid">
            <?= Html::submitButton('<i class="bi bi-bag-check me-1"></i>Place Order', ['class' => 'btn btn-dark btn-lg']) ?>
        </div>
        <p class="text-muted small text-center mt-2">
            Orders are reviewed by our team before dispatch.
        </p>
    </div>

</div>

<?php \yii\bootstrap5\ActiveForm::end(); ?>

<script>
// Clone item row when "Add Another Product" is clicked
document.getElementById('add-row').addEventListener('click', function () {
    const container  = document.getElementById('items-container');
    const firstRow   = container.querySelector('.item-row');
    const newRow     = firstRow.cloneNode(true);
    newRow.querySelector('select').value = '';
    newRow.querySelector('input[type=number]').value = 1;
    newRow.querySelector('.remove-row').disabled = false;
    container.appendChild(newRow);
});

// Remove an item row
document.getElementById('items-container').addEventListener('click', function (e) {
    if (e.target.closest('.remove-row')) {
        const row = e.target.closest('.item-row');
        if (document.querySelectorAll('.item-row').length > 1) {
            row.remove();
        }
    }
});
</script>
