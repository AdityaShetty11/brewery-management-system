<?php

namespace backend\modules\production\controllers;

use backend\modules\production\models\ProductionOrderSearch;

use common\models\Order;
use common\models\Product;
use common\models\ProductionOrder;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Manages production orders.
 * Requires `manageProduction` permission (brewmaster+).
 */
class ProductionOrderController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    ['allow' => true, 'roles' => ['manageProduction']],
                ],
            ],
            'verbs' => [
                'class'   => VerbFilter::class,
                'actions' => ['transition' => ['post'], 'delete' => ['post']],
            ],
        ];
    }

    public function actionIndex(): string
    {
        $searchModel  = new ProductionOrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView(int $id): string
    {
        $model = $this->findModel($id);

        return $this->render('view', ['model' => $model]);
    }

    public function actionCreate(): \yii\web\Response|string
    {
        $model    = new ProductionOrder();
        $products = Product::find()->andWhere(['is_active' => 1])->orderBy('name')->all();
        $orders   = Order::find()
            ->andWhere(['status' => [Order::STATUS_CONFIRMED, Order::STATUS_IN_PRODUCTION]])
            ->orderBy('order_number')
            ->all();

        $productList = \yii\helpers\ArrayHelper::map($products, 'id', 'name');
        $orderList   = \yii\helpers\ArrayHelper::map($orders,   'id', 'order_number');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', "Production order {$model->reference} created.");
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model'       => $model,
            'productList' => $productList,
            'orderList'   => $orderList,
        ]);
    }

    public function actionUpdate(int $id): \yii\web\Response|string
    {
        $model    = $this->findModel($id);
        $products = \yii\helpers\ArrayHelper::map(
            Product::find()->andWhere(['is_active' => 1])->orderBy('name')->all(),
            'id', 'name'
        );
        $orderList = \yii\helpers\ArrayHelper::map(
            Order::find()->andWhere(['status' => [Order::STATUS_CONFIRMED, Order::STATUS_IN_PRODUCTION]])->all(),
            'id', 'order_number'
        );

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Production order updated.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model'       => $model,
            'productList' => $products,
            'orderList'   => $orderList,
        ]);
    }

    public function actionTransition(int $id): \yii\web\Response
    {
        $model     = $this->findModel($id);
        $newStatus = Yii::$app->request->post('status', '');

        if ($model->transitionTo($newStatus)) {
            Yii::$app->session->setFlash('success',
                "Production order {$model->reference} moved to \"{$model->getStatusLabel()}\"."
            );
        } else {
            Yii::$app->session->setFlash('error', implode(' ', $model->getFirstErrors()));
        }

        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionDelete(int $id): \yii\web\Response
    {
        $model = $this->findModel($id);

        if ($model->status !== ProductionOrder::STATUS_PLANNED) {
            Yii::$app->session->setFlash('error', 'Only planned production orders can be deleted.');
            return $this->redirect(['index']);
        }

        $model->softDelete();
        Yii::$app->session->setFlash('success', "Production order deleted.");

        return $this->redirect(['index']);
    }

    private function findModel(int $id): ProductionOrder
    {
        $model = ProductionOrder::find()
            ->with(['product', 'batches.ingredients.rawMaterial', 'batches.brewMaster'])
            ->andWhere(['production_order.id' => $id])
            ->one();

        if ($model === null) {
            throw new NotFoundHttpException('Production order not found.');
        }

        return $model;
    }
}
