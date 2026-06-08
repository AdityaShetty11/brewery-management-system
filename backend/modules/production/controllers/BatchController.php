<?php

namespace backend\modules\production\controllers;


use common\models\Batch;
use common\models\BatchIngredient;
use common\models\ProductionOrder;
use common\models\RawMaterial;
use common\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Manages brewing batches and their ingredient lists.
 * Requires `manageProduction` permission.
 */
class BatchController extends Controller
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
                'actions' => [
                    'transition'        => ['post'],
                    'delete'            => ['post'],
                    'add-ingredient'    => ['post'],
                    'remove-ingredient' => ['post'],
                ],
            ],
        ];
    }

    public function actionCreate(int $production_order_id): \yii\web\Response|string
    {
        $productionOrder = ProductionOrder::findOne($production_order_id);

        if (!$productionOrder) {
            throw new NotFoundHttpException('Production order not found.');
        }

        $model                       = new Batch();
        $model->production_order_id  = $production_order_id;
        $model->brew_master_id       = Yii::$app->user->id;

        $brewMasters = $this->getBrewMasterList();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', "Batch {$model->batch_number} created.");
            return $this->redirect(['/production/production-order/view', 'id' => $production_order_id]);
        }

        return $this->render('create', [
            'model'          => $model,
            'productionOrder'=> $productionOrder,
            'brewMasters'    => $brewMasters,
        ]);
    }

    public function actionView(int $id): string
    {
        $batch       = $this->findModel($id);
        $materials   = RawMaterial::find()->orderBy('name')->all();
        $materialMap = \yii\helpers\ArrayHelper::map($materials, 'id', fn($m) => "{$m->name} ({$m->unit})");
        $brewMasters = $this->getBrewMasterList();

        return $this->render('view', [
            'model'       => $batch,
            'materialMap' => $materialMap,
            'brewMasters' => $brewMasters,
        ]);
    }

    public function actionUpdate(int $id): \yii\web\Response|string
    {
        $batch       = $this->findModel($id);
        $brewMasters = $this->getBrewMasterList();

        if ($batch->load(Yii::$app->request->post()) && $batch->save()) {
            Yii::$app->session->setFlash('success', 'Batch updated.');
            return $this->redirect(['view', 'id' => $id]);
        }

        return $this->render('update', [
            'model'       => $batch,
            'brewMasters' => $brewMasters,
        ]);
    }

    public function actionTransition(int $id): \yii\web\Response
    {
        $batch     = $this->findModel($id);
        $newStatus = Yii::$app->request->post('status', '');

        // For completion, capture actual_yield from form
        if ($newStatus === Batch::STATUS_COMPLETED) {
            $yield = Yii::$app->request->post('actual_yield');
            if ($yield !== null && (int) $yield > 0) {
                $batch->actual_yield = (int) $yield;
                $batch->save(false);
            }
        }

        if ($batch->transitionTo($newStatus)) {
            Yii::$app->session->setFlash('success',
                "Batch {$batch->batch_number} advanced to \"{$batch->getStatusLabel()}\"."
            );
        } else {
            Yii::$app->session->setFlash('error', implode(' ', $batch->getFirstErrors()));
        }

        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionAddIngredient(int $batchId): \yii\web\Response
    {
        $batch = $this->findModel($batchId);

        if ($batch->status !== Batch::STATUS_PLANNED) {
            Yii::$app->session->setFlash('error', 'Ingredients can only be modified on planned batches.');
            return $this->redirect(['view', 'id' => $batchId]);
        }

        $ingredient                  = new BatchIngredient();
        $ingredient->batch_id        = $batchId;
        $ingredient->raw_material_id = (int) Yii::$app->request->post('raw_material_id', 0);
        $ingredient->quantity        = (float) Yii::$app->request->post('quantity', 0);

        if ($ingredient->save()) {
            Yii::$app->session->setFlash('success', 'Ingredient added.');
        } else {
            Yii::$app->session->setFlash('error', 'Could not save ingredient: ' . implode(' ', $ingredient->getFirstErrors()));
        }

        return $this->redirect(['view', 'id' => $batchId]);
    }

    public function actionRemoveIngredient(int $id): \yii\web\Response
    {
        $ingredient = BatchIngredient::findOne($id);

        if (!$ingredient) {
            throw new NotFoundHttpException('Ingredient not found.');
        }

        $batchId = $ingredient->batch_id;
        $ingredient->softDelete();

        return $this->redirect(['view', 'id' => $batchId]);
    }

    public function actionDelete(int $id): \yii\web\Response
    {
        $batch = $this->findModel($id);
        $orderId = $batch->production_order_id;

        if ($batch->status !== Batch::STATUS_PLANNED) {
            Yii::$app->session->setFlash('error', 'Only planned batches can be deleted.');
            return $this->redirect(['/production/production-order/view', 'id' => $orderId]);
        }

        $batch->softDelete();

        return $this->redirect(['/production/production-order/view', 'id' => $orderId]);
    }

    private function getBrewMasterList(): array
    {
        $auth  = Yii::$app->authManager;
        $role  = $auth->getRole('brewmaster');
        $users = $role ? $auth->getUserIdsByRole('brewmaster') : [];

        if (empty($users)) {
            return \yii\helpers\ArrayHelper::map(
                User::find()->andWhere(['status' => \common\models\User::STATUS_ACTIVE])->all(),
                'id', 'username'
            );
        }

        return \yii\helpers\ArrayHelper::map(
            User::find()->andWhere(['id' => $users])->all(),
            'id', 'username'
        );
    }

    private function findModel(int $id): Batch
    {
        $model = Batch::find()
            ->with(['productionOrder.product', 'ingredients.rawMaterial', 'brewMaster'])
            ->andWhere(['batch.id' => $id])
            ->one();

        if ($model === null) {
            throw new NotFoundHttpException('Batch not found.');
        }

        return $model;
    }
}
