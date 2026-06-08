<?php

namespace backend\modules\crm\controllers;

use backend\modules\crm\models\CompanySearch;

use common\models\CustomerCompany;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Manages customer company CRUD.
 * All actions require the `manageCrm` permission.
 */
class CompanyController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['manageCrm'],
                    ],
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
        $searchModel  = new CompanySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView(int $id): string
    {
        $company = $this->findModel($id);

        return $this->render('view', [
            'model'        => $company,
            'contacts'     => $company->contacts,
            'interactions' => $company->interactions,
        ]);
    }

    public function actionCreate(): \yii\web\Response|string
    {
        $model = new CustomerCompany();
        $model->created_by = Yii::$app->user->id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', "Company \"{$model->name}\" created.");
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', ['model' => $model]);
    }

    public function actionUpdate(int $id): \yii\web\Response|string
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', "Company \"{$model->name}\" updated.");
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', ['model' => $model]);
    }

    public function actionDelete(int $id): \yii\web\Response
    {
        $model = $this->findModel($id);
        $model->softDelete();

        Yii::$app->session->setFlash('success', "Company \"{$model->name}\" removed.");

        return $this->redirect(['index']);
    }

    private function findModel(int $id): CustomerCompany
    {
        $model = CustomerCompany::findOne($id);

        if ($model === null) {
            throw new NotFoundHttpException('Company not found.');
        }

        return $model;
    }
}
