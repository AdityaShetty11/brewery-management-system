<?php

/** @var yii\web\View $this */
/** @var common\models\User $model */
/** @var string[] $assignedRoles */
/** @var string[] $allRoles */

use common\models\User;
use yii\helpers\Html;

$this->title = $model->username;

$statusLabel = match ($model->status) {
    User::STATUS_ACTIVE   => ['Active', 'success'],
    User::STATUS_INACTIVE => ['Inactive', 'warning'],
    default               => ['Deleted', 'danger'],
};

$auth = Yii::$app->authManager;

// Build permission matrix: role => [permissions]
$rolePermissions = [];
foreach ($allRoles as $roleName) {
    $role = $auth->getRole($roleName);
    $perms = $auth->getPermissionsByRole($roleName);
    $rolePermissions[$roleName] = array_keys($perms);
}
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">
        <i class="bi bi-person-circle me-2 text-secondary"></i><?= Html::encode($this->title) ?>
    </h1>
    <div class="d-flex gap-2">
        <?= Html::a('<i class="bi bi-pencil me-1"></i>Edit', ['update', 'id' => $model->id], ['class' => 'btn btn-outline-primary btn-sm']) ?>
        <?= Html::a('<i class="bi bi-arrow-left me-1"></i>Back', ['index'], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
    </div>
</div>

<div class="row g-3">
    <!-- User Info -->
    <div class="col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-header"><i class="bi bi-person me-1"></i>Account Info</div>
            <div class="card-body">
                <dl class="mb-0">
                    <dt class="small text-muted">Username</dt>
                    <dd><?= Html::encode($model->username) ?></dd>

                    <dt class="small text-muted">Email</dt>
                    <dd><?= Html::encode($model->email) ?></dd>

                    <dt class="small text-muted">Status</dt>
                    <dd><span class="badge bg-<?= $statusLabel[1] ?>"><?= $statusLabel[0] ?></span></dd>

                    <dt class="small text-muted">Registered</dt>
                    <dd><?= date('d M Y, H:i', $model->created_at) ?></dd>
                </dl>
            </div>
        </div>
    </div>

    <!-- Assigned Role -->
    <div class="col-md-8">
        <div class="card shadow-sm mb-3">
            <div class="card-header"><i class="bi bi-shield-lock me-1"></i>Assigned Role</div>
            <div class="card-body">
                <?php if (empty($assignedRoles)): ?>
                    <p class="text-muted mb-2">No role assigned.</p>
                <?php else: ?>
                    <div class="mb-3">
                        <?php foreach ($assignedRoles as $role): ?>
                            <span class="badge brew-badge me-1 fs-6"><?= Html::encode($role) ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Quick role-assign form -->
                <?php $form = \yii\bootstrap5\ActiveForm::begin(['action' => ['assign-role', 'id' => $model->id], 'method' => 'post']); ?>
                <div class="d-flex gap-2 align-items-end">
                    <div class="flex-grow-1">
                        <?= Html::dropDownList('role', $assignedRoles[0] ?? '', array_combine($allRoles, $allRoles) + ['' => '— No Role —'], ['class' => 'form-select form-select-sm']) ?>
                    </div>
                    <?= Html::submitButton('<i class="bi bi-check-lg me-1"></i>Assign', ['class' => 'btn btn-dark btn-sm']) ?>
                </div>
                <?php \yii\bootstrap5\ActiveForm::end(); ?>
            </div>
        </div>

        <!-- Permission Matrix -->
        <div class="card shadow-sm">
            <div class="card-header"><i class="bi bi-key me-1"></i>Permissions (via role hierarchy)</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered mb-0 small">
                        <thead>
                            <tr>
                                <th>Permission</th>
                                <?php foreach ($allRoles as $r): ?>
                                    <th class="text-center <?= in_array($r, $assignedRoles) ? 'table-active' : '' ?>">
                                        <?= Html::encode($r) ?>
                                    </th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $allPerms = array_unique(array_merge(...array_values($rolePermissions)));
                            sort($allPerms);
                            foreach ($allPerms as $perm):
                            ?>
                            <tr>
                                <td><code class="text-brew"><?= Html::encode($perm) ?></code></td>
                                <?php foreach ($allRoles as $r): ?>
                                    <td class="text-center <?= in_array($r, $assignedRoles) ? 'table-active' : '' ?>">
                                        <?= in_array($perm, $rolePermissions[$r]) ? '<i class="bi bi-check-circle-fill text-success"></i>' : '<span class="text-muted">·</span>' ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.brew-badge {
    background: var(--brew-gold);
    color: var(--brew-darker);
    font-family: 'Cinzel', serif;
    font-size: 0.75rem;
    letter-spacing: 0.4px;
}
.text-brew { color: var(--brew-copper); }
.table thead th.table-active {
    background: rgba(200,134,10,0.2) !important;
    color: var(--brew-dark);
}
.table td.table-active {
    background: rgba(200,134,10,0.08) !important;
}
</style>
