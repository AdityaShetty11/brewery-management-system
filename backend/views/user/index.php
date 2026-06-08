<?php

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

use common\models\User;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'User Management';

$auth = Yii::$app->authManager;
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0"><i class="bi bi-people-fill me-2 text-secondary"></i><?= Html::encode($this->title) ?></h1>
    <?= Html::a('<i class="bi bi-plus-lg me-1"></i>Add User', ['create'], ['class' => 'btn btn-dark']) ?>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'tableOptions' => ['class' => 'table table-hover table-striped mb-0'],
            'layout'       => '{items}{pager}',
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                'username',
                'email:email',

                [
                    'label'  => 'Role',
                    'format' => 'raw',
                    'value'  => function ($model) use ($auth) {
                        $roles = array_keys($auth->getRolesByUser($model->id));
                        if (empty($roles)) {
                            return '<span class="badge bg-secondary">—</span>';
                        }
                        return implode(' ', array_map(fn($r) => '<span class="badge brew-badge">' . Html::encode($r) . '</span>', $roles));
                    },
                ],

                [
                    'attribute' => 'status',
                    'format'    => 'raw',
                    'value'     => fn($m) => match ($m->status) {
                        User::STATUS_ACTIVE   => '<span class="badge bg-success">Active</span>',
                        User::STATUS_INACTIVE => '<span class="badge bg-warning text-dark">Inactive</span>',
                        default               => '<span class="badge bg-danger">Deleted</span>',
                    },
                ],

                [
                    'attribute' => 'created_at',
                    'label'     => 'Registered',
                    'format'    => ['date', 'php:d M Y'],
                ],

                [
                    'class'    => ActionColumn::class,
                    'template' => '{view} {update} {delete}',
                    'urlCreator' => fn($action, $model) => Url::to([$action, 'id' => $model->id]),
                    'buttons' => [
                        'view'   => fn($url, $model) => Html::a('<i class="bi bi-eye"></i>', $url, ['class' => 'btn btn-sm btn-outline-secondary me-1']),
                        'update' => fn($url, $model) => Html::a('<i class="bi bi-pencil"></i>', $url, ['class' => 'btn btn-sm btn-outline-primary me-1']),
                        'delete' => fn($url, $model) => Html::a('<i class="bi bi-trash"></i>', $url, [
                            'class' => 'btn btn-sm btn-outline-danger',
                            'data'  => ['confirm' => "Delete user \"{$model->username}\"?", 'method' => 'post'],
                        ]),
                    ],
                ],
            ],
        ]) ?>
    </div>
</div>

<style>
.brew-badge {
    background: var(--brew-gold);
    color: var(--brew-darker);
    font-family: 'Cinzel', serif;
    font-size: 0.7rem;
    letter-spacing: 0.4px;
}
</style>
