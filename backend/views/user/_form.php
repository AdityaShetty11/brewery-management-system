<?php

/** @var yii\web\View $this */
/** @var common\models\User $model */
/** @var string[] $roles      all available role names */
/** @var string   $currentRole currently assigned role name ('' for none) */

use common\models\User;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

$isNew = $model->isNewRecord;
$roleOptions = array_combine($roles, array_map(fn($r) => ucfirst($r), $roles));
$roleOptions = ['' => '— No Role —'] + $roleOptions;
?>

<?php $form = ActiveForm::begin(['id' => 'user-form']); ?>

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label cinzel-label">Username</label>
        <input type="text"
               name="User[username]"
               value="<?= Html::encode($model->username) ?>"
               class="form-control <?= $model->hasErrors('username') ? 'is-invalid' : '' ?>"
               required autofocus>
        <?php if ($model->hasErrors('username')): ?>
            <div class="invalid-feedback"><?= Html::encode($model->getFirstError('username')) ?></div>
        <?php endif; ?>
    </div>

    <div class="col-md-6">
        <label class="form-label cinzel-label">Email</label>
        <input type="email"
               name="User[email]"
               value="<?= Html::encode($model->email) ?>"
               class="form-control <?= $model->hasErrors('email') ? 'is-invalid' : '' ?>"
               required>
        <?php if ($model->hasErrors('email')): ?>
            <div class="invalid-feedback"><?= Html::encode($model->getFirstError('email')) ?></div>
        <?php endif; ?>
    </div>

    <div class="col-md-6">
        <label class="form-label cinzel-label">
            Password <?= $isNew ? '' : '<span class="text-muted small">(leave blank to keep current)</span>' ?>
        </label>
        <input type="password"
               name="User[password]"
               class="form-control <?= $model->hasErrors('password_hash') ? 'is-invalid' : '' ?>"
               <?= $isNew ? 'required' : '' ?>
               placeholder="<?= $isNew ? 'At least 8 characters' : '••••••••' ?>"
               minlength="8">
    </div>

    <div class="col-md-3">
        <label class="form-label cinzel-label">Status</label>
        <?= Html::dropDownList('User[status]', $model->status, [
            User::STATUS_ACTIVE   => 'Active',
            User::STATUS_INACTIVE => 'Inactive',
        ], ['class' => 'form-select']) ?>
    </div>

    <div class="col-md-3">
        <label class="form-label cinzel-label">Role</label>
        <?= Html::dropDownList('role', $currentRole, $roleOptions, ['class' => 'form-select']) ?>
    </div>
</div>

<hr class="my-4" style="border-color: rgba(200,134,10,0.25);">

<div class="d-flex gap-2">
    <?= Html::submitButton(
        $isNew ? '<i class="bi bi-person-plus me-1"></i>Create User' : '<i class="bi bi-check-lg me-1"></i>Save Changes',
        ['class' => 'btn btn-dark']
    ) ?>
    <?= Html::a('Cancel', $isNew ? ['index'] : ['view', 'id' => $model->id], ['class' => 'btn btn-outline-secondary']) ?>
</div>

<?php ActiveForm::end(); ?>

<style>
.cinzel-label {
    font-family: 'Cinzel', serif;
    font-size: 0.78rem;
    letter-spacing: 0.5px;
    color: var(--brew-dark);
}
</style>
