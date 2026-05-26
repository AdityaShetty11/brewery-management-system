<?php

namespace backend\modules\product\controllers;

use backend\modules\product\models\ProductSearch;
use common\models\AuditLog;
use common\models\Product;
use common\models\ProductCategory;
use common\models\StockTransaction;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Manages the product catalogue — CRUD, stock adjustments, toggle active.
 * Requires `manageProducts` permission.
 */
class ProductController extends Controller
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
                'actions' => ['delete' => ['post'], 'toggle-active' => ['post'], 'adjust-stock' => ['post']],
            ],
        ];
    }

    public function actionIndex(): string
    {
        $searchModel  = new ProductSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView(int $id): string
    {
        $product      = $this->findModel($id);
        $transactions = StockTransaction::find()
            ->where(['item_type' => 'finished_good', 'item_id' => $id])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(20)
            ->all();

        return $this->render('view', [
            'model'        => $product,
            'transactions' => $transactions,
        ]);
    }

    public function actionCreate(): \yii\web\Response|string
    {
        $model      = new Product();
        $categories = $this->getCategoryList();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            AuditLog::record('product.created', 'Product', $model->id);
            Yii::$app->session->setFlash('success', "Product \"{$model->name}\" created.");
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', ['model' => $model, 'categories' => $categories]);
    }

    public function actionUpdate(int $id): \yii\web\Response|string
    {
        $model      = $this->findModel($id);
        $categories = $this->getCategoryList();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            AuditLog::record('product.updated', 'Product', $model->id);
            Yii::$app->session->setFlash('success', "Product updated.");
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', ['model' => $model, 'categories' => $categories]);
    }

    public function actionDelete(int $id): \yii\web\Response
    {
        $model = $this->findModel($id);
        $model->softDelete();

        AuditLog::record('product.deleted', 'Product', $id);
        Yii::$app->session->setFlash('success', "Product \"{$model->name}\" removed.");

        return $this->redirect(['index']);
    }

    public function actionToggleActive(int $id): \yii\web\Response
    {
        $model            = $this->findModel($id);
        $model->is_active = $model->is_active ? 0 : 1;
        $model->save(false);

        $state = $model->is_active ? 'activated' : 'deactivated';
        Yii::$app->session->setFlash('success', "Product {$state}.");

        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionAdjustStock(int $id): \yii\web\Response
    {
        $model    = $this->findModel($id);
        $request  = Yii::$app->request;
        $qty      = (int) $request->post('qty', 0);
        $type     = $request->post('type', StockTransaction::TYPE_ADJUSTMENT);
        $notes    = $request->post('notes', 'Manual adjustment');

        if ($qty === 0) {
            Yii::$app->session->setFlash('error', 'Quantity must not be zero.');
            return $this->redirect(['view', 'id' => $id]);
        }

        // Negative qty for 'out' transactions
        $adjustedQty = $type === StockTransaction::TYPE_OUT ? -abs($qty) : abs($qty);
        $model->adjustStock($adjustedQty, $type, 'manual', null, $notes);

        AuditLog::record('product.stock_adjusted', 'Product', $id, ['old' => $model->stock_qty - $adjustedQty], ['new' => $model->stock_qty]);
        Yii::$app->session->setFlash('success', "Stock updated. New quantity: {$model->stock_qty}.");

        return $this->redirect(['view', 'id' => $id]);
    }

    private function getCategoryList(): array
    {
        return ProductCategory::find()->select(['name', 'id'])->indexBy('id')->column();
    }

    private function findModel(int $id): Product
    {
        $model = Product::findOne($id);

        if ($model === null) {
            throw new NotFoundHttpException('Product not found.');
        }

        return $model;
    }
}
