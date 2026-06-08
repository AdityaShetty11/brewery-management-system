<?php
/** @var array $roleData */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Role Management';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <?= Html::a('<i class="bi bi-plus-lg me-1"></i>New Role', ['create'], ['class' => 'btn btn-sm btn-brew']) ?>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Role Name</th>
                    <th>Description</th>
                    <th class="text-center">Permissions</th>
                    <th class="text-center">Users</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($roleData as $i => $row): ?>
                <tr>
                    <td class="text-muted small"><?= $i + 1 ?></td>
                    <td>
                        <span class="badge brew-badge fs-6"><?= Html::encode($row['name']) ?></span>
                    </td>
                    <td class="text-muted small"><?= Html::encode($row['description']) ?></td>
                    <td class="text-center">
                        <span class="badge bg-secondary"><?= $row['permissionCount'] ?></span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-info text-dark"><?= $row['userCount'] ?></span>
                    </td>
                    <td class="text-center">
                        <?= Html::a('<i class="bi bi-eye"></i>', ['view', 'name' => $row['name']],
                            ['class' => 'btn btn-sm btn-outline-secondary me-1', 'title' => 'View']) ?>
                        <?= Html::a('<i class="bi bi-pencil"></i>', ['update', 'name' => $row['name']],
                            ['class' => 'btn btn-sm btn-outline-primary me-1', 'title' => 'Edit']) ?>
                        <?php
                        $coreRoles = ['admin', 'customer', 'staff', 'brewmaster', 'warehouse'];
                        if (!in_array($row['name'], $coreRoles, true) && $row['userCount'] === 0):
                        ?>
                            <?= Html::a('<i class="bi bi-trash"></i>', ['delete', 'name' => $row['name']], [
                                'class'  => 'btn btn-sm btn-outline-danger',
                                'method' => 'post',
                                'data'   => ['confirm' => "Delete role \"{$row['name']}\"?"],
                                'title'  => 'Delete',
                            ]) ?>
                        <?php else: ?>
                            <button class="btn btn-sm btn-outline-danger" disabled title="Cannot delete built-in or assigned role">
                                <i class="bi bi-trash"></i>
                            </button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.brew-badge {
    background: var(--brew-gold);
    color: var(--brew-darker);
    font-family: 'Cinzel', serif;
    letter-spacing: 0.4px;
}
.btn-brew {
    background: var(--brew-gold);
    color: var(--brew-darker);
    border: none;
    font-family: 'Cinzel', serif;
    font-size: 0.82rem;
    font-weight: 600;
}
.btn-brew:hover {
    background: var(--brew-gold-lt, #e8a020);
    color: var(--brew-darker);
}
</style>
