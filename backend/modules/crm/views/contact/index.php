<?php

/** @var yii\web\View $this */
/** @var backend\modules\crm\models\ContactSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var common\models\CustomerCompany|null $company */

use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = $company ? "Contacts — {$company->name}" : 'All Contacts';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">
        <i class="bi bi-people me-2 text-secondary"></i><?= Html::encode($this->title) ?>
    </h1>
    <div class="d-flex gap-2">
        <?php if ($company): ?>
            <?= Html::a('<i class="bi bi-building me-1"></i>Back to Company', ['/crm/company/view', 'id' => $company->id], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
            <?= Html::a('<i class="bi bi-plus-lg me-1"></i>Add Contact', ['create', 'company_id' => $company->id], ['class' => 'btn btn-dark btn-sm']) ?>
        <?php else: ?>
            <?= Html::a('<i class="bi bi-plus-lg me-1"></i>Add Contact', ['create'], ['class' => 'btn btn-dark']) ?>
        <?php endif; ?>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel'  => $searchModel,
            'tableOptions' => ['class' => 'table table-hover table-striped mb-0'],
            'layout'       => '{items}{pager}',
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                [
                    'attribute' => 'last_name',
                    'format'    => 'raw',
                    'value'     => fn($m) => Html::encode($m->last_name),
                ],
                [
                    'attribute' => 'first_name',
                    'value'     => fn($m) => Html::encode($m->first_name),
                ],
                [
                    'attribute' => 'company_id',
                    'label'     => 'Company',
                    'format'    => 'raw',
                    'value'     => fn($m) => $m->company
                        ? Html::a(Html::encode($m->company->name), ['/crm/company/view', 'id' => $m->company_id])
                        : '—',
                ],
                'role',
                'phone',
                'email:email',

                [
                    'class'    => ActionColumn::class,
                    'template' => '{update} {delete}',
                    'urlCreator' => fn($action, $model) => Url::to([$action, 'id' => $model->id]),
                    'buttons' => [
                        'update' => fn($url, $model) => Html::a('<i class="bi bi-pencil"></i>', $url, ['class' => 'btn btn-sm btn-outline-primary me-1']),
                        'delete' => fn($url, $model) => Html::a('<i class="bi bi-trash"></i>', $url, [
                            'class' => 'btn btn-sm btn-outline-danger',
                            'data'  => ['confirm' => "Remove contact \"{$model->getFullName()}\"?", 'method' => 'post'],
                        ]),
                    ],
                ],
            ],
        ]) ?>
    </div>
</div>
