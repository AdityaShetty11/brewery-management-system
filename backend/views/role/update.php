<?php
/** @var yii\rbac\Role $role */
/** @var string[] $allPermissions */
/** @var string[] $assignedPermissions */

use yii\helpers\Html;

$this->title = 'Edit Role: ' . $role->name;
?>

<div class="mb-3">
    <?= Html::a('<i class="bi bi-arrow-left me-1"></i>Back', ['view', 'name' => $role->name], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
</div>

<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card shadow-sm">
            <div class="card-header card-header-brew">
                <i class="bi bi-pencil me-1"></i> Edit Role:
                <span class="badge brew-badge ms-1"><?= Html::encode($role->name) ?></span>
            </div>
            <div class="card-body">
                <?php $form = \yii\bootstrap5\ActiveForm::begin(); ?>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Role Name</label>
                    <input type="text" class="form-control" value="<?= Html::encode($role->name) ?>" disabled>
                    <div class="form-text text-muted">Role name cannot be changed.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Description</label>
                    <input type="text" name="description"
                           value="<?= Html::encode($role->description) ?>"
                           class="form-control" placeholder="Short description">
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Permissions</label>
                    <div class="border rounded p-3" style="max-height:260px;overflow-y:auto;">
                        <?php foreach ($allPermissions as $perm): ?>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"
                                   name="permissions[]" value="<?= Html::encode($perm) ?>"
                                   id="perm_<?= Html::encode($perm) ?>"
                                   <?= in_array($perm, $assignedPermissions, true) ? 'checked' : '' ?>>
                            <label class="form-check-label small" for="perm_<?= Html::encode($perm) ?>">
                                <?= Html::encode($perm) ?>
                            </label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="form-text">Note: inherited permissions from child roles are not shown here.</div>
                </div>

                <div class="d-flex gap-2">
                    <?= Html::submitButton('<i class="bi bi-check-lg me-1"></i>Save Changes',
                        ['class' => 'btn btn-brew']) ?>
                    <?= Html::a('Cancel', ['view', 'name' => $role->name], ['class' => 'btn btn-outline-secondary']) ?>
                </div>

                <?php \yii\bootstrap5\ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<style>
.brew-badge { background: var(--brew-gold); color: var(--brew-darker); font-family: 'Cinzel', serif; letter-spacing: 0.4px; }
.card-header-brew { background: var(--brew-dark); color: var(--brew-gold); font-family: 'Cinzel', serif; font-size: 0.85rem; letter-spacing: 0.8px; border-bottom: 1px solid var(--brew-gold); }
.btn-brew { background: var(--brew-gold); color: var(--brew-darker); border: none; font-family: 'Cinzel', serif; font-size: 0.82rem; font-weight: 600; }
.btn-brew:hover { background: var(--brew-gold-lt, #e8a020); color: var(--brew-darker); }
</style>
