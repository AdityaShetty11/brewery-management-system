<?php

/** @var yii\web\View $this */
/** @var common\models\CustomerCompany $model */
/** @var common\models\CustomerContact[] $contacts */
/** @var common\models\CrmInteraction[] $interactions */

use yii\helpers\Html;

$this->title = $model->name;
?>

<!-- ── Header ─────────────────────────────────────────────────────── -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h4 mb-0"><?= Html::encode($model->name) ?></h1>
        <small class="text-muted"><?= Html::encode($model->industry ?? 'No industry set') ?></small>
    </div>
    <div>
        <?= Html::a('<i class="bi bi-pencil me-1"></i>Edit', ['update', 'id' => $model->id], ['class' => 'btn btn-outline-primary btn-sm me-1']) ?>
        <?= Html::a('<i class="bi bi-arrow-left me-1"></i>Back', ['index'], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
    </div>
</div>

<div class="row g-4">

    <!-- ── Company Details ──────────────────────────────────────────── -->
    <div class="col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-header fw-semibold">
                <i class="bi bi-info-circle me-1"></i>Details
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-5 text-muted">Phone</dt>
                    <dd class="col-sm-7"><?= Html::encode($model->phone ?? '—') ?></dd>

                    <dt class="col-sm-5 text-muted">Email</dt>
                    <dd class="col-sm-7"><?= $model->email ? Html::mailto(Html::encode($model->email)) : '—' ?></dd>

                    <dt class="col-sm-5 text-muted">Address</dt>
                    <dd class="col-sm-7"><?= Html::encode($model->getFullAddress() ?: '—') ?></dd>

                    <dt class="col-sm-5 text-muted">Notes</dt>
                    <dd class="col-sm-7"><?= Html::encode($model->notes ?? '—') ?></dd>
                </dl>
            </div>
        </div>
    </div>

    <!-- ── Contacts ─────────────────────────────────────────────────── -->
    <div class="col-md-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-semibold"><i class="bi bi-people me-1"></i>Contacts</span>
                <?= Html::a('<i class="bi bi-plus-lg"></i>', ['/crm/contact/create', 'company_id' => $model->id], ['class' => 'btn btn-sm btn-outline-dark']) ?>
            </div>
            <div class="card-body p-0">
                <?php if (empty($contacts)): ?>
                    <p class="text-muted p-3 mb-0">No contacts yet.</p>
                <?php else: ?>
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th><th>Role</th><th>Email</th><th>Phone</th><th></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($contacts as $contact): ?>
                            <tr>
                                <td><?= Html::encode($contact->getFullName()) ?></td>
                                <td><?= Html::encode($contact->role ?? '—') ?></td>
                                <td><?= $contact->email ? Html::mailto(Html::encode($contact->email)) : '—' ?></td>
                                <td><?= Html::encode($contact->phone ?? '—') ?></td>
                                <td>
                                    <?= Html::a('<i class="bi bi-pencil"></i>', ['/crm/contact/update', 'id' => $contact->id], ['class' => 'btn btn-sm btn-outline-primary me-1']) ?>
                                    <?= Html::a('<i class="bi bi-trash"></i>', ['/crm/contact/delete', 'id' => $contact->id], [
                                        'class' => 'btn btn-sm btn-outline-danger',
                                        'data'  => ['confirm' => 'Delete this contact?', 'method' => 'post'],
                                    ]) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

        <!-- ── Interaction Timeline ─────────────────────────────────── -->
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-semibold"><i class="bi bi-clock-history me-1"></i>Interaction History</span>
                <?= Html::a('<i class="bi bi-plus-lg"></i> Log', ['/crm/interaction/create', 'company_id' => $model->id], ['class' => 'btn btn-sm btn-outline-dark']) ?>
            </div>
            <div class="card-body">
                <?php if (empty($interactions)): ?>
                    <p class="text-muted mb-0">No interactions logged yet.</p>
                <?php else: ?>
                    <div class="timeline">
                    <?php foreach ($interactions as $ix): ?>
                        <div class="d-flex gap-3 mb-3">
                            <div class="flex-shrink-0 mt-1">
                                <span class="badge bg-secondary rounded-pill">
                                    <i class="bi <?= $ix->getTypeIcon() ?>"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between">
                                    <strong><?= Html::encode($ix->getTypeLabel()) ?></strong>
                                    <small class="text-muted"><?= Html::encode($ix->interaction_at) ?></small>
                                </div>
                                <?php if ($ix->contact): ?>
                                    <small class="text-muted">with <?= Html::encode($ix->contact->getFullName()) ?></small><br>
                                <?php endif; ?>
                                <p class="mb-0 mt-1"><?= Html::encode($ix->summary) ?></p>
                                <small class="text-muted">
                                    Logged by <?= Html::encode($ix->staff->username ?? 'unknown') ?>
                                    &nbsp;
                                    <?= Html::a('edit', ['/crm/interaction/update', 'id' => $ix->id], ['class' => 'link-secondary']) ?>
                                    &nbsp;
                                    <?= Html::a('delete', ['/crm/interaction/delete', 'id' => $ix->id], [
                                        'class' => 'link-danger',
                                        'data'  => ['confirm' => 'Delete this entry?', 'method' => 'post'],
                                    ]) ?>
                                </small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
