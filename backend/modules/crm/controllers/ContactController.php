<?php

namespace backend\modules\crm\controllers;

use backend\modules\crm\models\ContactSearch;
use common\models\AuditLog;
use common\models\CustomerCompany;
use common\models\CustomerContact;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Manages customer contact CRUD.
 * Contacts always belong to a company; company_id is required for create.
 */
class ContactController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    ['allow' => true, 'roles' => ['manageCrm']],
                ],
            ],
            'verbs' => [
                'class'   => VerbFilter::class,
                'actions' => ['delete' => ['post']],
            ],
        ];
    }

    public function actionIndex(?int $company_id = null): string
    {
        $searchModel  = new ContactSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $company_id);

        $company = $company_id ? CustomerCompany::findOne($company_id) : null;

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'company'      => $company,
        ]);
    }

    public function actionCreate(?int $company_id = null): \yii\web\Response|string
    {
        $model             = new CustomerContact();
        $model->company_id = $company_id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            AuditLog::record('crm.contact.created', 'CustomerContact', $model->id);
            Yii::$app->session->setFlash('success', "Contact \"{$model->getFullName()}\" created.");
            return $this->redirect(['/crm/company/view', 'id' => $model->company_id]);
        }

        $companies = CustomerCompany::find()->select(['name', 'id'])->indexBy('id')->column();

        return $this->render('create', [
            'model'     => $model,
            'companies' => $companies,
        ]);
    }

    public function actionUpdate(int $id): \yii\web\Response|string
    {
        $model     = $this->findModel($id);
        $companies = CustomerCompany::find()->select(['name', 'id'])->indexBy('id')->column();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            AuditLog::record('crm.contact.updated', 'CustomerContact', $model->id);
            Yii::$app->session->setFlash('success', "Contact updated.");
            return $this->redirect(['/crm/company/view', 'id' => $model->company_id]);
        }

        return $this->render('update', [
            'model'     => $model,
            'companies' => $companies,
        ]);
    }

    public function actionDelete(int $id): \yii\web\Response
    {
        $model      = $this->findModel($id);
        $companyId  = $model->company_id;
        $model->softDelete();

        AuditLog::record('crm.contact.deleted', 'CustomerContact', $id);
        Yii::$app->session->setFlash('success', 'Contact removed.');

        return $this->redirect(['/crm/company/view', 'id' => $companyId]);
    }

    private function findModel(int $id): CustomerContact
    {
        $model = CustomerContact::findOne($id);

        if ($model === null) {
            throw new NotFoundHttpException('Contact not found.');
        }

        return $model;
    }
}
