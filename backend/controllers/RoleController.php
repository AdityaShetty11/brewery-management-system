<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;

/**
 * RoleController — CRUD for RBAC roles and their permission assignments.
 * Requires the 'manageUsers' permission.
 */
class RoleController extends Controller
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
                'actions' => [
                    'delete'           => ['post'],
                    'assign-permission'=> ['post'],
                    'revoke-permission'=> ['post'],
                ],
            ],
        ];
    }

    // ─── List ────────────────────────────────────────────────────────────────

    public function actionIndex(): string
    {
        $auth  = Yii::$app->authManager;
        $roles = $auth->getRoles();

        // Enrich with user count and permission count
        $roleData = [];
        foreach ($roles as $name => $role) {
            $users       = $auth->getUserIdsByRole($name);
            $permissions = $this->getDirectPermissions($name);
            $roleData[]  = [
                'name'            => $name,
                'description'     => $role->description ?? '',
                'userCount'       => count($users),
                'permissionCount' => count($permissions),
                'createdAt'       => $role->createdAt,
            ];
        }

        return $this->render('index', ['roleData' => $roleData]);
    }

    // ─── Detail ──────────────────────────────────────────────────────────────

    public function actionView(string $name): string
    {
        $auth = Yii::$app->authManager;
        $role = $auth->getRole($name);
        if (!$role) {
            throw new NotFoundHttpException("Role \"$name\" not found.");
        }

        $allPermissions      = array_keys($auth->getPermissions());
        $assignedPermissions = $this->getDirectPermissions($name);
        $childRoles          = $this->getChildRoles($name);
        $userIds             = $auth->getUserIdsByRole($name);

        // Load user records
        $users = \common\models\User::find()
            ->where(['id' => $userIds])
            ->orderBy('username')
            ->all();

        return $this->render('view', [
            'role'                => $role,
            'allPermissions'      => $allPermissions,
            'assignedPermissions' => $assignedPermissions,
            'childRoles'          => $childRoles,
            'users'               => $users,
        ]);
    }

    // ─── Create ──────────────────────────────────────────────────────────────

    public function actionCreate(): \yii\web\Response|string
    {
        if (Yii::$app->request->isPost) {
            $auth        = Yii::$app->authManager;
            $name        = trim(Yii::$app->request->post('name', ''));
            $description = trim(Yii::$app->request->post('description', ''));

            if ($name === '') {
                Yii::$app->session->setFlash('error', 'Role name is required.');
                return $this->render('create', ['name' => '', 'description' => '']);
            }

            if ($auth->getRole($name)) {
                Yii::$app->session->setFlash('error', "Role \"$name\" already exists.");
                return $this->render('create', ['name' => $name, 'description' => $description]);
            }

            $role              = $auth->createRole($name);
            $role->description = $description;
            $auth->add($role);

            // Assign selected permissions
            $permissions = Yii::$app->request->post('permissions', []);
            foreach ($permissions as $permName) {
                $perm = $auth->getPermission($permName);
                if ($perm) {
                    $auth->addChild($role, $perm);
                }
            }

            Yii::$app->session->setFlash('success', "Role \"$name\" created.");
            return $this->redirect(['view', 'name' => $name]);
        }

        $allPermissions = array_keys(Yii::$app->authManager->getPermissions());

        return $this->render('create', [
            'name'           => '',
            'description'    => '',
            'allPermissions' => $allPermissions,
        ]);
    }

    // ─── Update ──────────────────────────────────────────────────────────────

    public function actionUpdate(string $name): \yii\web\Response|string
    {
        $auth = Yii::$app->authManager;
        $role = $auth->getRole($name);
        if (!$role) {
            throw new NotFoundHttpException("Role \"$name\" not found.");
        }

        $allPermissions      = array_keys($auth->getPermissions());
        $assignedPermissions = $this->getDirectPermissions($name);

        if (Yii::$app->request->isPost) {
            $description = trim(Yii::$app->request->post('description', ''));

            $role->description = $description;
            $auth->update($name, $role);

            // Sync permissions: revoke all direct, re-assign selected
            foreach ($assignedPermissions as $p) {
                $perm = $auth->getPermission($p);
                if ($perm) {
                    try { $auth->removeChild($role, $perm); } catch (\Exception $e) {}
                }
            }
            $permissions = Yii::$app->request->post('permissions', []);
            foreach ($permissions as $permName) {
                $perm = $auth->getPermission($permName);
                if ($perm) {
                    $auth->addChild($role, $perm);
                }
            }

            Yii::$app->session->setFlash('success', "Role \"$name\" updated.");
            return $this->redirect(['view', 'name' => $name]);
        }

        return $this->render('update', [
            'role'                => $role,
            'allPermissions'      => $allPermissions,
            'assignedPermissions' => $assignedPermissions,
        ]);
    }

    // ─── Delete ──────────────────────────────────────────────────────────────

    public function actionDelete(string $name): \yii\web\Response
    {
        $auth = Yii::$app->authManager;
        $role = $auth->getRole($name);
        if (!$role) {
            throw new NotFoundHttpException("Role \"$name\" not found.");
        }

        // Prevent deleting core system roles
        $coreRoles = ['admin', 'customer', 'staff', 'brewmaster', 'warehouse'];
        if (in_array($name, $coreRoles, true)) {
            Yii::$app->session->setFlash('error', "Cannot delete built-in role \"$name\".");
            return $this->redirect(['index']);
        }

        // Prevent deleting roles that have users assigned
        $users = $auth->getUserIdsByRole($name);
        if (!empty($users)) {
            Yii::$app->session->setFlash('error', "Cannot delete \"$name\" — " . count($users) . " user(s) still assigned.");
            return $this->redirect(['index']);
        }

        $auth->remove($role);
        Yii::$app->session->setFlash('success', "Role \"$name\" deleted.");

        return $this->redirect(['index']);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    /**
     * Returns permission names directly assigned to a role (not inherited).
     */
    private function getDirectPermissions(string $roleName): array
    {
        $auth     = Yii::$app->authManager;
        $role     = $auth->getRole($roleName);
        if (!$role) return [];

        $children = $auth->getChildren($roleName);
        $perms    = [];
        foreach ($children as $name => $item) {
            if ($item->type === \yii\rbac\Item::TYPE_PERMISSION) {
                $perms[] = $name;
            }
        }
        return $perms;
    }

    /**
     * Returns child role names for a given role.
     */
    private function getChildRoles(string $roleName): array
    {
        $auth     = Yii::$app->authManager;
        $children = $auth->getChildren($roleName);
        $roles    = [];
        foreach ($children as $name => $item) {
            if ($item->type === \yii\rbac\Item::TYPE_ROLE) {
                $roles[] = $name;
            }
        }
        return $roles;
    }
}
