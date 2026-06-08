<?php
/** @var yii\rbac\Role $role */
/** @var string[] $allPermissions */
/** @var string[] $assignedPermissions */
/** @var string[] $childRoles */
/** @var common\models\User[] $users */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Role: ' . $role->name;
?>

<div class="d-flex gap-2 mb-3">
    <?= Html::a('<i class="bi bi-arrow-left me-1"></i>All Roles', ['index'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
    <?= Html::a('<i class="bi bi-pencil me-1"></i>Edit Role', ['update', 'name' => $role->name], ['class' => 'btn btn-sm btn-brew']) ?>
</div>

<div class="row g-3 mb-4">
    <!-- Role Info -->
    <div class="col-md-4">
        <div class="card h-100 shadow-sm">
            <div class="card-header card-header-brew">
                <i class="bi bi-shield-lock me-1"></i> Role Details
            </div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr>
                        <th class="text-muted fw-normal">Name</th>
                        <td><span class="badge brew-badge fs-6"><?= Html::encode($role->name) ?></span></td>
                    </tr>
                    <tr>
                        <th class="text-muted fw-normal">Description</th>
                        <td><?= Html::encode($role->description ?: '—') ?></td>
                    </tr>
                    <tr>
                        <th class="text-muted fw-normal">Inherits</th>
                        <td>
                            <?php if ($childRoles): ?>
                                <?php foreach ($childRoles as $cr): ?>
                                    <span class="badge bg-secondary me-1"><?= Html::encode($cr) ?></span>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted fw-normal">Users</th>
                        <td><span class="badge bg-info text-dark"><?= count($users) ?></span></td>
                    </tr>
                    <tr>
                        <th class="text-muted fw-normal">Permissions</th>
                        <td><span class="badge bg-secondary"><?= count($assignedPermissions) ?></span></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Permissions Matrix -->
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header card-header-brew">
                <i class="bi bi-key me-1"></i> Direct Permissions
            </div>
            <div class="card-body p-0">
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Permission</th>
                            <th class="text-center">Granted</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($allPermissions as $perm): ?>
                        <tr>
                            <td class="small"><?= Html::encode($perm) ?></td>
                            <td class="text-center">
                                <?php if (in_array($perm, $assignedPermissions, true)): ?>
                                    <i class="bi bi-check-circle-fill text-success"></i>
                                <?php else: ?>
                                    <i class="bi bi-dash-circle text-muted"></i>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Users with this role -->
<div class="card shadow-sm">
    <div class="card-header card-header-brew">
        <i class="bi bi-people me-1"></i> Users with this Role
        <span class="badge bg-secondary ms-1"><?= count($users) ?></span>
    </div>
    <?php if ($users): ?>
    <div class="card-body p-0">
        <table class="table table-sm table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $i => $user): ?>
                <tr>
                    <td class="text-muted"><?= $i + 1 ?></td>
                    <td><?= Html::encode($user->username) ?></td>
                    <td class="text-muted small"><?= Html::encode($user->email) ?></td>
                    <td>
                        <?php if ($user->status === \common\models\User::STATUS_ACTIVE): ?>
                            <span class="badge bg-success">Active</span>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <?= Html::a('<i class="bi bi-person"></i>', ['/user/view', 'id' => $user->id],
                            ['class' => 'btn btn-sm btn-outline-secondary', 'title' => 'View User']) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="card-body text-muted text-center py-3">
        <i class="bi bi-people me-1"></i> No users assigned to this role yet.
    </div>
    <?php endif; ?>
</div>

<style>
.brew-badge { background: var(--brew-gold); color: var(--brew-darker); font-family: 'Cinzel', serif; letter-spacing: 0.4px; }
.card-header-brew { background: var(--brew-dark); color: var(--brew-gold); font-family: 'Cinzel', serif; font-size: 0.85rem; letter-spacing: 0.8px; border-bottom: 1px solid var(--brew-gold); }
.btn-brew { background: var(--brew-gold); color: var(--brew-darker); border: none; font-family: 'Cinzel', serif; font-size: 0.82rem; font-weight: 600; }
.btn-brew:hover { background: var(--brew-gold-lt, #e8a020); color: var(--brew-darker); }
</style>
