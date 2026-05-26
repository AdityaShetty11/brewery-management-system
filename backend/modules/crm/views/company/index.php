<?php

/** @var yii\web\View $this */
/** @var backend\modules\crm\models\CompanySearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Customer Companies';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0"><i class="bi bi-building me-2 text-secondary"></i><?= Html::encode($this->title) ?></h1>
    <?= Html::a('<i class="bi bi-plus-lg me-1"></i>Add Company', ['create'], ['class' => 'btn btn-dark']) ?>
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
                    'attribute' => 'name',
                    'format'    => 'raw',
                    'value'     => fn($m) => Html::a(Html::encode($m->name), ['view', 'id' => $m->id]),
                ],
                'industry',
                'city',
                'country',
                'phone',
                'email:email',

                [
                    'class'    => ActionColumn::class,
                    'template' => '{view} {update} {delete}',
                    'urlCreator' => fn($action, $model) => Url::to([$action, 'id' => $model->id]),
                    'buttons' => [
                        'view'   => fn($url, $model) => Html::a('<i class="bi bi-eye"></i>', $url, ['class' => 'btn btn-sm btn-outline-secondary me-1']),
                        'update' => fn($url, $model) => Html::a('<i class="bi bi-pencil"></i>', $url, ['class' => 'btn btn-sm btn-outline-primary me-1']),
                        'delete' => fn($url, $model) => Html::a('<i class="bi bi-trash"></i>', $url, [
                            'class' => 'btn btn-sm btn-outline-danger',
                            'data'  => ['confirm' => "Delete \"{$model->name}\"?", 'method' => 'post'],
                        ]),
                    ],
                ],
            ],
        ]) ?>
    </div>
</div>
