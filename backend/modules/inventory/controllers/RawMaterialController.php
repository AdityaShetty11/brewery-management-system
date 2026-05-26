<?php

namespace backend\modules\inventory\controllers;

use backend\modules\inventory\models\RawMaterialSearch;
use backend\modules\inventory\models\StockTransactionSearch;
use common\models\AuditLog;
use common\models\RawMaterial;
use common\models\StockTransaction;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Raw material CRUD and manual stock adjustments.
 * Requires `manageInventory` permission (warehouse role+).
 */
class RawMaterialController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    ['allow' => true, 'roles' => ['manageInventory']],
                ],
            ],
            'verbs' => [
                'class'   => VerbFilter::class,
                'actions' => [
                    'delete'       => ['post'],
                    'adjust-stock' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex(): string
    {
        $searchModel  = new RawMaterialSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionLowStock(): string
    {
        $searchModel = new RawMaterialSearch();
        $params      = Yii::$app->request->queryParams;

        // Force the low-stock filter
        $params[get_class($searchModel)]['lowStock'] = '1';
        $dataProvider = $searchModel->search($params);

        return $this->render('low-stock', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView(int $id): string
    {
        $material     = $this->findModel($id);
        $txSearch     = new StockTransactionSearch();
        $txProvider   = $txSearch->search(
            Yii::$app->request->queryParams,
            StockTransaction::ITEM_RAW,
            $id
        );

        return $this->render('view', [
            'model'        => $material,
            'txSearch'     => $txSearch,
            'txProvider'   => $txProvider,
        ]);
    }

    public function actionCreate(): \yii\web\Response|string
    {
        $model = new RawMaterial();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            AuditLog::record('inventory.raw_material.created', 'RawMaterial', $model->id);
            Yii::$app->session->setFlash('success', "Raw material \"{$model->name}\" created.");
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', ['model' => $model]);
    }

    public function actionUpdate(int $id): \yii\web\Response|string
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            AuditLog::record('inventory.raw_material.updated', 'RawMaterial', $model->id);
            Yii::$app->session->setFlash('success', "Material updated.");
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', ['model' => $model]);
    }

    public function actionAdjustStock(int $id): \yii\web\Response
    {
        $model   = $this->findModel($id);
        $request = Yii::$app->request;
        $qty     = (float) $request->post('qty', 0);
        $type    = $request->post('type', StockTransaction::TYPE_ADJUSTMENT);
        $notes   = $request->post('notes', 'Manual adjustment');

        if ($qty <= 0) {
            Yii::$app->session->setFlash('error', 'Quantity must be greater than zero.');
            return $this->redirect(['view', 'id' => $id]);
        }

        $adjustedQty = $type === StockTransaction::TYPE_OUT ? -$qty : $qty;
        $oldQty      = $model->stock_qty;

        $model->adjustStock($adjustedQty, $type, 'manual', null, $notes);

        AuditLog::record(
            'inventory.stock_adjusted',
            'RawMaterial',
            $id,
            ['stock_qty' => $oldQty],
            ['stock_qty' => $model->stock_qty]
        );

        Yii::$app->session->setFlash('success',
            "Stock updated for \"{$model->name}\". New quantity: {$model->stock_qty} {$model->unit}."
        );

        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionDelete(int $id): \yii\web\Response
    {
        $model = $this->findModel($id);
        $model->softDelete();

        AuditLog::record('inventory.raw_material.deleted', 'RawMaterial', $id);
        Yii::$app->session->setFlash('success', "Material \"{$model->name}\" removed.");

        return $this->redirect(['index']);
    }

    private function findModel(int $id): RawMaterial
    {
        $model = RawMaterial::findOne($id);

        if ($model === null) {
            throw new NotFoundHttpException('Raw material not found.');
        }

        return $model;
    }
}
