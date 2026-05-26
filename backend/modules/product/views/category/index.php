<?php

/** @var yii\web\View $this */
/** @var backend\modules\product\models\CategorySearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Product Categories';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0"><i class="bi bi-tags me-2 text-secondary"></i><?= Html::encode($this->title) ?></h1>
    <div>
        <?= Html::a('<i class="bi bi-plus-lg me-1"></i>Add Category', ['create'], ['class' => 'btn btn-dark btn-sm']) ?>
        <?= Html::a('<i class="bi bi-cup-straw me-1"></i>View Products', ['/product/product/index'], ['class' => 'btn btn-outline-secondary btn-sm ms-1']) ?>
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
                'name',
                'description:ntext',
                [
                    'label'  => 'Products',
                    'format' => 'raw',
                    'value'  => fn($m) => '<span class="badge bg-secondary">' . $m->getActiveProductCount() . '</span>',
                ],
                [
                    'class'    => ActionColumn::class,
                    'template' => '{update} {delete}',
                    'urlCreator' => fn($action, $model) => Url::to([$action, 'id' => $model->id]),
                    'buttons' => [
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
