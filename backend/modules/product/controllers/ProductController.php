<?php

namespace backend\modules\product\controllers;

use backend\modules\product\models\ProductSearch;

use common\models\Product;
use common\models\ProductCategory;
use common\models\StockTransaction;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\FileHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

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

        if ($model->load(Yii::$app->request->post())) {
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            if ($model->validate()) {
                $this->handleImageUpload($model);
                if ($model->save(false)) {
                    Yii::$app->session->setFlash('success', "Product \"{$model->name}\" created.");
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }
        }

        return $this->render('create', ['model' => $model, 'categories' => $categories]);
    }

    public function actionUpdate(int $id): \yii\web\Response|string
    {
        $model      = $this->findModel($id);
        $categories = $this->getCategoryList();

        if ($model->load(Yii::$app->request->post())) {
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            if ($model->validate()) {
                $this->handleImageUpload($model);
                if ($model->save(false)) {
                    Yii::$app->session->setFlash('success', 'Product updated.');
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }
        }

        return $this->render('update', ['model' => $model, 'categories' => $categories]);
    }

    public function actionDelete(int $id): \yii\web\Response
    {
        $model = $this->findModel($id);
        $model->softDelete();

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

        Yii::$app->session->setFlash('success', "Stock updated. New quantity: {$model->stock_qty}.");

        return $this->redirect(['view', 'id' => $id]);
    }

    private function getCategoryList(): array
    {
        return ProductCategory::find()->select(['name', 'id'])->indexBy('id')->column();
    }

    /**
     * Saves the uploaded image file and updates $model->image.
     * Deletes the old image if a new one is provided.
     */
    private function handleImageUpload(Product $model): void
    {
        if ($model->imageFile === null) {
            return;
        }

        $uploadDir = Yii::getAlias('@webroot') . '/uploads/products';
        FileHelper::createDirectory($uploadDir);

        // Remove previous image file
        if ($model->image && file_exists($uploadDir . '/' . $model->image)) {
            @unlink($uploadDir . '/' . $model->image);
        }

        $fileName = 'product_' . uniqid() . '.' . $model->imageFile->extension;
        $model->imageFile->saveAs($uploadDir . '/' . $fileName);
        $model->image = $fileName;
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
