<?php

namespace console\controllers;

use common\models\User;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;

/**
 * RBAC setup commands.
 *
 * Usage:
 *   php yii rbac/init          — creates all roles and permissions
 *   php yii rbac/assign-admin  — assigns admin role to user ID 1
 */
class RbacController extends Controller
{
    /**
     * Creates the full RBAC hierarchy.
     *
     * Run this ONCE after running migrations:
     *   php yii rbac/init
     */
    public function actionInit(): int
    {
        $auth = Yii::$app->authManager;
        $auth->removeAll();

        // Permissions

        $viewCatalog   = $this->createPermission($auth, 'viewCatalog',   'Browse the product catalog');
        $placeOrder    = $this->createPermission($auth, 'placeOrder',    'Place a new order');
        $viewOwnOrders = $this->createPermission($auth, 'viewOwnOrders', 'View own order history');
        $viewAllOrders = $this->createPermission($auth, 'viewAllOrders', 'View all customer orders');
        $manageCrm     = $this->createPermission($auth, 'manageCrm',     'Manage CRM companies and contacts');
        $manageProducts= $this->createPermission($auth, 'manageProducts','Manage product catalog');
        $manageOrders  = $this->createPermission($auth, 'manageOrders',  'Manage and process orders');
        $manageProduction = $this->createPermission($auth, 'manageProduction', 'Manage production orders and batches');
        $manageInventory  = $this->createPermission($auth, 'manageInventory',  'Manage raw materials and stock');
        $viewReports   = $this->createPermission($auth, 'viewReports',   'View sales, inventory, and production reports');
        $manageUsers   = $this->createPermission($auth, 'manageUsers',   'Manage user accounts and RBAC');

        // Roles

        // customer
        $customer = $auth->createRole('customer');
        $customer->description = 'Registered brewery customer';
        $auth->add($customer);
        $auth->addChild($customer, $viewCatalog);
        $auth->addChild($customer, $placeOrder);
        $auth->addChild($customer, $viewOwnOrders);

        // staff
        $staff = $auth->createRole('staff');
        $staff->description = 'Internal brewery staff';
        $auth->add($staff);
        $auth->addChild($staff, $customer);          // inherits customer permissions
        $auth->addChild($staff, $viewAllOrders);
        $auth->addChild($staff, $manageCrm);
        $auth->addChild($staff, $manageProducts);
        $auth->addChild($staff, $manageOrders);
        $auth->addChild($staff, $viewReports);

        // brewmaster
        $brewmaster = $auth->createRole('brewmaster');
        $brewmaster->description = 'Brew master — manages production';
        $auth->add($brewmaster);
        $auth->addChild($brewmaster, $staff);         // inherits staff permissions
        $auth->addChild($brewmaster, $manageProduction);

        // warehouse
        $warehouse = $auth->createRole('warehouse');
        $warehouse->description = 'Warehouse staff — manages inventory';
        $auth->add($warehouse);
        $auth->addChild($warehouse, $staff);          // inherits staff permissions
        $auth->addChild($warehouse, $manageInventory);

        // admin
        $admin = $auth->createRole('admin');
        $admin->description = 'System administrator — full access';
        $auth->add($admin);
        $auth->addChild($admin, $brewmaster);         // inherits brewmaster
        $auth->addChild($admin, $warehouse);          // inherits warehouse
        $auth->addChild($admin, $manageUsers);

        $this->stdout("RBAC hierarchy created successfully.\n");
        $this->stdout("Roles: customer, staff, brewmaster, warehouse, admin\n");
        $this->stdout("Run 'php yii rbac/assign-admin <userId>' to grant admin access.\n");

        return ExitCode::OK;
    }

    /**
     * Assigns the admin role to a specific user.
     *
     * Usage:
     *   php yii rbac/assign-admin 1
     */
    public function actionAssignAdmin(int $userId): int
    {
        $auth  = Yii::$app->authManager;
        $user  = User::findOne($userId);

        if (!$user) {
            $this->stderr("User with ID {$userId} not found.\n");
            return ExitCode::DATAERR;
        }

        $role = $auth->getRole('admin');

        if (!$role) {
            $this->stderr("Admin role not found. Run 'php yii rbac/init' first.\n");
            return ExitCode::UNAVAILABLE;
        }

        // Remove any existing roles first to avoid duplicate assignment
        $auth->revokeAll($userId);
        $auth->assign($role, $userId);

        // Make sure user is active
        $user->status = User::STATUS_ACTIVE;
        $user->save(false);

        $this->stdout("Admin role assigned to user '{$user->username}' (ID: {$userId}).\n");

        return ExitCode::OK;
    }

    /**
     * Lists all roles assigned to a user.
     *
     * Usage:
     *   php yii rbac/roles 1
     */
    public function actionRoles(int $userId): int
    {
        $auth  = Yii::$app->authManager;
        $roles = $auth->getRolesByUser($userId);

        if (empty($roles)) {
            $this->stdout("No roles assigned to user ID {$userId}.\n");
            return ExitCode::OK;
        }

        $this->stdout("Roles for user ID {$userId}:\n");
        foreach ($roles as $role) {
            $this->stdout("  - {$role->name}: {$role->description}\n");
        }

        return ExitCode::OK;
    }

    // Helper

    private function createPermission(\yii\rbac\ManagerInterface $auth, string $name, string $description): \yii\rbac\Permission
    {
        $permission              = $auth->createPermission($name);
        $permission->description = $description;
        $auth->add($permission);
        return $permission;
    }
}
