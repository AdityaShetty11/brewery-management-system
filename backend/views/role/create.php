<?php
/** @var string $name */
/** @var string $description */
/** @var string[] $allPermissions */

use yii\helpers\Html;

$this->title = 'Create Role';
?>

<div class="mb-3">
    <?= Html::a('<i class="bi bi-arrow-left me-1"></i>All Roles', ['index'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
</div>

<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card shadow-sm">
            <div class="card-header card-header-brew">
                <i class="bi bi-shield-plus me-1"></i> New Role
            </div>
            <div class="card-body">
                <?php $form = \yii\bootstrap5\ActiveForm::begin(['id' => 'role-form']); ?>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Role Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" value="<?= Html::encode($name) ?>"
                           class="form-control" placeholder="e.g. supervisor" required
                           pattern="[a-zA-Z0-9_]+" title="Letters, numbers and underscores only">
                    <div class="form-text">Use lowercase letters, numbers, underscores only. No spaces.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Description</label>
                    <input type="text" name="description" value="<?= Html::encode($description) ?>"
                           class="form-control" placeholder="Short description of this role">
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Assign Permissions</label>
                    <div class="border rounded p-3" style="max-height:260px;overflow-y:auto;">
                        <?php foreach ($allPermissions as $perm): ?>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"
                                   name="permissions[]" value="<?= Html::encode($perm) ?>"
                                   id="perm_<?= Html::encode($perm) ?>">
                            <label class="form-check-label small" for="perm_<?= Html::encode($perm) ?>">
                                <?= Html::encode($perm) ?>
                            </label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <?= Html::submitButton('<i class="bi bi-check-lg me-1"></i>Create Role',
                        ['class' => 'btn btn-brew']) ?>
                    <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
                </div>

                <?php \yii\bootstrap5\ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<style>
.card-header-brew { background: var(--brew-dark); color: var(--brew-gold); font-family: 'Cinzel', serif; font-size: 0.85rem; letter-spacing: 0.8px; border-bottom: 1px solid var(--brew-gold); }
.btn-brew { background: var(--brew-gold); color: var(--brew-darker); border: none; font-family: 'Cinzel', serif; font-size: 0.82rem; font-weight: 600; }
.btn-brew:hover { background: var(--brew-gold-lt, #e8a020); color: var(--brew-darker); }
</style>
