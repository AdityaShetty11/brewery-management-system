<?php

namespace backend\modules\crm\controllers;


use common\models\CustomerCompany;
use common\models\CustomerContact;
use common\models\CrmInteraction;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Manages CRM interaction log entries (calls, emails, meetings, notes).
 * Always scoped to a company.
 */
class InteractionController extends Controller
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

    public function actionCreate(int $company_id): \yii\web\Response|string
    {
        $model             = new CrmInteraction();
        $model->company_id = $company_id;
        $model->staff_id   = Yii::$app->user->id;
        $model->interaction_at = date('Y-m-d H:i:s');

        $contacts = CustomerContact::find()
            ->where(['company_id' => $company_id])
            ->all();

        $contactList = \yii\helpers\ArrayHelper::map($contacts, 'id', 'fullName');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Interaction logged.');
            return $this->redirect(['/crm/company/view', 'id' => $company_id]);
        }

        return $this->render('create', [
            'model'       => $model,
            'company'     => CustomerCompany::findOne($company_id),
            'contactList' => $contactList,
        ]);
    }

    public function actionUpdate(int $id): \yii\web\Response|string
    {
        $model    = $this->findModel($id);
        $contacts = CustomerContact::find()
            ->where(['company_id' => $model->company_id])
            ->all();
        $contactList = \yii\helpers\ArrayHelper::map($contacts, 'id', 'fullName');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Interaction updated.');
            return $this->redirect(['/crm/company/view', 'id' => $model->company_id]);
        }

        return $this->render('update', [
            'model'       => $model,
            'company'     => $model->company,
            'contactList' => $contactList,
        ]);
    }

    public function actionDelete(int $id): \yii\web\Response
    {
        $model     = $this->findModel($id);
        $companyId = $model->company_id;
        $model->softDelete();

        return $this->redirect(['/crm/company/view', 'id' => $companyId]);
    }

    private function findModel(int $id): CrmInteraction
    {
        $model = CrmInteraction::findOne($id);

        if ($model === null) {
            throw new NotFoundHttpException('Interaction not found.');
        }

        return $model;
    }
}
