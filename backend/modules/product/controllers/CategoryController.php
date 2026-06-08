<?php

namespace backend\modules\product\controllers;

use backend\modules\product\models\CategorySearch;

use common\models\ProductCategory;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Manages product category CRUD.
 * Requires `manageProducts` permission.
 */
class CategoryController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    ['allow' => true, 'roles' => ['manageProducts']],
                ],
            ],
            'verbs' => [
                'class'   => VerbFilter::class,
                'actions' => ['delete' => ['post']],
            ],
        ];
    }

    public function actionIndex(): string
    {
        $searchModel  = new CategorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate(): \yii\web\Response|string
    {
        $model = new ProductCategory();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', "Category \"{$model->name}\" created.");
            return $this->redirect(['index']);
        }

        return $this->render('create', ['model' => $model]);
    }

    public function actionUpdate(int $id): \yii\web\Response|string
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', "Category updated.");
            return $this->redirect(['index']);
        }

        return $this->render('update', ['model' => $model]);
    }

    public function actionDelete(int $id): \yii\web\Response
    {
        $model = $this->findModel($id);

        if ($model->getActiveProductCount() > 0) {
            Yii::$app->session->setFlash('error', "Cannot delete \"{$model->name}\" — it has active products.");
            return $this->redirect(['index']);
        }

        $model->softDelete();
        Yii::$app->session->setFlash('success', "Category deleted.");

        return $this->redirect(['index']);
    }

    private function findModel(int $id): ProductCategory
    {
        $model = ProductCategory::findOne($id);

        if ($model === null) {
            throw new NotFoundHttpException('Category not found.');
        }

        return $model;
    }
}
