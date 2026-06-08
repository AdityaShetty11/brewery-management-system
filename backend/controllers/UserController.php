<?php

namespace backend\controllers;

use common\models\User;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * UserController — manage users and their RBAC roles.
 * Requires the 'manageUsers' permission.
 */
class UserController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    ['allow' => true, 'roles' => ['manageUsers']],
                ],
            ],
            'verbs' => [
                'class'   => VerbFilter::class,
                'actions' => ['delete' => ['post']],
            ],
        ];
    }

    // ─── List ────────────────────────────────────────────────────────────────

    public function actionIndex(): string
    {
        $dataProvider = new ActiveDataProvider([
            'query' => User::find()->orderBy(['created_at' => SORT_DESC]),
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('index', ['dataProvider' => $dataProvider]);
    }

    // ─── Detail ──────────────────────────────────────────────────────────────

    public function actionView(int $id): string
    {
        $model = $this->findModel($id);
        $auth  = Yii::$app->authManager;

        $assignedRoles = array_keys($auth->getRolesByUser($id));
        $allRoles      = array_keys($auth->getRoles());

        return $this->render('view', [
            'model'          => $model,
            'assignedRoles'  => $assignedRoles,
            'allRoles'       => $allRoles,
        ]);
    }

    // ─── Create ──────────────────────────────────────────────────────────────

    public function actionCreate(): \yii\web\Response|string
    {
        $model = new User();
        $model->scenario = 'create';

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post('User', []);
            $role = Yii::$app->request->post('role', '');

            $model->username = $post['username'] ?? '';
            $model->email    = $post['email'] ?? '';
            $model->status   = (int) ($post['status'] ?? User::STATUS_ACTIVE);
            $model->setPassword($post['password'] ?? '');
            $model->generateAuthKey();

            if ($model->save()) {
                $this->assignRole($model->id, $role);
                Yii::$app->session->setFlash('success', "User \"{$model->username}\" created.");
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        $roles = array_keys(Yii::$app->authManager->getRoles());

        return $this->render('create', ['model' => $model, 'roles' => $roles]);
    }

    // ─── Update ──────────────────────────────────────────────────────────────

    public function actionUpdate(int $id): \yii\web\Response|string
    {
        $model = $this->findModel($id);
        $auth  = Yii::$app->authManager;
        $roles = array_keys($auth->getRoles());
        $currentRole = array_key_first($auth->getRolesByUser($id)) ?? '';

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post('User', []);
            $role = Yii::$app->request->post('role', '');

            $model->username = $post['username'] ?? $model->username;
            $model->email    = $post['email'] ?? $model->email;
            $model->status   = (int) ($post['status'] ?? $model->status);

            if (!empty($post['password'])) {
                $model->setPassword($post['password']);
            }

            if ($model->save()) {
                $this->assignRole($model->id, $role);
                Yii::$app->session->setFlash('success', "User \"{$model->username}\" updated.");
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model'       => $model,
            'roles'       => $roles,
            'currentRole' => $currentRole,
        ]);
    }

    // ─── Delete ──────────────────────────────────────────────────────────────

    public function actionDelete(int $id): \yii\web\Response
    {
        $model = $this->findModel($id);

        // Prevent deleting yourself
        if ($id === Yii::$app->user->id) {
            Yii::$app->session->setFlash('error', 'You cannot delete your own account.');
            return $this->redirect(['index']);
        }

        Yii::$app->authManager->revokeAll($id);
        $model->delete();

        Yii::$app->session->setFlash('success', 'User deleted.');

        return $this->redirect(['index']);
    }

    // ─── Assign Role (AJAX-friendly) ─────────────────────────────────────────

    public function actionAssignRole(int $id): \yii\web\Response
    {
        $this->findModel($id);
        $role = Yii::$app->request->post('role', '');
        $this->assignRole($id, $role);

        Yii::$app->session->setFlash('success', 'Role updated.');

        return $this->redirect(['view', 'id' => $id]);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function findModel(int $id): User
    {
        $model = User::findOne($id);
        if ($model === null) {
            throw new NotFoundHttpException('User not found.');
        }
        return $model;
    }

    private function assignRole(int $userId, string $roleName): void
    {
        $auth = Yii::$app->authManager;
        $auth->revokeAll($userId);

        if ($roleName !== '') {
            $role = $auth->getRole($roleName);
            if ($role !== null) {
                $auth->assign($role, $userId);
            }
        }
    }
}
