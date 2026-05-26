<?php

namespace backend\modules\inventory;

/**
 * Inventory module — raw material CRUD, stock transaction log, low-stock alerts.
 * Registered in backend/config/main.php under 'modules' as 'inventory'.
 * Requires `manageInventory` permission (warehouse role and above).
 */
class InventoryModule extends \yii\base\Module
{
    public $controllerNamespace = 'backend\modules\inventory\controllers';

    public function init(): void
    {
        parent::init();
    }
}
