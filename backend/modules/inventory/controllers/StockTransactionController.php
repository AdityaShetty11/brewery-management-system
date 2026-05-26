<?php

namespace backend\modules\inventory\controllers;

use backend\modules\inventory\models\StockTransactionSearch;
use common\models\StockTransaction;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

/**
 * Read-only view of all stock transactions — the full inventory ledger.
 * Requires `manageInventory` permission.
 */
class StockTransactionController extends Controller
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
        ];
    }

    public function actionIndex(): string
    {
        $searchModel  = new StockTransactionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}
